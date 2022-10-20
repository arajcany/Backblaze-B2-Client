<?php

namespace App\BackblazeB2;

use Zaxbux\BackblazeB2\Interfaces\AuthorizationCacheInterface;
use Zaxbux\BackblazeB2\Object\AccountAuthorization;

class AuthorizationCache implements AuthorizationCacheInterface
{

    /**
     * @inheritDoc
     */
    public function put($key, AccountAuthorization $authorization): void
    {
        $expiry = (time() + AuthorizationCacheInterface::EXPIRES) - 60; //60 seconds safety margin

        $authorization = $authorization->jsonSerialize();
        $authorization['expiry'] = $expiry;
        $authorization = json_encode($authorization, JSON_PRETTY_PRINT);

        $saveLocation = sys_get_temp_dir() . "/b2_authorization_{$key}.json";
        file_put_contents($saveLocation, $authorization);
    }

    /**
     * @inheritDoc
     */
    public function get($key): ?AccountAuthorization
    {
        $saveLocation = sys_get_temp_dir() . "/b2_authorization_{$key}.json";
        if (!is_file($saveLocation)) {
            return null;
        }

        $authorization = json_decode(file_get_contents($saveLocation), JSON_OBJECT_AS_ARRAY);
        if (isset($authorization['authorizationToken'])) {
            if (isset($authorization['expiry'])) {
                $currentTime = time();
                if ($currentTime < $authorization['expiry']) {
                    return AccountAuthorization::fromArray($authorization);
                }
            }
        }

        return null;
    }
}