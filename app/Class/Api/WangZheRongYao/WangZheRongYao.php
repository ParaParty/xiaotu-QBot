<?php

namespace App\Class\Api\WangZheRongYao;


use App\Class\Api\WangZheRongYao\Response\Search_Other;
use Illuminate\Support\Facades\Http;

class WangZheRongYao
{
    /**
     * 信息查询
     * @param string $nickName 玩家昵称
     * @return Search_Other|false|null
     */
    public static function search_other(string $nickName): Search_Other|null|false
    {
        $url = 'http://82.157.7.86:2530/pvp/json/search.php';
        $response = HTTP::get($url, [
            'name' => $nickName
        ]);
        if ($response->json('returnCode') !== 0) {
            return false;
        }
        if ($response->json('datasize') === 0) {
            return null;
        }
        return new Search_Other($response->json('data'));
    }

}
