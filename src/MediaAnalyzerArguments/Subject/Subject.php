<?php

namespace Legit\Dapi\MediaAnalyzerArguments\Subject;

use DateTimeInterface;

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
            'height' => is_numeric($this->height) ? \floatval($this->height) : null,
            'weight' => is_numeric($this->weight) ? \floatval($this->weight) : null,
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
