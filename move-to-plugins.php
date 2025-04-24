<?php
// move-to-plugins.php

$sourceDir = realpath(__DIR__ . '/../orm-wordpress');
$destDir   = __DIR__ . '/../../../wp-content/plugins/ORM-WordPress';
$vendorDir = realpath(__DIR__ . '/../../../vendor');

function moveFiles( $source, $destination ) {
    if ( !file_exists($source) ) {
        die("❌ Source directory does not exist: $source\n");
    }

    if ( !file_exists($destination) ) {
        mkdir($destination, 0755, true);
    }

    $items = scandir($source);
    foreach ( $items as $item ) {
        if ( in_array($item, ['.', '..']) )
            continue;

        $srcPath  = $source . '/' . $item;
        $destPath = $destination . '/' . $item;

        if ( is_dir($srcPath) ) {
            moveFiles($srcPath, $destPath);
            @rmdir($srcPath);
        } else {
            if ( @rename($srcPath, $destPath) ) {
            } else {
                echo "❌ Failed to move file: $item\n";
            }
        }
    }
}

function deleteDirectory( $dir ) {
    if ( !file_exists($dir) ) {
        echo "⚠️ Vendor directory not found: $dir\n";
        return;
    }

    $items = scandir($dir);
    foreach ( $items as $item ) {
        if ( $item === '.' || $item === '..' )
            continue;

        $path = $dir . '/' . $item;
        if ( is_dir($path) ) {
            deleteDirectory($path);
            @rmdir($path);
        } else {
            @unlink($path);
        }
    }

    if ( @rmdir($dir) ) {
    } else {
        echo "❌ Failed to delete vendor directory: $dir\n";
    }
}

echo "🚀 Starting transfer...\n";
moveFiles($sourceDir, $destDir);

deleteDirectory($vendorDir);
echo "🏁 Done.\n";
?>
