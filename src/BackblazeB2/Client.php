<?php

namespace App\BackblazeB2;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Zaxbux\BackblazeB2\Http\Middleware\ApplyAuthorizationMiddleware;
use Zaxbux\BackblazeB2\Http\Middleware\ExceptionMiddleware;
use Zaxbux\BackblazeB2\Http\Middleware\RetryMiddleware;
use Zaxbux\BackblazeB2\Utils;

class Client extends \Zaxbux\BackblazeB2\Client
{

    private array $guzzleConfig;

    public function __construct($config, $guzzleConfig = [])
    {
        $defaultConfig = [
            'authorizationCache' => new AuthorizationCache(),
        ];

        if (isset($config['authorizationCache'])) {
            if ($config['authorizationCache'] === false) {
                unset($defaultConfig['authorizationCache']);
                unset($config['authorizationCache']);
            }
        }

        $config = array_merge($defaultConfig, $config);

        $this->guzzleConfig = $guzzleConfig;
        parent::__construct($config);
    }

    protected function createDefaultHttpClient(): ClientInterface
    {
        $stack = $this->config->handler();

        $stack->push(new ExceptionMiddleware(), 'exception_handler');
        $stack->push(new ApplyAuthorizationMiddleware($this), 'b2_authorization');
        $stack->push(new RetryMiddleware($this->config), 'retry');

        $defaultGuzzleConfig = [
            'base_uri' => \Zaxbux\BackblazeB2\Client::B2_API_VERSION,
            'http_errors' => $this->config->useHttpErrors ?? false,
            'allow_redirects' => false,
            'handler' => $stack,
            'headers' => [
                'User-Agent' => Utils::getUserAgent($this->config->applicationName()),
            ],
        ];

        $guzzleConfig = array_merge($defaultGuzzleConfig, $this->guzzleConfig);

        return new GuzzleClient($guzzleConfig);
    }

}