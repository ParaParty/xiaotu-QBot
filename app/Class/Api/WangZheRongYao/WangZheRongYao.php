<?php

namespace App\Class\Api\WangZheRongYao;

use Illuminate\Support\Facades\Http;

class WangZheRongYao
{
    /**
     * 信息查询
     * @param string $nickName 玩家昵称
     * @return Api\Search\Search|false|null
     */
    public static function search(string $nickName): Api\Search\Search|null|false
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
        return new Api\Search\Search($response->json('data'));
    }

}
