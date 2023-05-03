<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\SevenPCQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class SevenPCQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new SevenPCQuestionnaire(0, 0, 0, 0, 0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new SevenPCQuestionnaire(1, 1, 1, 1, 1, 1, 1);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new SevenPCQuestionnaire(
                random_int(0, 1),
                random_int(0, 1),
                random_int(0, 1),
                random_int(0, 1),
                random_int(0, 1),
                random_int(0, 1),
                random_int(0, 1)
            );
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        for ($i = 0; $i < 7; $i++) {
            $exceptionIsThrown = false;
            try {
                $arr = array_fill(0, 7, 4);
                for ($j = 0; $j < $i; $j++) {
                    $arr[$j] = 1;
                }
                new SevenPCQuestionnaire(...$arr);
            } catch (Throwable $e) {
                $exceptionIsThrown = true;
                $this->assertEquals(sprintf('question%dSevenPC should be between 0 and 1', $i + 1), $e->getMessage());
            }
            $this->assertTrue($exceptionIsThrown);
        }

        for ($i = 0; $i < 7; $i++) {
            $exceptionIsThrown = false;
            try {
                $arr = array_fill(0, 7, -1);
                for ($j = 0; $j < $i; $j++) {
                    $arr[$j] = 1;
                }
                new SevenPCQuestionnaire(...$arr);
            } catch (Throwable $e) {
                $exceptionIsThrown = true;
                $this->assertEquals(sprintf('question%dSevenPC should be between 0 and 1', $i + 1), $e->getMessage());
            }
            $this->assertTrue($exceptionIsThrown);
        }
    }

    public function testToArray()
    {
        $questionnaire = new SevenPCQuestionnaire(0, 1, 0, 1, 0, 1, 0);
        $arr = $questionnaire->toArray();
        $this->assertCount(7, array_keys($arr));
        $this->assertEquals(0, $arr['question1SevenPC']);
        $this->assertEquals(1, $arr['question2SevenPC']);
        $this->assertEquals(0, $arr['question3SevenPC']);
        $this->assertEquals(1, $arr['question4SevenPC']);
        $this->assertEquals(0, $arr['question5SevenPC']);
        $this->assertEquals(1, $arr['question6SevenPC']);
        $this->assertEquals(0, $arr['question7SevenPC']);
    }

    public function testGetName()
    {
        $questionnaire = new SevenPCQuestionnaire(0, 1, 0, 1, 0, 1, 0);
        $this->assertEquals('7PC', $questionnaire::getName());
    }
}
