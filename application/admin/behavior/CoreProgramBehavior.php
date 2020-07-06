<?php

namespace app\admin\behavior;

/**
 * 系统行为扩展：
 */
class CoreProgramBehavior {
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
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_CoreProgramBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize();
    }

    protected function _initialize() {
        $this->setChanneltypeStatus();
    }

    /**
     * 根据前端模板自动开启系统模型
     */
    protected function setChanneltypeStatus()
    {
        /*不在以下相应的控制器和操作名里不往下执行，以便提高性能*/
        $ctlActArr = array(
            'Index@index',
            'System@clearCache',
        );
        $ctlActStr = self::$controllerName.'@'.self::$actionName;
        if (!in_array($ctlActStr, $ctlActArr)) {
            return false;
        }
        /*--end*/
        
        $planPath = 'template/pc';
        $planPath = realpath($planPath);
        if (!file_exists($planPath)) {
            return false;
        }
        $ctl_name_arr = array();
        $dirRes   = opendir($planPath);
        $view_suffix = config('template.view_suffix');
        while($filename = readdir($dirRes))
        {
            if(preg_match('/^(lists|view)?_/i', $filename) == 1)
            {
                $tplname = preg_replace('/([^_]+)?_([^\.]+)\.'.$view_suffix.'$/i', '${2}', $filename);
                $ctl_name_arr[] = ucwords($tplname);
            } elseif (preg_match('/\.'.$view_suffix.'$/i', $filename) == 1) {
                $tplname = preg_replace('/\.'.$view_suffix.'$/i', '', $filename);
                $ctl_name_arr[] = ucwords($tplname);
            }
        }
        $ctl_name_arr = array_unique($ctl_name_arr);

        if (!empty($ctl_name_arr)) {
            M('Channeltype')->where('id > 0')->cache(true,null,"channeltype")->update(array('status'=>0, 'update_time'=>getTime()));
            $map = array(
                'ctl_name'  => array('IN', $ctl_name_arr),
            );
            M('Channeltype')->where($map)->cache(true,null,"channeltype")->update(array('status'=>1, 'update_time'=>getTime()));
        } 
    }
}
