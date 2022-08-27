<?php
namespace App\Class\Api\Tianxing\Api\Ncov\Base;

class NcovDataNews
{
    /**
     * @var int id
     */
    public int $id;

    /**
     * @var int timestamp
     */
    public int $pubDate;

    /**
     * @var string 时间文本化 12小时前
     */
    public string $pubDateStr;

    /**
     * @var string 标题 省|概括
     */
    public string $title;

    /**
     * @var string 内容
     */
    public string $summary;

    /**
     * @var string 来源 四川卫健委/央视新闻app
     */
    public string $infoSource;

    /**
     * @var string 链接
     */
    public string $sourceUrl;

    public function __construct(array $data)
    {
        $this->id=$data['id'];
        $this->pubDate=$data['pubDate'];
        $this->pubDateStr=$data['pubDateStr'];
        $this->title=$data['title'];
        $this->summary=$data['summary'];
        $this->infoSource=$data['infoSource'];
        $this->sourceUrl=$data['sourceUrl'];
    }
}
