<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/paths.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\BackblazeB2\Client;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Zaxbux\Flysystem\BackblazeB2Adapter;

//Your Backblaze B2 account details
$keyId = '...';
$key = '...';
$bucketId = '...';

//BackblazeB2 Client options
$config = [
    'applicationKeyId' => $keyId,
    'applicationKey' => $key,
    //'authorizationCache' => false, //uncomment to stop using the AuthorizationCache, but why would you?
];

//Guzzle options
$guzzleConfig = [
    'verify' => CONFIG . "cacert.pem"
];

$client = new Client($config, $guzzleConfig);
$adapter = new BackblazeB2Adapter($client, $bucketId);
$filesystem = new Filesystem($adapter);
$baseDir = '';
$recursive = false;

try {
    $listing = $filesystem->listContents($baseDir, $recursive);

    /**
     * @var StorageAttributes $item
     */
    foreach ($listing as $item) {
        $path = $item->path();

        if ($item instanceof FileAttributes) {
            print_r("FILE: " . $path . "\r\n");
            // handle the file
        } elseif ($item instanceof DirectoryAttributes) {
            print_r("FOLDER: " . $path . "\r\n");
            // handle the directory
        }
    }
} catch (FilesystemException $exception) {
    // handle the error
}

