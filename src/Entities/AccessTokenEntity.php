<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuthServer\Entities;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class AccessTokenEntity implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use TokenEntityTrait;
    use EntityTrait;

    /**
     * Create a new token instance.
     *
     * @param string $userIdentifier
     * @param ScopeEntityInterface[] $scopes
     * @param ClientEntityInterface $client
     */
    public function __construct(string $userIdentifier, array $scopes, ClientEntityInterface $client)
    {
        $this->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }

        $this->setClient($client);
    }
}
