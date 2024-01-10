<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\DiagnosisSupportArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\SeverityAssessmentArguments;
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

    public function severityAssessment(SeverityAssessmentArguments $arguments): SeverityAssessmentResponse
    {
        $json = $this->aiClient->severityAssessment($arguments);
        $response = SeverityAssessmentResponse::createFromJson($json);

        return $response;
    }

    public function diagnosisSupport(DiagnosisSupportArguments $arguments): DiagnosisSupportResponse
    {
        $json = $this->aiClient->diagnosisSupport($arguments);
        $response = DiagnosisSupportResponse::createFromJson($arguments, $json);

        return $response;
    }
}
