<?php

namespace OAuthServer\Event;

readonly class AccessTokenCreated
{
    /**
     * Create a new event instance.
     *
     * @param string $tokenId
     * @param string|null $userId
     * @param string $clientId
     */
    public function __construct(public string $tokenId, public ?string $userId, public string $clientId)
    {}
}
