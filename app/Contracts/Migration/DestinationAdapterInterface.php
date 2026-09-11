<?php

namespace App\Contracts\Migration;

use App\Models\DestinationConnection;

interface DestinationAdapterInterface
{
    /**
     * Set the connection instance for this adapter.
     */
    public function setConnection(DestinationConnection $connection): self;

    /**
     * Authenticate and verify the connection is valid.
     */
    public function verifyConnection(): bool;

    /**
     * Check if the destination has sufficient capacity (disk, inodes) for the migration.
     */
    public function checkCapacity(array $inventoryData): array;
    
    /**
     * Provision the initial account on the destination server.
     */
    public function provisionAccount(array $accountData): array;
}
