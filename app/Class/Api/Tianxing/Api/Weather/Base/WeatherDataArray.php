<?php

namespace App\Class\Api\Tianxing\Api\Weather\Base;

use App\Class\Api\Tianxing\Base\DataArray;

class WeatherDataArray extends DataArray
{
    public function getNextData(): ?WeatherData
    {
        $data = parent::getNextData();
        return $data ?new WeatherData($data): null;
    }
}
