<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\{DiagnosisSupportArguments, MediaAnalyzerArguments, SeverityAssessmentArguments};
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class AiClient
{
    private const SEVERITY_ASSESSMENT = '/v2/legit_health/predict';
    private const DIAGNOSIS_SUPPORT_ENDPOINT = '/v2/legit_health/diagnosis_support';

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
    public function severityAssessment(SeverityAssessmentArguments $arguments): array
    {
        return $this->send($arguments, self::SEVERITY_ASSESSMENT);
    }

    /**
     * @throws MediaAnalyzerException
     */
    public function diagnosisSupport(DiagnosisSupportArguments $arguments): array
    {
        return $this->send($arguments, self::DIAGNOSIS_SUPPORT_ENDPOINT);
    }

    /**
     * @throws MediaAnalyzerException
     */
    private function send(MediaAnalyzerArguments $arguments, string $path): array
    {
        try {
            $response = $this->httpClient->request('POST', $path, [
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
