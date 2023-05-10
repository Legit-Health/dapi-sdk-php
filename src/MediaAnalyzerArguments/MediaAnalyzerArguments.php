<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

class MediaAnalyzerArguments
{
    public function __construct(
        public readonly string $requestId,
        public readonly MediaAnalyzerData $data,
        public readonly OrderDetail $orderDetail = new OrderDetail()
    ) {
    }

    public function toArray(): array
    {
        return [
            'requestId' => $this->requestId,
            'data' => $this->data->toArray(),
            'orderDetail' => $this->orderDetail->toArray(),
        ];
    }
}
