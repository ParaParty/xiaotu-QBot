<?php
namespace App\Class\QBotReturn_Base;
class _forward_msg
{
    /**
     * @var string 消息内容
     */
    public string $content;

    /**
     * @var int 时间戳
     */
    public int $time;

    /**
     * @var _sender 发送者信息
     */
    public _sender $sender;
}
