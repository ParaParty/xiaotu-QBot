<?php
namespace App\Class\Api\Tianxing\Api\Weather;

use App\Class\Api\Tianxing\Base\Response;

class Weather extends Response
{
    /**
     * @var Base\WeatherDataArray 返回数据
     */
    public Base\WeatherDataArray $data;
}
