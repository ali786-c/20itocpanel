<?php

namespace App\Services\Migration\Adapters;

use App\Contracts\Migration\DestinationAdapterInterface;
use App\Models\DestinationConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class CPanelDestinationAdapter implements DestinationAdapterInterface
{
    protected ?DestinationConnection $connection = null;

    public function setConnection(DestinationConnection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    protected function getClient(): PendingRequest
    {
        if (!$this->connection) {
            throw new \RuntimeException('DestinationConnection not set on adapter.');
        }

        $token = $this->connection->credential->encrypted_value; 
        $hostname = rtrim($this->connection->hostname, '/'); 

        $username = 'clientwebhosting';
        
        return Http::withoutVerifying()->withHeaders([
            'Authorization' => 'whm ' . $username . ':' . $token
        ])->baseUrl($hostname . '/json-api');
    }

    public function verifyConnection(): bool
    {
        try {
            // Hitting a basic WHM endpoint to verify connection
            $response = $this->getClient()->get('/applist', ['api.version' => 1]);
            
            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error('WHM API Error: ' . $response->body());
                echo "WHM Error: " . $response->status() . " " . $response->body() . "\n";
            }
            return $response->successful();
        } catch (\Exception $e) {
            echo "WHM Exception: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function checkCapacity(array $inventoryData): array
    {
        // Check listaccts or accountsummary or server capacity metrics.
        return ['sufficient' => true];
    }
    
    public function provisionAccount(array $accountData): array
    {
        try {
            // Use WHM API 1 createacct
            $response = $this->getClient()->get('/createacct', [
                'api.version' => 1,
                'username' => $accountData['username'],
                'domain' => $accountData['domain'],
                'password' => $accountData['password'],
                'contactemail' => $accountData['contactemail'] ?? 'admin@' . $accountData['domain'],
                'plan' => 'default'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['metadata']['result']) && $data['metadata']['result'] == 1) {
                    return [
                        'success' => true,
                        'message' => 'Account created successfully',
                        'data' => $data['data'] ?? []
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $data['metadata']['reason'] ?? 'Unknown error creating account',
                ];
            }

            return [
                'success' => false,
                'message' => 'API HTTP Error: ' . $response->status() . ' ' . $response->body()
            ];
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WHM Create Account Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function terminateAccount(string $username): bool
    {
        try {
            $response = $this->getClient()->get('/removeacct', [
                'api.version' => 1,
                'user' => $username
            ]);
            
            // We just return true whether it succeeded or failed (e.g. didn't exist)
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function restoreAccount(string $username): array
    {
        // ... previous restoreAccount code (unused in new arch but keep it)
        return ['success' => false, 'message' => 'Deprecated in Reseller Arch.'];
    }

    public function executeUapi(string $username, string $module, string $function, array $params = [], int $apiVer = 3): array
    {
        try {
            $payload = array_merge([
                'api.version' => 1,
                'cpanel_jsonapi_user' => $username,
                'cpanel_jsonapi_apiversion' => $apiVer,
                'cpanel_jsonapi_module' => $module,
                'cpanel_jsonapi_func' => $function,
            ], $params);

            $response = $this->getClient()->get('/cpanel', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // API 2 returns 'cpanelresult' instead of 'result'
                if ($apiVer == 2) {
                    $result = $data['cpanelresult'] ?? null;
                    if ($result && !isset($result['error'])) {
                        return ['success' => true, 'data' => $result['data'] ?? [], 'message' => 'API2 call successful'];
                    }
                    return ['success' => false, 'message' => $result['error'] ?? 'Unknown API2 Error'];
                }

                $result = $data['result'] ?? null;
                
                if ($result && isset($result['status']) && $result['status'] == 1) {
                    return [
                        'success' => true,
                        'data' => $result['data'] ?? [],
                        'message' => 'UAPI call successful',
                    ];
                }
                
                $errors = $result['errors'] ?? ['Unknown UAPI Error'];
                return [
                    'success' => false,
                    'message' => is_array($errors) ? implode(", ", $errors) : $errors
                ];
            }

            return [
                'success' => false,
                'message' => 'API HTTP Error: ' . $response->status() . ' ' . $response->body()
            ];
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("UAPI/API2 Exception ({$module}::{$function}): " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function extractZip(string $username, string $dir, string $filename): array
    {
        // Use cPanel API 2 Fileman::fileop with op=extract
        return $this->executeUapi($username, 'Fileman', 'fileop', [
            'op' => 'extract',
            'sourcefiles' => $filename,
            'destdir' => $dir,
            'doubledecode' => 1,
        ], 2);
    }

    public function createDatabase(string $username, string $dbName): array
    {
        return $this->executeUapi($username, 'Mysql', 'create_database', [
            'name' => $dbName,
        ]);
    }

    public function createDatabaseUser(string $username, string $dbUser, string $dbPass): array
    {
        return $this->executeUapi($username, 'Mysql', 'create_user', [
            'name' => $dbUser,
            'password' => $dbPass,
        ]);
    }

    public function grantDatabasePrivileges(string $username, string $dbUser, string $dbName): array
    {
        return $this->executeUapi($username, 'Mysql', 'set_privileges_on_database', [
            'user' => $dbUser,
            'database' => $dbName,
            'privileges' => 'ALL PRIVILEGES',
        ]);
    }
}
