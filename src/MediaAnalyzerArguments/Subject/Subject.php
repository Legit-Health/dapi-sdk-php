<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Subject;

use DateTimeInterface;
use LegitHealth\Dapi\Utils\FloatUtils;

readonly class Subject
{
    public function __construct(
        public ?string $id = null,
        public ?Gender $gender = null,
        public ?string $height = null,
        public ?string $weight = null,
        public ?DateTimeInterface $birthdate = null,
        public ?string $practitionerId = null,
        public ?Company $company = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->id,
            'gender' => $this->gender?->value,
            'height' => FloatUtils::floatOrNull($this->height),
            'weight' => FloatUtils::floatOrNull($this->weight),
            'birthdate' => $this->birthdate?->format('Y-m-d'),
            'generalPractitioner' => [
                'identifier' => $this->practitionerId
            ],
            'managingOrganization' => [
                'identifier' => $this->company?->id,
                'display' => $this->company?->name,
            ]
        ];
    }
}
