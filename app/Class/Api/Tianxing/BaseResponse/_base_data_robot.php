<?php
namespace App\Class\Api\Tianxing\BaseResponse;

class _base_data_robot
{
    /**
     * @var string type  text:文本/view:图文/image:图片
     */
    public string $type;

    /**
     * @var string  文本内容 或 json文本
     */
    public string $reply;
}
