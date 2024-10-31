<?php

namespace OAuthServer;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Repositories\AccessTokenRepository;
use function Hyperf\Support\make;

class ResourceServerFactory
{
    protected $config;

    public function __construct(protected ContainerInterface $container)
    {
        $this->config    = $container->get(ConfigInterface::class);
    }

    public function __invoke(): ResourceServer
    {
        return new ResourceServer(
            make(AccessTokenRepository::class),
            'file://' . BASE_PATH . '/var/oauth-public.key',
        );
    }
}
