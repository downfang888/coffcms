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

namespace app\admin\controller;

use think\Page;
use think\Db;
use app\admin\logic\FieldLogic;

/**
 * 模型字段控制器
 */
class Field extends Base
{
    public $fieldLogic;
    public $arctype_channel_id;

    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
        $this->fieldLogic = new FieldLogic();
        $this->arctype_channel_id = config('global.arctype_channel_id');
    }

    /**
     * 模型字段管理
     */
    public function channel_index()
    {
        $channel_id = input('param.channel_id/d', 1);
        $assign_data = array();
        $condition = array();
        // 获取到所有GET参数
        $param = input('param.');

        /*同步更新附加表字段到自定义模型字段表中*/
        if (empty($param['searchopt'])) {
            $this->fieldLogic->synChannelTableColumns($channel_id);
        }
        /*--end*/

        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['name'] = array('LIKE', "%{$param[$key]}%");
                    // 过滤指定字段
                    // $banFields = ['id'];
                    // $condition['name'] = array(
                    //     array('LIKE', "%{$param[$key]}%"),
                    //     array('notin', $banFields),
                    // );
                } else {
                    $condition[$key] = array('eq', $param[$key]);
                }
            }
        }

        /*显示主表与附加表*/
        $condition['channel_id'] = array('eq', $channel_id);

        $cfieldM =  M('channelfield');
        $count = $cfieldM->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $cfieldM->where($condition)->order('sort_order asc, channel_id desc, id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $assign_data['page'] = $show; // 赋值分页输出
        $assign_data['list'] = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        /*字段类型列表*/
        $assign_data['fieldtypeList'] = M('field_type')->field('name,title')->getAllWithIndex('name');
        /*--end*/

        /*有效的模型列表*/
        $channeltype_list = model('Channeltype')->getAll('*', ['status'=>1], 'nid');
        foreach ($channeltype_list as $key => $val) {
            if ('guestbook' == $key) { // 排除留言模型
                unset($channeltype_list[$key]);
            }
        }
        $assign_data['channeltype_list'] = $channeltype_list;
        /*--end*/

        /*模型ID*/
        $assign_data['channel_id'] = $channel_id;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 新增-模型字段
     */
    public function channel_add()
    {
        $channel_id = input('param.channel_id/d', 0);
        if (empty($channel_id)) {
            $this->error('参数有误！');
        }

        if (IS_POST) {
            $post = input('post.', '', 'trim');

            /*去除中文逗号，过滤左右空格与空值*/
            $dfvalue = str_replace('，', ',', $post['dfvalue']);
            $dfvalueArr = explode(',', $dfvalue);
            foreach ($dfvalueArr as $key => $val) {
                $tmp_val = trim($val);
                if ('' == $tmp_val) {
                    unset($dfvalueArr[$key]);
                    continue;
                }
                $dfvalueArr[$key] = trim($val);
            }
            $dfvalue = implode(',', $dfvalueArr);
            /*--end*/

            if (empty($post['dtype']) || empty($post['title']) || empty($post['name'])) {
                $this->error("缺少必填信息！");
            }

            if (1 == preg_match('/^([_]+|[0-9]+)$/', $post['name'])) {
                $this->error("字段名称格式不正确！");
            }

            /*默认值必填字段*/
            $fieldtype_list = model('Field')->getFieldTypeAll('name,title,ifoption', 'name');
            if (isset($fieldtype_list[$post['dtype']]) && 1 == $fieldtype_list[$post['dtype']]['ifoption']) {
                if (empty($dfvalue)) {
                    $this->error("你设定了字段为【".$fieldtype_list[$post['dtype']]['title']."】类型，默认值不能为空！ ");
                }
            }
            /*--end*/

            /*当前模型对应的数据表*/
            $table = M('channeltype')->where('id',$channel_id)->getField('table');
            $table = PREFIX.$table.'_content';
            /*--end*/

            /*检测字段是否存在于主表与附加表中*/
            if (true == $this->fieldLogic->checkChannelFieldList($table, $post['name'], $channel_id)) {
                $this->error("字段名称 ".$post['name']." 与系统字段冲突！");
            }
            /*--end*/

            /*组装完整的SQL语句，并执行新增字段*/
            $fieldinfos = $this->fieldLogic->GetFieldMake($post['dtype'], $post['name'], $dfvalue, $post['title']);
            $ntabsql = $fieldinfos[0];
            $buideType = $fieldinfos[1];
            $maxlength = $fieldinfos[2];
            $sql = " ALTER TABLE `$table` ADD  $ntabsql ";
            if (false !== Db::execute($sql)) {
                /*保存新增字段的记录*/
                $newData = array(
                    'dfvalue'   => $dfvalue,
                    'maxlength' => $maxlength,
                    'define'  => $buideType,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                );
                $data = array_merge($post, $newData);
                M('channelfield')->save($data);
                /*--end*/

                /*重新生成数据表字段缓存文件*/
                try {
                    schemaTable($table);
                } catch (\Exception $e) {}
                /*--end*/

                \think\Cache::clear('channelfield');
                $this->success("操作成功！", url('Field/channel_index', array('channel_id'=>$channel_id)));
            }
            $this->error('操作失败');
        }

        /*字段类型列表*/
        $assign_data['fieldtype_list'] = model('Field')->getFieldTypeAll('name,title,ifoption');
        /*--end*/
        
        /*模型ID*/
        $assign_data['channel_id'] = $channel_id;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 编辑-模型字段
     */
    public function channel_edit()
    {
        $channel_id = input('param.channel_id/d', 0);
        if (empty($channel_id)) {
            $this->error('参数有误！');
        }

        if (IS_POST) {
            $post = input('post.', '', 'trim');

            $info = model('Channelfield')->getInfo($post['id'], 'ifsystem');
            if (!empty($info['ifsystem'])) {
                $this->error('系统字段不允许更改！');
            }

            $old_name = $post['old_name'];
            /*去除中文逗号，过滤左右空格与空值*/
            $dfvalue = str_replace('，', ',', $post['dfvalue']);
            $dfvalueArr = explode(',', $dfvalue);
            foreach ($dfvalueArr as $key => $val) {
                $tmp_val = trim($val);
                if ('' == $tmp_val) {
                    unset($dfvalueArr[$key]);
                    continue;
                }
                $dfvalueArr[$key] = trim($val);
            }
            $dfvalue = implode(',', $dfvalueArr);
            /*--end*/

            if (empty($post['dtype']) || empty($post['title']) || empty($post['name'])) {
                $this->error("缺少必填信息！");
            }

            if (1 == preg_match('/^([_]+|[0-9]+)$/', $post['name'])) {
                $this->error("字段名称格式不正确！");
            }

            /*默认值必填字段*/
            $fieldtype_list = model('Field')->getFieldTypeAll('name,title,ifoption', 'name');
            if (isset($fieldtype_list[$post['dtype']]) && 1 == $fieldtype_list[$post['dtype']]['ifoption']) {
                if (empty($dfvalue)) {
                    $this->error("你设定了字段为【".$fieldtype_list[$post['dtype']]['title']."】类型，默认值不能为空！ ");
                }
            }
            /*--end*/

            /*当前模型对应的数据表*/
            $table = M('channeltype')->where('id',$post['channel_id'])->getField('table');
            $table = PREFIX.$table.'_content';
            /*--end*/

            /*检测字段是否存在于主表与附加表中*/
            if (true == $this->fieldLogic->checkChannelFieldList($table, $post['name'], $channel_id, array($old_name))) {
                $this->error("字段名称 ".$post['name']." 与系统字段冲突！");
            }
            /*--end*/

            /*组装完整的SQL语句，并执行编辑字段*/
            $fieldinfos = $this->fieldLogic->GetFieldMake($post['dtype'], $post['name'], $dfvalue, $post['title']);
            $ntabsql = $fieldinfos[0];
            $buideType = $fieldinfos[1];
            $maxlength = $fieldinfos[2];
            $sql = " ALTER TABLE `$table` CHANGE COLUMN `{$old_name}` $ntabsql ";
            if (false !== Db::execute($sql)) {
                /*保存更新字段的记录*/
                $newData = array(
                    'dfvalue'   => $dfvalue,
                    'maxlength' => $maxlength,
                    'define'  => $buideType,
                    'update_time' => getTime(),
                );
                $data = array_merge($post, $newData);
                M('channelfield')->where('id',$post['id'])->cache(true,null,"channelfield")->save($data);
                /*--end*/

                /*重新生成数据表字段缓存文件*/
                try {
                    schemaTable($table);
                } catch (\Exception $e) {}
                /*--end*/

                $this->success("操作成功！", url('Field/channel_index', array('channel_id'=>$post['channel_id'])));
            } else {
                $sql = " ALTER TABLE `$table` ADD  $ntabsql ";
                if (false === Db::execute($sql)) {
                    $this->error('操作失败！');
                }
            }
        }

        $id = input('param.id/d', 0);
        $info = array();
        if (!empty($id)) {
            $info = model('Channelfield')->getInfo($id);
        }
        if (!empty($info['ifsystem'])) {
            $this->error('系统字段不允许更改！');
        }
        $assign_data['info'] = $info;

        /*字段类型列表*/
        $assign_data['fieldtype_list'] = model('Field')->getFieldTypeAll('name,title,ifoption');
        /*--end*/
        
        /*模型ID*/
        $assign_data['channel_id'] = $channel_id;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 删除-模型字段
     */
    public function channel_del()
    {
        $channel_id = input('channel_id/d', 0);
        $id = input('del_id/d', 0);
        if(!empty($id)){
            /*删除表字段*/
            $row = $this->fieldLogic->delChannelField($id);
            /*--end*/
            if (0 < $row['code']) {
                $map = array(
                    'id'    => array('eq', $id),
                    'channel_id'    => $channel_id,
                );
                $result = M('channelfield')->field('channel_id,name')->where($map)->select();
                $name_list = get_arr_column($result, 'name');
                /*删除字段的记录*/
                M('channelfield')->where($map)->delete();
                /*--end*/

                /*获取模型标题*/
                $channel_title = '';
                if (!empty($channel_id)) {
                    $channel_title = M('channeltype')->where('id',$channel_id)->getField('title');
                }
                /*--end*/
                adminLog('删除'.$channel_title.'字段：'.implode(',', $name_list));
                $this->success('删除成功');
            }

            \think\Cache::clear('channelfield');
            respose(array('status'=>0, 'msg'=>$row['msg']));

        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 删除多图字段的图集
     */
    public function del_channelimgs()
    {
        $aid = input('aid/d','0');
        $channel = input('channel/d', ''); // 模型ID
        if (!empty($aid) && !empty($channel)) {
            $path = input('filename',''); // 图片路径
            $fieldname = input('fieldname/s', ''); // 多图字段

            /*模型附加表*/
            $table = M('channeltype')->where('id',$channel)->getField('table');
            $tableExt = $table.'_content';
            /*--end*/

            /*除去多图字段值中的图片*/
            $info = M($tableExt)->field("{$fieldname}")->where("aid", $aid)->find();
            $valueArr = explode(',', $info[$fieldname]);
            foreach ($valueArr as $key => $val) {
                if ($path == $val) {
                    unset($valueArr[$key]);
                }
            }
            $value = implode(',', $valueArr);
            M($tableExt)->where('aid', $aid)->update(array($fieldname=>$value, 'update_time'=>getTime()));
            /*--end*/
        }
    }

    /**
     * 栏目字段管理
     */
    public function arctype_index()
    {
        $channel_id = $this->arctype_channel_id;
        $assign_data = array();
        $condition = array();
        // 获取到所有GET参数
        $param = input('param.');

        /*同步更新栏目主表字段到自定义字段表中*/
        if (empty($param['searchopt'])) {
            $this->fieldLogic->synArctypeTableColumns($channel_id);
        }
        /*--end*/

        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['name'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $condition[$key] = array('eq', $param[$key]);
                }
            }
        }

        // 模型ID
        $condition['channel_id'] = array('eq', $channel_id);
        $condition['ifsystem'] = array('neq', 1);

        $cfieldM =  M('channelfield');
        $count = $cfieldM->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $cfieldM->where($condition)->order('sort_order asc, ifsystem asc, id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $assign_data['page'] = $show; // 赋值分页输出
        $assign_data['list'] = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        /*字段类型列表*/
        $assign_data['fieldtypeList'] = M('field_type')->field('name,title')->getAllWithIndex('name');
        /*--end*/

        $assign_data['channel_id'] = $channel_id;

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 新增-栏目字段
     */
    public function arctype_add()
    {
        $channel_id = $this->arctype_channel_id;
        if (empty($channel_id)) {
            $this->error('参数有误！');
        }

        if (IS_POST) {
            $post = input('post.', '', 'trim');

            /*去除中文逗号，过滤左右空格与空值*/
            $dfvalue = str_replace('，', ',', $post['dfvalue']);
            $dfvalueArr = explode(',', $dfvalue);
            foreach ($dfvalueArr as $key => $val) {
                $tmp_val = trim($val);
                if ('' == $tmp_val) {
                    unset($dfvalueArr[$key]);
                    continue;
                }
                $dfvalueArr[$key] = trim($val);
            }
            $dfvalue = implode(',', $dfvalueArr);
            /*--end*/

            if (empty($post['dtype']) || empty($post['title']) || empty($post['name'])) {
                $this->error("缺少必填信息！");
            }

            if (1 == preg_match('/^([_]+|[0-9]+)$/', $post['name'])) {
                $this->error("字段名称格式不正确！");
            }

            /*默认值必填字段*/
            $fieldtype_list = model('Field')->getFieldTypeAll('name,title,ifoption', 'name');
            if (isset($fieldtype_list[$post['dtype']]) && 1 == $fieldtype_list[$post['dtype']]['ifoption']) {
                if (empty($dfvalue)) {
                    $this->error("你设定了字段为【".$fieldtype_list[$post['dtype']]['title']."】类型，默认值不能为空！ ");
                }
            }
            /*--end*/

            /*栏目对应的单页表*/
            $tableExt = PREFIX.'single_content';
            /*--end*/

            /*检测字段是否存在于主表与附加表中*/
            if (true == $this->fieldLogic->checkChannelFieldList($tableExt, $post['name'], 6)) {
                $this->error("字段名称 ".$post['name']." 与系统字段冲突！");
            }
            /*--end*/

            /*组装完整的SQL语句，并执行新增字段*/
            $fieldinfos = $this->fieldLogic->GetFieldMake($post['dtype'], $post['name'], $dfvalue, $post['title']);
            $ntabsql = $fieldinfos[0];
            $buideType = $fieldinfos[1];
            $maxlength = $fieldinfos[2];
            $table = PREFIX.'arctype';
            $sql = " ALTER TABLE `$table` ADD  $ntabsql ";
            if (false !== Db::execute($sql)) {
                /*保存新增字段的记录*/
                $newData = array(
                    'dfvalue'   => $dfvalue,
                    'maxlength' => $maxlength,
                    'define'  => $buideType,
                    'ifmain'    => 1,
                    'ifsystem'  => 0,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                );
                $data = array_merge($post, $newData);
                M('channelfield')->save($data);
                /*--end*/

                /*重新生成数据表字段缓存文件*/
                try {
                    schemaTable($table);
                } catch (\Exception $e) {}
                /*--end*/

                \think\Cache::clear('channelfield');
                \think\Cache::clear("arctype");
                $this->success("操作成功！", url('Field/arctype_index'));
            }
            $this->error('操作失败');
        }

        /*字段类型列表*/
        $assign_data['fieldtype_list'] = model('Field')->getFieldTypeAll('name,title,ifoption');
        /*--end*/
        
        /*模型ID*/
        $assign_data['channel_id'] = $channel_id;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 编辑-栏目字段
     */
    public function arctype_edit()
    {
        $channel_id = $this->arctype_channel_id;
        if (empty($channel_id)) {
            $this->error('参数有误！');
        }

        if (IS_POST) {
            $post = input('post.', '', 'trim');

            $info = model('Channelfield')->getInfo($post['id'], 'ifsystem');
            if (!empty($info['ifsystem'])) {
                $this->error('系统字段不允许更改！');
            }

            $old_name = $post['old_name'];
            /*去除中文逗号，过滤左右空格与空值*/
            $dfvalue = str_replace('，', ',', $post['dfvalue']);
            $dfvalueArr = explode(',', $dfvalue);
            foreach ($dfvalueArr as $key => $val) {
                $tmp_val = trim($val);
                if ('' == $tmp_val) {
                    unset($dfvalueArr[$key]);
                    continue;
                }
                $dfvalueArr[$key] = trim($val);
            }
            $dfvalue = implode(',', $dfvalueArr);
            /*--end*/

            if (empty($post['dtype']) || empty($post['title']) || empty($post['name'])) {
                $this->error("缺少必填信息！");
            }

            if (1 == preg_match('/^([_]+|[0-9]+)$/', $post['name'])) {
                $this->error("字段名称格式不正确！");
            }

            /*默认值必填字段*/
            $fieldtype_list = model('Field')->getFieldTypeAll('name,title,ifoption', 'name');
            if (isset($fieldtype_list[$post['dtype']]) && 1 == $fieldtype_list[$post['dtype']]['ifoption']) {
                if (empty($dfvalue)) {
                    $this->error("你设定了字段为【".$fieldtype_list[$post['dtype']]['title']."】类型，默认值不能为空！ ");
                }
            }
            /*--end*/

            /*栏目对应的单页表*/
            $tableExt = PREFIX.'single_content';
            /*--end*/

            /*检测字段是否存在于主表与附加表中*/
            if (true == $this->fieldLogic->checkChannelFieldList($tableExt, $post['name'], 6, array($old_name))) {
                $this->error("字段名称 ".$post['name']." 与系统字段冲突！");
            }
            /*--end*/

            /*组装完整的SQL语句，并执行编辑字段*/
            $fieldinfos = $this->fieldLogic->GetFieldMake($post['dtype'], $post['name'], $dfvalue, $post['title']);
            $ntabsql = $fieldinfos[0];
            $buideType = $fieldinfos[1];
            $maxlength = $fieldinfos[2];
            $table = PREFIX.'arctype';
            $sql = " ALTER TABLE `$table` CHANGE COLUMN `{$old_name}` $ntabsql ";
            if (false !== Db::execute($sql)) {
                /*保存更新字段的记录*/
                $newData = array(
                    'dfvalue'   => $dfvalue,
                    'maxlength' => $maxlength,
                    'define'  => $buideType,
                    'ifmain'    => 1,
                    'ifsystem'  => 0,
                    'update_time' => getTime(),
                );
                $data = array_merge($post, $newData);
                M('channelfield')->where('id',$post['id'])->cache(true,null,"channelfield")->save($data);
                /*--end*/

                /*重新生成数据表字段缓存文件*/
                try {
                    schemaTable($table);
                } catch (\Exception $e) {}
                /*--end*/

                \think\Cache::clear("arctype");
                $this->success("操作成功！", url('Field/arctype_index'));
            } else {
                $sql = " ALTER TABLE `$table` ADD  $ntabsql ";
                if (false === Db::execute($sql)) {
                    $this->error('操作失败！');
                }
            }
        }

        $id = input('param.id/d', 0);
        $info = array();
        if (!empty($id)) {
            $info = model('Channelfield')->getInfo($id);
        }
        if (!empty($info['ifsystem'])) {
            $this->error('系统字段不允许更改！');
        }
        $assign_data['info'] = $info;

        /*字段类型列表*/
        $assign_data['fieldtype_list'] = model('Field')->getFieldTypeAll('name,title,ifoption');
        /*--end*/
        
        /*模型ID*/
        $assign_data['channel_id'] = $channel_id;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 删除-栏目字段
     */
    public function arctype_del()
    {
        $channel_id = $this->arctype_channel_id;
        $id = input('del_id/d', 0);
        if(!empty($id)){
            /*删除表字段*/
            $row = $this->fieldLogic->delArctypeField($id);
            /*--end*/
            if (0 < $row['code']) {
                $map = array(
                    'id'    => array('eq', $id),
                    'channel_id'    => $channel_id,
                );
                $result = M('channelfield')->field('channel_id,name')->where($map)->select();
                $name_list = get_arr_column($result, 'name');
                /*删除字段的记录*/
                M('channelfield')->where($map)->delete();
                /*--end*/

                adminLog('删除栏目字段：'.implode(',', $name_list));
                $this->success('删除成功');
            }

            \think\Cache::clear('channelfield');
            \think\Cache::clear("arctype");
            respose(array('status'=>0, 'msg'=>$row['msg']));

        }else{
            $this->error('参数有误');
        }
    }
}