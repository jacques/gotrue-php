<?php declare(strict_types=1);
/**
 * Gotrue client for PHP
 *
 * @author    Jacques Marneweck <jacques@siberia.co.za>
 * @copyright 2022 Jacques Marneweck.  All rights strictly reserved.
 */

namespace Gotrue;

use Brick\VarExporter\VarExporter;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use RuntimeException;

final class Admin
{
    public function __construct(protected GuzzleClient $client, protected ?string $token) {}

    /**
     * @param string $audience
     *
     * @return array
     */
    public function listUsers(string $audience): array
    {
        $response = $this->client->request('GET', '/admin/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token ?? '',
            ],
            'query_params' => [
                'audience' => $audience,
            ]
        ]);

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param string $userid
     *
     * @return array
     */
    public function getUser(string $userid): array
    {
        $response = $this->client->request('GET', \sprintf('/admin/users/%s', $userid));

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param string $userid
     *
     * @return array
     */
    public function updateUser(string $userid): array
    {
        $response = $this->client->request('PUT', \sprintf('/admin/users/%s', $userid));

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param string $userid
     *
     * @return array
     */
    public function createUser(string $email, string $password, array $attributes = []): array
    {
        $attributes['email'] = $email;
        $attributes['password'] = $password;

        $response = $this->client->request('POST', '/admin/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token ?? '',
            ],
            'json' => $attributes,
        ]);

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param string $userid
     *
     * @return array
     */
    public function deleteUser(string $userid): array
    {
        $response = $this->client->request('DELETE', \sprintf('/admin/users/%s', $userid));

        return (\json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }
}
