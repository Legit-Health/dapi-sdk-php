<?php

namespace Legit\Dapi\Tests\MediaAnalyzerArguments\Questionnaires;

use Legit\Dapi\MediaAnalyzerArguments\Questionnaires\ApasiLocalQuestionnaire;
use PHPUnit\Framework\TestCase;
use Throwable;

class ApasiLocalQuestionnaireTest extends TestCase
{
    public function testValidate()
    {
        $exceptionIsThrown = false;
        try {
            new ApasiLocalQuestionnaire(5);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertFalse($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApasiLocalQuestionnaire(10);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);

        $exceptionIsThrown = false;
        try {
            new ApasiLocalQuestionnaire(-1);
        } catch (Throwable) {
            $exceptionIsThrown = true;
        }
        $this->assertTrue($exceptionIsThrown);
    }

    public function testToArray()
    {
        $apasiLocalQuestionnaire = new ApasiLocalQuestionnaire(5);
        $arr = $apasiLocalQuestionnaire->toArray();
        $this->assertCount(1, array_keys($arr));
        $this->assertEquals(5, $arr['surface']);
    }

    public function testGetName()
    {
        $apasiLocalQuestionnaire = new ApasiLocalQuestionnaire(5);
        $this->assertEquals('APASI_LOCAL', $apasiLocalQuestionnaire->getName());
    }
}
