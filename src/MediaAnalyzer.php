<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use LegitHealth\Dapi\MediaAnalyzerResponse\{DiagnosisSupportResponse, SeverityAssessmentResponse};
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

    public function severityAssessment(MediaAnalyzerArguments $mediaAnalyzerArguments): SeverityAssessmentResponse
    {
        $json = $this->aiClient->severityAssessment($mediaAnalyzerArguments);
        $response = SeverityAssessmentResponse::createFromJson($json);

        return $response;
    }

    public function diagnosisSupport(MediaAnalyzerArguments $mediaAnalyzerArguments): DiagnosisSupportResponse
    {
        $json = $this->aiClient->diagnosisSupport($mediaAnalyzerArguments);
        $response = DiagnosisSupportResponse::createFromJson($mediaAnalyzerArguments, $json);

        return $response;
    }
}
