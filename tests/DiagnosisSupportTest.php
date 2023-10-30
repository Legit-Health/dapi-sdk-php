<?php

namespace LegitHealth\Dapi\Tests;

use DateTimeImmutable;
use Dotenv\Dotenv;
use LegitHealth\Dapi\MediaAnalyzer;
use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\DiagnosisSupportData;
use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Company;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Gender;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use PHPUnit\Framework\TestCase;

class DiagnosisSupportTest extends TestCase
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

        $fileToUpload1 = $currentDir . '/tests/resources/psoriasis_01.png';
        $image1 = file_get_contents($fileToUpload1);

        $fileToUpload2 = $currentDir . '/tests/resources/psoriasis_02.png';
        $image2 = file_get_contents($fileToUpload2);

        $fileToUpload3 = $currentDir . '/tests/resources/psoriasis_03.png';
        $image3 = file_get_contents($fileToUpload3);

        $diagnosisSupportData = new DiagnosisSupportData(
            content: [
                base64_encode($image1),
                base64_encode($image2),
                base64_encode($image3)
            ]
        );
        $mediaAnalyzerArguments = new MediaAnalyzerArguments($this->generateRandom(), $diagnosisSupportData);
        $response = $mediaAnalyzer->diagnosisSupport($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);


        foreach ($response->observations as $observation) {
            $this->assertGreaterThan(0, count($observation->conclusions));
            $firstConclusion = $observation->conclusions[0];
            $this->assertNotEmpty($firstConclusion->conclusionCode->code);
            $this->assertNotEmpty($firstConclusion->conclusionCode->codeSystem);
            $this->assertNotEmpty($firstConclusion->pathologyCode);
            $this->assertNotEmpty($firstConclusion->probability);

            $this->assertNull($observation->explainabilityMedia);

            $metrics = $observation->metrics;
            $this->assertGreaterThan(0, $metrics->sensitivity);
            $this->assertGreaterThan(0, $metrics->specificity);

            $originalMedia = $observation->originalMedia;
            $this->assertNotEmpty($originalMedia->detectedModality);
            $this->assertNotEmpty($originalMedia->content);
            $this->assertTrue($originalMedia->mediaValidity->isValid);
            foreach ($originalMedia->mediaValidity->validityMetrics as $validityMetric) {
                $this->assertTrue($validityMetric->pass);
                $this->assertNotEmpty($validityMetric->name);
            }

            $this->assertGreaterThan(50.0, $originalMedia->mediaValidity->diqaScore);
            $this->assertNull($originalMedia->mediaValidity->getFailedValidityMetric());

            $preliminaryFindings = $observation->preliminaryFindings;
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isMalignantSuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);
        }

        $metrics = $response->metrics;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertGreaterThan(0, count($response->conclusions));
        $firstConclusion = $response->conclusions[0];
        $this->assertNotEmpty($firstConclusion->conclusionCode->code);
        $this->assertNotEmpty($firstConclusion->conclusionCode->codeSystem);
        $this->assertNotEmpty($firstConclusion->pathologyCode);
        $this->assertNotEmpty($firstConclusion->probability);

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

        $fileToUpload1 = $currentDir . '/tests/resources/psoriasis_01.png';
        $image1 = file_get_contents($fileToUpload1);

        $fileToUpload2 = $currentDir . '/tests/resources/psoriasis_02.png';
        $image2 = file_get_contents($fileToUpload2);

        $fileToUpload3 = $currentDir . '/tests/resources/psoriasis_03.png';
        $image3 = file_get_contents($fileToUpload3);

        $diagnosisSupportData = new DiagnosisSupportData(
            content: [
                base64_encode($image1),
                base64_encode($image2),
                base64_encode($image3)
            ],
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
        $mediaAnalyzerArguments = new MediaAnalyzerArguments($this->generateRandom(), $diagnosisSupportData);
        $response = $mediaAnalyzer->diagnosisSupport($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);


        foreach ($response->observations as $observation) {
            $this->assertGreaterThan(0, count($observation->conclusions));
            $firstConclusion = $observation->conclusions[0];
            $this->assertNotEmpty($firstConclusion->conclusionCode->code);
            $this->assertNotEmpty($firstConclusion->conclusionCode->codeSystem);
            $this->assertNotEmpty($firstConclusion->pathologyCode);
            $this->assertNotEmpty($firstConclusion->probability);

            $this->assertNull($observation->explainabilityMedia);

            $metrics = $observation->metrics;
            $this->assertGreaterThan(0, $metrics->sensitivity);
            $this->assertGreaterThan(0, $metrics->specificity);

            $originalMedia = $observation->originalMedia;
            $this->assertNotEmpty($originalMedia->detectedModality);
            $this->assertNotEmpty($originalMedia->content);
            $this->assertTrue($originalMedia->mediaValidity->isValid);
            foreach ($originalMedia->mediaValidity->validityMetrics as $validityMetric) {
                $this->assertTrue($validityMetric->pass);
                $this->assertNotEmpty($validityMetric->name);
            }

            $this->assertGreaterThan(50.0, $originalMedia->mediaValidity->diqaScore);
            $this->assertNull($originalMedia->mediaValidity->getFailedValidityMetric());

            $preliminaryFindings = $observation->preliminaryFindings;
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isMalignantSuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
            $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);
        }

        $metrics = $response->metrics;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

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
        $image1 = file_get_contents($fileToUpload);

        $fileToUpload1 = $currentDir . '/tests/resources/psoriasis_01.png';
        $image2 = file_get_contents($fileToUpload1);

        $image3 = file_get_contents($fileToUpload);

        $predictData = new DiagnosisSupportData(
            content: [
                base64_encode($image1),
                base64_encode($image2),
                base64_encode($image3)
            ]
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments($this->generateRandom(), $predictData);

        $response = $mediaAnalyzer->diagnosisSupport($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsBiopsySuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->needsSpecialistsAttention);

        $metrics = $response->metrics;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);


        $this->assertGreaterThan(0, $response->conclusions);

        $this->assertGreaterThan(0, $response->iaSeconds);
        $failedMedias = $response->getIndexOfFailedMedias();
        $this->assertCount(2, $failedMedias);
        $this->assertEquals(0, $failedMedias[0]->index);
        $this->assertEquals('isDermatologyDomain', $failedMedias[0]->failedMetric->name);
        $this->assertEquals(2, $failedMedias[1]->index);
        $this->assertEquals('isDermatologyDomain', $failedMedias[1]->failedMetric->name);
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
