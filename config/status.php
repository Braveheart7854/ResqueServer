<?php
/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/9/28
 * Time: 16:04
 */
define('SUCCESS',10000);
define('FAILED',10001);
define('UNLOGIN',10002);
define('PARAM_ERROR',10003);
define('PASSWORD_FALSE',10004);
define('CAPTCHA_SEND_ERROR',10005);
define('CAPTCHA_LIMIT',10006);//短信发送太频繁
define('PARTFAILED',10007); //部分失败
define('REPEATCOMMIT',10008); //重复提交
define('FORBINDEN',10009); //没有权限

return [
    10000 => '操作成功',
    10001 => '操作失败',
    10002 => '请先登录',
    10003 => '参数错误',
    10004 => '密码错误',
    10005 => '短信验证码发送失败',
    10006 => '短信发送太频繁',
    10007 => '部分失败',
    10008 => '请勿重复提交',
    10009 => '没有权限',
];