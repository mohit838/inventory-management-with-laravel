<?php

namespace App\DTO;

class SalesChartData
{
    public function __construct(
        public array $labels,
        public array $values
    ) {}
}
