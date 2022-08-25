<?php
namespace App\Class\Api\WangZheRongYao\BaseResponse;

use phpDocumentor\Reflection\DocBlock\Tags\Formatter\AlignFormatter;

class _base_search_other
{

    /**
     * @var string 游戏昵称
     */
    public string $nickName;

    /**
     * @var int 角色ID
     */
    public int $id;

    /**
     * @var int 原始角色ID
     */
    public int $raw_id;

    /**
     * @var string 性别
     */
    public string $gender;

    /**
     * @var string 在线状态
     */
    public string $status;

    /**
     * @var string 上次在线时间
     */
    public string $lastOnlineTime;

    /**
     * @var int 游戏等级
     */
    public int $level;

    /**
     * @var int 信誉等级
     */
    public int $creditLevel;

    /**
     * @var string 贵族等级 未知为 ***
     */
    public string $vipLevel;

    /**
     * @var string 所在区服
     */
    public string $block;

    /**
     * @var string 段位
     */
    public string $dan;

    /**
     * @var int 巅峰积分
     */
    public int $danScore;

    /**
     * @var string 英雄 已拥有/未拥有
     */
    public string $heroSum;

    /**
     * @var string 皮肤 已拥有/未拥有
     */
    public string $skinSum;

    /**
     * @var int 游戏场次
     */
    public int $gameSum;

    /**
     * @var string 胜率
     */
    public string $victoryRate;

    /**
     * @var float 评分
     */
    public float $score;

    /**
     * @var int 金牌
     */
    public int $goldMedalSum;

    /**
     * @var int 银牌
     */
    public int $silverMedalSum;

    /**
     * @var int 当前赛季最高连胜
     */
    public int $nowVictoryMax;

    /**
     * @var string 历史表现（Web URL）
     */
    public string $history;

    /**
     * @var string 游戏头像（图片URL）
     */
    public string $icon;

    /**
     * @var array 常用英雄[3]{hero,score,victoryRate,usageSum}
     */
    public array $likeHeroes;

}
