<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$adapter = new \App\Services\Migration\Adapters\TwentyISourceAdapter();
$conn = \App\Models\SourceConnection::first();
$adapter->setConnection($conn);

$res = $adapter->getInventory('3648191');
print_r($res);

// Also try to get FTP details
$resWeb = $adapter->getInventory('3648191/web');
echo "\n\nWEB DETAILS:\n";
print_r($resWeb);

$resFtp = $adapter->getInventory('3648191/ftp');
echo "\n\nFTP DETAILS:\n";
print_r($resFtp);
