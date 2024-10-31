<?php

namespace OAuthServer\Middleware;

use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Server\MiddlewareInterface;
use OAuthServer\Repositories\ClientRepository;
use OAuthServer\Exception\AuthenticationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use League\OAuth2\Server\Exception\OAuthServerException;

class ClientMiddleware implements MiddlewareInterface
{
    use ValidateScopeTrait;

    protected $client;

    public function __construct(protected ClientRepository $repository, protected ResourceServer $server)
    {}

    public function process(Request $request, Handler $handler): ResponseInterface
    {
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            throw new AuthenticationException("Unauthorize: {$exception->getMessage()}");
        } catch (\Exception $exception) {
            throw new AuthenticationException("Unauthorize: {$exception->getMessage()}");
        }

        $dispatched = $request->getAttribute(\Hyperf\HttpServer\Router\Dispatched::class);
        $scopes = $dispatched->handler->options['scopes']?? [];

        $this->validate($request, $scopes);

        $request = $request->withAttribute('client', $this->client);

        return $handler->handle($request);
    }

    protected function validate($request, $scopes): void
    {
        $client = $this->repository->findActive($request->getAttribute('oauth_client_id'));

        if (is_null($client)) {
            throw new AuthenticationException("Unauthorize.");
        }
        
        if($client->password_client) {
            throw new AuthenticationException("Unauthorize.");
        }

        $this->client   = $client;

        $tokenScope = $request->getAttribute('oauth_scopes')?? [];

        $this->validateScopes($tokenScope, $scopes);
    }
}
