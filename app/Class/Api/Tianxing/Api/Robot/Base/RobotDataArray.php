<?php

namespace App\Class\Api\Tianxing\Api\Robot\Base;

use App\Class\Api\Tianxing\Base\DataArray;

class RobotDataArray extends DataArray
{
    public function getNextData(): ?RobotData
    {
        $data = parent::getNextData();
        return $data ? new RobotData($data) : null;
    }
}
