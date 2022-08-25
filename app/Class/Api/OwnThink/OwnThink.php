<?php

namespace App\Class\Api\OwnThink;

use App\Class\Api\OwnThink\Response\Question;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class OwnThink
{
    private string $appId;

    public function __construct(string $appId)
    {
        $this->appId = $appId;
    }

    /**
     * 聊天接口
     * @param string $spoken 聊天内容
     * @param string $user_id 用户标识
     * @return Question
     */
    public function question(string $spoken, string $user_id): Question
    {
        $url = 'https://api.ownthink.com/bot';
        $response = $this->request($url, [
            'spoken' => $spoken,
            'userid' => $user_id
        ]);
        $ret = new Question();
        $ret->message = $response->json('message');
        $ret->type = $response->json('data.type');
        $ret->info = (object)$response->json('data.info');
        return $ret;
    }

    //请求封装
    private function request(string $url, array $data): Response
    {
        $data['Appid'] = $this->appId;
        return Http::post($url, $data);
    }
}
