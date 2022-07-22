<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\ApasiLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\AuasLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;
use PHPUnit\Framework\TestCase;

class QuestionnairesTest extends TestCase
{
    public function testToArray()
    {
        $auasQuestionnaire = new AuasLocalQuestionnaire(3);
        $apasiQuestionnaire = new ApasiLocalQuestionnaire(4);
        $questionnaires = new Questionnaires([$auasQuestionnaire, $apasiQuestionnaire]);

        $arr = $questionnaires->toArray();

        $this->assertCount(2, array_keys($arr));

        $this->assertArrayHasKey('AUAS_LOCAL', $arr);
        $this->assertArrayHasKey('itchiness', $arr['AUAS_LOCAL']);
        $this->assertEquals(3, $arr['AUAS_LOCAL']['itchiness']);

        $this->assertArrayHasKey('APASI_LOCAL', $arr);
        $this->assertArrayHasKey('surface', $arr['APASI_LOCAL']);
        $this->assertEquals(4, $arr['APASI_LOCAL']['surface']);
    }
}
