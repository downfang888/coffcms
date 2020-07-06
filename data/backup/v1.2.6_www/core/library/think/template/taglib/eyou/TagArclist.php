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

namespace think\template\taglib\eyou;

use app\home\logic\FieldLogic;

/**
 * 文章列表
 */
class TagArclist extends Base
{
    public $tid = '';
    public $fieldLogic;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        $this->tid = I("param.tid/s", ''); // 应用于栏目列表
        /*应用于文档列表*/
        $aid = I('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = M('archives')->where('aid', $aid)->getField('typeid');
        }
        /*--end*/
        /*tid为目录名称的情况下*/
        $this->tid = $this->getTrueTypeid($this->tid);
        /*--end*/
    }

    /**
     * 获取多条记录
     * @author wengxianhu by 2018-4-20
     */
    public function getArclist($param = array(),  $limit = 15, $orderby = '', $addfields = '', $orderWay = '')
    {
        $result = false;

        $channeltype = !empty($param['channel']) ? $param['channel'] : '';
        $param['typeid'] = !empty($param['typeid']) ? $param['typeid'] : $this->tid;
        empty($orderWay) && $orderWay = 'desc';

        /*多语言*/
        if (!empty($param['typeid'])) {
            $param['typeid'] = model('LanguageAttr')->getBindValue($param['typeid'], 'arctype');
            if (empty($param['typeid'])) {
                echo '标签arclist报错：找不到与第一套【'.$this->main_lang.'】语言关联绑定的属性 typeid 值。';
                return false;
            }
        }
        /*--end*/

        $typeid = $param['typeid'];

        /*不指定模型ID、栏目ID，默认显示所有可以发布文档的模型ID下的文档*/
        if (empty($channeltype) && empty($typeid)) {
            $allow_release_channel = config('global.allow_release_channel');
            $channeltype = $param['channel'] = implode(',', $allow_release_channel);
        }
        /*--end*/

        if (!empty($channeltype)) { // 如果指定了频道ID，则频道下的所有文档都展示
            unset($param['typeid']);
        } else {
            // unset($param['channel']);
            if (!empty($typeid)) {
                $typeidArr = explode(',', $typeid);
                if (count($typeidArr) == 1) {
                    $channel_info = M('Arctype')->field('id,current_channel')->where(array('id'=>array('eq', $typeid)))->find();
                    if (empty($channel_info)) {
                        echo '标签arclist报错：指定属性 typeid 的栏目ID不存在。';
                        return false;
                    }
                    $channeltype = !empty($channel_info) ? $channel_info["current_channel"] : ''; // 当前栏目ID所属模型ID

                    /*获取当前栏目下的所有同模型的子孙栏目*/
                    $arctype_list = model("Arctype")->getHasChildren($channel_info['id']);
                    foreach ($arctype_list as $key => $val) {
                        if ($channeltype != $val['current_channel']) {
                            unset($arctype_list[$key]);
                        }
                    }
                    $typeids = get_arr_column($arctype_list, "id");
                    $typeid = implode(",", $typeids);
                    /*--end*/
                } elseif (count($typeidArr) > 1) {
/*                    $firstTypeid = $typeidArr[0];
                    $firstTypeid = M('Arctype')->where(array('id|dirname'=>array('eq', $firstTypeid)))->getField('id');
                    $channeltype = M('Arctype')->where(array('id'=>array('eq', $firstTypeid)))->getField('current_channel');*/
                }
                $param['channel'] = $channeltype;
            }
        }

/*        if (empty($typeid) && empty($channeltype)) {
            echo '标签arclist报错：至少指定属性 typeid | channelid 任何一个。';
            return $result;
        }*/

        // 查询条件
        $condition = array();
        foreach (array('keywords','typeid','notypeid','flag','noflag','channel') as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    array_push($condition, "a.title LIKE %{$param[$key]}%");
                } elseif ($key == 'channel') {
                    array_push($condition, "a.channel IN ({$channeltype})");
                } elseif ($key == 'typeid') {
                    array_push($condition, "a.typeid IN ({$typeid})");
                } elseif ($key == 'notypeid') {
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    array_push($condition, "a.typeid NOT IN (".$param[$key].")");
                } elseif ($key == 'flag') {
                    $flag_arr = explode(",", $param[$key]);
                    $where_or_flag = array();
                    foreach ($flag_arr as $k2 => $v2) {
                        if ($v2 == "c") {
                            array_push($where_or_flag, "a.is_recom = 1");
                        } elseif ($v2 == "h") {
                            array_push($where_or_flag, "a.is_head = 1");
                        } elseif ($v2 == "a") {
                            array_push($where_or_flag, "a.is_special = 1");
                        } elseif ($v2 == "j") {
                            array_push($where_or_flag, "a.is_jump = 1");
                        }
                    }
                    if (!empty($where_or_flag)) {
                        $where_flag_str = " (".implode(" OR ", $where_or_flag).") ";
                        array_push($condition, $where_flag_str);
                    }
                } elseif ($key == 'noflag') {
                    $flag_arr = explode(",", $param[$key]);
                    $where_or_flag = array();
                    foreach ($flag_arr as $nk2 => $nv2) {
                        if ($nv2 == "c") {
                            array_push($where_or_flag, "a.is_recom <> 1");
                        } elseif ($nv2 == "h") {
                            array_push($where_or_flag, "a.is_head <> 1");
                        } elseif ($nv2 == "a") {
                            array_push($where_or_flag, "a.is_special <> 1");
                        } elseif ($nv2 == "j") {
                            array_push($where_or_flag, "a.is_jump <> 1");
                        }
                    }
                    if (!empty($where_or_flag)) {
                        $where_flag_str = " (".implode(" OR ", $where_or_flag).") ";
                        array_push($condition, $where_flag_str);
                    }
                } else {
                    array_push($condition, "a.{$key} = '".$param[$key]."'");
                }
            }
        }
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        $where_str = "";
        if (0 < count($condition)) {
            $where_str = implode(" AND ", $condition);
        }

        // 给排序字段加上表别名
        switch ($orderby) {
            case 'hot':
            case 'click':
                $orderby = "a.click {$orderWay}";
                break;

            case 'now':
            case 'aid':
                $orderby = "a.aid {$orderWay}";
                break;

            case 'pubdate':
            case 'add_time':
                $orderby = "a.add_time {$orderWay}";
                break;
                
            case 'sortrank':
            case 'sort_order':
                $orderby = "a.sort_order {$orderWay}";
                break;
                
            case 'rand':
                $orderby = "rand()";
                break;
            
            default:
                {
                    if (empty($orderby)) {
                        $orderby = "a.sort_order asc, a.aid desc";
                    } elseif (trim($orderby) != 'rand()') {
                        $orderbyArr = explode(',', $orderby);
                        foreach ($orderbyArr as $key => $val) {
                            $val = trim($val);
                            if (preg_match('/^([a-z]+)\./i', $val) == 0) {
                                $val = 'a.'.$val;
                                $orderbyArr[$key] = $val;
                            }
                        }
                        $orderby = implode(',', $orderbyArr);
                    }
                }
                break;
        }

        // 获取查询的控制器名
        $channeltype_info = model('Channeltype')->getInfo($channeltype);
        $controller_name = $channeltype_info['ctl_name'];
        $channeltype_table = $channeltype_info['table'];

        switch ($channeltype) {
            case '-1':
            {
                break;
            }
            
            default:
            {
                $field = "b.*, a.*";
                $result = db('archives')
                    ->field($field)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                    ->where($where_str)
                    ->where('a.lang', $this->home_lang)
                    ->orderRaw($orderby)
                    ->limit($limit)
                    ->select();

                $aidArr = array();
                foreach ($result as $key => $val) {
                    array_push($aidArr, $val['aid']); // 收集文档ID

                    /*栏目链接*/
                    if ($val['is_part'] == 1) {
                        $val['typeurl'] = $val['typelink'];
                    } else {
                        $val['typeurl'] = typeurl(MODULE_NAME.'/'.$controller_name."/lists", $val);
                    }
                    /*--end*/
                    /*文档链接*/
                    if ($val['is_jump'] == 1) {
                        $val['arcurl'] = $val['jumplinks'];
                    } else {
                        $val['arcurl'] = arcurl(MODULE_NAME.'/'.$controller_name.'/view', $val);
                    }
                    /*--end*/
                    /*封面图*/
                    if (empty($val['litpic'])) {
                        $val['is_litpic'] = 0; // 无封面图
                    } else {
                        $val['is_litpic'] = 1; // 有封面图
                    }
                    $val['litpic'] = get_default_pic($val['litpic']); // 默认封面图
                    /*--end*/

                    $result[$key] = $val;
                }

                /*附加表*/
                if (!empty($addfields) && !empty($aidArr)) {
                    $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
                    $addfields = trim($addfields, ',');
                    $tableContent = $channeltype_table.'_content';
                    $rowExt = M($tableContent)->field("aid,$addfields")->where('aid','in',$aidArr)->getAllWithIndex('aid');
                    /*自定义字段的数据格式处理*/
                    $rowExt = $this->fieldLogic->getChannelFieldList($rowExt, $channeltype, true);
                    /*--end*/
                    foreach ($result as $key => $val) {
                        $valExt = !empty($rowExt[$val['aid']]) ? $rowExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        $result[$key] = $val;
                    }
                }
                /*--end*/

                break;
            }
        }

        return $result;
    }
}