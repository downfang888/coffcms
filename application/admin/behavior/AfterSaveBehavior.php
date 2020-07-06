<?php

namespace app\admin\behavior;

/**
 * 系统行为扩展：新增/更新/删除之后的后置操作
 */
class AfterSaveBehavior {
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;

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
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_AfterSaveBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize();
    }

    protected function _initialize() {
        if ('POST' == request()->method()) {
            $this->clearArchives();
            $this->sitemap();
        }
    }

    /**
     * 清除小程序插件缓存
     * @access public
     */
    protected function clearArchives()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctlArr = array(
            'Arctype',
            'Article',
            'Product',
            'Images',
            'Download',
            'Guestbook',
        );
        $actArr = array(
            'add',
            'edit',
            'del',
            'attribute_add',
            'attribute_edit',
            'attribute_del',
        );
        if (in_array(self::$controllerName, $ctlArr) && in_array(self::$actionName, $actArr)) {
            \think\Cache::clear('minipro');
        }
        /*--end*/
    }

    /**
     * 自动生成sitemap
     * @access public
     */
    protected function sitemap()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctlActArr = array(
            'Article@add',
            'Product@add',
            'Images@add',
            'Download@add',
        );
        $ctlActStr = self::$controllerName.'@'.self::$actionName;
        if (in_array($ctlActStr, $ctlActArr)) {
            sitemap_auto();
        }
        /*--end*/
    }
}
