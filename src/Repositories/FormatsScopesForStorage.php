<?php

namespace OAuthServer\Repositories;

trait FormatsScopesForStorage
{
    /**
     * Format the given scopes for storage.
     *
     * @param  array  $scopes
     * @return string
     */
    public function formatScopesForStorage(array $scopes): string
    {
        return json_encode($this->scopesToArray($scopes));
    }

    /**
     * Get an array of scope identifiers for storage.
     *
     * @param  array  $scopes
     * @return array
     */
    public function scopesToArray(array $scopes): array
    {
        return array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $scopes);
    }
}
