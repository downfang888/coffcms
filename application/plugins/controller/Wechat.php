<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\plugins\controller;

class Wechat extends Base
{
    /**
     * 公众号配置
     */
    public $config;

    /**
     * 逻辑对象
     */
    public $logic_obj;

    /**
     * 是否来自于微信的服务器配置验证
     */
    public $is_check_signature = false;

    /**
     * 构造方法
     */
    public function __construct($appid = '', $ctl = ''){
        parent::__construct();

        //获取配置
        $map = array(
            'appid' => $appid,
        );
        $this->config = M('weapp_wx_config')->where($map)->find();
        if ($this->config['wait_access'] == 0) {
            exit($_GET["echostr"]);
        }

        $echostr = isset($_GET['echostr']) ? $_GET['echostr'] : ''; // 是否来自于微信的服务器配置验证
        if ($echostr) {
            $this->is_check_signature = true;
        }else{
            $this->is_check_signature = false;
            $this->logic_obj = $this->getctl($ctl, $this->config);
        }
    }

    public function getctl($ctl = '', $config = array())
    {
        $class = '\\app\\plugins\\logic\\Wechat'.$ctl.'Logic'; //
        return new $class($config);
    }
    
    /**
     * 服务器地址(用户消息和开发者需要的事件推送，会被转发到该URL中)
     */
    public function valid()
    {
        if ($this->is_check_signature) {
            $this->checkSignature();
        }else{
            $this->logic_obj->responseMsg();
        }
    }

    /**
    * 开发者对签名（即signature）的效验，来判断此条消息的真实性
    * @return [type] [description]
    */
    public function checkSignature()
    {
        //微信会发送4个参数到我们的服务器后台 签名 时间戳 随机字符串 随机数
        $signature = $_GET['signature']; // 签名
        $timestamp = $_GET['timestamp']; // 时间戳
        $echoStr = $_GET['echostr']; // 随机字符串
        $nonce = $_GET['nonce']; // 随机数
        if ($signature && $timestamp && $echoStr && $nonce) {
            // 微信公众号基本配置中的token
            $token = $this->config['w_token'];
            //将token、timestamp、nonce按字典序排序
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            // 将三个参数字符串拼接成一个字符串进行sha1加密 
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            // 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
            if($tmpStr == $signature){
                header('content-type:text');
                exit($echoStr);
            } else {
                exit('');
            }
        }
    }
}