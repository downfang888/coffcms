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

$home_rewrite = array();
$route = array(
    '__pattern__' => array(
        'tid' => '\w+',
        'aid' => '\d+',
    ),
    '__alias__' => array(),
    '__domain__' => array(),
);

$globalConfig = tpCache('global');
// mysql的sql-mode模式参数
$system_sql_mode = !empty($globalConfig['system_sql_mode']) ? $globalConfig['system_sql_mode'] : config('ey_config.system_sql_mode');
config('ey_config.system_sql_mode', $system_sql_mode);
// 多语言数量
$system_langnum = !empty($globalConfig['system_langnum']) ? intval($globalConfig['system_langnum']) : config('ey_config.system_langnum');
config('ey_config.system_langnum', $system_langnum);
// 前台默认语言
$system_home_default_lang = !empty($globalConfig['system_home_default_lang']) ? $globalConfig['system_home_default_lang'] : config('ey_config.system_home_default_lang');
config('ey_config.system_home_default_lang', $system_home_default_lang);
// URL模式
$seo_pseudo = !empty($globalConfig['seo_pseudo']) ? intval($globalConfig['seo_pseudo']) : config('ey_config.seo_pseudo');

$uiset = I('param.uiset/s', 'off');
if ('on' == trim($uiset, '/')) { // 可视化页面必须是兼容模式的URL
    config('ey_config.seo_inlet', 0);
    config('ey_config.seo_pseudo', 1);
    config('ey_config.seo_dynamic_format', 1);
} else {
    // URL模式
    config('ey_config.seo_pseudo', $seo_pseudo);
    // 动态URL格式
    $seo_dynamic_format = !empty($globalConfig['seo_dynamic_format']) ? intval($globalConfig['seo_dynamic_format']) : config('ey_config.seo_dynamic_format');
    config('ey_config.seo_dynamic_format', $seo_dynamic_format);
    // 伪静态格式
    $seo_rewrite_format = !empty($globalConfig['seo_rewrite_format']) ? intval($globalConfig['seo_rewrite_format']) : config('ey_config.seo_rewrite_format');
    config('ey_config.seo_rewrite_format', $seo_rewrite_format); 
    // 是否隐藏入口文件
    $seo_inlet = !empty($globalConfig['seo_inlet']) ? $globalConfig['seo_inlet'] : config('ey_config.seo_inlet');
    config('ey_config.seo_inlet', $seo_inlet);

    if (3 == $seo_pseudo) {
        $lang_rewrite = [];
        $lang_rewrite_str = '';
        /*多语言*/
        $lang = input('param.lang/s');
        if (is_language()) {
            if (!stristr($request->baseFile(), 'index.php')) {
                if (!empty($lang) && $lang != $system_home_default_lang) {
                    $lang_rewrite_str = '<lang>/';
                    $lang_rewrite = [
                        // 首页
                        $lang_rewrite_str.'$' => array('home/Index/index',array('method' => 'get', 'ext' => ''), 'cache'=>1),
                    ];
                }
            } else {
                if (get_current_lang() != get_default_lang()) {
                    $lang_rewrite_str = '<lang>/';
                    $lang_rewrite = [
                        // 首页
                        $lang_rewrite_str.'$' => array('home/Index/index',array('method' => 'get', 'ext' => ''), 'cache'=>1),
                    ];
                }
            }
        }
        /*--end*/
        if (1 == $seo_rewrite_format) { // 精简伪静态
            $home_rewrite = array(
                // 列表页
                $lang_rewrite_str.'<tid>$' => array('home/Lists/index',array('method' => 'get', 'ext' => ''), 'cache'=>1),
                // 内容页
                $lang_rewrite_str.'<dirname>/<aid>$' => array('home/View/index',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 留言提交
                $lang_rewrite_str.'guestbook/submit$' => array('home/Lists/gbook_submit',array('method' => 'post', 'ext' => 'html'), 'cache'=>1),
                // 下载文件
                $lang_rewrite_str.'downfile/<id>/<uhash>$' => array('home/View/downfile',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 标签伪静态
                $lang_rewrite_str.'tags$' => array('home/Tags/index',array('method' => 'get', 'ext' => ''), 'cache'=>1),
                $lang_rewrite_str.'tags/<tagid>$' => array('home/Tags/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                // 搜索伪静态
                $lang_rewrite_str.'search$' => array('home/Search/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
            );
        } else {
            $home_rewrite = array(
                // 文章模型伪静态
                $lang_rewrite_str.'article$' => array('home/Article/index',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'article/<tid>$' => array('home/Article/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'article/<dirname>/<aid>$' => array('home/Article/view',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 产品模型伪静态
                $lang_rewrite_str.'product$' => array('home/Product/index',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'product/<tid>$' => array('home/Product/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'product/<dirname>/<aid>$' => array('home/Product/view',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 图集模型伪静态
                $lang_rewrite_str.'images$' => array('home/Images/index',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'images/<tid>$' => array('home/Images/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'images/<dirname>/<aid>$' => array('home/Images/view',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 下载模型伪静态
                $lang_rewrite_str.'download$' => array('home/Download/index',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'download/<tid>$' => array('home/Download/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'download/<dirname>/<aid>$' => array('home/Download/view',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                $lang_rewrite_str.'downfile/<id>/<uhash>$' => array('home/View/downfile',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 单页模型伪静态
                $lang_rewrite_str.'single$' => array('home/Single/index',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'single/<tid>$' => array('home/Single/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'single/<dirname>/<aid>$' => array('home/Single/view',array('method' => 'get', 'ext' => 'html'),'cache'=>1),
                // 标签伪静态
                $lang_rewrite_str.'tags$' => array('home/Tags/index',array('method' => 'get', 'ext' => ''), 'cache'=>1),
                $lang_rewrite_str.'tags/<tagid>$' => array('home/Tags/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                // 搜索伪静态
                $lang_rewrite_str.'search$' => array('home/Search/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                // 留言模型
                $lang_rewrite_str.'guestbook$' => array('home/Guestbook/index',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'guestbook/<tid>$' => array('home/Guestbook/lists',array('method' => 'get', 'ext' => 'html'), 'cache'=>1),
                $lang_rewrite_str.'guestbook/submit$' => array('home/View/submit',array('method' => 'post', 'ext' => 'html'), 'cache'=>1),
            );
        }
        $home_rewrite = array_merge($lang_rewrite, $home_rewrite);
    }

    /*插件模块路由*/
    $weapp_route_file = 'weapp/route.php';
    if (file_exists(APP_PATH.$weapp_route_file)) {
        $weapp_route = include_once $weapp_route_file;
        $route = array_merge($weapp_route, $route);
    }
    /*--end*/
}

$route = array_merge($route, $home_rewrite);

return $route;
