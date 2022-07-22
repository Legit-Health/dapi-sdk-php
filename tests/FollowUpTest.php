<?php

namespace LegitHealth\Dapi\Tests;

use DateTimeImmutable;
use Dotenv\Dotenv;
use LegitHealth\Dapi\MediaAnalyzer;
use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\FollowUpArguments;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
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
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\UasLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Company;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Gender;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use PHPUnit\Framework\TestCase;

class FollowUpTest extends TestCase
{
    public function testPsoriasisFollowUp()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = new MediaAnalyzer(
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

        $followUpArguments = new FollowUpArguments(
            $this->generateRandom(),
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

        $response = $mediaAnalyzer->followUp($followUpArguments);

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

        $metrics = $response->metricsValue;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNotEmpty($response->explainabilityMedia);

        $this->assertCount(4, $response->scoringSystemsValues);

        // APASI
        $apasiLocalScoringSystemValue = $response->getScoringSystemValues('APASI_LOCAL');
        $this->assertGreaterThanOrEqual(0, $apasiLocalScoringSystemValue->getScore()->calculatedScore);
        $this->assertNotNull($apasiLocalScoringSystemValue->getScore()->category);

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetCalculatedValue('desquamation')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('desquamation')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('desquamation')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetCalculatedValue('erythema')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('erythema')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('erythema')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetCalculatedValue('induration')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('induration')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('induration')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($apasiLocalScoringSystemValue->getFacetCalculatedValue('surface')->intensity);
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('surface')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $apasiLocalScoringSystemValue->getFacetCalculatedValue('surface')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(6)
            )
        );

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemValues('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->questionnaireScore);

        // PURE4
        $pure4ScoringSystemValue = $response->getScoringSystemValues('PURE4');
        $this->assertGreaterThanOrEqual(0, $pure4ScoringSystemValue->getScore()->questionnaireScore);

        // PASI_LOCAL
        $pasiLocalScoringSystemValue = $response->getScoringSystemValues('PASI_LOCAL');
        $this->assertGreaterThanOrEqual(0, $pasiLocalScoringSystemValue->getScore()->questionnaireScore);
    }

