<?php

namespace App\Class\Api\OwnThink\Api\Question;

class Question
{
    /**
     * @var string success/error
     */
    public string $message;

    /**
     * @var int 消息类型(5000:text)
     */
    public int $type;

    /**
     * @var object 信息体(eg. text)
     */
    public object $info;
}
