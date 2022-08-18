<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Pure4Questionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class Pure4QuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new Pure4Questionnaire(...[
                'question1_pure' => 0,
                'question2_pure' => 0,
                'question3_pure' => 0,
                'question4_pure' => 0,
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new Pure4Questionnaire(...[
                'question1_pure' => 1,
                'question2_pure' => 1,
                'question3_pure' => 1,
                'question4_pure' => 1,
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new Pure4Questionnaire(...[
                'question1_pure' => random_int(0, 1),
                'question2_pure' => random_int(0, 1),
                'question3_pure' => random_int(0, 1),
                'question4_pure' => random_int(0, 1)
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        for ($i = 0; $i < 4; $i++) {
            $exceptionIsThrown = false;
            try {
                $arr = array_fill(0, 4, 2);
                for ($j = 0; $j < $i; $j++) {
                    $arr[$j] = 0;
                }
                new Pure4Questionnaire(...$arr);
            } catch (Throwable $e) {
                $exceptionIsThrown = true;
                $this->assertEquals(sprintf('question%d_pure should be between 0 and 1', $i + 1), $e->getMessage());
            }
            $this->assertTrue($exceptionIsThrown);
        }

        for ($i = 0; $i < 4; $i++) {
            $exceptionIsThrown = false;
            try {
                $arr = array_fill(0, 4, -1);
                for ($j = 0; $j < $i; $j++) {
                    $arr[$j] = 1;
                }
                new Pure4Questionnaire(...$arr);
            } catch (Throwable $e) {
                $exceptionIsThrown = true;
                $this->assertEquals(sprintf('question%d_pure should be between 0 and 1', $i + 1), $e->getMessage());
            }
            $this->assertTrue($exceptionIsThrown);
        }
    }

    public function testToArray()
    {
        $pure4Questionnaire = new Pure4Questionnaire(0, 1, 0, 1);
        $arr = $pure4Questionnaire->toArray();
        $this->assertCount(4, array_keys($arr));
        $this->assertEquals(0, $arr['question1_pure']);
        $this->assertEquals(1, $arr['question2_pure']);
        $this->assertEquals(0, $arr['question3_pure']);
        $this->assertEquals(1, $arr['question4_pure']);

        $pure4Questionnaire = new Pure4Questionnaire(...[
            'question1_pure' => 1,
            'question2_pure' => 0,
            'question3_pure' => 1,
            'question4_pure' => 0,
        ]);
        $arr = $pure4Questionnaire->toArray();
        $this->assertCount(4, array_keys($arr));
        $this->assertEquals(1, $arr['question1_pure']);
        $this->assertEquals(0, $arr['question2_pure']);
        $this->assertEquals(1, $arr['question3_pure']);
        $this->assertEquals(0, $arr['question4_pure']);
    }

    public function testGetName()
    {
        $pure4Questionnaire = new Pure4Questionnaire(0, 1, 0, 0);
        $this->assertEquals('PURE4', $pure4Questionnaire::getName());
    }
}
