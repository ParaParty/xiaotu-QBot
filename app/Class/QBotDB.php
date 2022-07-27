<?php

namespace App\Class;

use Carbon\Traits\Date;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class QBotDB
{
    /**
     * 获取配置
     * @param string $key 欲获取配置组名
     * @param string $value 欲获取配置建名，若留空则返回所有
     * @return mixed 返回配置信息或null
     */
    public static function getConfig(string $key, string $value = ''): mixed
    {
        $value = $value ? 'value->' . $value : 'value';
        return DB::table('config')
            ->where('key', $key)
            ->value($value);
    }

    /**
     * 设置配置
     * @param string $key 欲配置配置组名
     * @param string $value 欲配置配置建名
     * @param mixed $data 欲配置配置建值
     * @return bool 成功与否
     */
    public static function setConfig(string $key, string $value, mixed $data): bool
    {
        $value = $value ? 'value->' . $value : 'value';
        return DB::table('config')
            ->where('key', $key)
            ->updateOrInsert(['key' => $key], [$value => $data]);
    }

    /**
     * 获取用户数据
     * @param int $user_id 欲配置用户数据组名
     * @param string $value 欲配置用户建名，若留空则返回所有
     * @return mixed 返回配置信息或null
     */
    public static function getUserData(int $user_id, string $value = ''): mixed
    {
        $value = $value ? 'data->' . $value : 'data';
        return DB::table('users')
            ->where('id', $user_id)
            ->value($value);
    }

    /**
     * 设置用户数据
     * @param int $user_id 欲配置配置组名
     * @param string $value 欲配置配置建名
     * @param mixed $data 欲配置配置建值
     * @return bool
     */
    public static function setUserData(int $user_id, string $value, mixed $data): bool
    {
        $value = $value ? 'user_data->' . $value : 'user_data';
        if (!DB::table('users')
            ->where('user_id', $user_id)
            ->first()) {
            DB::table('users')
                ->insert(['user_id' => $user_id, 'user_data' => '{}']);
        }
        $user = DB::table('users')
            ->where('user_id', $user_id);
        $index_arr = explode('->', $value);
        if (($i = count($index_arr)) === 1) {
            //value=user_date,覆写用户数据
            return (bool)$user->update([$value => $data]);
        }
        $index_str = 'user_data';
        //这是坨屎山，别碰它，会变得不幸
        //我也不知道有的数值为什么是这个值
        //但是它能润
        //能润就行
        //
        //~~~~~~~~~~~~~~~~~初音未来保佑~~~~~~~~~~~~~~~~
        //~~~~~~~~~~~~~~~~~让它一直的润~~~~~~~~~~~~~~~~
        //                   #####
        // *               #########
        // *              ############
        // *              #############
        // *             ##  ###########
        // *            ###  ###### #####
        // *            ### #######   ####
        // *           ###  ########## ####
        // *          ####  ########### ####
        // *        #####   ###########  #####
        // *       ######   ### ########   #####
        // *       #####   ###   ########   ######
        // *      ######   ###  ###########   ######
        // *     ######   #### ##############  ######
        // *    #######  ##################### #######
        // *    #######  ##############################
        // *   #######  ###### ################# #######
        // *   #######  ###### ###### #########   ######
        // *   #######    ##  ######   ######     ######
        // *   #######        ######    #####     #####
        // *    ######        #####     #####     ####
        // *     #####        ####      #####     ###
        // *      #####      ;###        ###      #
        // *        ##       ####        ####
        for ($max = $i - 1, $i = 1; $i <= $max; $i++) {
            if (!DB::table('users')
                ->where('user_id', $user_id)
                ->value($index_str)) {
                for ($j = $max - $i + 1; $j >= 1; $j--) {
                    //构造json update数组
                    $data = [$index_arr[$j+1] => $data];
                }
                echo $index_str;
                return (bool)DB::table('users')
                    ->where('user_id', $user_id)
                    ->update([$index_str => $data]);
            }
            $index_str .= '->' . $index_arr[$i];
        }

        return (bool)DB::table('users')
            ->where('user_id', $user_id)
            ->update([$value => $data]);
    }

    /**
     * 获取菜单
     * @param string $title 欲获取菜单名
     * @return mixed 返回菜单文本内容或null
     */
    public static function getMenu(string $title): mixed
    {
        return DB::table('menu')
            ->where('title', $title)
            ->orwhere('title', 'like', '%\_' . $title)
            ->value('content');
    }

    /**
     * 设置菜单
     * @param string $title
     * @param string $content
     * @return bool 成功与否
     */
    public static function setMenu(string $title, string $content): bool
    {
        return DB::table('menu')
            ->where('title', $title)
            ->orwhere('title', 'like', '%\_' . $title)
            ->updateOrInsert(['title' => $title], ['content' => $content]);
    }

    /**
     * 获取用户发言数据(本地)
     * @param array $search_data 搜索参数
     * @return mixed 返回发言数组或null
     */
    public static function getSpeech(array $search_data = []): mixed
    {
        $search_data['end_time'] = $search_data['end_time'] ?? time();
        $search_data['begin_time'] = $search_data['begin_time'] ?? 0;
        if ($search_data['begin_time'] >= $search_data['end_time']) {
            return false;
        }

        return DB::table('speech')
            ->where('user_id', 'like', $search_data['user_id'] ?? '%')
            ->where('group_id', 'like', $search_data['group_id'] ?? '%')
            ->whereBetween('datetime', [
                date('Y-m-d H:i:s', $search_data['begin_time']),
                date('Y-m-d H:i:s', $search_data['end_time'])
            ])
            ->latest('datetime')
            ->get();
    }

    /**
     * 添加一条用户发言数据(本地)
     * @param array $set_data 发言信息
     * @return bool 返回成功与否
     */
    public static function setSpeech(array $set_data): bool
    {
        return DB::table('speech')->insert($set_data);
    }
}


