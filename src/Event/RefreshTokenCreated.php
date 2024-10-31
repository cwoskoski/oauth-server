<?php

namespace OAuthServer\Event;

readonly class RefreshTokenCreated
{

    /**
     * Create a new event instance.
     *
     * @param  string  $refreshTokenId
     * @param  string  $accessTokenId
     * @return void
     */
    public function __construct(public string $refreshTokenId, public string $accessTokenId)
    {}
}
