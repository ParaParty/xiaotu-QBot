<?php

namespace App\Class;

use App\Class\QBotReturn\get_forward_msg;
use App\Class\QBotReturn\get_image;
use App\Class\QBotReturn\get_msg;
use App\Class\QBotReturn\send_group_msg;
use App\Class\QBotReturn\send_private_msg;
use App\Class\QBotReturn_Base\_forward_msg;
use App\Class\QBotReturn_Base\_sender;
use http\Message;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\Types\Boolean;

class QBotHttpApi
{
    #region Base

    protected string $url;
    protected string $access_token;

    //protected string $self_id;

    /**
     * 构造函数
     * @param string $url Http Api地址
     * @param string $access_token 鉴权token
     * @param string $self_id 机器人QQ账号
     */
    public function __construct(string $url, string $access_token = '', string $self_id = '')
    {
        if ($url[strlen($url) - 1] !== '/') {
            $url .= '/';
        }
        $this->url = $url;
        $this->access_token = $access_token;

        //$this->self_id = $self_id;
    }

    /**
     * 发送群消息
     * @param string $group_id 目标群号
     * @param string $message 消息内容
     * @param bool $auto_escape 是否纯文本发送(不解析CQ码)，默认为false
     * @return bool|send_group_msg 请求成功与否|如果有数据则返回数据
     */
    public function send_group_msg(string $group_id, string $message, bool $auto_escape = false): bool|send_group_msg
    {
        $api = 'send_group_msg';
        $message=TCode::replace($message);
        if (is_array($result = $this->request($api, [
            'group_id' => $group_id,
            'message' => $message,
            'auto_escape' => $auto_escape
        ]))) {
            $data = new send_group_msg();
            $data->message_id = $result['message_id'];
            return $data;
        }
        return $result;
    }


    #endregion

    #region Message

    /**
     * 发送请求
     * @param string $api Http Api地址
     * @param array $data 请求数据
     * @return array|bool 请求成功与否|如果有数据则返回数据
     */
    protected function request(string $api, array $data): array|bool
    {
        try {
            $data =json_decode(
                str_replace('\\\\u','\\u',
                json_encode($data, JSON_THROW_ON_ERROR)
                ), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return false;
        }
        //鉴权
        if ($this->access_token) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->access_token
            ])->post($this->url . $api, $data);
        } else {
            $response = Http::post($this->url, $data);
        }
        if ($response->ok()) {
            //请求成功
            if ($response->json('retcode') !== 0) {
                //操作失败，暂定返回false
                return false;
            }
            if (empty($response->json('data'))) {
                //无数据接口
                return true;
            }
            //返回数据
            return (array)$response->json('data');
        }
        return false;
    }

    /**
     * 发送私聊消息
     * @param int $user_id 目标QQ账号
     * @param string $message 消息内容
     * @param bool $auto_escape 是否纯文本发送(不解析CQ码)，默认为false
     * @return bool|send_private_msg
     */
    public function send_private_msg(int $user_id, string $message, bool $auto_escape = false): bool|send_private_msg
    {
        $api = 'send_private_msg';
        $message=TCode::replace($message);
        if (is_array($result = $this->request($api, [
            'user_id' => $user_id,
            'message' => $message,
            'auto_escape' => $auto_escape
        ]))) {
            $data = new send_private_msg();
            $data->message_id = $result['message_id'];
            return $data;
        }
        return $result;
    }

    /**
     * 撤回消息
     * @param int $message_id 消息ID
     * @return bool 请求成功与否
     */
    public function delete_msg(int $message_id): bool
    {
        $api = 'delete_msg';
        return $this->request($api, [
            'message_id' => $message_id
        ]);
    }

    /**
     * 获取消息
     * @param int $message_id
     * @return bool|get_msg 请求成功与否|如果有数据则返回数据
     */
    public function get_msg(int $message_id): bool|get_msg
    {
        $api = 'get_msg';
        if (is_array($result = $this->request($api, [
            'message_id' => $message_id
        ]))) {
            $data = new get_msg();
            $data->message_id = $result['message_id'];
            return $data;
        }
        return $result;
    }

    /**
     * 获取合并消息
     * @param int $message_id
     * @return bool|get_forward_msg 请求成功与否|如果有数据则返回数据，数组成员为 XuriQBotReturn_get_forward_msg
     */
    public function get_forward_msg(int $message_id): bool|get_forward_msg
    {
        $api = 'get_forward_msg';
        if (is_array($result = $this->request($api, [
            'message_id' => $message_id
        ]))) {
            $data = new get_forward_msg();
            foreach ($result['messages'] as $i => $message) {
                $data->messages[$i] = new _forward_msg();
                $data->messages[$i]->content = $message['content'];
                $data->messages[$i]->time = $message['time'];

                $data->messages[$i]->sender = new _sender();
                $data->messages[$i]->sender->user_id = $message['sender']['user_id'];
                $data->messages[$i]->sender->nickname = $message['sender']['nickname'];
            }
            return $data;
        }
        return $result;
    }

    /**
     * 获取图片信息
     * @param string $file
     * @return bool|get_image 请求成功与否|如果有数据则返回数据
     */
    public function get_image(string $file): array|bool
    {
        $api = 'get_image';
        if (is_array($result = $this->request($api, [
            'file' => $file
        ]))) {
            $data = new get_image();
            $data->size = $result['size'];
            $data->filename = $result['filename'];
            $data->url = $result['url'];
            return $data;
        }
        return $result;
    }

    #endregion

    #region Event
    /**
     * 群组单人禁言
     * @param int $group_id 群组id
     * @param int $user_id 用户id
     * @param int $duration 禁言时长，单位为秒，0为解禁
     * @return bool 请求成功与否
     */
    public function set_group_ban(int $group_id,int $user_id,int $duration=0): bool
    {
        $api = 'set_group_ban';
        return $this->request($api, [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'duration' => $duration,
        ]);
    }
    #endregion

    #region Other

    /**
     * 快速操作
     * @param string $reply 快速操作默认参数
     * @param array $data 附加数据，默认为空
     * @return array|string
     */
    public function rapidResponse(string $reply, array $data = []): array|string
    {
        $data['reply'] =TCode::replace($reply);
        try {
            $data =json_decode(
                str_replace('\\\\u','\\u',
                    json_encode($data, JSON_THROW_ON_ERROR)
                ), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return false;
        }
        return $data;
    }

    #endregion
}
