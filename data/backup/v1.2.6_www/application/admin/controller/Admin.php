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
use think\Verify;
use think\Db;
use think\db\Query;
use think\Session;
use app\admin\logic\AuthModularLogic;
use app\admin\logic\AuthRoleLogic;
use app\admin\model\AuthRole;

class Admin extends Base {
    private $modular_system_id = array(); // 系统默认的模块id，不可删除
    private $admin_system_id = array(1); // 系统默认的管理员ID，不可删除

    public function index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition['a.user_name|a.true_name'] = array('LIKE', "%{$keywords}%");
        }

        /*权限控制 by 小虎哥*/
        $admin_info = session('admin_info');
        if (0 < intval($admin_info['role_id'])) {
            $condition['a.admin_id|a.parent_id'] = $admin_info['admin_id'];
        } else {
            if (!empty($admin_info['parent_id'])) {
                $condition['a.admin_id|a.parent_id'] = $admin_info['admin_id'];
            }
        }
        /*--end*/

        /**
         * 数据查询
         */
        $count = DB::name('admin')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = DB::name('admin')->field('a.*, b.name AS role_name')
            ->alias('a')
            ->join('__AUTH_ROLE__ b', 'a.role_id = b.id', 'LEFT')
            ->where($condition)
            ->order('a.admin_id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        foreach ($list as $key => $val) {
            if (0 >= intval($val['role_id'])) {
                $val['role_name'] = !empty($val['parent_id']) ? '超级管理员' : '创始人';
            }
            $list[$key] = $val;
        }

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页集

        /*第一次同步CMS用户的栏目ID到权限组里*/
        $this->syn_built_auth_role();
        /*--end*/

        return $this->fetch();
    }

    /*
     * 管理员登陆
     */
    public function login()
    {
        if (session('?admin_id') && session('admin_id') > 0) {
            $web_adminbasefile = tpCache('web.web_adminbasefile');
            $web_adminbasefile = !empty($web_adminbasefile) ? $web_adminbasefile : '/login.php';
            $this->success("您已登录", $web_adminbasefile);
        }
      
        // $gb_funcs = get_extension_funcs('gd');
        $is_vertify = 1; // 默认开启验证码
        $admin_login_captcha = config('captcha.admin_login');
        if (!function_exists('imagettftext') || empty($admin_login_captcha['is_on'])) {
            $is_vertify = 0; // 函数不存在，不符合开启的条件
        }

        if (IS_POST) {
            if (1 == $is_vertify) {
                $verify = new Verify();
                if (!$verify->check(input('post.vertify'), "admin_login")) {
                    exit(json_encode(array('status'=>0,'msg'=>'验证码错误')));
                }
            }
            $condition['user_name'] = input('post.username/s');
            $condition['password'] = input('post.password/s');
            if (!empty($condition['user_name']) && !empty($condition['password'])) {
                $condition['password'] = func_encrypt($condition['password']);
                $admin_info = M('admin')->where($condition)->find();
                if (is_array($admin_info)) {
                    if ($admin_info['status'] == 0) {
                        exit(json_encode(array('status'=>0,'msg'=>'账号被禁用！')));
                    }

                    $role_id = !empty($admin_info['role_id']) ? $admin_info['role_id'] : -1;
                    $auth_role_info = array();
                    $role_name = !empty($admin_info['parent_id']) ? '超级管理员' : '创始人';
                    if (0 < intval($role_id)) {
                        $auth_role_info = M('auth_role')
                            ->field("a.*, a.name AS role_name")
                            ->alias('a')
                            ->where('a.id','eq', $role_id)
                            ->find();
                        if (!empty($auth_role_info)) {
                            $auth_role_info['language'] = unserialize($auth_role_info['language']);
                            $auth_role_info['cud'] = unserialize($auth_role_info['cud']);
                            $auth_role_info['permission'] = unserialize($auth_role_info['permission']);
                            $role_name = $auth_role_info['name'];
                        }
                    }
                    $admin_info['auth_role_info'] = $auth_role_info;
                    $admin_info['role_name'] = $role_name;

                    $last_login_time = getTime();
                    $last_login_ip = clientIP();
                    $login_cnt = $admin_info['login_cnt'] + 1;
                    M('admin')->where("admin_id = ".$admin_info['admin_id'])->save(array('last_login'=>$last_login_time, 'last_ip'=>$last_login_ip, 'login_cnt'=>$login_cnt, 'session_id'=>$this->session_id));
                    $admin_info['last_login'] = $last_login_time;
                    $admin_info['last_ip'] = $last_login_ip;

                    session('admin_id',$admin_info['admin_id']);
                    session('admin_info', $admin_info);
                    adminLog('后台登录');
                    $url = session('from_url') ? session('from_url') : request()->baseFile();
                    exit(json_encode(array('status'=>1,'url'=>$url)));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'账号密码不正确')));
                }
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'请填写账号密码')));
            }
        }

        $this->assign('is_vertify', $is_vertify);
        return $this->fetch();
    }

    /**
     * 验证码获取
     */
    public function vertify()
    {
        /*验证码插件开关*/
        $admin_login_captcha = config('captcha.admin_login');
        $config = (!empty($admin_login_captcha['is_on']) && !empty($admin_login_captcha['config'])) ? $admin_login_captcha['config'] : config('captcha.default');
        /*--end*/
        ob_clean(); // 清空缓存，才能显示验证码
        $Verify = new Verify($config);
        $Verify->entry('admin_login');
        exit();
    }
    
    /**
     * 修改管理员密码
     * @return \think\mixed
     */
    public function admin_pwd()
    {
        $admin_id = input('admin_id/d',0);
        $oldPwd = input('old_pw/s');
        $newPwd = input('new_pw/s');
        $new2Pwd = input('new_pw2/s');
       
        if(!$admin_id){
            $admin_id = session('admin_id');
        }
        $info = M('admin')->where("admin_id", $admin_id)->find();
        $info['password'] =  "";
        $this->assign('info',$info);
        
        if(IS_POST){
            //修改密码
            $enOldPwd = func_encrypt($oldPwd);
            $enNewPwd = func_encrypt($newPwd);
            $admin = M('admin')->where('admin_id' , $admin_id)->find();
            if(!$admin || $admin['password'] != $enOldPwd){
                exit(json_encode(array('status'=>-1,'msg'=>'旧密码不正确')));
            }else if($newPwd != $new2Pwd){
                exit(json_encode(array('status'=>-1,'msg'=>'两次密码不一致')));
            }else{
                $data = array(
                    'update_time'   => getTime(),
                    'password'      => $enNewPwd,
                );
                $row = M('admin')->where('admin_id' , $admin_id)->save($data);
                if($row){
                    adminLog('修改管理员密码');
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                }else{
                    exit(json_encode(array('status'=>-1,'msg'=>'操作失败')));
                }
            }
        }

        if (IS_AJAX) {
            return $this->fetch('admin/admin_pwd_ajax');
        } else {
            return $this->fetch('admin/admin_pwd');
        }
    }
    
    /**
     * 退出登陆
     */
    public function logout()
    {
        adminLog('安全退出');
        session_unset();
        // session_destroy();
        session::clear();
        cookie('admin-treeClicked', null); // 清除并恢复栏目列表的展开方式
        $this->success("安全退出", request()->baseFile());
    }
    
    /**
     * 新增管理员
     */
    public function admin_add()
    {
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $data = input('post.');

            if (0 < intval(session('admin_info.role_id'))) {
                $this->error("超级管理员才能操作！");
            }

            if (empty($data['password']) || empty($data['password2'])) {
                $this->error("密码不能为空！");
            }else if ($data['password'] != $data['password2']) {
                $this->error("两次密码输入不一致！");
            }

            $data['user_name'] = trim($data['user_name']);
            $data['password'] = func_encrypt($data['password']);
            $data['password2'] = func_encrypt($data['password2']);
            $data['role_id'] = intval($data['role_id']);
            $data['parent_id'] = session('admin_info.admin_id');
            $data['add_time'] = getTime();
            if (M('admin')->where("user_name", $data['user_name'])->count()) {
                $this->error("此用户名已被注册，请更换",url('Admin/admin_add'));
            } else {
                $admin_id = M('admin')->insertGetId($data);
                if ($admin_id) {
                    adminLog('新增管理员：'.$data['user_name']);
                    $this->success("操作成功", url('Admin/index'));
                } else {
                    $this->error("操作失败");
                }
            }
        }

        // 权限组
        $admin_role_list = model('AuthRole')->getRoleAll();
        $this->assign('admin_role_list', $admin_role_list);

        // 模块组
        $modules = getAllMenu();
        $this->assign('modules', $modules);

        // 权限集
        $auth_rules = get_auth_rule(['is_modules'=>1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_data = $arctype_array = array();
        $arctype = M('arctype')->select();
        if(! empty($arctype)){
            foreach ($arctype as $item){
                if($item['parent_id'] <= 0){
                    $arctype_data[] = $item;
                }
                $arctype_array[$item['parent_id']][] = $item;
            }
        }
        $this->assign('arctypes', $arctype_data);
        $this->assign('arctype_array', $arctype_array);

        // 插件
        $plugins = model('Weapp')->getList(['status'=>1]);
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    
    /**
     * 编辑管理员
     */
    public function admin_edit()
    {
        if (IS_POST) {
            $data = input('post.');
            $id = $data['admin_id'];

            if ($id == session('admin_info.admin_id')) {
                unset($data['role_id']); // 不能修改自己的权限组
            } else if (0 < intval(session('admin_info.role_id')) && session('admin_info.admin_id') != $id) {
                $this->error('禁止更改别人的信息！');
            }

            if (!empty($data['password']) || !empty($data['password2'])) {
                if ($data['password'] != $data['password2']) {
                    $this->error("两次密码输入不一致！");
                }
            }

            $user_name = $data['user_name'];
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = func_encrypt($data['password']);
            }
            unset($data['user_name']);

            /*不允许修改自己的权限组*/
            if (isset($data['role_id'])) {
                if (0 < intval(session('admin_info.role_id')) && intval($data['role_id']) != session('admin_info.role_id')) {
                    $data['role_id'] = session('admin_info.role_id');
                }
            }
            /*--end*/
            $data['update_time'] = getTime();
            $r = M('admin')->where('admin_id', $id)->save($data);
            if ($r) {
                adminLog('编辑管理员：'.$user_name);
                $this->success("操作成功",url('Admin/index'));
            } else {
                $this->error("操作失败");
            }
        }

        $id = input('get.id/d', 0);
        $info = M('admin')->field('a.*')
            ->alias('a')
            ->where("a.admin_id", $id)->find();
        $info['password'] =  "";
        $this->assign('info',$info);

        // 当前角色信息
        $admin_role_model = model('AuthRole');
        $role_info = $admin_role_model->getRole(array('id' => $info['role_id']));
        $this->assign('role_info', $role_info);

        // 权限组
        $admin_role_list = $admin_role_model->getRoleAll();
        $this->assign('admin_role_list', $admin_role_list);

        // 模块组
        $modules = getAllMenu();
        $this->assign('modules', $modules);

        // 权限集
        $auth_rules = get_auth_rule(['is_modules'=>1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_data = $arctype_array = array();
        $arctype = M('arctype')->select();
        if(! empty($arctype)){
            foreach ($arctype as $item){
                if($item['parent_id'] <= 0){
                    $arctype_data[] = $item;
                }
                $arctype_array[$item['parent_id']][] = $item;
            }
        }
        $this->assign('arctypes', $arctype_data);
        $this->assign('arctype_array', $arctype_array);

        // 插件
        $plugins = model('Weapp')->getList(['status'=>1]);
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    
    /**
     * 删除管理员
     */
    public function admin_del()
    {
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (in_array(session('admin_id'), $id_arr)) {
                $this->error('禁止删除自己');
            }
            if (!empty($id_arr)) {
                if (0 < intval(session('admin_info.role_id')) || !empty($parent_id) ) {
                    $count = M('admin')->where("admin_id in (".implode(',', $id_arr).") AND role_id = -1")
                        ->count();
                    if (!empty($count)) {
                        $this->error('禁止删除超级管理员');
                    }
                }

                $result = M('admin')->field('user_name')->where("admin_id",'IN',$id_arr)->select();
                $user_names = get_arr_column($result, 'user_name');

                $r = M('admin')->where("admin_id",'IN',$id_arr)->delete();
                if($r){
                    adminLog('删除管理员：'.implode(',', $user_names));
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            }else{
                $this->error('参数有误');
            }
        }
        $this->error('非法操作');
    }

    /*
     * 第一次同步CMS用户的栏目ID到权限组里
     * 默认赋予内置权限所有的内容栏目权限
     */
    private function syn_built_auth_role()
    {
        $authRole = new AuthRole;
        $roleRow = $authRole->getRoleAll(['built_in'=>1,'update_time'=>['elt',0]]);
        if (!empty($roleRow)) {
            $saveData = [];
            foreach ($roleRow as $key => $val) {
                $permission = $val['permission'];
                $arctype = M('arctype')->where('status',1)->column('id');
                if (!empty($arctype)) {
                    $permission['arctype'] = $arctype;
                } else {
                    unset($permission['arctype']);
                }
                $saveData[] = array(
                    'id'    => $val['id'],
                    'permission'    => $permission,
                    'update_time'   => getTime(),
                );
            }
            $authRole->saveAll($saveData);
        }
    }
}