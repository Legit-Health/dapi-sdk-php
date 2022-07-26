<?php

namespace LegitHealth\Dapi;

use LegitHealth\Dapi\MediaAnalyzerArguments\FollowUpArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\PredictArguments;
use LegitHealth\Dapi\MediaAnalyzerResponse\MediaAnalyzerResponse;

class MediaAnalyzer
{
    private AiClient $aiClient;

    public function __construct(
        string $baseUri,
        string $analyzerApiKey
    ) {
        $this->aiClient = new AiClient($baseUri, $analyzerApiKey);
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
