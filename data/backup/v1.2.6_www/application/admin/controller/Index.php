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
use app\admin\controller\Base;
use think\Controller;
use think\Db;

class Index extends Base
{
    public function index()
    {
        $language_db = Db::name('language');

        /*多语言列表*/
        $web_language_switch = tpCache('web.web_language_switch');
        $languages = [];
        if (1 == intval($web_language_switch)) {
            $languages = $language_db->field('a.mark, a.title')
                ->alias('a')
                ->where('a.status',1)
                ->getAllWithIndex('mark');
        }
        $this->assign('languages', $languages);
        $this->assign('web_language_switch', $web_language_switch);
        /*--end*/

        /*网站首页链接*/
        // 去掉入口文件
        $inletStr = '/index.php';
        $seo_inlet = config('ey_config.seo_inlet');
        1 == intval($seo_inlet) && $inletStr = '';
        // --end
        $home_default_lang = config('ey_config.system_home_default_lang');
        $admin_lang = get_admin_lang();
        $home_url = request()->domain().ROOT_DIR.'/';  // 支持子目录
        if ($home_default_lang != $admin_lang) {
            $home_url = $language_db->where(['mark'=>$admin_lang])->getField('url');
            if (empty($home_url)) {
                $seoConfig = tpCache('seo');
                $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
                if (1 == $seo_pseudo) {
                    $home_url = request()->domain().ROOT_DIR.$inletStr; // 支持子目录
                    if (!empty($inletStr)) {
                        $home_url .= '?';
                    } else {
                        $home_url .= '/?';
                    }
                    $home_url .= http_build_query(['lang'=>$admin_lang]);
                } else {
                    $home_url = request()->domain().ROOT_DIR.$inletStr.'/'.$admin_lang; // 支持子目录
                }
            }
        }
        $this->assign('home_url', $home_url);
        /*--end*/

        $this->assign('admin_info', getAdminInfo());
        $this->assign('menu',getMenuList());
        return $this->fetch();
    }
   
    public function welcome()
    {
        /*百度分享*/
        $webConfig = tpCache('web');
        $share = array(
            'bdText'    => $webConfig['web_title'],
            'bdPic'     => is_http_url($webConfig['web_logo']) ? $webConfig['web_logo'] : request()->domain().$webConfig['web_logo'],
            'bdUrl'     => $webConfig['web_basehost'],
        );
        $this->assign('share',$share);
        /*--end*/

        $this->assign('sys_info',$this->get_sys_info());
        $this->assign('web_show_popup_upgrade', tpCache('web.web_show_popup_upgrade'));
        return $this->fetch();
    }
    
    public function get_sys_info()
    {
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
        $sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off       
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';  
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = gethostbyname($_SERVER['SERVER_NAME']);
        $sys_info['postsize']       = @ini_get('file_uploads') ? ini_get('post_max_size') :'unknown';
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time").'s'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');
        $sys_info['version']        = file_get_contents(DATA_PATH.'conf/version.txt');
        $mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version']  = $mysqlinfo[0]['version'];
        if(function_exists("gd_info")){
            $gd = gd_info();
            $sys_info['gdinfo']     = $gd['GD Version'];
        }else {
            $sys_info['gdinfo']     = "未知";
        }
        if (extension_loaded('zip')) {
            $sys_info['zip']     = "YES";
        } else {
            $sys_info['zip']     = "NO";
        }
        $upgradeLogic = new \app\admin\logic\UpgradeLogic();
        $sys_info['curent_version'] = $upgradeLogic->curent_version; //当前程序版本
        $sys_info['web_name'] = tpCache('global.web_name');

        return $sys_info;
    }

    /**
     * 录入商业授权
     */
    public function authortoken()
    {
        $inc_type = 'web';
        if (IS_POST) {
            $web_authortoken = input('post.web_authortoken/s', '');
            $web_authortoken = trim($web_authortoken);
            $result = false;
            /*多语言*/
            if (is_language()) {
                $langRow = Db::name('language')->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->order('id asc')
                    ->select();
                foreach ($langRow as $key => $val) {
                    $result = tpCache($inc_type, ['web_authortoken'=>$web_authortoken], $val['mark']);
                }
            } else { // 单语言
                $result = tpCache($inc_type, array('web_authortoken'=>$web_authortoken));
            }
            /*--end*/
            if ($result) {
                $source = realpath('public/static/admin/images/logo_ey.png');
                $destination = realpath('public/static/admin/images/logo.png');
                @copy($source, $destination);

                session('isset_author', null);
                adminLog('录入商业授权');
                $this->success('操作成功', request()->baseFile(), '', 1, [], '_parent');
            }else{
                $this->error("操作失败!", U('Index/authortoken'));
            }
            exit;
        }
        $web_authortoken = tpCache($inc_type.'.web_authortoken');
        $this->assign('web_authortoken', $web_authortoken);
        $this->assign('inc_type', $inc_type);

        return $this->fetch();
    }

    /**
     * 更换后台logo
     */
    public function edit_adminlogo()
    {
        $filename = input('param.filename/s', '');
        if (!empty($filename)) {
            $source = realpath(preg_replace('#^'.ROOT_DIR.'/#i', '', $filename)); // 支持子目录
            $web_is_authortoken = tpCache('web.web_is_authortoken');
            if (empty($web_is_authortoken)) {
                $destination = realpath('public/static/admin/images/logo.png');
            } else {
                $destination = realpath('public/static/admin/images/logo_ey.png');
            }
            if (@copy($source, $destination)) {
                $this->success('操作成功');
            }
        }
        $this->error('操作失败');
    }

    /**
     * 待处理事项
     */
    public function pending_matters()
    {
        $html = '<div style="text-align: center; margin: 20px 0px; color:red;">惹妹子生气了，没啥好处理！</div>';
        echo $html;
    }
    
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal()
    {  
        $table = input('table'); // 表名
        $id_name = input('id_name'); // 表主键id名
        $id_value = input('id_value'); // 表主键id值
        $field  = input('field'); // 修改哪个字段
        $value  = input('value', '', null); // 修改字段值  
        M($table)->where("$id_name = $id_value")->cache(true,null,$table)->save(array($field=>$value)); // 根据条件保存修改的数据

        switch ($table) {
            case 'auth_modular':
                extra_cache('admin_auth_modular_list_logic', null);
                extra_cache('admin_all_menu', null);
                break;
            
            default:
                // 清除logic逻辑定义的缓存
                extra_cache('admin_'.$table.'_list_logic', null);
                // 清除一下缓存
                // delFile(RUNTIME_PATH.'html'); // 先清除缓存, 否则不好预览
                \think\Cache::clear($table);
                break;
        }

        /*清除页面缓存*/
        $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
        $htmlCacheLogic->clear_archives();
        /*--end*/
        
        $this->success('操作成功');
    }   
}
