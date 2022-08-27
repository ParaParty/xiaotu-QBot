<?php
namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovDataDescGlobal extends NcovDataDescStatistics
{
    /**
     * @var int 相比昨天新增累计确诊人数
     */
    public int $yesterdayConfirmedCountIncr;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->yesterdayConfirmedCountIncr = $data['yesterdayConfirmedCountIncr'] ?? -1;
    }
}
