<?php
namespace App\Class\QBotReturn;
use App\Class\QBotReturn_Base\_msg;

class get_msg extends _msg
{
    /**
     * @var int 消息真实id
     */
    public int $real_id;

    /**
     * @var object 发送者信息
     */
    public object $sender;

    /**
     * @var int 时间戳
     */
    public int $time;

    /**
     * @var string 消息内容
     */
    public string $message;

    /**
     * @var string 原始消息内容
     */
    public string $raw_message;
}
