<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\{DiagnosisSupportArguments, PredictArguments, SeverityAssessmentArguments};
use LegitHealth\Dapi\MediaAnalyzerResponse\{DiagnosisSupportResponse, PredictResponse, SeverityAssessmentResponse};
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
     * @deprecated
     */
    public function predict(PredictArguments $arguments): PredictResponse
    {
        $json = $this->aiClient->predict($arguments);
        $response = PredictResponse::createFromJson($json);

        return $response;
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
