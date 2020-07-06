<?php
/**
 * eyoucms
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥
 * Date: 2018-4-3
 */

namespace app\admin\logic;

use think\Model;
use think\Db;
/**
 * 模块逻辑定义
 * @package admin\Logic
 */
class AuthModularLogic extends Model
{

    /**
     * 获得指定分类下的子分类的数组
     *
     * @access  public
     * @param   int     $id     分类的ID
     * @param   int     $selected   当前选中分类的ID
     * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $level      限定返回的级数。为0时返回所有级数
     * @return  mix
     */
    public function auth_modular_list($id = 0, $selected = 0, $re_type = true, $level = 0)
    {
        static $res = NULL;
        
        if ($res === NULL)
        {
            $data = false;//extra_cache('admin_auth_modular_list_logic');
            if ($data === false)
            {
                $where = array(
                    'c.status' => 1,
                );
                $fields = "c.id, c.parent_id, c.name, c.controller, c.action, c.grade, c.url, c.sort_order, c.is_menu, count(s.id) as has_children";
                $res = DB::name('auth_modular')
                    ->field($fields)
                    ->alias('c')
                    ->join('__AUTH_MODULAR__ s','s.parent_id = c.id','LEFT')
                    ->where($where)
                    ->group('c.id')
                    ->order('c.parent_id asc, c.sort_order asc, c.id')
                    ->cache(true,EYOUCMS_CACHE_TIME,"auth_modular")
                    ->select();
                // extra_cache('admin_auth_modular_list_logic', $res);
            }
            else
            {
                $res = $data;
            }
        }
    
        if (empty($res) == true)
        {
            return $re_type ? '' : array();
        }
    
        $options = $this->auth_modular_options($id, $res); // 获得指定分类下的子分类的数组
    
        /* 截取到指定的缩减级别 */
        if ($level > 0)
        {
            if ($id == 0)
            {
                $end_level = $level;
            }
            else
            {
                $first_item = reset($options); // 获取第一个元素
                $end_level  = $first_item['level'] + $level;
            }
    
            /* 保留level小于end_level的部分 */
            foreach ($options AS $key => $val)
            {
                if ($val['level'] >= $end_level)
                {
                    unset($options[$key]);
                }
            }
        }
    
        $pre_key = 0;
        foreach ($options AS $key => $value)
        {
            $options[$key]['has_children'] = 0;
            if ($pre_key > 0)
            {
                if ($options[$pre_key]['id'] == $options[$key]['parent_id'])
                {
                    $options[$pre_key]['has_children'] = 1;
                }
            }
            $pre_key = $key;
        }
    
        if ($re_type == true)
        {
            $select = '';
            foreach ($options AS $var)
            {
                $select .= '<option value="' . $var['id'] . '" ';
                $select .= ($selected == $var['id']) ? "selected='true'" : '';
                $select .= '>';
                if ($var['level'] > 0)
                {
                    $select .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select .= htmlspecialchars(addslashes($var['name'])) . '</option>';
            }
    
            return $select;
        }
        else
        {
            foreach ($options AS $key => $value)
            {
                ///$options[$key]['url'] = build_uri('article_cat', array('acid' => $value['id']), $value['cat_name']);
            }
            return $options;
        }
    }
    
    /**
     * 过滤和排序所有文章分类，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int     $id     上级分类ID
     * @param   array   $arr        含有所有分类的数组
     * @param   int     $level      级别
     * @return  void
     */
    public function auth_modular_options($spec_id, $arr)
    {
        static $cat_options = array();
    
        if (isset($cat_options[$spec_id]))
        {
            return $cat_options[$spec_id];
        }
    
        if (!isset($cat_options[0]))
        {
            $level = $last_id = 0;
            $options = $id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $id = $value['id'];
                    if ($level == 0 && $last_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }
    
                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id]['id']    = $id;
                        $options[$id]['name']  = $value['name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_id  = $id;
                        $id_array = array($id);
                        $level_array[$last_id] = ++$level;
                        continue;
                    }
    
                    if ($value['parent_id'] == $last_id)
                    {
                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id]['id']    = $id;
                        $options[$id]['name']  = $value['name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] > 0)
                        {
                            if (end($id_array) != $last_id)
                            {
                                $id_array[] = $last_id;
                            }
                            $last_id    = $id;
                            $id_array[] = $id;
                            $level_array[$last_id] = ++$level;
                        }
                    }
                    elseif ($value['parent_id'] > $last_id)
                    {
                        break;
                    }
                }
    
                $count = count($id_array);
                if ($count > 1)
                {
                    $last_id = array_pop($id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_id != end($id_array))
                    {
                        $last_id = end($id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_id = 0;
                        $id_array = array();
                        continue;
                    }
                }
    
                if ($last_id && isset($level_array[$last_id]))
                {
                    $level = $level_array[$last_id];
                }
                else
                {
                    $level = 0;
                    break;
                }
            }
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }
    
        if (!$spec_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_id]))
            {
                return array();
            }
    
            $spec_id_level = $options[$spec_id]['level'];
    
            foreach ($options AS $key => $value)
            {
                if ($key != $spec_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }
    
            $spec_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_id_level == $value['level'] && $value['id'] != $spec_id) ||
                    ($spec_id_level > $value['level']))
                {
                    break;
                }
                else
                {
                    $spec_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_id] = $spec_id_array;
    
            return $spec_id_array;
        }
    }

}