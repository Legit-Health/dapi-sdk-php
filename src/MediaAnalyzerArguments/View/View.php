<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\View;

enum View: string
{
    case VertexProjection = 'Vertex projection';
    case LeftTrueLateral = 'Left true lateral';
    case AnteriorProjection = 'Anterior projection';
    case RightTrueLateral = 'Right true lateral';

    public function getCode(): string
    {
        return match ($this->value) {
            'Vertex projection' => '260461000',
            'Left true lateral' => '260432001',
            'Anterior projection' => '272460009',
            'Right true lateral' => '260436003',
            default => '',
        };
    }

    public function toArray(): array
    {
        return [
            "code" => $this->getCode(),
            "display" => $this->value,
            "system" => "http://snomed.info/sct",
        ];
    }
}
