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
use app\common\logic\ArctypeLogic;

class Single extends Base
{
    // 模型标识
    public $nid = 'single';
    // 模型ID
    public $channeltype = '';
    
    public function _initialize() {
        parent::_initialize();
        $channeltype_list = config('global.channeltype_list');
        $this->channeltype = $channeltype_list[$this->nid];
    }

    /**
     * 列表
     */
    public function index()
    {
        $arctype_list = array();
        // 目录列表
        $arctypeLogic = new ArctypeLogic(); 
        $map = array(
            'channeltype' => $this->channeltype,
        );
        $arctype_list = $arctypeLogic->arctype_list(0, 0, false, 0, $map);
        $this->assign('arctype_list', $arctype_list);

        return $this->fetch();
    }

    public function add()
    {
        if (IS_POST) {
            $post = I('post.');
            $content = I('content', '', null);

            // --存储数据
            $nowData = array(
                'title' => $post['typename'],
                'typeid'=> empty($post['typeid']) ? 0 : $post['typeid'],
                'channel'   => $this->channeltype,
                'add_time'  => getTime(),
                'update_time'     => getTime(),
            );
            $data = array_merge($post, $nowData);

            if (intval($data['aid']) > 0) {
                unset($data['add_time']);
                $aid = M('archives')->where(array('aid'=>$data['aid']))->update($data);
            } else {
                $aid = M('archives')->insertGetId($data);
            }
            
            if ($aid) {
                // ---------后置操作
                model('Single')->afterSave($aid, $data);
                // ---------end
                // --处理TAG标签
                adminLog('编辑单页：'.$data['title']);
                $this->success("操作成功!",U('Single/index'));
                exit;
            }

            $this->error("操作失败!",U('Single/index'));
            exit;
        }

        $assign_data = array();

        $typeid = I('typeid/d', 0);
        $field = array();
        if ($typeid > 0) {
            $field = model('Single')->getInfoByTypeid($typeid);
        }
        $assign_data['field'] = $field;

        // 栏目
        $selected = $typeid;
        $arctypeLogic = new ArctypeLogic();
        $map = array(
            'channeltype'    => $this->channeltype,
        );
        $select_html = $arctypeLogic->arctype_list(0, $selected, true, config('global.arctype_max_level'), $map);
        $assign_data['select_html'] = $select_html;

        // 阅读权限
        $arcrank_list = get_arcrank_list();
        $assign_data['arcrank_list'] = $arcrank_list;

        $this->assign($assign_data);
        return $this->fetch();
    }
}