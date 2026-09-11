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
        try {
            // WHM API 1 restoreaccount will automatically look for /home/cpmove-$username.tar.gz
            $response = $this->getClient()->get('/restoreaccount', [
                'api.version' => 1,
                'user' => $username,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['metadata']['result']) && $data['metadata']['result'] == 1) {
                    return [
                        'success' => true,
                        'message' => 'Account restored successfully',
                        'data' => $data['data'] ?? []
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $data['metadata']['reason'] ?? 'Unknown error restoring account',
                ];
            }

            return [
                'success' => false,
                'message' => 'API HTTP Error: ' . $response->status() . ' ' . $response->body()
            ];
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WHM Restore Account Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
