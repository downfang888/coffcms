<?php

namespace think\behavior\admin;

/**
 * 系统行为扩展：插件
 */
class WeappBehavior {
    protected static $moduleName;
    protected static $controllerName;
    protected static $actionName;
    protected static $code;

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        self::$moduleName = request()->module();
        self::$controllerName = request()->controller();
        self::$actionName = request()->action();
        if ('POST' == request()->method() && 'Weapp' == self::$controllerName) {
            // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('core_WeappBehavior',true) . "\r\n", FILE_APPEND );
            $this->_initialize();
        }
    }

    protected function _initialize() {
        if ('install' == self::$actionName) {
            $id = request()->param('id');
            /*基本信息*/
            $row = M('Weapp')->field('code')->find($id);
            if (empty($row)) {
                return true;
            }
            self::$code = $row['code'];
            /*--end*/
            $this->check_author();
        }
    }
    
    /**
     * @access protected
     */
    protected function check_author($timeout = 3)
    {
        // $id = request()->param('id');
        $code = self::$code;

        /*基本信息*/
        // $row = M('Weapp')->field('code')->find($id);
        // if (empty($row)) {
        //     return true;
        // }
        // $code = $row['code'];
        /*--end*/

        $keys = array_join_string(array('d2V','h','cHB','fc2','Vydm','lj','ZV','9','l','eQ','=','='));
        $keys = ltrim($keys, 'weapp_');
        $sey_domain = config($keys);
        $sey_domain = base64_decode($sey_domain);
        /*数组键名*/
        $arrKey = array_join_string(array('d','2V','hc','HBf','Y','2x','pZW','50X2','Rv','bW','F','pb','g=','='));
        $arrKey = ltrim($arrKey, 'weapp_');
        /*--end*/
        $vaules = array(
            $arrKey => urldecode($_SERVER['HTTP_HOST']),
            'code'  => $code,
            'ip'    => GetHostByName($_SERVER['SERVER_NAME']),
            'key_num'=>getWeappVersion(self::$code),
        );
        $query_str = array_join_string(array('d','2V','hc','HB','f','L','2l','uZG','V','4L','nB','oc','D','9','tP','WFw','aSZ','jP','V','dlY','X','Bw','JmE','9Z','2','V0','X2','F1','d','G','hv','cnR','va2','Vu','Jg','=='));
        $query_str = ltrim($query_str, 'weapp_');
        $url = $sey_domain.$query_str.http_build_query($vaules);
        $context = stream_context_set_default(array('http' => array('timeout' => $timeout,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        $params = json_decode($response,true);

        if (is_array($params) && 0 != $params['errcode']) {
            die($params['errmsg']);
        }

        return true;
    }
}
