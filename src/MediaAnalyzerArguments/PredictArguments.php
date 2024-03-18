<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

readonly class PredictArguments implements MediaAnalyzerArguments
{
    public function __construct(
        public string $requestId,
        public MediaAnalyzerData $data,
        public OrderDetail $orderDetail = new OrderDetail()
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
