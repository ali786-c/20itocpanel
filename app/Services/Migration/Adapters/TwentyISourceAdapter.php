<?php

namespace App\Services\Migration\Adapters;

use App\Contracts\Migration\SourceAdapterInterface;
use App\Models\SourceConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class TwentyISourceAdapter implements SourceAdapterInterface
{
    protected ?SourceConnection $connection = null;
    protected string $baseUrl = 'https://api.20i.com';

    public function setConnection(SourceConnection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    protected function getClient(): PendingRequest
    {
        if (!$this->connection) {
            throw new \RuntimeException('SourceConnection not set on adapter.');
        }

        // According to the plan, 20i API uses a Bearer token which is base64 encoded API key.
        // We will decrypt the token from the credential model.
        $token = $this->connection->credential->encrypted_value; // Replace with proper decryption logic later

        return Http::withoutVerifying()->withToken($token)->baseUrl($this->baseUrl);
    }

    public function verifyConnection(): bool
    {
        try {
            $response = $this->getClient()->get('/package');
            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error('20i API Error: ' . $response->body());
                echo "20i Error: " . $response->body() . "\n";
            }
            return $response->successful();
        } catch (\Exception $e) {
            echo "20i Exception: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function getInventory(string $packageId = ''): array
    {
        // For 20i, GET /package returns a list of packages.
        // If packageId is provided, we can fetch specific package details.
        // For discovery phase, we just want the list of all packages to populate the manifest.
        try {
            $endpoint = empty($packageId) ? '/package' : '/package/' . $packageId;
            $response = $this->getClient()->get($endpoint);
            
            if ($response->successful()) {
                // The API returns an object or array of packages.
                // We'll return it as an array to be stored in the DB.
                return $response->json() ?? [];
            }
            
            \Illuminate\Support\Facades\Log::error('20i Inventory Error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('20i Inventory Exception: ' . $e->getMessage());
            return [];
        }
    }
}
