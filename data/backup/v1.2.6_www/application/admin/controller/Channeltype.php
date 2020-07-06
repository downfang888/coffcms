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

class Channeltype extends Base
{
    private $channeltype_system_id = array(1,2,3,4,5,6,7,8,9); // 系统默认的模型ID，不可删除

    public function index()
    {
        $list = array();
        $get = input('get.');
        $keywords = input('keywords/s');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$get[$key]}%");
                } else {
                    $tmp_key = 'a.'.$key;
                    $condition[$tmp_key] = array('eq', $get[$key]);
                }
            }
        }

        $mod =  M('channeltype');
        $count = $mod->alias('a')->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $mod->alias('a')->where($condition)->order('status desc, sort_order asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页对象
        return $this->fetch();
    }
    
    /**
     * 新增
     */
    public function add()
    {
        if (IS_POST) {
            $post = input('post.', null, 'trim');

            $map = array(
                'nid' => strtolower($post['nid']),
            );
            if(M('channeltype')->where($map)->count('id') > 0){
                $this->error('该模型标识已存在，请检查', url('Channeltype/index'));
            }

            $nowData = array(
                'nid' => strtolower($post['nid']),
                'add_time'           => getTime(),
                'update_time'           => getTime(),
            );
            $data = array_merge($post, $nowData);
            $insertId = M('channeltype')->insertGetId($data);
            $_POST['id'] = $insertId;
            if ($insertId) {
                \think\Cache::clear('channeltype');
                extra_cache('admin_channeltype_list_logic', NULL);
                adminLog('新增模型：'.$post['title']);
                $this->success("操作成功", url('Channeltype/index'));
            } else {
                $this->error("操作失败");
            }
            exit;
        }

        $this->assign('prefix', PREFIX);

        return $this->fetch();
    }

    
    /**
     * 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.', null, 'trim');
            if(!empty($post['id'])){
                $nowData = array(
                    'update_time'       => getTime(),
                );
                $data = array_merge($post, $nowData);
                $r = M('channeltype')->where(array('id'=>$post['id']))->cache(true,null,"channeltype")->update($data);
            }
            if ($r) {
                extra_cache('admin_channeltype_list_logic', NULL);
                adminLog('编辑模型：'.$post['title']);
                $this->success("操作成功", url('Channeltype/index'));
            } else {
                $this->error("操作失败");
            }
        }

        $assign_data = array();

        $id = input('id/d');
        $info = M('channeltype')->field('a.*')
            ->alias('a')
            ->where(array('a.id'=>$id))
            ->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $assign_data['field'] = $info;
        $assign_data['prefix'] = PREFIX;

        $this->assign($assign_data);
        return $this->fetch();
    }

    
    /**
     * 删除
     */
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){
            foreach ($id_arr as $key => $val) {
                if (array_key_exists($val, $this->channeltype_system_id)) {
                    $this->error('系统预定义，不能删除');
                }
            }

            $count = M('arctype')->where("channeltype",'IN',$id_arr)->count('id');
            if ($count > 0){
                $this->error('该模型下有栏目，不允许删除，请先删除该模型下的栏目');
            }  

            $result = M('channeltype')->field('title')->where("id",'IN',$id_arr)->select();
            $title_list = get_arr_column($result, 'title');

            $r = M('channeltype')->where("id",'IN',$id_arr)->cache(true,null,"channeltype")->delete();
            if ($r) {
                // \think\Cache::clear('channeltype');
                extra_cache('admin_channeltype_list_logic', NULL);
                adminLog('删除模型：'.implode(',', $title_list));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }
}