    public function testAcne()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        ;
        $dotenv->load();
        $mediaAnalyzer = new MediaAnalyzer(
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

        $followUpArguments = new FollowUpArguments(
            $this->generateRandom(),
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

        $response = $mediaAnalyzer->followUp($followUpArguments);

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

        $metrics = $response->metricsValue;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNotEmpty($response->explainabilityMedia);

        $this->assertCount(2, $response->scoringSystemsValues);

        // ALEGI
        $alegiScoringSystemValue = $response->getScoringSystemValues('ALEGI');
        $this->assertGreaterThanOrEqual(0, $alegiScoringSystemValue->getScore()->calculatedScore);
        $this->assertNotNull($alegiScoringSystemValue->getScore()->category);

        $this->assertNotNull($alegiScoringSystemValue->getFacetCalculatedValue('lesionDensity')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetCalculatedValue('lesionDensity')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $alegiScoringSystemValue->getFacetCalculatedValue('lesionDensity')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(4)
            )
        );

        $this->assertNotNull($alegiScoringSystemValue->getFacetCalculatedValue('lesionNumber')->intensity);
        $this->assertThat(
            $alegiScoringSystemValue->getFacetCalculatedValue('lesionNumber')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertGreaterThan(0, $alegiScoringSystemValue->getFacetCalculatedValue('lesionNumber')->value);

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemValues('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->questionnaireScore);
    }

    public function testUrticariaFollowUp()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = new MediaAnalyzer(
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

        $followUpArguments = new FollowUpArguments(
            $this->generateRandom(),
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

        $response = $mediaAnalyzer->followUp($followUpArguments);

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

        $metrics = $response->metricsValue;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNotEmpty($response->explainabilityMedia);

        $this->assertCount(3, $response->scoringSystemsValues);

        // AUAS_LOCAL
        $auasLocalScoringSystemValue = $response->getScoringSystemValues('AUAS_LOCAL');
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->getScore()->calculatedScore);
        $this->assertNotNull($auasLocalScoringSystemValue->getScore()->category);

        $this->assertNotNull($auasLocalScoringSystemValue->getFacetCalculatedValue('hiveNumber')->intensity);
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->getFacetCalculatedValue('hiveNumber')->value);
        $this->assertGreaterThanOrEqual(0, $auasLocalScoringSystemValue->getFacetCalculatedValue('hiveNumber')->intensity);

        $this->assertNotNull($auasLocalScoringSystemValue->getFacetCalculatedValue('itchiness')->intensity);
        $this->assertThat(
            $auasLocalScoringSystemValue->getFacetCalculatedValue('itchiness')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $auasLocalScoringSystemValue->getFacetCalculatedValue('erythema')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemValues('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->questionnaireScore);

        // UAS_LOCAL
        $uasLocalScoringSystemValue = $response->getScoringSystemValues('UAS_LOCAL');
        $this->assertGreaterThanOrEqual(0, $uasLocalScoringSystemValue->getScore()->questionnaireScore);
    }

    public function testAtopicDermatitis()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = new MediaAnalyzer(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );


        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/dermatitis.jpg';
        $image = file_get_contents($fileToUpload);


        $ascoradLocal = new AscoradLocalQuestionnaire(27, 2, 3);
        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$ascoradLocal, $dlqi]);

        $followUpArguments = new FollowUpArguments(
            $this->generateRandom(),
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

        $response = $mediaAnalyzer->followUp($followUpArguments);

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

        $metrics = $response->metricsValue;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNotEmpty($response->explainabilityMedia);

        $this->assertCount(2, $response->scoringSystemsValues);

        // ASCORAD_LOCAL
        $ascoradLocalScoringSystemValue = $response->getScoringSystemValues('ASCORAD_LOCAL');
        $this->assertGreaterThanOrEqual(0, $ascoradLocalScoringSystemValue->getScore()->calculatedScore);
        $this->assertNotNull($ascoradLocalScoringSystemValue->getScore()->category);

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetCalculatedValue('crusting')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('crusting')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('crusting')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetCalculatedValue('dryness')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('dryness')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('dryness')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetCalculatedValue('erythema')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('erythema')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('erythema')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetCalculatedValue('excoriation')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('excoriation')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('excoriation')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetCalculatedValue('lichenification')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('lichenification')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('lichenification')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        $this->assertNotNull($ascoradLocalScoringSystemValue->getFacetCalculatedValue('swelling')->intensity);
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('swelling')->intensity,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
        $this->assertThat(
            $ascoradLocalScoringSystemValue->getFacetCalculatedValue('swelling')->value,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(3)
            )
        );

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemValues('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->questionnaireScore);
    }

    public function testHidradenitis()
    {
        $currentDir = getcwd();
        $dotenv = Dotenv::createImmutable($currentDir, '.env.local');
        $dotenv->load();
        $mediaAnalyzer = new MediaAnalyzer(
            $_ENV['API_URL'],
            $_ENV['API_KEY']
        );


        $currentDir = getcwd();
        $fileToUpload = $currentDir . '/tests/resources/hidradenitis_01.png';
        $image = file_get_contents($fileToUpload);


        $ihs4Local = new Ihs4LocalQuestionnaire(1, 2, 1);
        $dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
        $questionnaires = new Questionnaires([$ihs4Local, $dlqi]);

        $followUpArguments = new FollowUpArguments(
            $this->generateRandom(),
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

        $response = $mediaAnalyzer->followUp($followUpArguments);

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

        $metrics = $response->metricsValue;
        $this->assertGreaterThan(0, $metrics->sensitivity);
        $this->assertGreaterThan(0, $metrics->specificity);

        $this->assertGreaterThan(0, $response->iaSeconds);

        $this->assertNotEmpty($response->explainabilityMedia);

        $this->assertCount(3, $response->scoringSystemsValues);

        // AIHS4_LOCAL
        $aihs4LocalScoringSystemValue = $response->getScoringSystemValues('AIHS4_LOCAL');
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getScore()->calculatedScore);
        $this->assertNotNull($aihs4LocalScoringSystemValue->getScore()->category);

        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getFacetCalculatedValue('abscesseNumber')->value);
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getFacetCalculatedValue('drainingTunnelNumber')->value);
        $this->assertGreaterThanOrEqual(0, $aihs4LocalScoringSystemValue->getFacetCalculatedValue('noduleNumber')->value);

        // DLQI
        $dlqiScoringSystemValue = $response->getScoringSystemValues('DLQI');
        $this->assertGreaterThanOrEqual(0, $dlqiScoringSystemValue->getScore()->questionnaireScore);

        // IHS4_LOCAL
        $ihs4LocalScoringSystemValue = $response->getScoringSystemValues('IHS4_LOCAL');
        $this->assertGreaterThanOrEqual(0, $ihs4LocalScoringSystemValue->getScore()->questionnaireScore);
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
