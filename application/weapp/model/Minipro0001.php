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

namespace app\weapp\model;

use think\Model;
use think\Db;

/**
 * 模型
 */
class Minipro0001 extends Model
{
    // 插件标识
    public $nid;

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->nid = 'Minipro0001';
    }

    /**
     * 全局常量
     * @param string $type 类型
     * @author 小虎哥 by 2018-8-18
     */
    public function getGlobalsConf()
    {
        $cacheKey = 'model-'.$this->nid.'-getGlobalsConf';
        $result = cache($cacheKey);
        if (empty($result)) {
            $webData = tpCache('web');
            $barlist = $this->getBarlist();
            $webconfig = array(
                'web_name' => empty($barlist['nav_title']) ? $webData['web_name'] : $barlist['nav_title'],
                'web_copyright' => empty($barlist['copyright']) ? $webData['web_copyright'] : $barlist['copyright'],
            );
            $result = array(
                'webconfig' => $webconfig,
                'blist' => $barlist,
            );

            cache($cacheKey, $result, null, 'minipro');
        }

        return $result;
    }

    /**
     * 获取配置值
     * @param string $type 类型
     * @author 小虎哥 by 2018-8-18
     */
    public function getValue($type)
    {
        // $cacheKey = 'model-'.$this->nid.'-getValue-'.$type;
        // $value = cache($cacheKey);
        // if (empty($value)) {
            $map = array(
                'type'  => $type,
            );
            $value = M('weapp_minipro0001')->where($map)->cache(true,EYOUCMS_CACHE_TIME,"minipro")->value('value');
            $value = (array)json_decode($value, true);

            // cache($cacheKey, $value, null, 'minipro');
        // }

        return $value;
    }

    /**
     * 首页配置
     */
    public function getHomeConf() {
        // 定义公共常量
        $result = $this->getValue('home');
        foreach ($result as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k2 => $v2) {
                    /*转换图片为远程http*/
                    if (1 == preg_match('/(_img|_selimg)$/', $k2)) {
                        if (!is_http_url($v2)) {
                            $result[$key][$k2] = request()->domain().$v2;
                        }
                    }
                    /*--end*/
                }
            }
        }

        return $result;
    }

    /**
     * 获取幻灯片
     * @param int $num 数量
     * @param string $aid 文档ID，多个以逗号隔开
     */
    public function getSwipersList($aid = '')
    {
        // $cacheKey = 'model-'.$this->nid."-getSwipersList-{$aid}";
        // $result = cache($cacheKey);
        // if (empty($result)) {
            if (empty($aid)) {
                $map = array(
                    'is_head'   => 1,
                    'status'    => 1,
                    'lang'  => get_current_lang(),
                );
                $num = 8;
            } else {
                $map = array(
                    'aid'   => array('in', $aid),
                );
                $num = '';
            }
            $result = M('archives')->field('aid,litpic')
                ->where($map)
                ->order('sort_order asc, aid desc')
                ->limit($num)
                ->cache(true,EYOUCMS_CACHE_TIME,"minipro")
                ->select();
            foreach ($result as $key => $val) {
                $val['litpic'] = get_default_pic($val['litpic'], true);
                $result[$key] = $val;
            }

            // cache($cacheKey, $result, null, 'minipro');
        // }

        return $result;
    }

    /**
     * 获取全部栏目
     * @param string $channel 栏目ID，多个以逗号隔开
     * @param int $num 数量
     */
    public function getArctype($typeid = '', $channel = '')
    {
        $typeid = intval($typeid);
        $channel = intval($channel);
        $cacheKey = 'model-'.$this->nid."-getArctype-{$typeid}-{$channel}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $typename = ''; // 用于分享标题
            if (0 < $typeid) {
                $result = model('Arctype')->getChannelList($typeid, 'son');
                $arctypeInfo = model('Arctype')->getInfo($typeid);
                $parent_id = $arctypeInfo['parent_id'];
                $typename = $arctypeInfo['typename']; // 用于分享标题
                if (empty($result)) {
                    if (0 < intval($parent_id)) {
                        $selfRow[0] = array();
                        $selfRow[0] = model('Arctype')->getInfo($parent_id);
                        $selfRow[0]['typename'] = '全部';
                        $selfRow[0]['selected'] = true;
                        $result = model('Arctype')->getChannelList($typeid, 'self');
                        $typename = M('arctype')->where('id','eq',$typeid)->value('typename'); // 用于分享标题
                    } else {
                        $typename = $arctypeInfo['typename'];
                        $selfRow[0] = array();
                        $selfRow[0] = $arctypeInfo;
                        $selfRow[0]['typename'] = $typename;
                        $selfRow[0]['selected'] = true;
                    }
                    $result = array_merge($selfRow, $result);
                } else {
                    $selfRow[0] = array();
                    $selfRow[0] = model('Arctype')->getInfo($typeid);
                    $selfRow[0]['typename'] = '全部';
                    $selfRow[0]['selected'] = true;
                    $result = array_merge($selfRow, $result);
                }
            } else {  // 全部栏目
                $map = array();
                if (0 < $channel) {
                    $map['current_channel'] = array('eq', intval($channel));
                }
                /*获取所有栏目*/
                $arctypeLogic = new \app\common\logic\ArctypeLogic; 
                $result = $arctypeLogic->arctype_list(0, 0, false, 0, $map, false);
                /*--end*/
            }


            /*栏目层级归类成阶梯式*/
    /*        $arr = group_same_key($result, 'parent_id');
            $arctype_max_level = intval(config('global.arctype_max_level'));
            for ($i=0; $i < $arctype_max_level; $i++) { 
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) continue;
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            $result = $arr;*/
            /*--end*/

            $result = array(
                'conf' => array(
                    'shareTitle' => ($typename ? $typename.'_' : '').tpCache('web.web_name'),
                ),
                'row' => $result,
            );

            cache($cacheKey, $result, null, 'minipro');
        }

        return $result;
    }

    /**
     * 文档列表
     * @param string $param 查询条件的数组
     * @param int $page 页码
     * @param int $pagesize 每页记录数
     */
    public function getArchivesList($param = array(), $page = 1, $pagesize = null, $field = 'aid,title,litpic,seo_description,add_time')
    {
        $param['arcrank'] = isset($param['arcrank']) ? $param['arcrank'] : -1;
        $pagesize = empty($pagesize) ? config('paginate.list_rows') : $pagesize;
        $cacheKey = "model-".$this->nid."-getArchivesList-".json_encode($param)."-{$page}-{$pagesize}-{$field}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $condition = array();

            // 应用搜索条件
            foreach (['channel','typeid','flag','arcrank'] as $key) {
                if (isset($param[$key]) && ('' !== $param[$key] || null !== $param[$key])) {
                    if ('typeid' == $key) {
                        if (!empty($param[$key])) {
                            if (is_string($param[$key]) && stristr($param[$key], ',')) {
                                // 指定多个栏目ID
                                $typeid = func_preg_replace(array('，'), ',', $param[$key]);
                                $typeid = explode(',', $typeid);
                            } else if (is_string($param[$key]) && !stristr($param[$key], ',')) {
                                /*当前栏目ID，以及所有子栏目ID*/
                                $channel_info = M('Arctype')->field('id,current_channel')->where(array('id'=>array('eq', $param[$key])))->find();
                                $childrenRow = model('Arctype')->getHasChildren($param[$key]);
                                foreach ($childrenRow as $k2 => $v2) {
                                    if ($channel_info['current_channel'] != $v2['current_channel']) {
                                        unset($childrenRow[$k2]); // 排除不是同一模型的栏目
                                    }
                                }
                                $typeid = get_arr_column($childrenRow, 'id');
                                /*--end*/
                            }
                            $condition[$key] = array('IN', $typeid);
                        }
                    } else if ('channel' == $key) {
                        if (!empty($param[$key])) {
                            if (is_string($param[$key])) {
                                $channel = func_preg_replace(array('，'), ',', $param[$key]);
                                $channel = explode(',', $channel);
                            }
                            $condition[$key] = array('IN', $channel);
                        }
                    } else if ('flag' == $key) {
                        $tmp_key_arr = array();
                        $flag_arr = explode(",", $param[$key]);
                        foreach ($flag_arr as $k2 => $v2) {
                            if ($v2 == "c") {
                                array_push($tmp_key_arr, 'is_recom');
                            } elseif ($v2 == "h") {
                                array_push($tmp_key_arr, 'is_head');
                            } elseif ($v2 == "a") {
                                array_push($tmp_key_arr, 'is_special');
                            } elseif ($v2 == "j") {
                                array_push($tmp_key_arr, 'is_jump');
                            }
                        }
                        $tmp_key_str = implode('|', $tmp_key_arr);
                        $condition[$tmp_key_str] = array('eq', 1);
                    } else if ('arcrank' == $key) {
                        $condition[$key] = array('gt', $param[$key]);
                    } else {
                        $condition[$key] = array('eq', $param[$key]);
                    }
                }
            }

            $paginate = array(
                'page'  => $page,
            );
            $pages = M('archives')->field($field)
                ->where($condition)
                ->where('channel != 6')
                ->order('sort_order asc, aid desc')
                ->cache(true,EYOUCMS_CACHE_TIME,"minipro")
                ->paginate($pagesize, false, $paginate);

            $list = array();
            foreach ($pages->items() as $key => $val) {
                /*封面图*/
                if (isset($val['litpic'])) {
                    if (empty($val['litpic'])) {
                        $val['is_litpic'] = 0; // 无封面图
                    } else {
                        $val['is_litpic'] = 1; // 有封面图
                    }
                    $val['litpic'] = get_default_pic($val['litpic'], true); // 默认封面图
                }
                /*--end*/
                if (isset($val['add_time'])) {
                    $val['add_time'] = date('Y-m-d', $val['add_time']);
                }
                array_push($list, $val);
            }

            $result = array(
                'conf' => array(
                    'hasMore' => ($page < $pages->lastPage()) ? 1 : 0,
                ),
                'list' => $list,
            );

            cache($cacheKey, $result, null, 'minipro');
        }

        return $result;
    }

    /**
     * 文档详情
     * @param int $aid 文档ID
     */
    public function getArchivesView($aid = '')
    {
        $aid = intval($aid);
        $cacheKey = "model-".$this->nid."-getArchivesView-{$aid}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $status = 0;
            $msg = 'Request Error!';
            $row = array();
            if (0 < $aid) {
                $archivesModel = new \app\home\model\Archives;
                $row = $archivesModel->getViewInfo($aid, true);

                $status = 1;
                if (0 > $row['status']) {
                    $msg = '文档尚未审核，非作者本人无权查看';
                }
                /*--end*/
                $row['add_time'] = date('Y-m-d', $row['add_time']); // 格式化发布时间
                $row['update_time'] = date('Y-m-d', $row['update_time']); // 格式化更新时间
                $row['content'] = $this->get_httpimgurl($row['content']); // 转换内容图片为http路径

                /* 上一篇 */
                $preRow = M('archives')->field('a.aid, a.typeid, a.title')
                    ->alias('a')
                    ->where("a.typeid = ".$row['typeid']." AND a.aid < {$aid} AND a.status = 1")
                    ->order('a.aid desc')
                    ->find();

                /* 下一篇 */
                $nextRow = M('archives')->field('a.aid, a.typeid, a.title')
                    ->alias('a')
                    ->where("a.typeid = ".$row['typeid']." AND a.aid > {$aid} AND a.status = 1")
                    ->order('a.aid asc')
                    ->find();
            }

            $result = array(
                'conf' => array(
                    'status' => $status,
                    'msg'   => $msg,
                    'attrTitle' => '参数列表',
                    'contentTitle' => '详情介绍',
                    'shareTitle' => $row['title'].'_'.tpCache('web.web_name'),
                ),
                'row' => $row,
                'preRow' => $preRow,
                'nextRow' => $nextRow,
            );

            cache($cacheKey, $result, null, 'minipro');
        }

        return $result;
    }

    /**
     * 单页栏目详情
     * @param int $typeid 栏目ID
     */
    public function getSingleView($typeid = '')
    {
        $typeid = intval($typeid);
        $cacheKey = "model-".$this->nid."-getSingleView-{$typeid}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $status = 0;
            $msg = 'Request Error!';
            $row = array();
            if (0 < $typeid) {
                $archivesModel = new \app\home\model\Archives;
                $row = $archivesModel->getSingleInfo($typeid, true);

                $status = 1;
                if (0 == $row['status']) {
                    $msg = '该文档已屏蔽，无权查看';
                }
                /*--end*/
                $row['add_time'] = date('Y-m-d', $row['add_time']); // 格式化发布时间
                $row['update_time'] = date('Y-m-d', $row['update_time']); // 格式化更新时间
                $row['content'] = $this->get_httpimgurl($row['content']); // 转换内容图片为http路径
            }

            $result = array(
                'conf' => array(
                    'status' => $status,
                    'msg'   => $msg,
                    'shareTitle' => $row['title'].'_'.tpCache('web.web_name'),
                ),
                'row' => $row,
            );

            cache($cacheKey, $result, null, 'minipro');
        }

        return $result;
    }

    /**
     * 关于我们
     */
    public function getAbout()
    {
        $cacheKey = "model-".$this->nid."-getAbout";
        $result = cache($cacheKey);
        if (empty($result)) {
            $shareTitle = '';
            $row = $this->getValue('about');
            if ($row) {
                foreach ($row as $key => $val) {
                    /*转换图片为远程http*/
                    if (1 == preg_match('/^(logo|banner)$/', $key)) {
                        if (!is_http_url($val)) {
                            $row[$key] = request()->domain().$val;
                        }
                    }
                    /*--end*/
                }
                $row['content'] = $this->get_httpimgurl($row['content']); // 转换内容图片为http路径
                $shareTitle = $row['webname'];
            }

            $result = array(
                'conf' => array(
                    'shareTitle' => $shareTitle,
                ),
                'row' => $row,
            );

            cache($cacheKey, $result, null, 'minipro');
        }

        return $result;
    }

    /**
     * 留言栏目表单
     * @param int $typeid 栏目ID
     */
    public function getGuestbookForm($typeid)
    {
        $typeid = intval($typeid);
        // $cacheKey = "model-".$this->nid."-getGuestbookForm-{$typeid}";
        // $result = cache($cacheKey);
        // if (empty($result)) {
            $list = array();
            $typename = '';
            if (0 < $typeid) {
                $typename = M('arctype')->where('id','eq',$typeid)->value('typename');
                $list = M('GuestbookAttribute')->field('attr_id,attr_name,attr_input_type,attr_values')
                    ->where("typeid = $typeid")
                    ->order('sort_order asc')
                    ->cache(true,EYOUCMS_CACHE_TIME,"minipro")
                    ->select();
                foreach ($list as $key => $val) {
                    if (in_array($val['attr_input_type'], array(1,3))) {
                        $val['attr_values'] = explode(PHP_EOL, $val['attr_values']);
                        $list[$key] = $val;
                    }
                }
            }

            $result = array(
                'conf' => array(
                    'shareTitle' => $typename.'_'.tpCache('web.web_name'),
                ),
                'row' => $list,
            );

            // cache($cacheKey, $result, null, 'minipro');
        // }

        return $result;
    }

    /**
     * 留言表单提交
     * @param array $post post数据
     */
    public function getGuestbookSubmit($post = array())
    {
        $typeid = !empty($post['typeid']) ? intval($post['typeid']) : 0;
        $status = 0;
        $msg = '表单typeid值丢失！';
        if (0 < $typeid) {
            $ip = clientIP();
            $map = array(
                'ip'    => $ip,
                'typeid'    => $typeid,
                'add_time'  => array('gt', getTime() - 60),
            );
            $count = M('guestbook')->where($map)->count('aid');
            if (!empty($count)) {
                return array(
                    'status' => 0,
                    'msg'   => '同一个IP在60秒之内不能重复提交！',
                );
            }

            $channeltype_list = config('global.channeltype_list');
            $newData = array(
                'typeid'    => $typeid,
                'channel'   => $channeltype_list['guestbook'],
                'ip'    => $ip,
                'lang'  => get_current_lang(),
                'add_time'  => getTime(),
                'update_time' => getTime(),
            );
            $aid = M('guestbook')->insertGetId($newData);
            if ($aid > 0) {
                $this->saveGuestbookAttr($post, $aid, $typeid);
            }

            $status = 1;
            $msg = '操作成功';
        }

        $result = array(
            'gourl' => "",
            'status' => $status,
            'msg'   => $msg,
        );

        return $result;
    }

    /**
     *  给指定留言添加表单值到 guestbook_attr
     * @param int $aid  留言id
     * @param int $typeid  留言栏目id
     */
    private function saveGuestbookAttr($post, $aid, $typeid)
    {  
        // post 提交的属性  以 attr_id _ 和值的 组合为键名    
        foreach($post as $k => $v)
        {
            $attr_id = str_replace('attr_','',$k);
            if(!strstr($k, 'attr_'))
                continue;                                 

            //$v = str_replace('_', '', $v); // 替换特殊字符
            //$v = str_replace('@', '', $v); // 替换特殊字符
            if (is_array($v)) {
                $v = implode(',', $v);
            } else {
                $v = trim($v);
            }
            $adddata = array(
                'aid'   => $aid,
                'attr_id'   => $attr_id,
                'attr_value'   => $v,
                'lang'  => get_current_lang(),
                'add_time'   => getTime(),
                'update_time'   => getTime(),
            );
            M('GuestbookAttr')->add($adddata);                       
        }
    }

    /**
     * 图片地址替换成http路径
     * @param string $content 内容
     */
    private function get_httpimgurl($content = '')
    {
        $pregRule = "/<img(.*?)src(\s*)=(\s*)[\'|\"]\/(.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp|\.ico]))[\'|\"](.*?)[\/]?(\s*)>/i";
        $content = preg_replace($pregRule, '<img ${1} src="'.request()->domain().'/${4}" ${5} />', $content);

        return $content;
    }

    /**
     * 底部导航菜单
     */
    private function getBarlist() {
        // 定义公共常量
        $barlist = $this->getValue('global');
        foreach ($barlist as $key => $val) {
            /*转换图片为远程http*/
            if (1 == preg_match('/(_img|_selimg)$/', $key)) {
                if (!is_http_url($val)) {
                    $barlist[$key] = request()->domain().$val;
                }
            }
            /*--end*/
        }

        return $barlist;
    }

    /**
     * 图片拼接成http路径
     * @param string        $filename 路由地址
     * @param bool|string   $domain 域名
     */
    public function getImgRealpath($filename, $domain = true)
    {
        $web_cmspath = tpCache('web.web_cmspath');
        $filename = $web_cmspath.'/'.WEAPP_DIR_NAME.'/'.$this->nid.'/template/skin/images/'.$filename;
        if ($domain) {
            $filename = request()->domain().$filename;
        }
        return $filename;
    }
}