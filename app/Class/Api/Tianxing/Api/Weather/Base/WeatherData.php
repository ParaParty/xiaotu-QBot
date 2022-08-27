<?php
namespace App\Class\Api\Tianxing\Api\Weather\Base;

class WeatherData
{
    /**
     * @var string 地区
     */
    public string $area;

    /**
     * @var string 日期
     */
    public string $date;

    /**
     * @var string 星期
     */
    public string $week;

    /**
     * @var string 天气/早晚天气变化
     */
    public string $weather;

    /**
     * @var string 天气图标（图标名
     */
    public string $weatherImg;

    /**
     * @var string 实时气温
     */
    public string $real;

    /**
     * @var string 最低气温
     */
    public string $lowest;

    /**
     * @var string 最高气温
     */
    public string $highest;

    /**
     * @var string 风向
     */
    public string $wind;

    /**
     * @var int 风向角度
     */
    public int $windDeg;

    /**
     * @var int 风速
     */
    public int $windSpeed;

    /**
     * @var int windSc 风力
     */
    public int $windSc;

    /**
     * @var string 日出时分
     */
    public string $sunrise;

    /**
     * @var string 日落时分
     */
    public string $sunset;

    /**
     * @var string 月出时分
     */
    public string $moonrise;

    /**
     * @var string 月落时分
     */
    public string $moondown;


    /**
     * @var float 降雨量
     */
    public float $pcpn;

    /**
     * @var int 降雨概率(0~100)
     */
    public int $pop;

    /**
     * @var int 紫外线强度指数
     */
    public int $uv_index;

    /**
     * @var int 能见度(km)
     */
    public int $vis;

    /**
     * @var int 相对湿度
     */
    public int $humidity;

    /**
     * @var string 温馨提醒
     */
    public string $tips;

    public function __construct(array $data)
    {
        $this->area = $data['area'];
        $this->date = $data['date'];
        $this->week = $data['week'];
        $this->weather = $data['weather'];
        $this->real = $data['real'];
        $this->lowest = $data['lowest'];
        $this->highest = $data['highest'];
        $this->wind = $data['wind'];
        $this->windDeg = (int)$data['winddeg'];
        $this->windSpeed = (int)$data['windspeed'];
        $this->windSc = (int)$data['windsc'];
        $this->sunrise = $data['sunrise'];
        $this->sunset = $data['sunset'];
        $this->moonrise = $data['moonrise'];
        $this->moondown = $data['moondown'];
        $this->pcpn = (float)$data['pcpn'];
        $this->pop = (int)$data['pop'];
        $this->uv_index = (int)$data['uv_index'];
        $this->vis = (int)$data['vis'];
        $this->humidity = (int)$data['humidity'];
        $this->tips = $data['tips'];

    }
}
