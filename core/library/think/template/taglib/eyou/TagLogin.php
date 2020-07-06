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

/**
 * 会员登录
 */
class TagLogin extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员登录入口
     * @author wengxianhu by 2018-4-20
     */
    public function getLogin($type = 'default', $logouttxt = '', $logoutimg = '')
    {
        $result = false;

        /*退出的文本或者图片*/
        $logout_html = $logouttxt;
        empty($logout_html) && $logout_html = '退出';
        if (!empty($logoutimg)) {
            if (!is_http_url($logoutimg)) {
                $logoutimg = $this->root_dir.'/'.trim($logoutimg, '/');
            }
            $logout_html = "<img class='eyou_logout_img' src='{$logoutimg}' />";
        }
        /*--end*/

        $web_users_switch = tpCache('web.web_users_switch');
        if (1 == intval($web_users_switch)) {
            $users_open_register = getUsersConfigData('users.users_open_register');
            if (empty($users_open_register)) {
                $loginid = 'eyou_login_'.getTime();
                $logintxtid = 'eyou_login_txt_'.getTime();
                $regid = 'eyou_reg_'.getTime();
                $regtxtid = 'eyou_reg_txt_'.getTime();
                $result['loginurl'] = url('user/Users/login');
                $result['loginid'] = $loginid;
                $result['logintxtid'] = $logintxtid;
                $result['regurl'] = url('user/Users/reg');
                $result['regid'] = $regid;
                $result['regtxtid'] = $regtxtid;
                $result['logouturl'] = url('user/Users/logout');
                $result['centreurl'] = url('user/Users/centre');
                $result['logout_html'] = $logout_html;
                $result['type'] = $type;
                $version = getCmsVersion();
                $result_json = json_encode($result);
                $hidden = <<<EOF
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_login.js?v={$version}"></script>
<script type="text/javascript">
    var tag_login_result_json = {$result_json};
    tag_login(tag_login_result_json, "{$this->root_dir}");
</script>
EOF;
                $result['hidden'] = $hidden;
            }
        }

        return $result;
    }
}