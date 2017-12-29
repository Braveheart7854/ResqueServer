<?php

/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/9/28
 * Time: 15:15
 */
namespace App\Http\Controllers\Home\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public $loginAction = [];
    public $pageSize = 10;

    public $paramsArr = [
        'normal' => [],
        'normalType' => [],
        'need' => [],
        'needType' => []
    ];
    public $params = [];

    public function __construct($needLogin = false)
    {
        $requestUri = \Illuminate\Support\Facades\Request::path();
//        $requestUri = \Illuminate\Support\Facades\Request::server('REQUEST_URI');
//        $pathInfo = explode('?',$requestUri);
//        $paths = explode('/',$pathInfo[0]);
//        $requestUri = $paths[1].'/'.$paths[2];
        if (in_array($requestUri,$this->loginAction)){
            $this->checkLogin();
        }
    }

    public function checkLogin()
    {
        $this->middleware(function ($request, $next) {
            $account = \Session::get('account');
            if (empty($account)) {
                return response($this->returnJson(UNLOGIN));
            }
            return $next($request);
        });
    }

    public function returnJson($code, $message = '', $data = [])
    {
        if (empty($message)) $message = config('status.'.$code);
        $return = ['code' => $code, 'message' => $message, 'data' => $data];
        return $return;
        //输出json的头，并且不转义/
//        header('Content-Type:application/json');
//        echo json_encode($return, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
//        exit;
    }

    /**
     * @param Request $request
     * @param bool $strict  是否严格检查(非空)
     * @return mixed
     */
    function checkParams(Request $request, $strict = false)
    {
        if (!isset($this->paramsArr['normal']) || !isset($this->paramsArr['need'])) {
            return ['code'=>PARAM_ERROR, 'message'=>'调用方式不正确'];
        }
        
        $paramsInput = json_decode(file_get_contents("php://input"),true) ?? [];
        $params = array_merge($request->all(),$paramsInput);

        //过滤不需要的参数
        $params = $this->passParams(array_merge($this->paramsArr['normal'], $this->paramsArr['need']), $params);
        //必要检查
        $checkResult = $this->emptyCheck($this->paramsArr['need'], $params, $strict);
        if (!$checkResult) {
            return ['code'=>PARAM_ERROR, 'message'=>'参数不能为空'];
        }

        //格式检查
        if (isset($this->paramsArr['needType'])) {
            $result = $this->checkType($this->paramsArr['need'], $this->paramsArr['needType'], $params);
            if (!$result) return ['code'=>PARAM_ERROR, 'message'=>'参数格式不正确'];
        }
        if (isset($this->paramsArr['normalType'])) {
            $result = $this->checkType($this->paramsArr['normal'], $this->paramsArr['normalType'], $params);
            if (!$result) return ['code'=>PARAM_ERROR, 'message'=>'参数格式不正确'];
        }
        $this->params = $params;
        return true;
    }

    public function emptyCheck($needKeyArr, $valueArr, $strict = false)
    {
        if (empty($needKeyArr)) {
            return true;
        }
        foreach ($needKeyArr as $key) {
            if ($strict !== false) {
                if (empty($valueArr[$key])) {
                    return false;
                }
            } else {
                if (!isset($valueArr[$key])) {
                    return false;
                }
            }
        }
        return true;
    }

    public function checkType($needKeyArr, $typeArr, &$valueArr)
    {
        if (empty($needKeyArr)) return true;
        foreach ($needKeyArr as $k => $v) {
            if (isset($valueArr[$v])) {
                $flag = 0;
                switch ($typeArr[$k]) {
                    case 'string':
                        $valueArr[$v] = htmlentities(strval($valueArr[$v]));
                        break;
                    case 'int':
                        $flag = is_numeric($valueArr[$v]) ? 0 : 1;
                        $valueArr[$v] = intval($valueArr[$v]);
                        break;
                    case 'float':
                        $flag = is_numeric($valueArr[$v]) ? 0 : 1;
                        $valueArr[$v] = floatval($valueArr[$v]);
                        break;
                    default:
                        break;
                }
                if ($flag == 1) return false;
            }
        }
        return true;
    }

    public function passParams($allowedArr, $params)
    {
        foreach ($params as $key => &$value) {
            if (!in_array($key, $allowedArr)) {
                unset($params[$key]);
            } else {
                $value = self::fileterText($value);
            }
        }
        return $params;
    }

    static function fileterText($str, $filterEsc = false)
    {
        {
            $str = trim($str);
            $str = preg_replace('/[\a\f\e\0\x0B]/is', "", $str);
            $filter = $filterEsc;
            if ($filter) {
                $str = preg_replace('/[\n\r\t]/is', "", $str);
            }
            $str = htmlspecialchars($str, ENT_QUOTES);
            $str = self::filterTag($str);
            $str = self::filterCommon($str);
            return $str;
        }
    }

    static function filterCommon($str)
    {
        $str = str_replace("\\0", "0", $str);
        $str = str_replace("&#032;", "", $str);
        $str = preg_replace("/\\\$/", "&#036;", $str);
        $str = stripslashes($str);
        return $str;
    }

    //防xss,过滤tag
    static function filterTag($str)
    {
        $str = str_ireplace("javascript", "j&#097;v&#097;script", $str);
        $str = str_ireplace("alert", "&#097;lert", $str);
        $str = str_ireplace("about:", "&#097;bout:", $str);
        $str = str_ireplace("onmouseover", "&#111;nmouseover", $str);
        $str = str_ireplace("onclick", "&#111;nclick", $str);
        $str = str_ireplace("onload", "&#111;nload", $str);
        $str = str_ireplace("onsubmit", "&#111;nsubmit", $str);
        $str = str_ireplace("<script", "&#60;script", $str);
        $str = str_ireplace("onerror", "&#111;nerror", $str);
        $str = str_ireplace("document.", "&#100;ocument.", $str);
        return $str;
    }

    static function http($url, $param, $second = 0)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        //curl_setopt($oCurl, CURLOPT_HTTPHEADER , $header);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($param)){
            curl_setopt($oCurl, CURLOPT_POST, true);
//            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($param));
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $param);
        }

        $second = intval($second);
        if ($second) {
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $second);
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return json_decode(mb_convert_encoding($sContent, 'UTF-8', 'GBK,GB2312,BIG5,EUC-CN,GB18030,UNICODE'),true);
        } else {
            return false;
        }
    }

    public function getClientIP()
    {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

    public function getAccount(){
        return session('account');
    }
}