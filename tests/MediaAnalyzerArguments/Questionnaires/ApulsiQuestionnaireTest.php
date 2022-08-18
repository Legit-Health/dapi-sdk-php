<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\ApulsiQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class ApulsiQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(0, 1, 1);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(0, 0, 1);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(6, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(7, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(-1, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(1, 2, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApulsiQuestionnaire(1, 1, 2);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $apulsiQuestionnaire = new ApulsiQuestionnaire(2, 1, 0);
        $arr = $apulsiQuestionnaire->toArray();
        $this->assertCount(3, array_keys($arr));
        $this->assertEquals(2, $arr['erythema_surface']);
        $this->assertEquals(1, $arr['pain_apusa']);
        $this->assertEquals(0, $arr['odor_apusa']);
    }

    public function testGetName()
    {
        $apulsiQuestionnaire = new ApulsiQuestionnaire(2, 1, 0);
        $this->assertEquals('APULSI', $apulsiQuestionnaire::getName());
    }
}
