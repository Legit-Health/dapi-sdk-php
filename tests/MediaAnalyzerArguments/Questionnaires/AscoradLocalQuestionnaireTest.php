<?php

namespace LegitHealth\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\AscoradLocalQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class AscoradLocalQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 0,
                'itchinessScorad' => 0,
                'sleeplessness' => 0
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 100,
                'itchinessScorad' => 10,
                'sleeplessness' => 10
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => random_int(0, 100),
                'itchinessScorad' => random_int(0, 10),
                'sleeplessness' => random_int(0, 10)
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 101,
                'itchinessScorad' => 11,
                'sleeplessness' => 11
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 100,
                'itchinessScorad' => 11,
                'sleeplessness' => 11
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 100,
                'itchinessScorad' => 10,
                'sleeplessness' => 11
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => -1,
                'itchinessScorad' => -1,
                'sleeplessness' => -1
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 0,
                'itchinessScorad' => -1,
                'sleeplessness' => -1
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surfaceValue' => 0,
                'itchinessScorad' => 0,
                'sleeplessness' => -1
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $ascoradLocalQuestionnaire = new AscoradLocalQuestionnaire(27, 2, 1);
        $arr = $ascoradLocalQuestionnaire->toArray();
        $this->assertCount(3, array_keys($arr));
        $this->assertEquals(27, $arr['surfaceValue']);
        $this->assertEquals(2, $arr['itchinessScorad']);
        $this->assertEquals(1, $arr['sleeplessness']);
    }

    public function testGetName()
    {
        $ascoradLocalQuestionnaire = new AscoradLocalQuestionnaire(27, 2, 2);
        $this->assertEquals('ASCORAD_LOCAL', $ascoradLocalQuestionnaire::getName());
    }
}
