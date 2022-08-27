<?php
namespace App\Class\Api\Tianxing\BaseResponse;

class _base_data_weather
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

}
