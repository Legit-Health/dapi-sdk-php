<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\ScovidQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class ScovidQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new ScovidQuestionnaire(1, 8, 0, 1, 2, 3, 0, 1, 2, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ScovidQuestionnaire(1, -1, 0, 1, 2, 4, 0, 1, 2, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ScovidQuestionnaire(1, -1, 0, 1, 2, 3, 0, 1, 2, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ScovidQuestionnaire(1, 11, 0, 1, 2, 3, 0, 1, 2, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ScovidQuestionnaire(11, 10, 0, 1, 2, 3, 0, 1, 2, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ScovidQuestionnaire(-1, 10, 0, 1, 2, 3, 0, 1, 2, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $questionnaire = new ScovidQuestionnaire(8, 10, 0, 1, 2, 3, 0, 1, 2, 3);
        $arr = $questionnaire->toArray();
        $this->assertCount(10, array_keys($arr));
        $this->assertEquals(8, $arr['pain']);
        $this->assertEquals(10, $arr['itchinessScorad']);
        $this->assertEquals(0, $arr['fever']);
        $this->assertEquals(1, $arr['cough']);
        $this->assertEquals(2, $arr['cephalea']);
        $this->assertEquals(3, $arr['myalgiaorarthralgia']);
        $this->assertEquals(0, $arr['malaise']);
        $this->assertEquals(1, $arr['lossoftasteorolfactory']);
        $this->assertEquals(2, $arr['shortnessofbreath']);
        $this->assertEquals(3, $arr['otherskinproblems']);
    }

    public function testGetName()
    {
        $questionnaire = new ScovidQuestionnaire(8, 10, 0, 1, 2, 3, 0, 1, 2, 3);
        $this->assertEquals('SCOVID', $questionnaire::getName());
    }
}
