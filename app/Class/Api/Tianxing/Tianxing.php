<?php

namespace App\Class\Api\Tianxing;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Tianxing
{
    private string $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**天气查询(60min更新一次)
     * @param string $city 城市名
     * @return Api\Weather\Weather
     */
    public function weather(string $city): Api\Weather\Weather
    {
        $url = 'http://api.tianapi.com/tianqi/index';
        $response = $this->_request($url, [
            'city' => $city
        ]);
        $ret = new Api\Weather\Weather();
        $ret->code = $response->json('code');
        $ret->msg = $response->json('msg');
        if ($ret->code === 200) {
            $ret->data = new Api\Weather\Base\WeatherDataArray($response->json('newslist'));
        }
        return $ret;
    }

    /**疫情查询
     * @return Api\Ncov\Ncov
     */
    public function ncov(): Api\Ncov\Ncov
    {
        $url = 'http://api.tianapi.com/ncov/index';
        $response = $this->_request($url);
        $ret = new Api\Ncov\Ncov();
        $ret->code = $response->json('code');
        $ret->msg = $response->json('msg');
        if ($ret->code === 200) {
            $ret->data = new Api\Ncov\Base\NcovDataArray($response->json('newslist'));
        }
        return $ret;
    }

    /**机器人 智能闲聊
     * @param string $question 聊天内容
     * @param string $uniqueid 唯一标识符
     * @param int $mode 工作模式 0：宽松  1：精确  2：私有
     * @param int $priv 匹配模式 0：完整  1：智能  2：模糊  3：结尾  4：开头
     * @param int $restype 输入类型 0；文本  1：语音  2：人脸图片
     * @return Api\Robot\Robot
     */
    public function robot(string $question, string $uniqueid, int $mode = 0, int $priv = 0, int $restype = 0): Api\Robot\Robot
    {
        $url = 'http://api.tianapi.com/robot/index';
        $response = $this->_request($url, [
            'question' => $question,
            'uniqueid' => $uniqueid,
            'mode' => $mode,
            'priv' => $priv,
            'restype' => $restype
        ]);
        $ret = new Api\Robot\Robot();
        $ret->code = $response->json('code');
        $ret->msg = $response->json('msg');
        if ($ret->code === 200) {
            $ret->data = new Api\Robot\Base\RobotDataArray($response->json('newslist'));
        }
        return $ret;
    }

    /**
     * 请求封装
     * @param string $url
     * @param array $data
     * @return Response
     */
    private function _request(string $url, array $data = []): Response
    {
        $data['key'] = $this->apiKey;
        return Http::asForm()->post($url, $data);
    }
}
