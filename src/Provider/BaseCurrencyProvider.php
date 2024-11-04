<?php
namespace App\Provider;


use App\DTO\CurrenciesUpdateDTO;
use App\Trait\RateRoundTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BaseCurrencyProvider implements CurrencyProviderInterface
{
    use RateRoundTrait;

    const SOURCE_NAME = '';

    const URL = '';

    protected ?string $data;

    public function __construct(protected HttpClientInterface $client)
    {}

    public function isSupport(string $sourceName): bool
    {
        return $sourceName === static::SOURCE_NAME;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getData(): void
    {
        $this->data = $this->client->request('GET', static::URL)->getContent();
    }

    public function getPreparedDataForUpdate(): CurrenciesUpdateDTO
    {
        return new CurrenciesUpdateDTO();
    }
}