<?php

namespace App\Class\Api\WangZheRongYao\Api\Search\Base;

use App\Class\Api\WangZheRongYao\Base\DataArray;

class SearchDataArray extends DataArray
{
    public function getNextData(): ?SearchData
    {
        $data = parent::getNextData();
        return $data ?new SearchData($data): null;
    }
}
