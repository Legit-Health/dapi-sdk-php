<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Subject;

use DateTimeInterface;
use LegitHealth\Dapi\Utils\FloatUtils;

class Subject
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?Gender $gender = null,
        public readonly ?string $height = null,
        public readonly ?string $weight = null,
        public readonly ?DateTimeInterface $birthdate = null,
        public readonly ?string $practitionerId = null,
        public readonly ?Company $company = null
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
