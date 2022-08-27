<?php

namespace App\Class\Api\Tianxing\Response;


use App\Class\Api\Tianxing\BaseResponse\_base_data_weather;
use App\Class\Api\Tianxing\BaseResponse\_base_response;

class Weather extends _base_response
{
    /**
     * @var int 索引
     */
    private int $i = 0;

    /**
     * @return _base_data_weather|null
     */
    public function getNextData(): ?_base_data_weather
    {
        if (!isset($this->data[$this->i])) {
            return null;
        }
        $data = $this->data[$this->i];
        $this->i++;
        $ret = new _base_data_weather();
        $ret->area = $data['area'];
        $ret->date = $data['date'];
        $ret->week = $data['week'];
        $ret->weather = $data['weather'];
        $ret->real = $data['real'];
        $ret->lowest = $data['lowest'];
        $ret->highest = $data['highest'];
        $ret->wind = $data['wind'];
        $ret->windDeg = (int)$data['winddeg'];
        $ret->windSpeed = (int)$data['windspeed'];
        $ret->windSc = (int)$data['windsc'];
        $ret->sunrise = $data['sunrise'];
        $ret->sunset = $data['sunset'];
        $ret->moonrise = $data['moonrise'];
        $ret->moondown = $data['moondown'];
        $ret->pcpn = (float)$data['pcpn'];
        $ret->pop = (int)$data['pop'];
        $ret->uv_index = (int)$data['uv_index'];
        $ret->vis = (int)$data['vis'];
        $ret->humidity = (int)$data['humidity'];
        $ret->tips = $data['tips'];

        return $ret;
    }
}
