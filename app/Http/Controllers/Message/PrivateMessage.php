<?php

namespace App\Http\Controllers\Message;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;

use App\Class\QBotRequest\private_message;
use App\Class\QBotReturn\send_private_msg;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\fromJSON;

class PrivateMessage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return array|string
     */
    public function __invoke(Request $request): array|string
    {
        //Http Api
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));

        //收到的请求
        $fromData = new private_message($request->input());

        //$qbot->send_private_msg($fromData->user_id,$ret);
        //$qbot->send_private_msg($fromData->user_id,'\ud83d\udcdd\ud83d\udca1\u2699\ufe0f');
        return $qbot->rapidResponse($fromData->message);

    }
}
