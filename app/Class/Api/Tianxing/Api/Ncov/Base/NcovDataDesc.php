<?php

namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovDataDesc extends NcovDataDescForeign
{
    /**
     * @var int id
     */
    public int $id;

    /**
     * @var int 创建时间
     */
    public int $createTime;

    /**
     * @var int 更新时间
     */
    public int $modifyTime;

    /**
     * @var int 现存无症状人数
     */
    public int $seriousCount;

    /**
     * @var int 相比昨天现存无症状人数
     */
    public int $seriousIncr;

    /**
     * @var int 相比昨天境外输入确诊人数
     */
    public int $yesterdaySuspectedCountIncr;

    /**
     * @var int 相比昨天新增累计确诊人数
     */
    public int $yesterdayConfirmedCountIncr;

    /**
     * @var int 国内高风险地区数量
     */
    public int $highDangerCount;

    /**
     * @var int 国内中风险地区数量
     */
    public int $midDangerCount;

    /**
     * @var NcovDataDescForeign 国外数据
     */
    public NcovDataDescForeign $foreignStatistics;

    /**
     * @var NcovDataDescGlobal 全球数据
     */
    public NcovDataDescGlobal $globalStatistics;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->id = $data['id'];
        $this->createTime = $data['createTime'] / 1000;
        $this->modifyTime = $data['modifyTime'] / 1000;
        $this->seriousCount = $data['seriousCount'];
        $this->seriousIncr = $data['seriousIncr'] ?? -1;
        $this->yesterdayConfirmedCountIncr = $data['yesterdayConfirmedCountIncr'] ?? -1;
        $this->yesterdaySuspectedCountIncr = $data['yesterdaySuspectedCountIncr'] ?? -1;
        $this->highDangerCount = $data['highDangerCount'];
        $this->midDangerCount = $data['midDangerCount'];
        $this->foreignStatistics = new NcovDataDescForeign($data['foreignStatistics']);
        $this->globalStatistics = new NcovDataDescGlobal($data['globalStatistics']);
    }
}
