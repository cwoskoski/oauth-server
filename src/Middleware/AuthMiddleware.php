<?php

namespace OAuthServer\Middleware;

use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Server\MiddlewareInterface;
use OAuthServer\Repositories\UserRepository;
use OAuthServer\Repositories\ClientRepository;
use OAuthServer\Exception\AuthenticationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class AuthMiddleware implements MiddlewareInterface
{
    use ValidateScopeTrait;

    protected $client;
    protected $user;

    public function __construct(protected UserRepository $userRepository, protected ClientRepository $repository, protected ResourceServer $server)
    {}

    public function process(Request $request, Handler $handler): ResponseInterface
    {
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException|AuthenticationException|\Exception $exception) {
            throw new AuthenticationException("Unauthorize: {$exception->getMessage()}");
        }

        $dispatched = $request->getAttribute(\Hyperf\HttpServer\Router\Dispatched::class);
        $scopes = $dispatched->handler->options['scopes']?? [];

        $this->validate($request, $scopes);

        // asign oauth client & user information
        $request = $request->withAttribute('client', $this->client);
        $request = $request->withAttribute('user', $this->user);

        return $handler->handle($request);
    }

    protected function validate($request, $scopes)
    {
        $client = $this->repository->findActive($request->getAttribute('oauth_client_id'));

        if (is_null($client)) {
            throw new AuthenticationException("Unauthorize.");
        }

        $userId = $request->getAttribute('oauth_user_id');
        $user = $this->userRepository->getUserByProviderUserId($userId, $client);

        if (is_null($user)) {
            throw new AuthenticationException("Unauthorize.");
        }

        $this->client   = $client;
        $this->user     = $user;

        $tokenScope = $request->getAttribute('oauth_scopes')?? [];

        $this->validateScopes($tokenScope, $scopes);
    }
}
