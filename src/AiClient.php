<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class AiClient
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public static function createWithParams(
        string $baseUri,
        string $analyzerApiKey
    ): self {
        return new self(HttpClient::createForBaseUri($baseUri, [
            'headers' => [
                'x-api-key' => $analyzerApiKey
            ]
        ]));
    }

    public static function createWithHttpClient(HttpClientInterface $httpClient): self
    {
        return new self($httpClient);
    }

    /**
     * @throws MediaAnalyzerException
     */
    public function predict(MediaAnalyzerArguments $arguments): array
    {
        return $this->send($arguments);
    }

    /**
     * @throws MediaAnalyzerException
     */
    public function followUp(MediaAnalyzerArguments $arguments): array
    {
        return $this->send($arguments);
    }

    /**
     * @throws MediaAnalyzerException
     */
    private function send(MediaAnalyzerArguments $arguments): array
    {
        try {
            $response = $this->httpClient->request('POST', '/v2/legit_health/predict', [
                'json' => $arguments->toArray(),
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new MediaAnalyzerException('Invalid response from server', $statusCode);
            }
            return $response->toArray();
        } catch (Throwable $exception) {
            throw new MediaAnalyzerException(
                $exception->getMessage()
            );
        }
    }
}
