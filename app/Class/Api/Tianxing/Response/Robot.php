<?php

namespace App\Class\Api\Tianxing\Response;

use App\Class\Api\Tianxing\BaseResponse\_base_data_robot;
use App\Class\Api\Tianxing\BaseResponse\_base_response;


class Robot extends _base_response
{
    /**
     * @var int 索引
     */
    private int $i = 0;

    /**
     * @return _base_data_robot|null
     */
    public function getNextData(): ?_base_data_robot
    {
        if (!isset($this->data[$this->i])) {
            return null;
        }
        $data = $this->data[$this->i];
        $this->i++;
        $ret = new _base_data_robot();
        $ret->type = $data['datatype'];
        $ret->reply = $data['reply'] ?? '';
        return $ret;
    }
}
