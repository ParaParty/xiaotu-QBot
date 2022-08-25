<?php

namespace App\Class\QBotRequest;

use App\Class\QBotReturn_Base\_msg;

class group_message extends _msg
{

    /**
     * @var int $group_id 群号
     */
    public int $group_id;
    /**
     * @var int $user_id QQ号
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
    /**
     * @var string $user_role 用户角色:admin管理员,member:群成员
     */
    public string $user_role;
    /**
     * @var string $user_card 群员群名片
     */
    public string $user_card;
    /**
     * @var string $user_title 群员头衔
     */
    public string $user_title;
    /**
     * @var string $user_level 群员头衔
     */
    public string $user_level;

    public function __construct(array $data)
    {
        $this->time = $data['time'];
        $this->self_id = $data['self_id'];
        $this->message_id = $data['message_id'];
        $this->message = $data['message'];
        $this->group_id = $data['group_id'];
        $this->user_id = $data['sender']['user_id'];
        $this->user_nickname = $data['sender']['nickname'];
        $this->user_role = $data['sender']['role'];
        $this->user_title = $data['sender']['title']??'';
        $this->user_card = $data['sender']['card']??'';
        $this->user_level= $data['sender']['level']??'';
    }
}
