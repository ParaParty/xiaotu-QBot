<?php

namespace App\Class\Api\Tianxing;

use App\Class\Api\Tianxing\Response\Robot;
use App\Class\Api\Tianxing\Response\Weather;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Tianxing
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**天气查询(60min更新一次)
     * @param string $city 城市名
     * @return Weather
     */
    public function weather(string $city): Weather
    {
        $url = 'http://api.tianapi.com/tianqi/index';
        $response = $this->request($url, [
            'city' => $city
        ]);
        $ret = new Weather();
        $ret->code = $response->json('code');
        $ret->msg = $response->json('msg');
        if ($ret->code === 200) {
            $ret->data = $response->json('newslist');
        }
        return $ret;
    }

    private function request(string $url, array $data): Response
    {
        $data['key'] = $this->apiKey;
        return Http::asForm()->post($url, $data);
    }

    //请求封装

    /**机器人 智能闲聊
     * @param string $question 聊天内容
     * @param string $uniqueid 唯一标识符
     * @param int $mode 工作模式 0：宽松  1：精确  2：私有
     * @param int $priv 匹配模式 0：完整  1：智能  2：模糊  3：结尾  4：开头
     * @param int $restype 输入类型 0；文本  1：语音  2：人脸图片
     * @return Robot
     */
    public function robot(string $question, string $uniqueid, int $mode = 0, int $priv = 0, int $restype = 0): Robot
    {
        $url = 'http://api.tianapi.com/robot/index';
        $response = $this->request($url, [
            'question' => $question,
            'uniqueid' => $uniqueid,
            'mode' => $mode,
            'priv' => $priv,
            'restype' => $restype
        ]);
        $ret = new Robot();
        $ret->code = $response->json('code');
        $ret->msg = $response->json('msg');
        if ($ret->code === 200) {
            $ret->data = $response->json('newslist');
        }
        return $ret;
    }
}
