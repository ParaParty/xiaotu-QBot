<?php
namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovDataDescStatistics
{
    /**
     * @var int 现存确诊人数
     */
    public int $currentConfirmedCount;

    /**
     * @var int 累计确诊人数
     */
    public int $confirmedCount;

    /**
     * @var int 累计治愈人数
     */
    public int $curedCount;

    /**
     * @var int 累计死亡人数
     */
    public int $deadCount;

    /**
     * @var int 相比昨天现存确诊人数
     */
    public int $currentConfirmedIncr;

    /**
     * @var int 相比昨天累计确诊人数
     */
    public int $confirmedIncr;

    /**
     * @var int 相比昨天治愈人数
     */
    public int $curedIncr;

    /**
     * @var int 相比昨天死亡人数
     */
    public int $deadIncr;

    public function __construct(array $data)
    {
        $this->currentConfirmedCount = $data['currentConfirmedCount'];
        $this->confirmedCount = $data['confirmedCount'];
        $this->curedCount = $data['curedCount'];
        $this->deadCount = $data['deadCount'];
        $this->currentConfirmedIncr = $data['currentConfirmedIncr'] ?? -1;
        $this->confirmedIncr = $data['confirmedIncr'] ?? -1;
        $this->curedIncr = $data['curedIncr'] ?? -1;
        $this->deadIncr = $data['deadIncr'] ?? -1;
    }
}
