<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuthServer\Repositories;

use RuntimeException;
use Hyperf\DbConnection\Db;
use OAuthServer\Entities\UserEntity;
use OAuthServer\OneTimePasswordInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?\League\OAuth2\Server\Entities\UserEntityInterface
    {
        $provider = $clientEntity->provider ?: config('oauth.provider');

        if (is_null($config = config('databases.'.$provider, null))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $query = Db::connection($provider);

        $user = $query->table(config('oauth.user_table', 'users'))->where(config('oauth.find_by', 'email'), $username)->first();

        if (! $user) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        return new UserEntity($user->id);
    }

    public function getUserEntityByOtp(
        $phone,
        $code,
        $grantType,
        ClientEntityInterface $clientEntity,
        OneTimePasswordInterface $otp
    ) {
        $provider = $clientEntity->provider ?: config('oauth.provider');

        if (is_null($config = config('databases.'.$provider, null))) {
            throw new RuntimeException('Unable to determine authentication from configuration.');
        }

        if ($grantType !== 'otp') {
            throw new RuntimeException('Invalid Grant Type');
        }

        $query = Db::connection($provider);

        $user = $query->table(config('oauth.user_table', 'users'))->where('phone', $phone)->first();

        if (! $user) {
            return;
        }

        if (! $otp->verify($user->phone, $code)) {
            return;
        }

        return new UserEntity($user->id);
    }

    public function getUserByProviderUserId($id, $client): UserEntity
    {
        $provider = $client->provider;

        if (is_null($provider)) {
            $provider = config('oauth.provider');
        }

        $query = Db::connection($provider);
        $user = $query->table(config('oauth.user_table', 'users'))->find($id);
        
        unset($user->password);

        return new UserEntity($user->id);
    }
}
