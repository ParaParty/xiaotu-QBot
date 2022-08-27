<?php
namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovDataDescForeign extends NcovDataDescStatistics
{
    /**
     * @var int 累计境外输入
     */
    public int $suspectedCount;

    /**
     * @var int 新增境外输入
     */
    public int $suspectedIncr;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->suspectedCount = $data['suspectedCount'];
        $this->suspectedIncr = $data['suspectedIncr'] ?? -1;
    }
}
