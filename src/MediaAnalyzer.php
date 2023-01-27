<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\FollowUpArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\PredictArguments;
use LegitHealth\Dapi\MediaAnalyzerResponse\MediaAnalyzerResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MediaAnalyzer
{
    public function __construct(private AiClient $aiClient)
    {
    }

    public static function createWithParams(
        string $baseUri,
        string $analyzerApiKey
    ): self {
        return new self(AiClient::createWithParams($baseUri, $analyzerApiKey));
    }

    public static function createWithHttpClient(HttpClientInterface $httpClient): self
    {
        return new self(AiClient::createWithHttpClient($httpClient));
    }

    public function predict(PredictArguments $arguments): MediaAnalyzerResponse
    {
        $json = $this->aiClient->predict($arguments);
        $response = MediaAnalyzerResponse::createFromJson($json);

        return $response;
    }

    public function followUp(FollowUpArguments $arguments): MediaAnalyzerResponse
    {
        $json = $this->aiClient->followUp($arguments);
        $response = MediaAnalyzerResponse::createFromJson($json);

        return $response;
    }
}
