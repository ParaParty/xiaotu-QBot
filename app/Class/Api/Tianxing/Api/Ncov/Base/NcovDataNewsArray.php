<?php
namespace App\Class\Api\Tianxing\Api\Ncov\Base;

use App\Class\Api\Tianxing\Base\DataArray;

class NcovDataNewsArray extends DataArray
{
    public function getNextData(): ?NcovDataNews
    {
        $data = parent::getNextData();
        return $data ?new NcovDataNews($data): null;
    }
}
