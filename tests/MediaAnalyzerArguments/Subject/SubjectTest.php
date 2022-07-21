<?php

namespace Legit\Dapi\Tests\MediaAnalyzerArguments\Subject;

use Legit\Dapi\MediaAnalyzerArguments\Subject\Company;
use Legit\Dapi\MediaAnalyzerArguments\Subject\Gender;
use Legit\Dapi\MediaAnalyzerArguments\Subject\Subject;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SubjectTest extends TestCase
{
    public function testToArrayWithAllFields()
    {
        $subject = new Subject(
            'subject_id',
            Gender::Female,
            '1.75',
            '70.1',
            DateTimeImmutable::createFromFormat('Ymd', '19861020'),
            'practitioner_id',
            new Company('company_id', 'company_name')
        );

        $arr = $subject->toArray();

        $this->assertEquals('subject_id', $arr['identifier']);
        $this->assertEquals('female', $arr['gender']);
        $this->assertEquals('1.75', $arr['height']);
        $this->assertEquals('70.1', $arr['weight']);
        $this->assertEquals('1986-10-20', $arr['birthdate']);
        $this->assertEquals('practitioner_id', $arr['generalPractitioner']['identifier']);
        $this->assertEquals('company_id', $arr['managingOrganization']['identifier']);
        $this->assertEquals('company_name', $arr['managingOrganization']['display']);
    }

    public function testToArrayWithoutFields()
    {
        $subject = new Subject();

        $arr = $subject->toArray();

        $this->assertEquals(null, $arr['identifier']);
        $this->assertEquals(null, $arr['gender']);
        $this->assertEquals(null, $arr['height']);
        $this->assertEquals(null, $arr['weight']);
        $this->assertEquals(null, $arr['birthdate']);
        $this->assertEquals(null, $arr['generalPractitioner']['identifier']);
        $this->assertEquals(null, $arr['managingOrganization']['identifier']);
        $this->assertEquals(null, $arr['managingOrganization']['display']);
    }
}
