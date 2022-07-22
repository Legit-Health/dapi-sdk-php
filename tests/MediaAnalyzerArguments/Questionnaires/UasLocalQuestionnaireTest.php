<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\UasLocalQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class UasLocalQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new UasLocalQuestionnaire(3, 15);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UasLocalQuestionnaire(0, 15);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UasLocalQuestionnaire(-1, 15);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UasLocalQuestionnaire(10, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $auasLocalQuestionnaire = new UasLocalQuestionnaire(3, 5);
        $arr = $auasLocalQuestionnaire->toArray();
        $this->assertCount(2, array_keys($arr));
        $this->assertEquals(3, $arr['itchiness']);
        $this->assertEquals(5, $arr['hiveNumber']);
    }

    public function testGetName()
    {
        $auasLocalQuestionnaire = new UasLocalQuestionnaire(3, 5);
        $this->assertEquals('UAS_LOCAL', $auasLocalQuestionnaire->getName());
    }
}
