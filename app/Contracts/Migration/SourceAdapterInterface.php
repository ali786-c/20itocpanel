<?php

namespace App\Contracts\Migration;

use App\Models\SourceConnection;

interface SourceAdapterInterface
{
    /**
     * Set the connection instance for this adapter.
     */
    public function setConnection(SourceConnection $connection): self;

    /**
     * Authenticate and verify the connection is valid.
     */
    public function verifyConnection(): bool;

    /**
     * Get a complete, normalized inventory of the source package.
     */
    public function getInventory(string $packageId = ''): array;
}
