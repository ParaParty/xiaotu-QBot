<?php

namespace App\Class\Api\Tianxing\Api\Ncov;

use App\Class\Api\Tianxing\Base\Response;

class Ncov extends Response
{
    /**
     * @var Base\NcovDataArray 返回数据
     */
    public Base\NcovDataArray $data;
}
