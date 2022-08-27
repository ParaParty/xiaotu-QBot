<?php
namespace App\Class\Api\Tianxing\Api\Ncov\Base;

use App\Class\Api\Tianxing\Base\DataArray;

class NcovDataArray extends DataArray
{
    public function getNextData(): ?NcovData
    {
        $data = parent::getNextData();
        return $data ?new NcovData($data): null;
    }
}
