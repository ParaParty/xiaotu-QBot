<?php

namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovData
{
    /**
     * @var NcovDataDesc
     */
    public NcovDataDesc $desc;

    /**
     * @var NcovDataNewsArray 新闻
     */
    public NcovDataNewsArray $news;

    /**
     * @var NcovDataRiskarea 中高风险地区
     */
    public NcovDataRiskarea $riskarea;

    public function __construct(array $data)
    {
        $this->news = new NcovDataNewsArray($data['news']);
        $this->desc = new NcovDataDesc($data['desc']);
        $this->riskarea=new NcovDataRiskarea($data['riskarea']);

    }
}
