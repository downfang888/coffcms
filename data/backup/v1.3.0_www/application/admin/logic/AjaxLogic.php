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

    /**
     * 析构函数
     */
    function  __construct() {
        $this->request = request();
    }

    /**
     * 进入登录页面需要异步处理的业务
     */
    public function login_handle()
    {
        $this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
        $this->update_robots(); // 自动给rotots.txt站点地图的sitemap.xml补充域名
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
    }
    
    /**
     * 只保留最近三个月的操作日志
     */
    private function del_adminlog()
    {
        $mtime = strtotime("-3 month");
        Db::name('admin_log')->where([
            'log_time'  => ['lt', $mtime],
            ])->delete();
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
     * 自动给rotots.txt站点地图的sitemap.xml补充域名
     */
    private function update_robots()
    {
        $filename = 'robots.txt';
        if (file_exists($filename) && is_file($filename)) {
            $robots = @file_get_contents(ROOT_PATH . $filename);
            if (!stristr($robots, $this->request->domain().'/sitemap.xml')) {
                $robots = preg_replace('#Sitemap:(.*)(/)?sitemap.xml#i', 'Sitemap: '.$this->request->domain().'/sitemap.xml', $robots);
                is_writable($filename) && @file_put_contents($filename, $robots);
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
            $files = glob($path.'/sess_*');
            foreach ($files as $key => $file) {
                $filemtime = filemtime($file);
                if (getTime() - intval($filemtime) > config('login_expire')) {
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
}
