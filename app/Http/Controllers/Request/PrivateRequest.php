<?php

namespace App\Http\Controllers\Request;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrivateRequest extends Controller
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
        $config = QBotDB::getConfig('request', 'friend', true);
        if ($config->approve) {
            return $qbot->rapidResponse('', [
                'approve' => true
            ]);
        }
        return [];
    }
}
