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

$icon_arr = array(
    'article' => 'fa fa-file-text',
    'product'  => 'fa fa-cubes',
    'images'  => 'fa fa-file-picture-o',
    'download'  => 'fa fa-cloud-download',
    'single'  => 'fa fa-bookmark-o',
    'about'  => 'fa fa-minus',
    'job'  => 'fa fa-minus',
    'guestbook'  => 'fa fa-file-text-o',
    'feedback'  => 'fa fa-file-text-o',
);
$main_lang= get_main_lang();
$admin_lang = get_admin_lang();
$domain = request()->domain();
$default_words = array();
$default_addcontent = array();

// 获取有栏目的模型列表
$channel_list = model('Channeltype')->getArctypeChannel('yes');
foreach ($channel_list as $key => $val) {
    $default_words[] = array(
        'name'  => $val['ntitle'],
        'action'  => 'index',
        'controller'  => $val['ctl_name'],
        'url'  => $val['typelink'],
        'icon'  => $icon_arr[$val['nid']],
    );
    if (!in_array($val['nid'], array('single','guestbook','feedback'))) {
        $default_addcontent[] = array(
            'name'  => $val['ntitle'],
            'action'  => 'add',
            'controller'  => $val['ctl_name'],
            'url'  => $val['typelink'],
            'icon'  => $icon_arr[$val['nid']],
        );
    }
}

/*PC端可视编辑URl*/
$uiset_pc_url = '';
if (file_exists(ROOT_PATH.'template/pc/uiset.txt')) {
    $uiset_pc_url = url('Uiset/pc', array(), true, $domain);
}
/*--end*/

/*手机端可视编辑URl*/
$uiset_mobile_url = '';
if (file_exists(ROOT_PATH.'template/mobile/uiset.txt')) {
    $uiset_mobile_url = url('Uiset/mobile', array(), true, $domain);
}
/*--end*/

/*清理数据URl*/
$uiset_data_url = '';
if (!empty($uiset_pc_url) || !empty($uiset_mobile_url)) {
    $uiset_data_url = url('Uiset/ui_index', array(), true, $domain);
}
/*--end*/

/*可视编辑URL*/
$uiset_index_arr = array();
if (!empty($uiset_pc_url) || !empty($uiset_mobile_url)) {
    $uiset_index_arr = array(
        'controller' => 'Weapp',
        'action' => 'index',
        'url' => url('Uiset/index', array(), true, $domain),
    );
}
/*--end*/

/*SEO优化URl*/
$seo_index_arr = array();
if ($main_lang == $admin_lang) {
    $seo_index_arr = array(
        'controller' => 'Seo',
        'action' => 'index',
        'url' => '',
    );
}
/*--end*/

/*备份还原URl*/
$tools_index_arr = array();
if ($main_lang == $admin_lang) {
    $tools_index_arr = array(
        'controller' => 'Tools',
        'action' => 'index',
        'url' => '',
    );
}
/*--end*/

/*字段管理URl*/
$field_cindex_arr = array();
if ($main_lang == $admin_lang) {
    $field_cindex_arr = array(
        'controller' => 'Field',
        'action' => 'channel_index',
        'url' => '',
    );
}
/*--end*/

/*插件应用URl*/
$weapp_index_arr = array();
// $weappDirList = glob(ROOT_PATH.'weapp/*');
if (1 == tpCache('web.web_weapp_switch') && file_exists(ROOT_PATH.'weapp')) {
    $weapp_index_arr = array(
        'controller' => 'Weapp',
        'action' => 'index',
        'url' => '',
    );
}
/*--end*/

/**
 * 权限模块属性说明
 * array
 *      id  主键ID
 *      parent_id   父ID
 *      name    模块名称
 *      controller  控制器
 *      action  操作名
 *      url     跳转链接(控制器与操作名为空时，才使用url)
 *      target  打开窗口方式
 *      icon    菜单图标
 *      grade   层级
 *      is_menu 是否显示菜单
 *      is_modules  是否显示权限模块分组
 *      child   子模块
 */
