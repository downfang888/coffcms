<?php

namespace think\behavior;

/**
 * 
 */
class TestBehavior {

    // 行为扩展的执行入口必须是run
    public function run(&$params){
/*        if (!defined('BIND_MODULE')) {
            throw new \Exception("非法访问，系统尚未绑定插件模块");
        }*/
        
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('core_InitHookBehavior',true) . "\r\n", FILE_APPEND );
    }
}
