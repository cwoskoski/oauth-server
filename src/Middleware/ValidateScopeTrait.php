<?php

namespace OAuthServer\Middleware;

use OAuthServer\Exception\MissingScopeException;
use OAuthServer\Exception\AuthenticationException;

/**
 *
 */
trait ValidateScopeTrait
{
    /**
     * Validate token credentials.
     *
     * @throws MissingScopeException
     */
    protected function validateScopes(array $tokenScopes, array $scopes): void
    {
        if (in_array('*', $tokenScopes, true)) {
            return;
        }

        foreach ($scopes as $scope) {
            if (! $this->can($tokenScopes, $scope)) {
                throw new MissingScopeException($scope);
            }
        }
    }

    protected function can($tokenScopes, $scope): bool
    {
        $scopes = [$scope];

        foreach ($scopes as $scope) {
            if (array_key_exists($scope, array_flip($tokenScopes))) {
                return true;
            }
        }

        return false;
    }
}
