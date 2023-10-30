<?php

namespace LegitHealth\Dapi\Tests;

use DateTimeImmutable;
use Dotenv\Dotenv;
use LegitHealth\Dapi\MediaAnalyzer;
use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Company;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\PredictData;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Gender;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use PHPUnit\Framework\TestCase;

class PredictTest extends TestCase
{
    public function testBasePredict()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $fileToUpload = $currentDir . '/tests/resources/psoriasis_01.png';
        $image = file_get_contents($fileToUpload);

        $predictData = new PredictData(
            content: base64_encode($image)
        );
        $mediaAnalyzerArguments = new MediaAnalyzerArguments($this->generateRandom(), $predictData);
        $response = $mediaAnalyzer->predict($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);

        $this->assertNotEmpty($response->modality);

        $mediaValidity = $response->mediaValidity;
        $this->assertTrue($mediaValidity->isValid);
        $this->assertGreaterThan(0, $mediaValidity->diqaScore);
        foreach ($mediaValidity->validityMetrics as $validityMetric) {
            $this->assertTrue($validityMetric->pass);
            $this->assertNotEmpty($validityMetric->name);
        }

        $metrics = $response->metrics;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertNotNull($response->explainabilityMedia);

        $this->assertCount(0, $response->scoringSystemsResults);

        $this->assertGreaterThan(0, count($response->conclusions));
        $firstConclusion = $response->conclusions[0];
        $this->assertNotEmpty($firstConclusion->conclusionCode->code);
        $this->assertNotEmpty($firstConclusion->conclusionCode->codeSystem);
        $this->assertNotEmpty($firstConclusion->pathologyCode);
        $this->assertNotEmpty($firstConclusion->probability);

        $this->assertGreaterThan(0, $response->iaSeconds);
    }

    public function testInvalidImage()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $fileToUpload = $currentDir . '/tests/resources/invalid.png';
        $image = file_get_contents($fileToUpload);

        $predictData = new PredictData(
            content: base64_encode($image)
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments($this->generateRandom(), $predictData);

        $response = $mediaAnalyzer->predict($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);

        $this->assertNotEmpty($response->modality);

        $mediaValidity = $response->mediaValidity;
        $this->assertFalse($mediaValidity->isValid);
        $this->assertGreaterThan(0, $mediaValidity->diqaScore);
        $failedMetric = $mediaValidity->getFailedValidityMetric();
        $this->assertEquals('isDermatologyDomain', $failedMetric->name);

        $metrics = $response->metrics;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertNotNull($response->explainabilityMedia);

        $this->assertCount(0, $response->scoringSystemsResults);
        $this->assertGreaterThan(0, $response->iaSeconds);
    }

    public function testPredictWithAllFields()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/psoriasis_01.png';
        $image = file_get_contents($fileToUpload);

        $predictData = new PredictData(
            content: base64_encode($image),
            bodySiteCode: BodySiteCode::ArmLeft,
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '69.00',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company Name')
            )
        );
        $mediaAnalyzerArguments = new MediaAnalyzerArguments($this->generateRandom(), $predictData);
        $response = $mediaAnalyzer->predict($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);

        $this->assertNotEmpty($response->modality);

        $mediaValidity = $response->mediaValidity;
        $this->assertTrue($mediaValidity->isValid);
        $this->assertGreaterThan(0, $mediaValidity->diqaScore);
        foreach ($mediaValidity->validityMetrics as $validityMetric) {
            $this->assertTrue($validityMetric->pass);
            $this->assertNotEmpty($validityMetric->name);
        }

        $metrics = $response->metrics;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertNotNull($response->explainabilityMedia);

        $this->assertCount(0, $response->scoringSystemsResults);

        $this->assertGreaterThan(0, count($response->conclusions));
        $firstConclusion = $response->conclusions[0];
        $this->assertNotEmpty($firstConclusion->conclusionCode->code);
        $this->assertNotEmpty($firstConclusion->conclusionCode->codeSystem);
        $this->assertNotEmpty($firstConclusion->pathologyCode);
        $this->assertNotEmpty($firstConclusion->probability);

        $this->assertGreaterThan(0, $response->iaSeconds);
    }

    private function generateRandom($length = 15)
    {
        return substr(
            str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"),
            10,
            $length
        );
    }
}
