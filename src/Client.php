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

final class Client
{
    protected GuzzleClient $client;

    public Admin $admin;

    public User $user;

    private const VERSION = '0.0.0';

    public function __construct(protected string $url, protected ?string $token = null)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $url,
            'headers' => [
                'User-Agent' => 'gotrue-php/'.self::VERSION.' '.\GuzzleHttp\default_user_agent(),
            ],
        ]);

        $this->admin = new Admin($this->client, $this->token);
        $this->user = new User($this->client, $this->token);
    }

    /**
     * Get public settings for GoTrue instance.
     *
     * @return array
     */
    public function settings(): array
    {
        $response = $this->client->request('GET', '/settings');

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * Create a new user.
     *
     * @param string $email
     * @param string $password
     * @param array  $attributes
     *
     * @return array
     */
    public function signup(string $email, string $password, array $attributes = []): array
    {
        $response = $this->client->request('POST', '/signup', [
            'json' => [
                'email' => $email,
                'password' => $password,
                'user_metadata' => $attributes,
            ],
        ]);

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * Log user in.
     *
     * @param string $email
     * @param string $password
     *
     * @return array
     */
    public function login(string $email, string $password): array
    {
        $response = $this->client->request('POST', '/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => 'grant_type=password&username='. urlencode($email) . '&password=' . urlencode($password),
        ]);

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }
}
