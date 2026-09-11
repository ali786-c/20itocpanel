<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$adapter = new \App\Services\Migration\Adapters\TwentyISourceAdapter();
$conn = \App\Models\SourceConnection::first();
$adapter->setConnection($conn);

// Get client and send POST to unlock FTP
$client = Http::withoutVerifying()->withToken($conn->credential->encrypted_value)->baseUrl('https://api.20i.com');

$res = $client->post('/package/3648191/web/unlock-ftp');
echo "web/unlock-ftp: " . $res->status() . " " . $res->body() . "\n";

$res = $client->post('/package/3648191/ftp/unlock');
echo "ftp/unlock: " . $res->status() . " " . $res->body() . "\n";
