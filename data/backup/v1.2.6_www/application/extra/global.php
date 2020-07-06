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

return array(
    // CMS根目录文件夹
    'wwwroot_dir' => ['application','core','data','extend','html','public','template','uploads','vendor','weapp'],
    // 一天的时间戳
    'one_day_time'  => 86400,
    // 发送短信默认有效时间
    'sms_default_time_out' => 120,
    // 发送邮箱默认有效时间
    'email_default_time_out' => 180,
    // 栏目最多级别
    'arctype_max_level' => 3,
    // 模型标识
    'channeltype_list' => array(
        // 文章模型标识
        'article' => 1,
        // 产品模型标识
        'product' => 2,
        // 图片集模型
        'images'    => 3,
        // 下载模型标识
        'download' => 4,
        // 单页模型标识
        'single' => 6,
        // 留言模型标识
        'guestbook' => 8,
    ),
    // 发布文档的模型ID
    'allow_release_channel' => array(1,2,3,4),
    // 广告类型
    'ad_media_type' => array(
        1   => '图片',
        // 2   => 'flash',
        // 3   => '文字',
    ),
    'attr_input_type_arr' => array(
        0   => '单行文本',
        1   => '下拉框',
        2   => '多行文本',
        3   => 'HTML文本',
    ),
    // 栏目自定义字段的channel_id值
    'arctype_channel_id' => -99,
    // 栏目表原始字段
    'arctype_table_fields' => array('id','channeltype','current_channel','parent_id','typename','dirname','dirpath','englist_name','grade','typelink','litpic','templist','tempview','seo_title','seo_keywords','seo_description','sort_order','is_hidden','is_part','admin_id','is_del','status','lang','add_time','update_time'),
    // 网络图片扩展名
    'image_ext' => 'jpg,jpeg,gif,bmp,ico,png',
    // 后台语言Cookie变量
    'admin_lang' => 'admin_lang',
    // 前台语言Cookie变量
    'home_lang' => 'home_lang',
    // URL全局参数（比如：可视化uiset、多模板v、多语言lang）
    'parse_url_param'   => ['uiset','v','lang'],
    // 清理文件时，需要查询的数据表和字段
    'get_tablearray' => array(
        0 => array(
            'table' => 'ad',
            'field' => 'litpic',
        ),
        1 => array(
            'table' => 'archives',
            'field' => 'litpic',
        ),
        2 => array(
            'table' => 'arctype',
            'field' => 'litpic',
        ),
        3 => array(
            'table' => 'images_upload',
            'field' => 'image_url',
        ),
        4 => array(
            'table' => 'links',
            'field' => 'logo',
        ),
        5 => array(
            'table' => 'product_img',
            'field' => 'image_url',
        ),
        6 => array(
            'table' => 'ad',
            'field' => 'intro',
        ),
        7 => array(
            'table' => 'article_content',
            'field' => 'content',
        ),
        8 => array(
            'table' => 'download_content',
            'field' => 'content',
        ),
        9 => array(
            'table' => 'images_content',
            'field' => 'content',
        ),
        10 => array(
            'table' => 'product_content',
            'field' => 'content',
        ),
        11 => array(
            'table' => 'single_content',
            'field' => 'content',
        ),
        12 => array(
            'table' => 'config',
            'field' => 'value',
        ),
        13 => array(
            'table' => 'ui_config',
            'field' => 'value',
        ),
        14 => array(
            'table' => 'download_file',
            'field' => 'file_url',
        ),
        15 => array(
            'table' => 'weapp_minipro0001',
            'field' => 'value',
        ),
        16 => array(
            'table' => 'weapp',
            'field' => 'data',
        ),
        // 后续可持续添加数据表和字段，格式参照以上
    ),
);
