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

namespace app\plugins\util;

/**
 * 微信公众号操作类
 */
class WechatBasisUtil
{
    private $config = [];    //微信公众号配置
    private $errorMsg = '';  //错误字符串信息
    private $debug = false;   //是否开启调试
    private $tagsMap = null; //粉丝标签映射

    public $baseObj;
    public $messagesObj;
    public $msg_serviceObj;
    public $materialObj;
    public $userObj;
    
    public function __construct($config)
    {
        vendor('wechat.wechat');

        $this->config = $config;

        /* 微信公众平台类 */
        $this->baseObj = $this->getClassObj('base');
        $this->messagesObj = $this->getClassObj('messages');
        $this->msg_serviceObj = $this->getClassObj('messages_service');
        $this->materialObj = $this->getClassObj('material');
        $this->userObj = $this->getClassObj('user');
    }

    public function getClassObj($className)
    {
        $class = '\\'.$className; //
        return new $class($this->config); //实例化对应的类
    }
    
    public function getError() 
    {
        return $this->errorMsg;
    }
    
    private function setError($error)
    {
        if (!is_string($error)) {
            $error = json_encode($error, JSON_UNESCAPED_UNICODE);
        }
        $this->errorMsg = $error;
    }
    
    public function isDedug()
    {
        return $this->debug;
    }
    
    public function logDebugFile($content)
    {
        if (!$this->debug) {
            return;
        }
        if (!is_string($content)) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }
        file_put_contents ( DATA_PATH."wechat.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export($content,true) . "\r\n", FILE_APPEND );
        // file_put_contents("./wechat.log", date('Y-m-d H:i:s').' -- '.$content."\n", FILE_APPEND);
    }

    /**
     * 根据关键词获取回复内容
     */
    public function getKeywordContent($keyword = '')
    {
        $map = array(
            'keyword'   => $keyword,
            'token'     => $this->config['token'],
        );
        $k_info = M('weapp_wx_keyword')->field('pid, type')->where($map)->find();

        //自动回复模式
        switch ($k_info['type']) {
            case 'TEXT': // 文本
                $t_info = M('weapp_wx_text')->field('text')->find($k_info['pid']);
                $content = $t_info['text']?:'默认文本';
                break;

            case 'PIC': // 图片
                $p_info = M('weapp_wx_pic')->field('media_id')->find($k_info['pid']);
                $content = array("MediaId"=>$p_info['media_id']);
                break;

            case 'IMG': // 单图文
                $i_info = M('weapp_wx_img')->field('title, litpic, intro, url')->find($k_info['pid']);
                $litpic = is_http_url($i_info['litpic']) ? $i_info['litpic'] : SITE_URL.$i_info['litpic'];
                $content = array();
                $content[] = array("Title"=>$i_info['title'],  "Description"=>$i_info['intro'], "PicUrl"=>$litpic, "Url" =>$i_info['url']);
                break;

            case 'NEWS': // 组合图文
                $n_info = M('weapp_wx_news')->field('img_id')->find($k_info['pid']);
                $arr = explode(',', $n_info['img_id']);
                $img_list = M('weapp_wx_img')->where(array('id'=>array('in',$arr)))->getAllWithIndex('id');
                $content = array();
                foreach ($arr as $key => $val) {
                    $litpic = is_http_url($img_list[$val]['litpic']) ? $img_list[$val]['litpic'] : SITE_URL.$img_list[$val]['litpic'];
                    $content[] = array("Title"=>$img_list[$val]['title'],  "Description"=>$img_list[$val]['intro'], "PicUrl"=>$litpic, "Url" =>$img_list[$val]['url']);
                }
                break;
            
            default:
                $content = false;//"没有这个关键词：".$keyword;
                break;
        }

        $this->logDebugFile($content);

        return $content;
    }

    /**
     * 获取带参数临时二维码的参数，并转为数组
     */
    public function convert_ticket_data($object)
    {
        $form_data_str = $object->EventKey;

        if ($object->Event == 'subscribe') { // 用户未关注时，进行关注后的事件推送
            $form_data_str = substr($form_data_str, 8);
        } else if ($object->Event == 'SCAN') { // 用户已关注时的事件推送
            $form_data_str = substr($form_data_str, 0);
        }
        $form_data = json_decode(base64_decode($form_data_str), true);

        return $form_data;
    }
}