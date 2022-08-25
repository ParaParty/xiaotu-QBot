<?php
namespace App\Class\Api\Tianxing\BaseResponse;
class _base_response
{
    /**
     * @var int 状态码
     */
    public int $code;

    /**
     * @var string 状态信息
     */
    public string $msg;

    /**
     * @var array 返回数据
     */
    public array $data;

}
