<?php

namespace app\admin\behavior;

/**
 * 系统行为扩展：入口index.php的检测自动隐藏
 */
class TestingBehavior {
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
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_TestingBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize();
    }

    protected function _initialize() {
        $this->checkInlet();
    }

    /**
     * 检测url入口index.php是否被重写隐藏
     * @access public
     */
    static public function checkInlet() {
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

        $now_seo_inlet = 0; // 默认不隐藏入口
        
        /*检测是否支持URL重写隐藏应用的入口文件index.php*/
        try {
            $response = false;
            $url = SITE_URL.'/api/Rewrite/testing.html';
            $context = stream_context_set_default(array('http' => array('timeout' => 3,'method'=>'GET')));
            $response = @file_get_contents($url,false,$context);
/*            $ch = curl_init($url);            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 设置cURL允许执行的最长秒数
            $response = curl_exec ($ch);
            curl_close ($ch);  */
            if ($response == 'ok') {
                $now_seo_inlet = 1;
            }
        } catch (Exception $e) {}
        /*--end*/

        $seo_inlet = tpCache('seo.seo_inlet');
        if ($seo_inlet != $now_seo_inlet) {
            tpCache('seo', array('seo_inlet'=>$now_seo_inlet));
        }
    }

    /**
     * 根据IP判断是否本地局域网访问
     * @access public
     */
    static protected function is_local($ip){
        if(preg_match('/^(localhost|127\.|192\.)/', $ip) === 1){  
            return true;      
        }else{  
            return false;     
        }     
    }
}
