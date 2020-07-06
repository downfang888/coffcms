<?php

namespace app\admin\behavior;

/**
 * 系统行为扩展：
 */
class AppEndBehavior {
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;
    protected static $method;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct()
    {

    }

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        self::$actionName = request()->action();
        self::$controllerName = request()->controller();
        self::$moduleName = request()->module();
        self::$method = request()->method();
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_CoreProgramBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize();
    }

    private function _initialize() {
        $this->saveBaseFile(); // 存储后台入口文件路径
        $this->renameInstall(); // 重命名安装目录
        $this->resetAuthor(); // 临时处理授权问题
        $this->sitemap(); // 自动生成sitemap
    }

    /**
     * 自动生成sitemap
     * @access public
     */
    private function sitemap()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctlArr = ['Arctype','Article','Product','Images','Download'];
        $actArr = ['add','edit'];
        if ('POST' == self::$method && in_array(self::$controllerName, $ctlArr) && in_array(self::$actionName, $actArr)) {
            sitemap_auto();
        }
        /*--end*/
    }

    /**
     * 临时处理授权问题
     */
    private function resetAuthor()
    {
        /*在以下相应的控制器和操作名里执行，以便提高性能*/
        $ctlActArr = array(
            'Index@index',
        );
        $ctlActStr = self::$controllerName.'@'.self::$actionName;
        if (in_array($ctlActStr, $ctlActArr) && 'GET' == self::$method) {
            if(!empty($_SESSION['isset_resetAuthor']))
                return false;
            $_SESSION['isset_resetAuthor'] = 1;

            session('isset_author', null);
        }
        /*--end*/
    }

    /**
     * 存储后台入口文件路径，比如：/login.php
     */
    private function saveBaseFile()
    {
        /*在以下相应的控制器和操作名里执行，以便提高性能*/
        $ctlActArr = array(
            'Admin@login',
            'Index@index',
        );
        $ctlActStr = self::$controllerName.'@'.self::$actionName;
        if (in_array($ctlActStr, $ctlActArr) && 'GET' == self::$method) {
            $baseFile = request()->baseFile();
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', ['web_adminbasefile'=>$baseFile], $val['mark']);
                }
            } else { // 单语言
                tpCache('web', ['web_adminbasefile'=>$baseFile]);
            }
            /*--end*/
        }
        /*--end*/
    }

    /**
     * 重命名安装目录，提高网站安全性
     */
    private function renameInstall()
    {
        /*在以下相应的控制器和操作名里执行，以便提高性能*/
        $ctlActArr = array(
            'Admin@login',
            'Index@index',
        );
        $ctlActStr = self::$controllerName.'@'.self::$actionName;
        if (in_array($ctlActStr, $ctlActArr) && 'GET' == self::$method) {
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
                    return false;
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
    }
}