return  array(
    '1000'=>array(
        'id'=>1000,
        'parent_id'=>0,
        'name'=>'',
        'controller'=>'',
        'action'=>'',
        'url'=>'',
        'target'=>'workspace',
        'grade'=>0,
        'is_menu'=>1,
        'is_modules'=>1,
        'child'=>array(
            '1001' => array(
                'id'=>1001,
                'parent_id'=>1000,
                'name' => '栏目管理',
                'controller'=>'Arctype',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-sitemap',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
            '1002' => array(
                'id'=>1002,
                'parent_id'=>1000,
                'name' => '内容管理',
                'controller'=>'Archives',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-list',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
            '1003' => array(
                'id'=>1003,
                'parent_id'=>1000,
                'name' => '广告管理',
                'controller'=>'Other',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-image',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
        ),
    ),
        
    '2000'=>array(
        'id'=>2000,
        'parent_id'=>0,
        'name'=>'设置',
        'controller'=>'',
        'action'=>'',
        'url'=>'', 
        'target'=>'workspace',
        'grade'=>0,
        'is_menu'=>1,
        'is_modules'=>1,
        'child'=>array(
            '2001' => array(
                'id'=>2001,
                'parent_id'=>2000,
                'name' => '基本信息',
                'controller'=>'System',
                'action'=>'web',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-cog',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
            '2002' => array(
                'id'=>2002,
                'parent_id'=>2000,
                'name' => '可视编辑',
                'controller'=>isset($uiset_index_arr['controller']) ? $uiset_index_arr['controller'] : '',
                'action'=>isset($uiset_index_arr['action']) ? $uiset_index_arr['action'] : '',
                'url'=>isset($uiset_index_arr['url']) ? $uiset_index_arr['url'] : '',
                'target'=>'workspace',
                'icon'=>'fa fa-tachometer',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child'=>array(
                    '2002001' => array(
                        'id'=>2002001,
                        'parent_id'=>2002,
                        'name' => '电脑版',
                        'controller'=>'',
                        'action'=>'',
                        'url'=>$uiset_pc_url, 
                        'target'=>'_blank',
                        'icon'=>'fa fa-desktop',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2002002' => array(
                        'id'=>2002002,
                        'parent_id'=>2002,
                        'name' => '手机版',
                        'controller'=>'',
                        'action'=>'',
                        'url'=>$uiset_mobile_url, 
                        'target'=>'_blank',
                        'icon'=>'fa fa-mobile',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2002003' => array(
                        'id'=>2002003,
                        'parent_id'=>2002,
                        'name' => '数据清理',
                        'controller'=>'Uiset',
                        'action'=>'ui_index',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2003' => array(
                'id'=>2003,
                'parent_id'=>2000,
                'name' => '营销设置',
                'controller'=>'Other',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-paper-plane',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child'=>array(
                    '2003001' => array(
                        'id'=>2003001,
                        'parent_id'=>2003,
                        'name' => 'SEO优化', 
                        'controller'=>isset($seo_index_arr['controller']) ? $seo_index_arr['controller'] : '',
                        'action'=>isset($seo_index_arr['action']) ? $seo_index_arr['action'] : '',
                        'url'=>isset($seo_index_arr['url']) ? $seo_index_arr['url'] : '',
                        'target'=>'workspace',
                        'icon'=>'fa fa-newspaper-o',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2003002' => array(
                        'id'=>2003002,
                        'parent_id'=>2003,
                        'name' => '友情链接', 
                        'controller'=>'Links',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-link',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2004' => array(
                'id'=>2004,
                'parent_id'=>2000,
                'name' => '高级选项',
                'controller'=>'Senior',
                'action'=>'',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-code',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(
                    '2004001' => array(
                        'id'=>2004001,
                        'parent_id'=>2004,
                        'name' => '管理员', 
                        'controller'=>'Admin',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-user',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    // '2004006' => array(
                    //     'id'=>2004006,
                    //     'parent_id'=>2004,
                    //     'name' => '多语言', 
                    //     'controller'=>'Language',
                    //     'action'=>'index', 
                    //     'url'=>'', 
                    //     'target'=>'workspace',
                    //     'icon'=>'fa fa-globe',
                    //     'grade'=>2,
                    //     'is_menu'=>1,
                    //     'is_modules'=>0,
                    //     'child' => array(),
                    // ),
                    '2004002' => array(
                        'id'=>2004002,
                        'parent_id'=>2004,
                        'name' => '备份还原', 
                        'controller'=>isset($tools_index_arr['controller']) ? $tools_index_arr['controller'] : '',
                        'action'=>isset($tools_index_arr['action']) ? $tools_index_arr['action'] : '',
                        'url'=>isset($tools_index_arr['url']) ? $tools_index_arr['url'] : '',
                        'target'=>'workspace',
                        'icon'=>'fa fa-database',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004003' => array(
                        'id'=>2004003,
                        'parent_id'=>2004,
                        'name' => '模板管理', 
                        'controller'=>'Filemanager',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-folder-open',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004004' => array(
                        'id'=>2004004,
                        'parent_id'=>2004,
                        'name' => '字段管理', 
                        'controller'=>isset($field_cindex_arr['controller']) ? $field_cindex_arr['controller'] : '',
                        'action'=>isset($field_cindex_arr['action']) ? $field_cindex_arr['action'] : '',
                        'url'=>isset($field_cindex_arr['url']) ? $field_cindex_arr['url'] : '',
                        'target'=>'workspace',
                        'icon'=>'fa fa-cogs',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004005' => array(
                        'id'=>2004005,
                        'parent_id'=>2004,
                        'name' => '清除缓存',
                        'controller'=>'System',
                        'action'=>'clear_cache', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2005' => array(
                'id'=>2005,
                'parent_id'=>2000,
                'name' => '插件应用',
                'controller'=>isset($weapp_index_arr['controller']) ? $weapp_index_arr['controller'] : '',
                'action'=>isset($weapp_index_arr['action']) ? $weapp_index_arr['action'] : '',
                'url'=>isset($weapp_index_arr['url']) ? $weapp_index_arr['url'] : '',
                'target'=>'workspace',
                'icon'=>'fa fa-recycle',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>0,
                'child'=>array(),
            ),
        ),
    ),
);