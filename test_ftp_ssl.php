<?php
$hosts = ['ftp.sg.stackcp.com', 'ftp.nexloop.co.nz'];
$user = 'nexloop.co.nz';
$pass = '4)OOLiZj5hf';

foreach($hosts as $host) {
    echo "Trying FTP over SSL on $host...\n";
    $conn = @ftp_ssl_connect($host, 21, 10);
    if ($conn) {
        if (@ftp_login($conn, $user, $pass)) {
            echo "SUCCESS: Logged in to $host using FTPS!\n";
            ftp_pasv($conn, true);
            $files = ftp_nlist($conn, '/public_html');
            if ($files !== false) {
                echo "Files found: " . count($files) . "\n";
            } else {
                echo "Failed to list files.\n";
            }
            ftp_close($conn);
            exit(0);
        } else {
            echo "FAILED login on $host.\n";
        }
    } else {
        echo "FAILED connection to $host.\n";
    }
    
    echo "Trying Plain FTP on $host...\n";
    $conn = @ftp_connect($host, 21, 10);
    if ($conn) {
        if (@ftp_login($conn, $user, $pass)) {
            echo "SUCCESS: Logged in to $host using Plain FTP!\n";
            ftp_pasv($conn, true);
            $files = ftp_nlist($conn, '/public_html');
            if ($files !== false) {
                echo "Files found: " . count($files) . "\n";
            } else {
                echo "Failed to list files.\n";
            }
            ftp_close($conn);
            exit(0);
        } else {
            echo "FAILED login on $host.\n";
        }
    } else {
        echo "FAILED connection to $host.\n";
    }
}
