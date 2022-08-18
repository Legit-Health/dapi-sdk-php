<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Ihs4LocalQuestionnaire;
use PHPUnit\Framework\TestCase;

class Ihs4LocalQuestionnaireTest extends TestCase
{
    public function testToArray()
    {
        $ihs4LocalQuestionnaire = new Ihs4LocalQuestionnaire(5, 4, 2);
        $arr = $ihs4LocalQuestionnaire->toArray();
        $this->assertCount(3, array_keys($arr));
        $this->assertEquals(5, $arr['noduleNumber']);
        $this->assertEquals(4, $arr['abscesseNumber']);
        $this->assertEquals(2, $arr['drainingTunnelNumber']);
    }

    public function testGetName()
    {
        $ihs4LocalQuestionnaire = new Ihs4LocalQuestionnaire(5, 4, 2, );
        $this->assertEquals('IHS4_LOCAL', $ihs4LocalQuestionnaire::getName());
    }
}
