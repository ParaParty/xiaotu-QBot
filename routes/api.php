<?php

use App\Class\Api\OwnThink\OwnThink;
use App\Class\Api\Tianxing\Tianxing;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

use App\Class\TCode;
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

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Metowolf\Meting;

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

//test2
Route::get('/test/api', static function () {


});
Route::get('/test/req', static function () {
    echo(Http::asJson()->post('http://qbot.xuri.team/api/index', [
        'time' => time(),
        'post_type' => 'message',
        'message_type' => 'group',
        'sub_type' => 'normal',
        'user_id' => 205998108,
        'group_id' => 465434639,
        'message' => 'test',
        'message_id' => '555',
        'sender' => [
            'user_id' => 205998108,
            'nickname' => 'www'
        ]
    ])->body());
});

//??????????????????
Route::post('/index', MessageRoute::class);
//????????????
Route::prefix('message')->name('message.')->group(static function () {
    //?????????
    Route::match(['post', 'get'], '/group', GroupMessage::class)->name('group');
    //????????????
    Route::match(['post', 'get'], '/private', PrivateMessage::class)->name('private');
});

//????????????
Route::prefix('event')->name('event.')->group(static function () {
    //?????????
    Route::match(['post', 'get'], '/group', GroupEvent::class)->name('group');
    //????????????
    Route::match(['post', 'get'], '/private', PrivateEvent::class)->name('private');
});

//????????????
Route::prefix('request')->name('request.')->group(static function () {
    //?????????
    Route::match(['post', 'get'], '/group', GroupRequest::class)->name('group');
    //????????????
    Route::match(['post', 'get'], '/private', PrivateRequest::class)->name('private');
});



