<?php

namespace App\Http\Controllers;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Http\Controllers\Message\PrivateMessage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class MessageRoute extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|void
     */
    public function __invoke(Request $request)
    {
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));
        //鉴权
        if (($key = QBotDB::getConfig('system', 'http_secret')) &&
            $request->header('X-Signature') !== 'sha1=' . hash_hmac('sha1', $request->getContent(), $key)) {
            //鉴权失败
            abort(401);
        }

        //路由
        switch ($request->input('post_type')) {
            case 'message':
                switch ($request->input('message_type')) {
                    case 'group':
                        //过滤群
                        if (QBotDB::getConfig('system', 'only_group') === 'true' &&
                            (int)QBotDB::getConfig('system', 'group') !== $request->input('group_id')) {
                            abort(403);
                        }
                        //过滤非正常群员消息（匿名，系统）
                        if ($request->input('sub_type') !== 'normal') {
                            abort(403);
                        }
                        return redirect()->route('message.group', $request->input());
                    case 'private':
                        return redirect()->route('message.private', $request->input());
                    default:
                        abort(400);
                }
            case 'event':
                switch ($request->input('event_type')) {
                    case 'group':
                        return redirect(route('event.group', $request->input()));
                    case 'private':
                        return redirect(route('event.private', $request->input()));
                    default:
                        abort(400);
                }
            case 'request':
                switch ($request->input('request_type')) {
                    case 'group':
                        return redirect(route('request.group', $request->input()));
                    case 'private':
                        return redirect(route('request.private', $request->input()));
                    default:
                        abort(400);
                }
        }
        abort(400);
    }
}
