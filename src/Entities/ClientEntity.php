<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuthServer\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait;
    use ClientTrait;

    /**
     * The client identifier.
     *
     * @var string
     */
    protected string $identifier;

    /**
     * The client's provider.
     *
     * @var string
     */
    public string $provider;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public mixed $projectId;

    /**
     * Create a new client instance.
     *
     * @param string $identifier
     * @param string $name
     * @param string $redirectUri
     * @param bool $isConfidential
     * @param string|null $provider
     * @param null $projectId
     */
    public function __construct(
        string $identifier,
        string $name,
        string $redirectUri,
        bool $isConfidential = false,
        ?string $provider = null,
        $projectId = null)
    {
        $this->setIdentifier((string) $identifier);

        $this->name = $name;
        $this->setRedirectUri($redirectUri);
        $this->setConfidential($isConfidential);
        $this->provider = $provider;
        $this->projectId = $projectId;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function setRedirectUri(string|array $uri): void
    {
        if (is_string($uri)) {
            $uri = explode(',', $uri);
        }

        $this->redirectUri = $uri;
    }

    public function setConfidential(): void
    {
        $this->isConfidential = true;
    }
}
