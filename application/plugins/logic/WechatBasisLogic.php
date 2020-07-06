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

namespace app\plugins\logic;

use think\Model;
use think\Db;
use app\plugins\util\WechatBasisUtil;

/**
 * 逻辑定义
 * Class WechatBasisLogic
 */
class WechatBasisLogic extends Model
{
    public $config;

    public $baseObj;
    public $messagesObj;
    public $msg_serviceObj;
    public $materialObj;
    public $userObj;

    public $basisUtil;

    public function __construct($config){
        vendor('wechat.wechat');
        $this->config = $config;

        /* 微信公众平台类 */
        $this->baseObj = $this->getClassObj('base');
        $this->messagesObj = $this->getClassObj('messages');
        $this->msg_serviceObj = $this->getClassObj('messages_service');
        $this->materialObj = $this->getClassObj('material');
        $this->userObj = $this->getClassObj('user');

        $this->basisUtil = new WechatBasisUtil($this->config);
    }

    public function getClassObj($className)
    {
        $class = '\\'.$className; //
        return new $class($this->config); //实例化对应的类
    }

    /**
     * 获取access_token
     */
    public function get_access_token()
    {
        return $this->baseObj->access_token;
    }

    /**
     * 响应消息
     */
    public function responseMsg()
    {
        /* 消息类 */
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)){
            // $this->messagesObj->logger("R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            // 关于重试的消息排重
            if ($RX_TYPE == "event") { // 事件类型消息推荐使用FromUserName + CreateTime 排重
                if ($postObj->Event == "subscribe" || $postObj->Event == "unsubscribe") {
                    $eventmsg_val = md5($postObj->FromUserName.$postObj->CreateTime);
                    $eventmsg_list = cache('basis_eventmsg_list');
                    $eventmsg_list = ($eventmsg_list == false) ? array() : $eventmsg_list;
                    if ($eventmsg_list && in_array($eventmsg_val, $eventmsg_list)) {
                        exit('success');
                    } else {
                        array_push($eventmsg_list, $eventmsg_val);
                        cache('basis_eventmsg_list', $eventmsg_list, 1800);
                    }
                }
            } else { // 有msgid的消息推荐使用msgid排重
                $msg_id = (string)$postObj->MsgId;
                $msgid_list = cache('basis_msgid_list');
                $msgid_list = ($msgid_list == false) ? array() : $msgid_list;
                if ($msgid_list && in_array($msg_id, $msgid_list)) {
                    exit('success');
                } else {
                    array_push($msgid_list, $msg_id);
                    cache('basis_msgid_list', $msgid_list, 1800);
                }
            }

            // $this->messagesObj->logger("R \r\n".var_export($postObj,true));

            //消息类型分离
            switch ($RX_TYPE)
            {
                // 事件
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;

                // 文本
                case "text":
                    $result = $this->receiveText($postObj);
                    break;

                // 图片
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;

                // 地理位置
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;

                // 语音
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;

                // 视频
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;

                // 链接
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;

                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            // $this->messagesObj->logger("T \r\n".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    /**
     * 接收事件消息
     */
    public function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            // 关注时的事件推送
            case "subscribe":
                $row = M('weapp_wx_subscribe')->where('token', $this->config['token'])->find();
                if (!empty($row)) {
                    if ('TEXT' == $row['type']) {
                        $this->msg_serviceObj->sendServiceText($object, $row['text']);
                        $result = 'success';
                    } else if ('PIC' == $row['type']) {
                        $content = array("MediaId"=>$row['media_id']);
                        $result = $this->messagesObj->transmitImage($object, $content);
                    }
                }
                // $result = $this->handleSubscribeEvent($object);
                return $result;
                break;

            // 取消关注事件
            case "unsubscribe":
                $content = "取消关注";
                break;

            // 点击菜单拉取消息时的事件推送
            case "CLICK":
                $object->Content = $object->EventKey;
                exit($this->receiveText($object));
                break;

            // 点击菜单跳转链接时的事件推送
            case "VIEW":
                // $this->msg_serviceObj->sendServiceText($object, "点击菜单跳转链接时的事件推送 ".$object->EventKey);
                // $content = "跳转链接 ".$object->EventKey;
                break;

            // 扫描带参数二维码场景，用户已关注时的事件推送
            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                $result = $this->handleSubscribeEvent($object);
                return $result;
                break;

            // 上报地理位置事件(此功能要开启获取用户地理位置的接口，可以设置用户进行对话时上报一次，或者用户进行对话后每隔5s上报一次)
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;

            // 扫码推事件且弹出“消息接收中”提示框的事件推送
            case "scancode_waitmsg":
                if ($object->ScanCodeInfo->ScanType == "qrcode"){
                    $content = "扫码带提示：类型 二维码 结果：".$object->ScanCodeInfo->ScanResult;
                }else if ($object->ScanCodeInfo->ScanType == "barcode"){
                    $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
                    $codeValue = $codeinfo[1];
                    $content = "扫码带提示：类型 条形码 结果：".$codeValue;
                }else{
                    $content = "扫码带提示：类型 ".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
                }
                break;

            // 扫码推事件的事件推送
            case "scancode_push":
                $content = "扫码推事件";
                break;

            // 弹出系统拍照发图的事件推送
            case "pic_sysphoto":
                $content = "系统拍照";
                break;

            // 弹出微信相册发图器的事件推送
            case "pic_weixin":
                $content = "相册发图：数量 ".$object->SendPicsInfo->Count;
                break;

            // 弹出拍照或者相册发图的事件推送
            case "pic_photo_or_album":
                $content = "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
                break;

            // 弹出地理位置选择器的事件推送
            case "location_select":
                $content = "发送位置：标签 ".$object->SendLocationInfo->Label;
                break;

            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }

        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->messagesObj->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->messagesObj->transmitMusic($object, $content);
            }
        }else{
            $result = $this->messagesObj->transmitText($object, $content);
        }

        return $result;
    }

    /**
     * 接收文本消息
     */
    public function receiveText($object)
    {
        $keyword = trim($object->Content);
        $content = $this->basisUtil->getKeywordContent($keyword);

        //将消息转发到客服系统
/*        if (preg_match('/(在吗)|(客服)/i', $keyword)) {
            $this->msg_serviceObj->sendServiceText($object, '这是客服自动回复的消息_1');
            $result = $this->msg_serviceObj->transmitService($object);
            return $result;
        } else if (strstr($keyword, "表情")){
            $content = "中国：".$this->messagesObj->bytes_to_emoji(0x1F1E8).$this->messagesObj->bytes_to_emoji(0x1F1F3)."\n仙人掌：".$this->messagesObj->bytes_to_emoji(0x1F335);
        }else if (strstr($keyword, "测试图片")){
            // 上传临时素材接口
            $pic = ROOT_PATH.'public/static/common/images/bag-imgB.jpg';
            $params = $this->materialObj->mediaUpload($pic);
            $content = array("MediaId"=>$params['media_id']);
        } else if (strstr($keyword, "关注")) {
            $this->msg_serviceObj->sendServiceText($object, '这是客服自动回复的消息_1');
            $this->msg_serviceObj->sendServiceText($object, '这是客服自动回复的消息_2');
            $result = $this->handleSubscribeEvent($object);
            return $result;
        }else if (strstr($keyword, "音乐")){
            $content = array();
            $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3"); 
        }*/

/*        if ($content == false) {
            $content = "亲，有什么能帮助您的吗？\n欢迎咨询官网客服，感谢支持！";
        }*/

        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->messagesObj->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->messagesObj->transmitMusic($object, $content);
            }else if (isset($content['MediaId'])){
                $result = $this->messagesObj->transmitImage($object, $content);
            }
        }else{
            $result = $this->messagesObj->transmitText($object, $content);
        }
        
        return $result;
    }

    /**
     * 接收图片消息
     */
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->messagesObj->transmitImage($object, $content);
        return $result;
    }

    /**
     * 接收位置消息
     */
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，经度为：".$object->Location_Y."；纬度为：".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->messagesObj->transmitText($object, $content);
        return $result;
    }

    /**
     * 接收语音消息
     */
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->messagesObj->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->messagesObj->transmitVoice($object, $content);
        }
        return $result;
    }

    /**
     * 接收视频消息
     */
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->messagesObj->transmitVideo($object, $content);
        return $result;
    }

    /**
     * 接收链接消息
     */
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->messagesObj->transmitText($object, $content);
        return $result;
    }

    /**
     * 处理关注事件
     * @param object $object
     * @return string
     */
    public function handleSubscribeEvent($object)
    {
        $begin = time();
        /* 获取表单数据(来自带参数的二维码) */
        $ticket_data = $this->basisUtil->convert_ticket_data($object);
        /* 获得openId值 */
        $openid = $object->FromUserName;
        /* 获取用户基本信息 */
        $user_info = $this->userObj->get_user_info($openid);
        $headimgurl = preg_replace('/\/\d$/i', '/132', $user_info['headimgurl']);
        /* 给背景图片添加图片水印、文字水印，生成新的海报 */
        $image_sy = array(
            array(
                'src_path'=>$headimgurl,
                'src_w'=>0,
                'src_h'=>0,
                'locate'=>array(415, 137),
                'alpha'=>100,
                'info_bg'=>array(),
            ),
        );

        $text_sy = array();
        $company_arr = array(
            'text'=>isset($ticket_data['company'])?$ticket_data['company']:'', // 文案
            'fontfile'=>'hgzb.ttf', // 字体文件 
            'size'=>28, // 字体大小
            'color'=>'#fccfb5', // 字体颜色
            'locate'=>10, // 文字写入位置
            'offset'=>array(0, 0), // 文字相对当前位置的偏移量
            'angle'=>0, // 文字倾斜角度
            'info_bg'=>array('width'=>415, 'height'=>137),
        );
        array_push($text_sy, $company_arr);

        $name_arr = array(
            'text'=>isset($form_data['name'])?$form_data['name']:$user_info['nickname'], // 文案
            'fontfile'=>'hgzb.ttf', // 字体文件 
            'size'=>28, // 字体大小
            'color'=>'#fccfb5', // 字体颜色
            'locate'=>10, // 文字写入位置
            'offset'=>array(0, 50), // 文字相对当前位置的偏移量
            'angle'=>0, // 文字倾斜角度
            'info_bg'=>array('width'=>415, 'height'=>137),
        );
        array_push($text_sy, $name_arr);

        $mobile_arr = array(
            'text'=>isset($ticket_data['mobile'])?$ticket_data['mobile']:'139xxxxxx72', // 文案
            'fontfile'=>'hgzb.ttf', // 字体文件 
            'size'=>28, // 字体大小
            'color'=>'#fccfb5', // 字体颜色
            'locate'=>10, // 文字写入位置
            'offset'=>array(0, 100), // 文字相对当前位置的偏移量
            'angle'=>0, // 文字倾斜角度
            'info_bg'=>array('width'=>415, 'height'=>137),
        );
        array_push($text_sy, $mobile_arr);

        $path_bg = ROOT_PATH.'public/static/common/images/bag-imgB.jpg';
        $watermarkObj = $this->getClassObj('watermark');
        $pic = $watermarkObj->addWatermark($path_bg, $image_sy, $text_sy);
        /* 上传临时素材接口 */
        $params = $this->materialObj->mediaUpload($pic);
        if (isset($params['errcode'])) {
            $content = $params['errmsg'];
            $result = $this->messagesObj->transmitText($object, $content);
        } else {
            // 临时素材的media_id
            $media_id = $params['media_id'];
            $this->msg_serviceObj->sendServiceImage($object, $media_id);
            
            // $end = time();
            // $content = '耗时：'.($end - $begin).'秒……之后回复图片';
            // $msg_serviceObj->sendServiceText($object, $content);
        }

        return 'success';
    }
}