<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\AuasLocalQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class AuasLocalQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new AuasLocalQuestionnaire(3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AuasLocalQuestionnaire(0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AuasLocalQuestionnaire(-1);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AuasLocalQuestionnaire(10);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $auasLocalQuestionnaire = new AuasLocalQuestionnaire(3);
        $arr = $auasLocalQuestionnaire->toArray();
        $this->assertCount(1, array_keys($arr));
        $this->assertEquals(3, $arr['itchiness']);
    }

    public function testGetName()
    {
        $auasLocalQuestionnaire = new AuasLocalQuestionnaire(3);
        $this->assertEquals('AUAS_LOCAL', $auasLocalQuestionnaire->getName());
    }
}
