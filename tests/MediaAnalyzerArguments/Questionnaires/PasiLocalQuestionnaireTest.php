<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\PasiLocalQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class PasiLocalQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 0,
                'erythema' => 0,
                'induration' => 0,
                'desquamation' => 0,
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 6,
                'erythema' => 4,
                'induration' => 4,
                'desquamation' => 4
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => random_int(0, 6),
                'erythema' => random_int(0, 4),
                'induration' => random_int(0, 4),
                'desquamation' => random_int(0, 4)
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 7,
                'erythema' => 4,
                'induration' => 4,
                'desquamation' => 4
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('surface should be between 0 and 6', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => -1,
                'erythema' => 4,
                'induration' => 4,
                'desquamation' => 4
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('surface should be between 0 and 6', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 1,
                'erythema' => 5,
                'induration' => 4,
                'desquamation' => 4
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('erythema should be between 0 and 4', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 1,
                'erythema' => -5,
                'induration' => 4,
                'desquamation' => 4
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('erythema should be between 0 and 4', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 1,
                'erythema' => 4,
                'induration' => 5,
                'desquamation' => 4
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('induration should be between 0 and 4', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 1,
                'erythema' => 1,
                'induration' => -1,
                'desquamation' => 4
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('induration should be between 0 and 4', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 1,
                'erythema' => 4,
                'induration' => 4,
                'desquamation' => 5
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('desquamation should be between 0 and 4', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new PasiLocalQuestionnaire(...[
                'surface' => 1,
                'erythema' => 1,
                'induration' => 4,
                'desquamation' => -1
            ]);
        } catch (Throwable $e) {
            $exceptionIsThrown = true;
            $this->assertEquals('desquamation should be between 0 and 4', $e->getMessage());
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $pasiLocalQuestionnaire = new PasiLocalQuestionnaire(5, 1, 2, 3);
        $arr = $pasiLocalQuestionnaire->toArray();
        $this->assertCount(4, array_keys($arr));
        $this->assertEquals(5, $arr['surface']);
        $this->assertEquals(1, $arr['erythema']);
        $this->assertEquals(2, $arr['induration']);
        $this->assertEquals(3, $arr['desquamation']);

        $pasiLocalQuestionnaire = new PasiLocalQuestionnaire(...[
            'erythema' => 2,
            'surface' => 4,
            'induration' => 0,
            'desquamation' => 1,
        ]);
        $arr = $pasiLocalQuestionnaire->toArray();
        $this->assertCount(4, array_keys($arr));
        $this->assertEquals(4, $arr['surface']);
        $this->assertEquals(2, $arr['erythema']);
        $this->assertEquals(0, $arr['induration']);
        $this->assertEquals(1, $arr['desquamation']);
    }

    public function testGetName()
    {
        $pasiLocalQuestionnaire = new PasiLocalQuestionnaire(5, 1, 2, 3);
        $this->assertEquals('PASI_LOCAL', $pasiLocalQuestionnaire::getName());
    }
}
