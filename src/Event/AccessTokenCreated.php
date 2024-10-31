<?php

namespace OAuthServer\Event;

readonly class AccessTokenCreated
{
    /**
     * Create a new event instance.
     *
     * @param  string  $tokenId
     * @param  string  $userId
     * @param  string  $clientId
     * @return void
     */
    public function __construct(public string $tokenId, public string $userId, public string $clientId)
    {}
}
