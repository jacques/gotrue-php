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

final class User
{
    protected ?array $currentuser;

    public function __construct(protected GuzzleClient $client, protected ?string $token) {}

    public function clearSession(): void
    {
        $this->token = null;
        $this->currentuser = null;
    }

    /**
     * @param string $audience
     *
     * @return
     */
    public function getUserData(): array
    {
        $response = $this->client->request('GET', '/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token ?? '',
            ],
        ]);

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param string $audience
     *
     * @return
     */
    public function update(array $attributes)
    {
        $response = $this->client->request('PUT', '/user', [
            'json' => $attributes,
        ]);

        return (\json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }
}
