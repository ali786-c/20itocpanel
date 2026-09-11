<?php
$host = 'ftp.sg.stackcp.com';
$user = 'nexloop.co.nz';
$pass = '4)OOLiZj5hf';

echo "Connecting to $host...\n";
$conn = @ftp_connect($host, 21, 10);
if ($conn) {
    echo "Connected. Logging in...\n";
    if (@ftp_login($conn, $user, $pass)) {
        echo "Login SUCCESS!\n";
        ftp_pasv($conn, true);
        $files = ftp_nlist($conn, '/public_html');
        print_r($files);
        ftp_close($conn);
    } else {
        echo "Login FAILED.\n";
    }
} else {
    echo "Connection FAILED.\n";
}
