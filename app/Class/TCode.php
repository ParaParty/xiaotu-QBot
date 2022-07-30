<?php

namespace App\Class;

class TCode
{
    //QQ小表情
    public static array $face = [
        '太阳' => 74


    ];

    //屎山改，能润就行
    public static function replace(string $str, array $data = []): string
    {
        preg_match_all('/(?<!\\\\){.+?(?!\\\\)}/', $str, $arr, PREG_UNMATCHED_AS_NULL);
        foreach ($arr[0] as $key) {
            $key2 = substr($key, 1, -1);
            if (isset($data[$key2])) {
                //提交参数
                $str = str_replace($key, $data[$key2], $str);
            } elseif ($key2[0]==='@') {
                if(is_numeric($qq=substr($key2,1))){
                    //QQ艾特
                    //懒的判断是不是标准QQ号了，毕竟屎山改，能润就行
                    $str = str_replace($key, self::at($qq), $str);
                }
            } elseif (isset(self::$face[$key2])) {
                //QQ小表情
                $str = str_replace($key, self::makeCQ_code('face', [
                    'id' => self::$face[$key2]
                ]), $str);
            }

        }
        //反转义处理
        return str_replace(array('\\{', '\\}'), array('{', '}'), $str);
    }

    private static function makeCQ_code(string $name, array $data): string
    {
        $str = "[CQ:$name";
        foreach ($data as $key => $value) {
            $str .= ",$key=$value";
        }
        $str .= ']';
        return $str;
    }

    public static function at(int $qq): string
    {
        return self::makeCQ_code('at', ['qq' => $qq]);
    }
}
