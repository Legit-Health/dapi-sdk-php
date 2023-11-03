<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use LegitHealth\Dapi\MediaAnalyzerResponse\{DiagnosisSupportResponse, MediaAnalyzerResponse};
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

    /**
     * @deprecated 6.0
     */
    public function predict(MediaAnalyzerArguments $mediaAnalyzerArguments): MediaAnalyzerResponse
    {
        $json = $this->aiClient->predict($mediaAnalyzerArguments);
        $response = MediaAnalyzerResponse::createFromJson($json);

        return $response;
    }

    public function followUp(MediaAnalyzerArguments $mediaAnalyzerArguments): MediaAnalyzerResponse
    {
        $json = $this->aiClient->followUp($mediaAnalyzerArguments);
        $response = MediaAnalyzerResponse::createFromJson($json);

        return $response;
    }

    public function diagnosisSupport(MediaAnalyzerArguments $mediaAnalyzerArguments): DiagnosisSupportResponse
    {
        $json = $this->aiClient->diagnosisSupport($mediaAnalyzerArguments);
        $response = DiagnosisSupportResponse::createFromJson($mediaAnalyzerArguments, $json);

        return $response;
    }
}
