<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-07-04
 */

namespace app\admin\controller;

use think\Db;
use think\Config;

class UsersRelease extends Base {
    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();

        // 会员中心配置信息
        $this->UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$this->UsersConfigData);
    }

    /**
     * 商城设置
     */
    public function conf(){
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post)) {
                foreach ($post as $key => $val) {
                    getUsersConfigData($key, $val);
                }
                $this->success('设置成功！');
            }
        }

        // 会员投稿配置信息
        $UsersC = getUsersConfigData('users');
        $this->assign('UsersC',$UsersC);
        return $this->fetch('conf');
    }

    public function ajax_users_level_bout()
    {
        $UsersLevel = Db::name('users_level')->where('lang',$this->admin_lang)->select();
        $LevelCount = count($UsersLevel);

        $this->assign('list',$UsersLevel);
        $this->assign('LevelCount',$LevelCount);
        return $this->fetch();   
    }
}