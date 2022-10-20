# Backblaze B2 Client

Extension of ```zaxbux/backblaze-b2-php``` Backblaze B2 Client Library
https://github.com/zaxbux/backblaze-b2-php.

I extended his library because I needed the ability to configure the Guzzle Client. In addition, the B2 Client will
automatically use AuthorizationCache I wrote. As such, this Library matches the ```zaxbux/backblaze-b2-php``` version
tagging.

Check the ```examples``` folder for a simple use case with ```league/flysystem```
and ``` zaxbux/flysystem-backblaze-b2``` adapter.

```composer require arajcany/backblaze-b2-client```

```php
//BackblazeB2 Client options
$config = [
    'applicationKeyId' => $keyId,
    'applicationKey' => $key,
    //'authorizationCache' => false, //uncomment to stop using the AuthorizationCache, but why would you?
];

//Guzzle Client options
$guzzleConfig = [
    'verify' => CONFIG . "cacert.pem"
];

$client = new Client($config, $guzzleConfig);
$adapter = new BackblazeB2Adapter($client, $bucketId);
$filesystem = new Filesystem($adapter);
```

