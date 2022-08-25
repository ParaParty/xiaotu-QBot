<?php

namespace App\Class\QBotRequest;

use App\Class\QBotReturn_Base\_msg;

class private_message extends _msg
{
    /**
     * @var int group_id QQ号
     */
    public int $user_id;
    /**
     * @var string $message 消息内容
     */
    public string $message;
    /**
     * @var string $user_nickname 用户昵称
     */
    public string $user_nickname;

    public function __construct(array $data)
    {
        $this->time = $data['time'];
        $this->message_id = $data['message_id'];
        $this->message = $data['message'];
        $this->user_id = $data['sender']['user_id'];
        $this->user_nickname = $data['sender']['nickname'];
    }
}
