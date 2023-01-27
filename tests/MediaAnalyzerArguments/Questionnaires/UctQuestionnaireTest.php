<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\UctQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class UctQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(4, 0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 4, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, 4, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, 0, 4);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(-1, 0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(5, 0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, -1, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 5, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, -1, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, 5, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, 0, -1);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new UctQuestionnaire(0, 0, 0, 5);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $uctQuestionnaire = new UctQuestionnaire(1, 2, 0, 4);
        $arr = $uctQuestionnaire->toArray();
        $this->assertCount(4, array_keys($arr));
        $this->assertEquals(1, $arr['question1Uct']);
        $this->assertEquals(2, $arr['question2Uct']);
        $this->assertEquals(0, $arr['question3Uct']);
        $this->assertEquals(4, $arr['question4Uct']);
    }

    public function testGetName()
    {
        $uctQuestionnaire = new UctQuestionnaire(1, 2, 0, 4);
        $this->assertEquals('UCT', $uctQuestionnaire::getName());
    }
}
