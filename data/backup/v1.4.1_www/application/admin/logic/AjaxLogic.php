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

namespace app\admin\logic;

use think\Model;
use think\Db;

/**
 * 逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class AjaxLogic extends Model
{
    private $request = null;
    private $admin_lang = 'cn';

    /**
     * 析构函数
     */
    function  __construct() {
        $this->request = request();
        $this->admin_lang = get_admin_lang();
    }

    /**
     * 进入登录页面需要异步处理的业务
     */
    public function login_handle()
    {
        $this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
        $this->clear_session_file(); // 清理过期的data/session文件
    }

    /**
     * 进入欢迎页面需要异步处理的业务
     */
    public function welcome_handle()
    {
        $this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
        $this->renameInstall(); // 重命名安装目录，提高网站安全性
        $this->del_adminlog(); // 只保留最近三个月的操作日志
        $this->syn_smtp_config(); // 同步插件【邮箱发送】的配置信息到内置表中
        tpversion(); // 统计装载量，请勿删除，谢谢支持！
    }
    
    /**
     * 只保留最近三个月的操作日志
     */
    private function del_adminlog()
    {
        $mtime = strtotime("-1 month");
        Db::name('admin_log')->where([
            'log_time'  => ['lt', $mtime],
            ])->delete();
        // 临时清理无效图片
        @unlink('./public/plugins/Ueditor/themes/default/images/worwdpasdte.png');
    }

    /**
     * 重命名安装目录，提高网站安全性
     * 在 Admin@login 和 Index@index 操作下
     */
    private function renameInstall()
    {
        $install_path = ROOT_PATH.'install';
        if (is_dir($install_path) && file_exists($install_path)) {
            $install_time = DEFAULT_INSTALL_DATE;
            $constsant_path = APP_PATH.'admin/conf/constant.php';
            if (file_exists($constsant_path)) {
                require_once($constsant_path);
                defined('INSTALL_DATE') && $install_time = INSTALL_DATE;
            }
            $new_path = ROOT_PATH.'install_'.$install_time;
            @rename($install_path, $new_path);
        } else { // 修补v1.1.6版本删除的安装文件 install.lock
            if(!empty($_SESSION['isset_install_lock']))
                return true;
            $_SESSION['isset_install_lock'] = 1;

            $install_time = DEFAULT_INSTALL_DATE;
            $constsant_path = APP_PATH.'admin/conf/constant.php';
            if (file_exists($constsant_path)) {
                require_once($constsant_path);
                defined('INSTALL_DATE') && $install_time = INSTALL_DATE;
            }
            $filename = ROOT_PATH.'install_'.$install_time.DS.'install.lock';
            if (!file_exists($filename)) {
                @file_put_contents($filename, '');
            }
        }
    }

    /**
     * 存储后台入口文件路径，比如：/login.php
     * 在 Admin@login 和 Index@index 操作下
     */
    private function saveBaseFile()
    {
        $baseFile = $this->request->baseFile();
        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->field('mark')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', ['web_adminbasefile'=>$baseFile], $val['mark']);
            }
        } else { // 单语言
            tpCache('web', ['web_adminbasefile'=>$baseFile]);
        }
        /*--end*/
    }

    /**
     * 自动纠正蜘蛛抓取文件rotots.txt  ，【版本1.4.1可以去掉该代码，以及相关调用】
     */
    public function update_robots()
    {
        $filename = 'robots.txt';
        if (file_exists($filename) && is_file($filename) && getCmsVersion() <= 'v1.4.0') {
            $system_robots_edit = tpCache('system.system_robots_edit');
            if (!empty($system_robots_edit)) {
                return true;
            }
            // 系统移除的抓取规则
            $removeList = [
                'robots.txtforeyoucms',
                'disallow:',
                'disallow:/*.php*',
                'disallow:/*.js*',
                'disallow:/*.css*',
                'disallow:/extend/',
                'disallow:/extend',
                'disallow:/data/',
                'disallow:/public/',
                'disallow:/template/',
                'disallow:/template',
                'disallow:/vendor/',
                'disallow:/vendor',
                'disallow:/weapp/',
                'disallow:/adm*',
                'sitemap:/sitemap.xml',
                'disallow:/install_*/',
                'disallow:/core/',
                'disallow:/application/',
                'disallow:/*&uiset=off*',
                'disallow:/*&uiset=on*',
            ];
            $robots = @file_get_contents(ROOT_PATH . $filename);
            $arr = explode(PHP_EOL, $robots);
            foreach ($arr as $key => $val) {
                if (empty($val)) {
                    unset($arr[$key]);
                    continue;
                }
                $is_unset = false;
                $val = trim($val);
                $str = str_replace(' ', '', strtolower($val));
                if (in_array($str, $removeList) || stristr($str, '#')) {
                    $is_unset = true;
                }

                // 移除系统指定的抓取规则
                if (true === $is_unset) {
                    unset($arr[$key]);
                    continue;
                }
            }
            if (!empty($arr)) {
                $robotsStr = implode(PHP_EOL, $arr);
                if (is_writable($filename)) {
                    @file_put_contents($filename, $robotsStr);
                    /*多语言*/
                    if (is_language()) {
                        $langRow = \think\Db::name('language')->field('mark')->order('id asc')->select();
                        foreach ($langRow as $key => $val) {
                            tpCache('system', ['system_robots_edit'=>1], $val['mark']);
                        }
                    } else { // 单语言
                        tpCache('system', ['system_robots_edit'=>1]);
                    }
                    /*--end*/
                }
            }
        }
    }

    /**
     * 清理过期的data/session文件
     */
    private function clear_session_file()
    {
        $path = \think\Config::get('session.path');
        if (!empty($path) && file_exists($path)) {
            $web_login_expiretime = tpCache('web.web_login_expiretime');
            empty($web_login_expiretime) && $web_login_expiretime = config('login_expire');
            $files = glob($path.'/sess_*');
            foreach ($files as $key => $file) {
                $filemtime = filemtime($file);
                if (getTime() - intval($filemtime) > $web_login_expiretime) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * 同步插件【邮箱发送】的配置信息到内置表中 -- 兼容1.3.0之前版本
     */
    private function syn_smtp_config()
    {
        $smtp_syn_weapp = tpCache('smtp.smtp_syn_weapp'); // 是否同步插件【邮箱发送】的配置
        if (empty($smtp_syn_weapp)) {

            /*同步之前安装邮箱插件的配置信息*/
            $data = \think\Db::name('weapp')->where('code','Smtpmail')->getField('data');
            if (!empty($data)) {
                $data = unserialize($data);
                if (is_array($data) && !empty($data)) {
                    foreach ($data as $key => $val) {
                        if (!in_array($key, ['smtp_server','smtp_port','smtp_user','smtp_pwd','smtp_from_eamil'])) {
                            unset($data[$key]);
                        }
                    }
                }
            }
            /*--end*/

            $data['smtp_syn_weapp'] = 1;

            /*多语言*/
            if (!is_language()) {
                tpCache('smtp',$data);
            } else {
                $smtp_tpl_db = \think\Db::name('smtp_tpl');
                $smtptplList = $smtp_tpl_db->field('tpl_id,lang')->getAllWithIndex('lang');
                $smtptplRow = $smtp_tpl_db->field('tpl_id,lang',true)
                    ->where('lang', get_main_lang())
                    ->order('tpl_id asc')
                    ->select();

                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    /*同步多语言邮件模板表数据*/
                    if (empty($smtptplList[$val['mark']]) && !empty($smtptplRow)) {
                        foreach ($smtptplRow as $key2 => $val2) {
                            $smtptplRow[$key2]['lang'] = $val['mark'];
                        }
                        model('SmtpTpl')->saveAll($smtptplRow);
                    }
                    /*--end*/
                    tpCache('smtp', $data, $val['mark']);
                }
            }
            /*--end*/
        }
    }

    /**
     * 升级前台会员中心的模板文件
     */
    public function update_template($type = '')
    {
        if (!empty($type)) {
            if ('users' == $type) {
                if (file_exists(ROOT_PATH.'template/pc/users') || file_exists(ROOT_PATH.'template/mobile/users')) {
                    /*升级之前，备份涉及的源文件*/
                    $upgrade = getDirFile(DATA_PATH.'backup'.DS.'tpl');
                    if (!empty($upgrade) && is_array($upgrade)) {
                        delFile(DATA_PATH.'backup'.DS.'template_www');
                        foreach ($upgrade as $key => $val) {
                            $source_file = ROOT_PATH.$val;
                            if (file_exists($source_file)) {
                                $destination_file = DATA_PATH.'backup'.DS.'template_www'.DS.$val;
                                tp_mkdir(dirname($destination_file));
                                @copy($source_file, $destination_file);
                            }
                        }

                        // 递归复制文件夹
                        $this->recurse_copy(DATA_PATH.'backup'.DS.'tpl', rtrim(ROOT_PATH, DS));
                    }
                    /*--end*/
                }
            }
        }
    }

    /**
     * 自定义函数递归的复制带有多级子目录的目录
     * 递归复制文件夹
     *
     * @param string $src 原目录
     * @param string $dst 复制到的目录
     * @return string
     */                        
    //参数说明：            
    //自定义函数递归的复制带有多级子目录的目录
    private function recurse_copy($src, $dst)
    {
        $planPath_pc = 'template/pc/';
        $planPath_m = 'template/mobile/';
        $dir = opendir($src);

        /*pc和mobile目录存在的情况下，才拷贝会员模板到相应的pc或mobile里*/
        $dst_tmp = str_replace('\\', '/', $dst);
        $dst_tmp = rtrim($dst_tmp, '/').'/';
        if (stristr($dst_tmp, $planPath_pc) && file_exists($planPath_pc)) {
            tp_mkdir($dst);
        } else if (stristr($dst_tmp, $planPath_m) && file_exists($planPath_m)) {
            tp_mkdir($dst);
        }
        /*--end*/

        while (false !== $file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    if (file_exists($src . DIRECTORY_SEPARATOR . $file)) {
                        /*pc和mobile目录存在的情况下，才拷贝会员模板到相应的pc或mobile里*/
                        $rs = true;
                        $src_tmp = str_replace('\\', '/', $src . DIRECTORY_SEPARATOR . $file);
                        if (stristr($src_tmp, $planPath_pc) && !file_exists($planPath_pc)) {
                            continue;
                        } else if (stristr($src_tmp, $planPath_m) && !file_exists($planPath_m)) {
                            continue;
                        }
                        /*--end*/
                        $rs = @copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                        if($rs) {
                            @unlink($src . DIRECTORY_SEPARATOR . $file);
                        }
                    }
                }
            }
        }
        closedir($dir);
    }

    /**
     * 第一次同步会员等级数据和会员产品分类 【1.4.1版本可以删掉这个方法，以及调用这个方法的代码】
     */
    public function sys_level_data()
    {
        $system_synleveldata = tpCache('system.system_synleveldata');
        if (empty($system_synleveldata)) {
            $levelRow = Db::name('users_level')->where(['is_system'=>0])->select();
            if (empty($levelRow)) {
                $saveData = [
                    [
                        'level_name'    => '中级会员',
                        'level_value'   => 50,
                        'is_system'     => 0,
                        'discount'      => 100,
                        'posts_count'   => 10,
                        'lang'          => $this->admin_lang,
                        'add_time'      => getTime(),
                        'update_time'   => getTime(),
                    ],
                    [
                        'level_name'    => '高级会员',
                        'level_value'   => 100,
                        'is_system'     => 0,
                        'discount'      => 100,
                        'posts_count'   => 20,
                        'lang'          => $this->admin_lang,
                        'add_time'      => getTime(),
                        'update_time'   => getTime(),
                    ],
                ];
                Db::name('users_level')->insertAll($saveData);
            }

            $count = Db::name('users_type_manage')->count();
            if (empty($count)) {
                $levelRow = Db::name('users_level')->where(['is_system'=>0])->select();
                if (!empty($levelRow)) {
                    $saveData = [];
                    foreach ($levelRow as $key => $val) {
                        $saveData[] = [
                            'type_name'     => '升级为本站'.$val['level_name'],
                            'level_id'      => $val['level_id'],
                            'price'         => intval($val['level_value']) * 2,
                            'limit_id'      => intval($key) + 2,
                            'sort_order'    => 100,
                            'lang'          => $this->admin_lang,
                            'add_time'      => getTime(),
                            'update_time'   => getTime(),
                        ];
                    }
                    $r = Db::name('users_type_manage')->insertAll($saveData);
                    if ($r) {
                        /*多语言*/
                        if (is_language()) {
                            $langRow = \think\Db::name('language')->order('id asc')
                                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                ->select();
                            foreach ($langRow as $key => $val) {
                                tpCache('system', ['system_synleveldata'=>1], $val['mark']);
                            }
                        } else { // 单语言
                            tpCache('system', ['system_synleveldata'=>1]);
                        }
                        /*--end*/
                    }
                }
            }
        }
    }
}
