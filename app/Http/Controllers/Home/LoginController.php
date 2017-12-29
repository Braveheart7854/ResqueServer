<?php
/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/11/21
 * Time: 9:51
 */

namespace App\Http\Controllers\Home;


use App\Http\Controllers\Home\Common\CommonController;
use App\Model\Players;
use Illuminate\Http\Request;


class LoginController extends CommonController
{
    CONST APP_ID = 205;
    const LOGIN_API = 'http://cas.dobest.com/cas/login';
    const LOGOUT_API = 'http://cas.dobest.com/cas/logout';

    private $ticket = '';

    public $loginAction = [];

    public function login(Request $request){
//        session(['account'=>'15068775512']);
//        session(['account'=>'sgs1000015']);
//        return redirect('bind/view-bind-area');;

        $this->ticket = $request->get('ticket');
        $result = $this->userValidate();
        
        if ($result['code'] == 0){
            $result['data']['account'] = $result['data']['inputUserId'];
            session($result['data']);

            $player = Players::getPlayerByAccount($result['data']['account']);
            if (empty($player))
                return redirect('bind/view-bind-area');
        }
        return redirect('bind/view-bind-account');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $url = env('APP_URL').'/bind/view-bind-area';

        $url = self::LOGOUT_API . '?' . "url=" . $url;
        return redirect($url);
    }

    public function userValidate()
    {
        $param = array(
            'app_id' => self::APP_ID,
            'ticket' => $this->ticket,
            'client_ip' => $this->getClientIP()
        );
        $param['hash'] = $this->generate_sign($param);
        $url = "http://userapi.dobest.com/cas/validate?" . http_build_query($param);

        $data = $this->http($url,$param);

        return $data;
    }

    private function generate_sign($param)
    {
        if (is_array($param)) {
            if (isset($param['sign'])) {
                unset($param['sign']);
            }
            ksort($param);
            $stringToBeSigned = '';
            foreach ($param as $key => $v) {
                $stringToBeSigned .= $key . "=" . $v;
            }
            $stringToBeSigned .= '3hiquK6n4ellEbhGqClUWw6S';
            return md5($stringToBeSigned);
        } else {
            return false;
        }
    }
}