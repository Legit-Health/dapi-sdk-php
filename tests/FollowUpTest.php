<?php

namespace LegitHealth\Dapi\Tests;

use DateTimeImmutable;
use Dotenv\Dotenv;
use LegitHealth\Dapi\MediaAnalyzer;
use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\FollowUpData;
use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\OrderDetail;
use LegitHealth\Dapi\MediaAnalyzerArguments\PreviousMedia\PreviousMedia;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\ApasiLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\AscoradLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\AuasLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\DlqiQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Ihs4LocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\PasiLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Pure4Questionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\SevenPCQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\UasLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Company;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Gender;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use LegitHealth\Dapi\MediaAnalyzerArguments\View\View;
use LegitHealth\Dapi\MediaAnalyzerResponse\Value\DetectionLabel;
use PHPUnit\Framework\TestCase;

class FollowUpTest extends TestCase
{
    public function testPsoriasisFollowUp()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );


        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/psoriasis_02.png';
        $image = file_get_contents($fileToUpload);

        $fileToUpload = $currentDir . '/tests/resources/psoriasis_01.png';
        $previousMediaImage = file_get_contents($fileToUpload);

        $apasiLocal = new ApasiLocalQuestionnaire(3);
        $pasiLocal = new PasiLocalQuestionnaire(3, 2, 1, 1);
        $pure4 = new Pure4Questionnaire(0, 0, 0, 1);
        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$apasiLocal, $pasiLocal, $pure4, $dlqi]);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Psoriasis',
            bodySiteCode: BodySiteCode::ArmLeft,
            previousMedias: [
                new PreviousMedia(base64_encode($previousMediaImage), DateTimeImmutable::createFromFormat('Ymd', '20220106'))
            ],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: array_map(fn (Questionnaire $questionnaire) => $questionnaire->getName(), $questionnaires->questionnaires),
            questionnaires: $questionnaires
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNull($response->explainabilityMedia->content);

        $this->assertCount(4, $response->scoringSystemsResults);

        // APASI
        $apasiLocalScoringSystemValue = $response->getScoringSystemResult('APASI_LOCAL');
        $this->assertGreaterThanOrEqual(0, $apasiLocalScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($apasiLocalScoringSystemValue->getScore()->category);

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetScore('desquamation')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('desquamation')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('desquamation')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetScore('erythema')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('erythema')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('erythema')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetScore('induration')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('induration')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('induration')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetScore('surface')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('surface')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetScore('surface')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(6)
            )
        );

        $this->assertNotNull(
            $apasiLocalScoringSystemValue->explainabilityMedia->content
        );
        if (
            $apasiLocalScoringSystemValue->explainabilityMedia->detections !== null &&
            count($apasiLocalScoringSystemValue->explainabilityMedia->detections) > 0
        ) {
            $this->assertGreaterThanOrEqual(0, $apasiLocalScoringSystemValue->explainabilityMedia->detections);
            $detection = $apasiLocalScoringSystemValue->explainabilityMedia->detections[0];
            $this->assertGreaterThanOrEqual(0, $detection->confidence);
            $this->assertGreaterThanOrEqual(0, $detection->p1->x);
            $this->assertGreaterThanOrEqual(0, $detection->p1->y);
            $this->assertGreaterThanOrEqual(0, $detection->p2->x);
            $this->assertGreaterThanOrEqual(0, $detection->p2->y);
        }

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemResult('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($dlqiScoringSystemValue->getScore()->category);
        foreach (range(1, 10) as $number) {
            $facetCode = sprintf('question%d', $number);
            $facetScore = $dlqiScoringSystemValue->getFacetScore($facetCode);
            $this->assertNull($facetScore->intensity);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0),
                    $this->lessThanOrEqual(3)
                )
            );
        }

        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->detections
        );

        // PURE4
        $pure4ScoringSystemValue = $response->getScoringSystemResult('PURE4');
        $this->assertGreaterThanOrEqual(0, $pure4ScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($pure4ScoringSystemValue->getScore()->category);
        foreach (range(1, 4) as $number) {
            $facetCode = sprintf('question%dPure', $number);
            $facetScore = $pure4ScoringSystemValue->getFacetScore($facetCode);
            $this->assertNull($facetScore->intensity);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0),
                    $this->lessThanOrEqual(3)
                )
            );
        }

        $this->assertNull(
            $pure4ScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $pure4ScoringSystemValue->explainabilityMedia->detections
        );

        // PASI_LOCAL
        $pasiLocalScoringSystemValue = $response->getScoringSystemResult('PASI_LOCAL');
        $this->assertGreaterThanOrEqual(0, $pasiLocalScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($pasiLocalScoringSystemValue->getScore()->category);

        $this->assertNull($pasiLocalScoringSystemValue->getFacetScore('desquamation')->intensity);
        $this->assertThat(
            $pasiLocalScoringSystemValue->getFacetScore('desquamation')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNull($pasiLocalScoringSystemValue->getFacetScore('erythema')->intensity);
        $this->assertThat(
            $pasiLocalScoringSystemValue->getFacetScore('erythema')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNull($pasiLocalScoringSystemValue->getFacetScore('induration')->intensity);
        $this->assertThat(
            $pasiLocalScoringSystemValue->getFacetScore('induration')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNull($pasiLocalScoringSystemValue->getFacetScore('surface')->intensity);
        $this->assertThat(
            $pasiLocalScoringSystemValue->getFacetScore('surface')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(6)
            )
        );

        $this->assertNull(
            $pasiLocalScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $pasiLocalScoringSystemValue->explainabilityMedia->detections
        );
    }

    public function testAcne()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/acne.jpg';
        $image = file_get_contents($fileToUpload);

        $fileToUpload = $currentDir . '/tests/resources/acne.jpg';
        $previousMediaImage = file_get_contents($fileToUpload);

        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$dlqi]);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Acne',
            bodySiteCode: BodySiteCode::HeadFront,
            previousMedias: [
                new PreviousMedia(base64_encode($previousMediaImage), DateTimeImmutable::createFromFormat('Ymd', '20220106'))
            ],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: ['DLQI', 'ALEGI'],
            questionnaires: $questionnaires
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $explainabilityMedia = $response->explainabilityMedia;
        $this->assertNotNull($response->explainabilityMedia);
        $this->assertNull($explainabilityMedia->content);
        $this->assertNull($explainabilityMedia->metrics->pxToCm);

        $this->assertCount(2, $response->scoringSystemsResults);

        // ALEGI
        $alegiScoringSystemValue = $response->getScoringSystemResult('ALEGI');
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->getScore()->score);
        $this->assertNotNull($alegiScoringSystemValue->getScore()->category);

        $this->assertNotNull($alegiScoringSystemValue->getFacetScore('lesionDensity')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionDensity')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionDensity')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($alegiScoringSystemValue->getFacetScore('lesionNumber')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionNumber')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertGreaterThan(0, $alegiScoringSystemValue->getFacetScore('lesionNumber')->value);
        $this->assertNotNull(
            $alegiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->explainabilityMedia->detections);
        if (count($alegiScoringSystemValue->explainabilityMedia->detections) > 0) {
            $detection = $alegiScoringSystemValue->explainabilityMedia->detections[0];
            $this->assertGreaterThanOrEqual(0, $detection->confidence);
            $this->assertGreaterThanOrEqual(0, $detection->p1->x);
            $this->assertGreaterThanOrEqual(0, $detection->p1->y);
            $this->assertGreaterThanOrEqual(0, $detection->p2->x);
            $this->assertGreaterThanOrEqual(0, $detection->p2->y);
            $this->assertEquals(DetectionLabel::AcneLesion, $detection->detectionLabel);
        }

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemResult('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($dlqiScoringSystemValue->getScore()->category);
        foreach (range(1, 10) as $number) {
            $facetCode = sprintf('question%d', $number);
            $facetScore = $dlqiScoringSystemValue->getFacetScore($facetCode);
            $this->assertNull($facetScore->intensity);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0),
                    $this->lessThanOrEqual(3)
                )
            );
        }
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->detections
        );
    }

    public function testAcneFaceAnteriorProjection()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/acne_face.jpg';
        $image = file_get_contents($fileToUpload);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Acne',
            bodySiteCode: BodySiteCode::HeadFront,
            previousMedias: [],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: ['ALEGI'],
            questionnaires: new Questionnaires([]),
            view: View::AnteriorProjection
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData,
            new OrderDetail(faceDetection: true)
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $explainabilityMedia = $response->explainabilityMedia;
        $this->assertNotNull($response->explainabilityMedia);
        $this->assertNull($explainabilityMedia->content);
        $this->assertNotNull($explainabilityMedia->metrics->pxToCm);

        $this->assertCount(1, $response->scoringSystemsResults);

        // ALEGI
        $alegiScoringSystemValue = $response->getScoringSystemResult('ALEGI');
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->getScore()->score);
        $this->assertNotNull($alegiScoringSystemValue->getScore()->category);

        $this->assertNotNull($alegiScoringSystemValue->getFacetScore('lesionDensity')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionDensity')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionDensity')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($alegiScoringSystemValue->getFacetScore('lesionNumber')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionNumber')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertGreaterThan(0, $alegiScoringSystemValue->getFacetScore('lesionNumber')->value);
        $this->assertNotNull(
            $alegiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->explainabilityMedia->detections);
        if (count($alegiScoringSystemValue->explainabilityMedia->detections) > 0) {
            $detection = $alegiScoringSystemValue->explainabilityMedia->detections[0];
            $this->assertGreaterThanOrEqual(0, $detection->confidence);
            $this->assertGreaterThanOrEqual(0, $detection->p1->x);
            $this->assertGreaterThanOrEqual(0, $detection->p1->y);
            $this->assertGreaterThanOrEqual(0, $detection->p2->x);
            $this->assertGreaterThanOrEqual(0, $detection->p2->y);
            $this->assertEquals(DetectionLabel::AcneLesion, $detection->detectionLabel);
        }
    }

    public function testAcneFaceLeftTrueLateral()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/acne_face.jpg';
        $image = file_get_contents($fileToUpload);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Acne',
            bodySiteCode: BodySiteCode::HeadFront,
            previousMedias: [],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: ['ALEGI'],
            questionnaires: new Questionnaires([]),
            view: View::LeftTrueLateral
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData,
            new OrderDetail(faceDetection: true)
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $explainabilityMedia = $response->explainabilityMedia;
        $this->assertNotNull($response->explainabilityMedia);
        $this->assertNull($explainabilityMedia->content);
        $this->assertNotNull($explainabilityMedia->metrics->pxToCm);

        $this->assertCount(1, $response->scoringSystemsResults);

        // ALEGI
        $alegiScoringSystemValue = $response->getScoringSystemResult('ALEGI');
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->getScore()->score);
        $this->assertNotNull($alegiScoringSystemValue->getScore()->category);

        $this->assertNotNull($alegiScoringSystemValue->getFacetScore('lesionDensity')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionDensity')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionDensity')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($alegiScoringSystemValue->getFacetScore('lesionNumber')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetScore('lesionNumber')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertGreaterThan(0, $alegiScoringSystemValue->getFacetScore('lesionNumber')->value);
        $this->assertNotNull(
            $alegiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->explainabilityMedia->detections);
        if (count($alegiScoringSystemValue->explainabilityMedia->detections) > 0) {
            $detection = $alegiScoringSystemValue->explainabilityMedia->detections[0];
            $this->assertGreaterThanOrEqual(0, $detection->confidence);
            $this->assertGreaterThanOrEqual(0, $detection->p1->x);
            $this->assertGreaterThanOrEqual(0, $detection->p1->y);
            $this->assertGreaterThanOrEqual(0, $detection->p2->x);
            $this->assertGreaterThanOrEqual(0, $detection->p2->y);
            $this->assertEquals(DetectionLabel::AcneLesion, $detection->detectionLabel);
        }
    }

    public function testUrticariaFollowUp()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );


        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/urticaria.jpg';
        $image = file_get_contents($fileToUpload);


        $auasLocal = new AuasLocalQuestionnaire(2);
        $uasLocal = new UasLocalQuestionnaire(2, 5);
        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$auasLocal, $uasLocal, $dlqi]);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Hives urticaria',
            bodySiteCode: BodySiteCode::ArmLeft,
            previousMedias: [],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: array_map(fn (Questionnaire $questionnaire) => $questionnaire->getName(), $questionnaires->questionnaires),
            questionnaires: $questionnaires
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNull($response->explainabilityMedia->content);

        $this->assertCount(3, $response->scoringSystemsResults);

        // AUAS_LOCAL
        $auasLocalScoringSystemValue = $response->getScoringSystemResult('AUAS_LOCAL');
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->getScore()->score);
        $this->assertNotNull($auasLocalScoringSystemValue->getScore()->category);

        $this->assertNotNull($auasLocalScoringSystemValue->getFacetScore('hiveNumber')->intensity);
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->getFacetScore('hiveNumber')->value);
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->getFacetScore('hiveNumber')->intensity);

        $this->assertNull($auasLocalScoringSystemValue->getFacetScore('itchiness')->intensity);
        $this->assertThat(
            $auasLocalScoringSystemValue->getFacetScore('itchiness')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );
        $this->assertNotNull(
            $auasLocalScoringSystemValue->explainabilityMedia->content
        );
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->explainabilityMedia->detections);
        if (count($auasLocalScoringSystemValue->explainabilityMedia->detections) > 0) {
            $detection = $auasLocalScoringSystemValue->explainabilityMedia->detections[0];
            $this->assertGreaterThanOrEqual(0, $detection->confidence);
            $this->assertGreaterThanOrEqual(0, $detection->p1->x);
            $this->assertGreaterThanOrEqual(0, $detection->p1->y);
            $this->assertGreaterThanOrEqual(0, $detection->p2->x);
            $this->assertGreaterThanOrEqual(0, $detection->p2->y);
            $this->assertEquals(DetectionLabel::Hive, $detection->detectionLabel);
        }

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemResult('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($dlqiScoringSystemValue->getScore()->category);
        foreach (range(1, 10) as $number) {
            $facetCode = sprintf('question%d', $number);
            $facetScore = $dlqiScoringSystemValue->getFacetScore($facetCode);
            $this->assertNull($facetScore->intensity);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0),
                    $this->lessThanOrEqual(3)
                )
            );
        }
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->detections
        );


        // UAS_LOCAL
        $uasLocalScoringSystemValue = $response->getScoringSystemResult('UAS_LOCAL');
        $this->assertGreaterThanOrEqual(0, $uasLocalScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($uasLocalScoringSystemValue->getScore()->category);

        $this->assertNull($uasLocalScoringSystemValue->getFacetScore('hiveNumber')->intensity);
        $this->assertThat(
            $uasLocalScoringSystemValue->getFacetScore('hiveNumber')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0)
            )
        );

        $this->assertNull($uasLocalScoringSystemValue->getFacetScore('itchiness')->intensity);
        $this->assertThat(
            $uasLocalScoringSystemValue->getFacetScore('itchiness')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );
        $this->assertNull(
            $uasLocalScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $uasLocalScoringSystemValue->explainabilityMedia->detections
        );
    }

    public function testAtopicDermatitis()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );


        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/dermatitis.jpg';
        $image = file_get_contents($fileToUpload);


        $ascoradLocal = new AscoradLocalQuestionnaire(27, 2, 3);
        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$ascoradLocal, $dlqi]);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Atopic dermatitis',
            bodySiteCode: BodySiteCode::ArmLeft,
            previousMedias: [],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: array_map(fn (Questionnaire $questionnaire) => $questionnaire->getName(), $questionnaires->questionnaires),
            questionnaires: $questionnaires
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNull($response->explainabilityMedia->content);

        $this->assertCount(2, $response->scoringSystemsResults);

        // ASCORAD_LOCAL
        $ascoradLocalScoringSystemValue = $response->getScoringSystemResult('ASCORAD_LOCAL');
        $this->assertGreaterThanOrEqual(0, $ascoradLocalScoringSystemValue->getScore()->score);
        $this->assertNotNull($ascoradLocalScoringSystemValue->getScore()->category);

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('crusting')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('crusting')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('crusting')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('dryness')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('dryness')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('dryness')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('erythema')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('erythema')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('erythema')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('excoriation')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('excoriation')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('excoriation')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('lichenification')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('lichenification')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('lichenification')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('swelling')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('swelling')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('swelling')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNull($ascoradLocalScoringSystemValue->getFacetScore('itchinessScorad')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('itchinessScorad')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(10)
            )
        );

        $this->assertNull($ascoradLocalScoringSystemValue->getFacetScore('sleeplessness')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('sleeplessness')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(10)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetScore('surfaceValue')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetScore('surfaceValue')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertNotNull(
            $ascoradLocalScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $ascoradLocalScoringSystemValue->explainabilityMedia->detections
        );

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemResult('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($dlqiScoringSystemValue->getScore()->category);
        foreach (range(1, 10) as $number) {
            $facetCode = sprintf('question%d', $number);
            $facetScore = $dlqiScoringSystemValue->getFacetScore($facetCode);
            $this->assertNull($facetScore->intensity);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0),
                    $this->lessThanOrEqual(3)
                )
            );
        }
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->detections
        );
    }

    public function testHidradenitis()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );


        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/hidradenitis_01.png';
        $image = file_get_contents($fileToUpload);


        $ihs4Local = new Ihs4LocalQuestionnaire(1, 2, 1);
        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$ihs4Local, $dlqi]);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Hidradenitis suppurativa',
            bodySiteCode: BodySiteCode::ArmLeft,
            previousMedias: [],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: ['AIHS4_LOCAL', 'IHS4_LOCAL', 'DLQI'],
            questionnaires: $questionnaires
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

        $preliminaryFindings = $response->preliminaryFindings;
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->hasConditionSuspicion);
        $this->assertGreaterThanOrEqual(0, $preliminaryFindings->isPreMalignantSuspicion);
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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNull($response->explainabilityMedia->content);

        $this->assertCount(3, $response->scoringSystemsResults);

        // AIHS4_LOCAL
        $aihs4LocalScoringSystemValue = $response->getScoringSystemResult('AIHS4_LOCAL');
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getScore()->score);
        $this->assertNotNull($aihs4LocalScoringSystemValue->getScore()->category);

        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getFacetScore('abscesseNumber')->value);
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getFacetScore('drainingTunnelNumber')->value);
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getFacetScore('noduleNumber')->value);
        $this->assertNotNull(
            $aihs4LocalScoringSystemValue->explainabilityMedia->content
        );
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->explainabilityMedia->detections);
        if (count($aihs4LocalScoringSystemValue->explainabilityMedia->detections) > 0) {
            $detection = $aihs4LocalScoringSystemValue->explainabilityMedia->detections[0];
            $this->assertGreaterThanOrEqual(0, $detection->confidence);
            $this->assertGreaterThanOrEqual(0, $detection->p1->x);
            $this->assertGreaterThanOrEqual(0, $detection->p1->y);
            $this->assertGreaterThanOrEqual(0, $detection->p2->x);
            $this->assertGreaterThanOrEqual(0, $detection->p2->y);
            $this->assertContains($detection->detectionLabel, [DetectionLabel::Hive, DetectionLabel::DrainingTunnel, DetectionLabel::Abscess]);
        }

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemResult('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($dlqiScoringSystemValue->getScore()->category);
        foreach (range(1, 10) as $number) {
            $facetCode = sprintf('question%d', $number);
            $facetScore = $dlqiScoringSystemValue->getFacetScore($facetCode);
            $this->assertNull($facetScore->intensity);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0),
                    $this->lessThanOrEqual(3)
                )
            );
        }
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $dlqiScoringSystemValue->explainabilityMedia->detections
        );

        // IHS4_LOCAL
        $ihs4LocalScoringSystemValue = $response->getScoringSystemResult('IHS4_LOCAL');
        $this->assertGreaterThanOrEqual(0, $ihs4LocalScoringSystemValue->getScore()->score);
        $this->assertNotEmpty($ihs4LocalScoringSystemValue->getScore()->category);

        foreach (['abscesseNumber', 'drainingTunnelNumber', 'noduleNumber'] as $facetCode) {
            $facetScore = $ihs4LocalScoringSystemValue->getFacetScore($facetCode);
            $this->assertThat(
                $facetScore->value,
                $this->logicalAnd(
                    $this->greaterThanOrEqual(0)
                )
            );
            $this->assertNull($facetScore->intensity);
        }
        $this->assertNull(
            $ihs4LocalScoringSystemValue->explainabilityMedia->content
        );
        $this->assertNull(
            $ihs4LocalScoringSystemValue->explainabilityMedia->detections
        );
    }

    public function testNevus()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = MediaAnalyzer::createWithParams(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );

        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/nevus.jpg';
        $image = file_get_contents($fileToUpload);

        $fileToUpload = $currentDir . '/tests/resources/nevus.jpg';
        $previousMediaImage = file_get_contents($fileToUpload);

        $sevenPC = new SevenPCQuestionnaire(1, 1, 1, 0, 0, 0, 1);
        $questionnaires = new Questionnaires([$sevenPC]);

        $followUpData = new FollowUpData(
            content: base64_encode($image),
            pathologyCode: 'Atypical nevus',
            bodySiteCode: BodySiteCode::ArmLeft,
            previousMedias: [
                new PreviousMedia(base64_encode($previousMediaImage), DateTimeImmutable::createFromFormat('Ymd', '20220106'))
            ],
            operator: Operator::Patient,
            subject: new Subject(
                $this->generateRandom(),
                Gender::Male,
                '1.75',
                '70',
                DateTimeImmutable::createFromFormat('Ymd', '19861020'),
                $this->generateRandom(),
                new Company($this->generateRandom(), 'Company')
            ),
            scoringSystems: ['7PC'],
            questionnaires: $questionnaires
        );

        $mediaAnalyzerArguments = new MediaAnalyzerArguments(
            $this->generateRandom(),
            $followUpData
        );

        $response = $mediaAnalyzer->followUp($mediaAnalyzerArguments);

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

        $this->assertGreaterThan(0, $response->iaSeconds);

        $explainabilityMedia = $response->explainabilityMedia;
        $this->assertNotNull($response->explainabilityMedia);
        $this->assertNull($explainabilityMedia->content);
        $this->assertNull($explainabilityMedia->metrics->pxToCm);

        $this->assertCount(1, $response->scoringSystemsResults);

        // NEVUS
        $sevenPCScoringSystemValue = $response->getScoringSystemResult('7PC');
        $this->assertGreaterThanOrEqual(0, $sevenPCScoringSystemValue->getScore()->score);
        $this->assertNotNull($sevenPCScoringSystemValue->getScore()->category);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question1SevenPC')->intensity);
        $this->assertEquals(1, $sevenPCScoringSystemValue->getFacetScore('question1SevenPC')->value);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question2SevenPC')->intensity);
        $this->assertEquals(1, $sevenPCScoringSystemValue->getFacetScore('question2SevenPC')->value);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question3SevenPC')->intensity);
        $this->assertEquals(1, $sevenPCScoringSystemValue->getFacetScore('question3SevenPC')->value);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question4SevenPC')->intensity);
        $this->assertEquals(0, $sevenPCScoringSystemValue->getFacetScore('question4SevenPC')->value);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question5SevenPC')->intensity);
        $this->assertEquals(0, $sevenPCScoringSystemValue->getFacetScore('question5SevenPC')->value);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question6SevenPC')->intensity);
        $this->assertEquals(0, $sevenPCScoringSystemValue->getFacetScore('question6SevenPC')->value);

        $this->assertNull($sevenPCScoringSystemValue->getFacetScore('question7SevenPC')->intensity);
        $this->assertEquals(1, $sevenPCScoringSystemValue->getFacetScore('question7SevenPC')->value);
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
