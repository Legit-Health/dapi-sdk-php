<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class AiClient
{
    private HttpClientInterface $httpClient;

    public function __construct(
        string $baseUri,
        string $analyzerApiKey
    ) {
        $this->httpClient = HttpClient::createForBaseUri($baseUri, [
            'headers' => [
                'x-api-key' => $analyzerApiKey
            ]
        ]);
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
                throw new MediaAnalyzerException('Invalid response', $statusCode);
            }
            return $response->toArray();
        } catch (Throwable $exception) {
            throw new MediaAnalyzerException(
                $exception->getMessage(),
                MediaAnalyzerException::AI_CLIENT,
                $exception
            );
        }
    }
}
