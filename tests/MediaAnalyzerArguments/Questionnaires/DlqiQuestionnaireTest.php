<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\DlqiQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class DlqiQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new DlqiQuestionnaire(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new DlqiQuestionnaire(3, 3, 3, 3, 3, 3, 3, 3, 3, 3);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new DlqiQuestionnaire(
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
                random_int(0, 3),
            );
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        for ($i = 0; $i < 10; $i++) {
            $exceptionIsThrown = false;
            try {
                $arr = array_fill(0, 10, 4);
                for ($j = 0; $j < $i; $j++) {
                    $arr[$j] = 3;
                }
                new DlqiQuestionnaire(...$arr);
            } catch (Throwable $e) {
                $exceptionIsThrown = true;
                $this->assertEquals(sprintf('question%d should be between 0 and 3', $i + 1), $e->getMessage());
            }
            $this->assertTrue($exceptionIsThrown);
        }

        for ($i = 0; $i < 10; $i++) {
            $exceptionIsThrown = false;
            try {
                $arr = array_fill(0, 10, -1);
                for ($j = 0; $j < $i; $j++) {
                    $arr[$j] = 3;
                }
                new DlqiQuestionnaire(...$arr);
            } catch (Throwable $e) {
                $exceptionIsThrown = true;
                $this->assertEquals(sprintf('question%d should be between 0 and 3', $i + 1), $e->getMessage());
            }
            $this->assertTrue($exceptionIsThrown);
        }
    }

    public function testToArray()
    {
        $dlqiQuestionnaire = new DlqiQuestionnaire(1, 2, 3, 1, 2, 3, 1, 2, 3, 1);
        $arr = $dlqiQuestionnaire->toArray();
        $this->assertCount(10, array_keys($arr));
        $this->assertEquals(1, $arr['question1']);
        $this->assertEquals(2, $arr['question2']);
        $this->assertEquals(3, $arr['question3']);
        $this->assertEquals(1, $arr['question4']);
        $this->assertEquals(2, $arr['question5']);
        $this->assertEquals(3, $arr['question6']);
        $this->assertEquals(1, $arr['question7']);
        $this->assertEquals(2, $arr['question8']);
        $this->assertEquals(3, $arr['question9']);
        $this->assertEquals(1, $arr['question10']);
    }

    public function testGetName()
    {
        $dlqiQuestionnaire = new DlqiQuestionnaire(1, 2, 3, 1, 2, 3, 1, 2, 3, 3);
        $this->assertEquals('DLQI', $dlqiQuestionnaire::getName());
    }
}
