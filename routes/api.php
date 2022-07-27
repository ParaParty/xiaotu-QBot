<?php

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\QBotRequest\private_message;
use App\Class\QBotReturn\send_private_msg;
use App\Http\Controllers\MessageRoute;

use App\Http\Controllers\Event\GroupEvent;
use App\Http\Controllers\Event\PrivateEvent;

use App\Http\Controllers\Message\GroupMessage;
use App\Http\Controllers\Message\PrivateMessage;

use App\Http\Controllers\Request\GroupRequest;
use App\Http\Controllers\Request\PrivateRequest;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//test
Route::get('/test/api', static function () {
    $data=DB::table('daibi')
        ->get();
    foreach ($data as $user) {
        QBotDB::setUserData($user->qq,'银行系统->旭日币',$user->money);
        QBotDB::setUserData($user->qq,'银行系统->旭日勋章',$user->medal);
    }


});
Route::get('/test/req', static function () {
    echo(Http::asJson()->post('http://qbot.xuri.team/api/index', [
        'time' => time(),
        'post_type' => 'message',
        'message_type' => 'group',
        'sub_type'=>'normal',
        'user_id' => 205998108,
        'group_id'=>465434639,
        'message' => 'test',
        'message_id' => '555',
        'sender' => [
            'user_id' => 205998108,
            'nickname' => 'www'
        ]
    ])->body());
});

//消息解析入口
Route::post('/index', MessageRoute::class);
//消息路由
Route::prefix('message')->name('message.')->group(static function () {
    //群消息
    Route::match(['post', 'get'], '/group', GroupMessage::class)->name('group');
    //私聊消息
    Route::match(['post', 'get'], '/private', PrivateMessage::class)->name('private');
});

//事件路由
Route::prefix('event')->name('event.')->group(static function () {
    //群事件
    Route::match(['post', 'get'], '/group', GroupEvent::class)->name('group');
    //好友事件
    Route::match(['post', 'get'], '/private', PrivateEvent::class)->name('private');
});

//请求路由
Route::prefix('request')->name('request.')->group(static function () {
    //群请求
    Route::match(['post', 'get'], '/group', GroupRequest::class)->name('group');
    //好友请求
    Route::match(['post', 'get'], '/private', PrivateRequest::class)->name('private');
});



