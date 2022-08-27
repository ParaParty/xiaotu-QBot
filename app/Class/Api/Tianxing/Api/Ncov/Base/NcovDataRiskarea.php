<?php

namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovDataRiskarea
{
    /**
     * @var array 高风险地区 ['省·市·地址1,地址2]
     */
    public array $high;

    /**
     * @var array 中风险地区 ['省·市·地址1,地址2]
     */
    public array $mid;

    public function __construct(array $data)
    {
        $this->high = $data['high'];
        $this->mid = $data['mid'];
    }
}
