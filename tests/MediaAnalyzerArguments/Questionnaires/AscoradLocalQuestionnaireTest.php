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
                'surface' => 0,
                'itchiness' => 0,
                'sleeplessness' => 0
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => 100,
                'itchiness' => 10,
                'sleeplessness' => 10
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => random_int(0, 100),
                'itchiness' => random_int(0, 10),
                'sleeplessness' => random_int(0, 10)
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => 101,
                'itchiness' => 11,
                'sleeplessness' => 11
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => 100,
                'itchiness' => 11,
                'sleeplessness' => 11
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => 100,
                'itchiness' => 10,
                'sleeplessness' => 11
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => -1,
                'itchiness' => -1,
                'sleeplessness' => -1
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => 0,
                'itchiness' => -1,
                'sleeplessness' => -1
            ]);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new AscoradLocalQuestionnaire(...[
                'surface' => 0,
                'itchiness' => 0,
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
        $this->assertEquals(27, $arr['surface_value']);
        $this->assertEquals(2, $arr['itchiness_scorad']);
        $this->assertEquals(1, $arr['sleeplessness']);
    }

    public function testGetName()
    {
        $ascoradLocalQuestionnaire = new AscoradLocalQuestionnaire(27, 2, 2);
        $this->assertEquals('ASCORAD_LOCAL', $ascoradLocalQuestionnaire->getName());
    }
}
