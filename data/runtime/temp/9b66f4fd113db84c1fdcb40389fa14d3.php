<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:40:"./application/admin/template/seo\seo.htm";i:1590023511;s:75:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\layout.htm";i:1590023511;s:69:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\seo\bar.htm";i:1590023511;s:75:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\footer.htm";i:1545615936;}*/ ?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link href="/public/plugins/layui/css/layui.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css">
<link href="/public/static/admin/css/main.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css">
<link href="/public/static/admin/css/page.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css">
<link href="/public/static/admin/font/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="/public/static/admin/font/css/font-awesome-ie7.min.css">
<![endif]-->
<script type="text/javascript">
    var eyou_basefile = "<?php echo \think\Request::instance()->baseFile(); ?>";
    var module_name = "<?php echo MODULE_NAME; ?>";
    var GetUploadify_url = "<?php echo url('Uploadify/upload'); ?>";
    var __root_dir__ = "";
    var __lang__ = "<?php echo $admin_lang; ?>";
</script>  
<link href="/public/static/admin/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<link href="/public/static/admin/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css"/>
<style type="text/css">html, body { overflow: visible;}</style>
<script type="text/javascript" src="/public/static/admin/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/plugins/layer-v3.1.0/layer.js"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/public/static/admin/js/admin.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="/public/static/admin/js/common.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/public/static/admin/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/public/plugins/layui/layui.js"></script>
<script src="/public/static/admin/js/myFormValidate.js"></script>
<script src="/public/static/admin/js/myAjax2.js?v=<?php echo $version; ?>"></script>
<script src="/public/static/admin/js/global.js?v=<?php echo $version; ?>"></script>
<link href="/public/static/admin/css/diy_style.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css" />
</head>
<body class="bodystyle">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page atta">
        <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>SEO设置</h3>
                <h5></h5>
            </div>
            <ul class="tab-base nc-row">
                <?php if($main_lang == $admin_lang): if(is_check_access('Seo@seo') == '1'): ?>
                <li><a href="<?php echo url('Seo/seo'); ?>" <?php if('seo'==ACTION_NAME): ?>class="current"<?php endif; ?>><span>URL配置</span></a></li>
                <?php endif; endif; if($main_lang == $admin_lang): if(is_check_access('Sitemap@index') == '1'): ?>
                <li><a href="<?php echo url('Sitemap/index'); ?>" <?php if('Sitemap'==CONTROLLER_NAME): ?>class="current"<?php endif; ?>><span>Sitemap</span></a></li>
                <?php endif; endif; if(is_check_access('Links@index') == '1'): ?>
                <li><a href="<?php echo url('Links/index'); ?>" <?php if('Links'==CONTROLLER_NAME): ?>class="current"<?php endif; ?>><span>友情链接</span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <form method="post" id="handlepost" action="<?php echo url('Seo/handle'); ?>" enctype="multipart/form-data" name="form1">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="seo_pseudo">URL模式</label>
                </dt>
                <dd class="opt">
                    <?php if(is_array($seo_pseudo_list) || $seo_pseudo_list instanceof \think\Collection || $seo_pseudo_list instanceof \think\Paginator): $i = 0; $__LIST__ = $seo_pseudo_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <label>
                        <input type="radio" name="seo_pseudo" value="<?php echo $key; ?>" <?php if(isset($config['seo_pseudo']) && $config['seo_pseudo'] == $key): ?>checked="checked"<?php endif; ?>/><?php echo $vo; ?>&nbsp;
                    </label>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    <input type="hidden" id="seo_pseudo_old" value="<?php echo (isset($config['seo_pseudo']) && ($config['seo_pseudo'] !== '')?$config['seo_pseudo']:'1'); ?>"/>
                    <input type="hidden" name="seo_html_arcdir_limit" value="<?php echo $seo_html_arcdir_limit; ?>"/>
                    <input type="hidden" id="seo_inlet" value="<?php echo $config['seo_inlet']; ?>"/>
                    <input type="hidden" id="init_html" value="<?php echo (isset($init_html) && ($init_html !== '')?$init_html:'1'); ?>"/>
                </dd>
            </dl>
            <dl class="row <?php if(empty($config['seo_pseudo']) || 1 != $config['seo_pseudo'] || (1 == $config['seo_pseudo'] && 1 == $config['seo_dynamic_format'])): ?>none<?php endif; ?>" id="dl_seo_dynamic_format">
                <dt class="tit">
                    <label>动态格式</label>
                </dt>
                <dd class="opt">
                    <label><input type="radio" name="seo_dynamic_format" value="1" <?php if(!isset($config['seo_dynamic_format']) OR $config['seo_dynamic_format'] == 1): ?>checked="checked"<?php endif; ?>>完全兼容（<a href="javascript:void(0);" onclick="view_exp('view_1_1');">查看例子</a><span id="view_1_1" class="none">：http://www.a1.com/index.php?m=home&amp;c=Lists&amp;a=index&amp;tid=1</span>）</label>&nbsp;
                    <?php if(isset($config['seo_dynamic_format']) AND $config['seo_dynamic_format'] == 2): ?>
                    <br/>
                    <label><input type="radio" name="seo_dynamic_format" value="2" checked="checked">部分兼容&nbsp;<font color="red">[部分空间不支持]</font>（<a href="javascript:void(0);" onclick="view_exp('view_1_2');">查看例子</a><span id="view_1_2" class="none">：http://www.a1.com/home/Lists/index.html?tid=1</span>）</label>&nbsp;
                    <?php endif; ?>
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="row <?php if(isset($config['seo_pseudo']) && $config['seo_pseudo'] != 2): ?>none<?php endif; ?>" id="dl_seo_html_format">
                <?php if(!(empty($seo_pseudo_lang) || (($seo_pseudo_lang instanceof \think\Collection || $seo_pseudo_lang instanceof \think\Paginator ) && $seo_pseudo_lang->isEmpty()))): ?>
                <dl class="row">
                    <dt class="tit">
                        <label for="seo_pseudo_lang">多语言URL</label>
                    </dt>
                     <dd class="opt">
                        <label><input type="radio" name="seo_pseudo_lang" value="1" <?php if(!isset($seo_pseudo_lang) OR $seo_pseudo_lang != 3): ?>checked="checked"<?php endif; ?>/>动态URL</label>&nbsp;
                        <label><input type="radio" name="seo_pseudo_lang" value="3" <?php if(isset($seo_pseudo_lang) AND $seo_pseudo_lang == 3): ?>checked="checked"<?php endif; ?>>伪静态化</label>&nbsp;
                        <span class="err"></span>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <?php endif; ?>
                <dl class="row">
                    <dt class="tit">
                        <label for="seo_html_arcdir">页面保存目录</label>
                    </dt>
                    <dd class="opt">
                        <input id="seo_html_arcdir" name="seo_html_arcdir" value="<?php echo (isset($config['seo_html_arcdir']) && ($config['seo_html_arcdir'] !== '')?$config['seo_html_arcdir']:''); ?>" placeholder="留空默认根目录" type="text" />
                        （如：html）
                        <p class="notic">填写需要生成静态页面的文件夹名称，不能包含特殊字符，不能和根目录系统文件夹重名</p>
                        <p class="<?php if(empty($seo_html_arcdir_1) || (($seo_html_arcdir_1 instanceof \think\Collection || $seo_html_arcdir_1 instanceof \think\Paginator ) && $seo_html_arcdir_1->isEmpty())): ?>none<?php endif; ?>" style="color: red;" id="tips_seo_html_arcdir_1">页面将保存在 http://www.a1.com<span id="tips_seo_html_arcdir_2"><?php echo (isset($seo_html_arcdir_1) && ($seo_html_arcdir_1 !== '')?$seo_html_arcdir_1:''); ?></span>/</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label>列表页面名称</label>
                    </dt>
                    <dd class="opt">
                        <label><input type="radio" name="seo_html_listname" value="1" <?php if(isset($config['seo_html_listname']) && $config['seo_html_listname'] == 1): ?>checked="checked"<?php endif; ?>><?php if(!(empty($root_dir) || (($root_dir instanceof \think\Collection || $root_dir instanceof \think\Paginator ) && $root_dir->isEmpty()))): ?>子目录名称/<?php endif; ?>顶级目录名称/lists_ID.html（<a href="javascript:void(0);" onclick="view_exp('lists_2_1');">查看例子</a><span id="lists_2_1" class="none">：http://www.a1.com<span id="exp_seo_html_arcdir"><?php echo $seo_html_arcdir_1; ?></span>/news/lists_1.html</span>）</label>&nbsp;
                        <br/>
                        <label><input type="radio" name="seo_html_listname" value="2" <?php if(!isset($config['seo_html_listname']) || $config['seo_html_listname'] != 1): ?>checked="checked"<?php endif; ?>><?php if(!(empty($root_dir) || (($root_dir instanceof \think\Collection || $root_dir instanceof \think\Paginator ) && $root_dir->isEmpty()))): ?>子目录名称/<?php endif; ?>父级目录名称/子目录名称/（<a href="javascript:void(0);" onclick="view_exp('lists_2_2');">查看例子</a><span id="lists_2_2" class="none">：http://www.a1.com<span id="exp_seo_html_arcdir"><?php echo $seo_html_arcdir_1; ?></span>/news/lol/</span>）</label>&nbsp;
                        <br/>
                        <label><input type="radio" name="seo_html_listname" value="3" <?php if(isset($config['seo_html_listname']) && $config['seo_html_listname'] == 3): ?>checked="checked"<?php endif; ?>><?php if(!(empty($root_dir) || (($root_dir instanceof \think\Collection || $root_dir instanceof \think\Paginator ) && $root_dir->isEmpty()))): ?>子目录名称/<?php endif; ?>子目录名称/（<a href="javascript:void(0);" onclick="view_exp('lists_2_3');">查看例子</a><span id="lists_2_3" class="none">：http://www.a1.com<span id="exp_seo_html_arcdir"><?php echo $seo_html_arcdir_1; ?></span>/lol/</span>）</label>&nbsp;
                        <span class="err"></span>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label>文档页面名称</label>
                    </dt>
                    <dd class="opt">
                        <label><input type="radio" name="seo_html_pagename" value="1" <?php if(isset($config['seo_html_pagename']) && $config['seo_html_pagename'] == 1): ?>checked="checked"<?php endif; ?>><?php if(!(empty($root_dir) || (($root_dir instanceof \think\Collection || $root_dir instanceof \think\Paginator ) && $root_dir->isEmpty()))): ?>子目录名称/<?php endif; ?>顶级目录名称/ID.html（<a href="javascript:void(0);" onclick="view_exp('view_2_3');">查看例子</a><span id="view_2_3" class="none">：http://www.a1.com<span id="exp_seo_html_arcdir"><?php echo $seo_html_arcdir_1; ?></span>/news/10.html</span>）</label>&nbsp;
                        <br/>
                        <label><input type="radio" name="seo_html_pagename" value="2" <?php if(!isset($config['seo_html_pagename']) || $config['seo_html_pagename'] != 1): ?>checked="checked"<?php endif; ?>><?php if(!(empty($root_dir) || (($root_dir instanceof \think\Collection || $root_dir instanceof \think\Paginator ) && $root_dir->isEmpty()))): ?>子目录名称/<?php endif; ?>父级目录名称/子目录名称/ID.html（<a href="javascript:void(0);" onclick="view_exp('view_2_4');">查看例子</a><span id="view_2_4" class="none">：http://www.a1.com<span id="exp_seo_html_arcdir"><?php echo $seo_html_arcdir_1; ?></span>/news/lol/20.html</span>）</label>&nbsp;
                        <br/>
                        <label><input type="radio" name="seo_html_pagename" value="3" <?php if(isset($config['seo_html_pagename']) && $config['seo_html_pagename'] == 3): ?>checked="checked"<?php endif; ?>><?php if(!(empty($root_dir) || (($root_dir instanceof \think\Collection || $root_dir instanceof \think\Paginator ) && $root_dir->isEmpty()))): ?>子目录名称/<?php endif; ?>子目录名称/ID.html（<a href="javascript:void(0);" onclick="view_exp('view_2_5');">查看例子</a><span id="view_2_5" class="none">：http://www.a1.com<span id="exp_seo_html_arcdir"><?php echo $seo_html_arcdir_1; ?></span>/lol/20.html</span>）</label>&nbsp;
                        <span class="err"></span>
                        <p class="notic"></p>
                    </dd>
                </dl>
            </div>
            <dl class="row <?php if(isset($config['seo_pseudo']) && $config['seo_pseudo'] != 3): ?>none<?php endif; ?>" id="dl_seo_rewrite_format">
                <dt class="tit">
                    <label>伪静态格式</label>
                </dt>
                <dd class="opt">
                    <label><input type="radio" name="seo_rewrite_format" value="1" <?php if(!isset($config['seo_rewrite_format']) OR $config['seo_rewrite_format'] == 1): ?>checked="checked"<?php endif; ?>>目录名称（<a href="javascript:void(0);" onclick="view_exp('view_3_1');">查看例子</a><span id="view_3_1" class="none">：http://www.a1.com/about/</span>）</label>&nbsp;
                    <br/>
                    <label><input type="radio" name="seo_rewrite_format" value="2" <?php if(isset($config['seo_rewrite_format']) AND $config['seo_rewrite_format'] == 2): ?>checked="checked"<?php endif; ?>>模型标识（<a href="javascript:void(0);" onclick="view_exp('view_3_2');">查看例子</a><span id="view_3_2" class="none">：http://www.a1.com/single/about.html</span>）</label>&nbsp;
                    <br/>
                    <label><input type="radio" name="seo_rewrite_format" value="3" <?php if(isset($config['seo_rewrite_format']) AND $config['seo_rewrite_format'] == 3): ?>checked="checked"<?php endif; ?>>子目录名称（<a href="javascript:void(0);" onclick="view_exp('view_3_3');">查看例子</a><span id="view_3_3" class="none">：http://www.a1.com/news/</span>）</label>&nbsp;
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>栏目页SEO标题</label>
                </dt>
                <dd class="opt">
                    <label><input type="radio" name="seo_liststitle_format" value="1" <?php if(isset($config['seo_liststitle_format']) AND $config['seo_liststitle_format'] == 1): ?>checked="checked"<?php endif; ?>>栏目名称_网站名称</label>&nbsp;
                    <br/>
                    <label><input type="radio" name="seo_liststitle_format" value="2" <?php if(!isset($config['seo_liststitle_format']) OR $config['seo_liststitle_format'] == 2): ?>checked="checked"<?php endif; ?>>栏目名称_第N页_网站名称</label>&nbsp;
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>内容页SEO标题</label>
                </dt>
                <dd class="opt">
                    <label><input type="radio" name="seo_viewtitle_format" value="1" <?php if(isset($config['seo_viewtitle_format']) AND $config['seo_viewtitle_format'] == 1): ?>checked="checked"<?php endif; ?>>内容标题</label>&nbsp;
                    <br/>
                    <label><input type="radio" name="seo_viewtitle_format" value="2" <?php if(!isset($config['seo_viewtitle_format']) OR $config['seo_viewtitle_format'] == 2): ?>checked="checked"<?php endif; ?>>内容标题_网站名称</label>&nbsp;
                    <br/>
                    <label><input type="radio" name="seo_viewtitle_format" value="3" <?php if(isset($config['seo_viewtitle_format']) AND $config['seo_viewtitle_format'] == 3): ?>checked="checked"<?php endif; ?>>内容标题_栏目名称_网站名称</label>&nbsp;
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <!-- <dl class="row <?php if(empty($config['seo_inlet']) OR (1 == $config['seo_inlet'] AND 1 == $config['seo_force_inlet'])): else: ?>none<?php endif; ?>" id="dl_seo_force_inlet"> -->
            <dl class="row none" id="dl_seo_force_inlet">
                <dt class="tit">
                    <label for="site_url">强制去除index.php</label>
                </dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="seo_force_inlet1" class="cb-enable <?php if(isset($config['seo_force_inlet']) && $config['seo_force_inlet'] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="seo_force_inlet0" class="cb-disable <?php if(empty($config['seo_force_inlet'])): ?>selected<?php endif; ?>">关闭</label>
                        <input id="seo_force_inlet1" name="seo_force_inlet" value="1" type="radio" <?php if(isset($config['seo_force_inlet']) && $config['seo_force_inlet'] == 1): ?> checked="checked"<?php endif; ?>>
                        <input id="seo_force_inlet0" name="seo_force_inlet" value="0" type="radio" <?php if(empty($config['seo_force_inlet'])): ?> checked="checked"<?php endif; ?>>
                    </div>
                    <br/>
                    <p class=""></p>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="adsubmit();">确认提交</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">

    $(function(){
        $('input[name=seo_pseudo]').click(function(){
            var _this = this;
            $('#dl_seo_dynamic_format').hide();
            $('#dl_seo_html_format').hide();
            // $('#tab_base_html').attr('style','display:none!important');
            $('#dl_seo_rewrite_format').hide();
            $('#seo_right_uphtml').hide();
            var seo_pseudo = $(_this).val();
            if (1 == seo_pseudo) {
                if (1 != $('input[name=seo_dynamic_format]:checked').val()) {
                    $('#dl_seo_dynamic_format').show();
                }
                if (1 != $('#seo_inlet').val()) {
                    $('#dl_seo_force_inlet').show();
                } else {
                    $('#dl_seo_force_inlet').hide();
                }
            } else if (2 == seo_pseudo) {
                $('#dl_seo_force_inlet').hide();
                msg = "静态模式下注意几点：<br/>1、修改模板记得生成<br/>2、更新后台数据记得生成<br/>3、安装的插件需要更新至最新版本";
                layer.alert(msg, {icon: 6, closeBtn:false, title: false});
                $('#dl_seo_html_format').show();
                // $('#tab_base_html').show();
                $('#seo_right_uphtml').show();
            } else {
                $('#dl_seo_rewrite_format').show();
                if (1 != $('#seo_inlet').val()) {
                    $('#dl_seo_force_inlet').show();
                } else {
                    $('#dl_seo_force_inlet').hide();
                }
            }

            var seo_pseudo_old = $('#seo_pseudo_old').val();
            if (3 == seo_pseudo) {
                layer_loading('正在检测');
                $.ajax({
                    url: "<?php echo url('Seo/ajax_checkHtmlDirpath'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {seo_pseudo_new:seo_pseudo, _ajax:1},
                    // async: true,
                    success: function(res){
                        layer.closeAll();
                        if (res.code == 0) {
                            layer.msg(res.msg, {icon: 5, time: 1500});
                        } else {
                            if (res.data.msg != '') {
                                $('input[name=seo_pseudo]').each(function(i,o){
                                    if($(o).val() == seo_pseudo_old){
                                        $(o).attr('checked',true);
                                    } else {
                                        $(o).attr('checked',false);
                                    }
                                })
                                $('#dl_seo_html_format').show();
                                $('#seo_right_uphtml').show();
                                // $('#tab_base_html').show();
                                var seo_pseudo = $(_this).val();
                                //询问框
                                var height = res.data.height + 116;
                                if (350 <= height) height = 350;
                                var confirm1 = layer.confirm(res.data.msg, {
                                        title: false
                                        ,area: ['320px', height+'px']
                                        ,btn: ['手工删除','自动删除'] //按钮

                                    }, function(){
                                        layer.close(confirm1);

                                    }, function(){
                                        layer_loading('正在处理');
                                        $.ajax({
                                            url: "<?php echo url('Seo/ajax_delHtmlDirpath'); ?>",
                                            type: "POST",
                                            dataType: "json",
                                            data: {_ajax:1},
                                            success: function(res){
                                                layer.closeAll();
                                                if (1 == res.code) {
                                                    $('input[name=seo_pseudo]').each(function(i,o){
                                                        if($(o).val() == seo_pseudo){
                                                            $(o).attr('checked',true);
                                                        } else {
                                                            $(o).attr('checked',false);
                                                        }
                                                    })
                                                    $('#dl_seo_html_format').hide();
                                                    $('#seo_right_uphtml').hide();
                                                    // $('#tab_base_html').attr('style','display:none!important');
                                                    layer.msg(res.msg, {icon: 1, time: 1500});
                                                } else {
                                                    layer.alert(res.data.msg, {icon: 5, title: false});
                                                }
                                            },
                                            error: function(e){
                                                layer.closeAll();
                                                layer.alert(ey_unknown_error, {icon: 5, title:false});
                                            }
                                        })
                                    }
                                );
                            }
                        }
                    },
                    error: function(){
                        layer.closeAll();
                        layer.alert(ey_unknown_error, {icon: 5, title:false});
                    }
                });
            }
        });

        $('#seo_html_arcdir').keyup(function(){
            var seo_html_arcdir = $(this).val();
            if (seo_html_arcdir != '') {
                if (seo_html_arcdir.substr(0,1) == '/') seo_html_arcdir = seo_html_arcdir.substr(1);
                seo_html_arcdir = '/' + seo_html_arcdir;
                $('#tips_seo_html_arcdir_1').show();
                $('#tips_seo_html_arcdir_2').html(seo_html_arcdir);
            } else {
                $('#tips_seo_html_arcdir_1').hide();
            }
            $('#exp_seo_html_arcdir').html(seo_html_arcdir);
        });

        $('input[name="seo_force_inlet"]').click(function(){
            if (1 == $(this).val()) {
                layer.open({
                    type: 2,
                    title: false,
                    area: ['0px', '0px'],
                    shade: 0.0,
                    closeBtn: 0,
                    shadeClose: true,
                    content: '//<?php echo \think\Request::instance()->host(); ?>/api/Rewrite/testing.html',
                    success: function(layero, index){
                        layer.close(index);
                        var body = layer.getChildFrame('body', index);
                        var content = body.html();
                        if (content.indexOf("Congratulations on passing") == -1)
                        {
                            $('label[for=seo_force_inlet1]').removeClass('selected');
                            $('#seo_force_inlet1').attr('checked','');
                            $('label[for=seo_force_inlet0]').addClass('selected');
                            $('#seo_force_inlet0').attr('checked','checked');
                            layer.alert('不支持去除index.php，请<a href="http://www.eyoucms.com/plus/view.php?aid=7874" target="_blank">点击查看教程</a>', {icon: 2, title:false});
                        }
                    }
                });
            }
        });

        checkInlet();

        // 自动检测隐藏index.php
        function checkInlet() {
            if (2 == $('input[name=seo_pseudo]:checked').val()) {
                $('#dl_seo_force_inlet').hide();
            }
            layer.open({
                type: 2,
                title: false,
                area: ['0px', '0px'],
                shade: 0.0,
                closeBtn: 0,
                shadeClose: true,
                content: '//<?php echo \think\Request::instance()->host(); ?>/api/Rewrite/setInlet.html',
                success: function(layero, index){
                    layer.close(index);
                    var body = layer.getChildFrame('body', index);
                    var content = body.html();
                    if (content.indexOf("Congratulations on passing") == -1)
                    {
                        $('#seo_inlet').val(0);
                        $('label[for=seo_force_inlet1]').removeClass('selected');
                        $('#seo_force_inlet1').attr('checked','');
                        $('label[for=seo_force_inlet0]').addClass('selected');
                        $('#seo_force_inlet0').attr('checked','checked');
                        if (2 != $('input[name=seo_pseudo]:checked').val()) {
                            $('#dl_seo_force_inlet').show();
                        }
                        $.ajax({
                            type : "POST",
                            url  : "/index.php?m=api&c=Rewrite&a=setInlet",
                            data : {seo_inlet:0,_ajax:1},
                            dataType : "JSON",
                            success: function(res) {

                            }
                        });
                    } else {
                        $('#seo_inlet').val(1);
                        $('#dl_seo_force_inlet').hide();
                        $('label[for=seo_force_inlet0]').removeClass('selected');
                        $('#seo_force_inlet0').attr('checked','');
                        $('label[for=seo_force_inlet1]').addClass('selected');
                        $('#seo_force_inlet1').attr('checked','checked');
                    }
                }
            });
        }
    });

    function adsubmit(){
        if($("input[name='seo_pseudo']:checked").val() == '2'){
            var seo_html_arcdir = $('input[name="seo_html_arcdir"]').val();
            if (seo_html_arcdir != '') {
                seo_html_arcdir = seo_html_arcdir_new = seo_html_arcdir.replace('\\', '/');
                var reg = /^([0-9a-zA-Z\_\-\/]+)$/;
                if (seo_html_arcdir != '/' && reg.test(seo_html_arcdir)) {
                    // 去掉最前面的斜杆
                    if (seo_html_arcdir_new.substr(0,1) == '/') seo_html_arcdir_new = seo_html_arcdir_new.substr(1);
                    var html_arcdir_arr = seo_html_arcdir_new.split("/");
                    var html_arcdir_one = html_arcdir_arr[0]; // 一级路径名
                    var seo_html_arcdir_limit = $('input[name="seo_html_arcdir_limit"]').val();
                    var seo_html_arcdir_limit_arr = seo_html_arcdir_limit.split(",");
                    if (seo_html_arcdir_limit_arr.indexOf(html_arcdir_one) >= 0){
                        layer.msg('页面保存路径的目录名 '+html_arcdir_one+' 与内置禁用的重复!', {icon: 2,time: 3000});
                        $('input[name="seo_html_arcdir"]').focus();
                        return false;
                    }
                }else{
                    showErrorMsg('页面保存路径的格式错误！');
                    $('input[name="seo_html_arcdir"]').focus();
                    return false;
                }
            }
        }
        layer_loading("正在处理");
        var init_html = $('#init_html').val();
        $.ajax({
            url: "<?php echo url('Seo/handle', ['_ajax'=>1]); ?>",
            type: 'POST',
            dataType: 'json',
            data: $('#handlepost').serialize(),
            success: function(res){
                if (1 == res.code) {
                    if (2 == init_html || 2 != $("input[name='seo_pseudo']:checked").val()) {
                        // layer.closeAll();
                        layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                            window.location.href = res.url;
                        });
                    } else {
                        layer.closeAll();
                        var confirm1 = layer.confirm('配置成功，是否要生成整站页面？', {
                                title: false
                                ,closeBtn: false
                                ,btn: ['是','否'] //按钮

                            }, function(){
                                var url = eyou_basefile+"?m=admin&c=Seo&a=site&lang="+__lang__;
                                var index = layer.open({
                                    type: 2,
                                    title: '开始生成',
                                    area: ['500px', '300px'],
                                    fix: false, 
                                    maxmin: false,
                                    content: url,
                                    end: function(layero, index){
                                        layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                                            window.location.href = res.url;
                                        });
                                    }
                                });
                            }, function(){
                                layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                                    window.location.href = res.url;
                                });
                            }
                        );
                    }
                } else {
                    layer.closeAll();
                    layer.alert(res.msg, {icon: 5, title:false});
                }
            },
            error: function(e){
                layer.closeAll();
                layer.alert(ey_unknown_error, {icon: 5, title:false});
            }
        });
    }

    function view_exp(id)
    {
        $('#'+id).toggle();
    }
</script>

<div class="seo-right <?php if(2 != $config['seo_pseudo']): ?>none<?php endif; ?>" id="seo_right_uphtml">
    <style>
        .seo-right{
            position: fixed;
            top: 70px;
            margin-top: 0px;
    /*        top: 50%;
            margin-top: -185px;*/
            right: 30px;
            width: 350px;
            height: 270px;
            background-color:#fff;
            z-index:666666;
            border: 1px solid #ddd;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        .seo-html dt.tit {
           width: 60px;
           padding-left:20px;
        }   
    </style>
    <div class="page">
        <form method="get" id="html_handlepost" name="form2">
            <div class="ncap-form-default seo-html">
                <dl class="row">
                    <dt class="tit">
                        <label>整站页面</label>
                    </dt>
                    <dd class="opt">       
                        <a href="javascript:void(0);" id="up_site" class="ncap-btn ncap-btn-green">一键生成</a>
                        <span class="err"></span>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label>首页</label>
                    </dt>
                    <dd class="opt">       
                        <a href="javascript:void(0);" id="up_index" class="ncap-btn ncap-btn-green">一键生成</a>
                        <span class="err"></span>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">栏目页</dt>
                    <dd class="opt">
                        <select name="html_typeid" id="html_typeid">
                            <option value="0">所有栏目…</option>
                            <?php echo $select_html; ?>
                        </select>
                        &nbsp;<a href="javascript:void(0);" id="up_channel" class="ncap-btn ncap-btn-green">一键生成</a>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">文档页</dt>
                    <dd class="opt">
                        <select name="html_arc_typeid" id="html_arc_typeid">
                            <option value="0">所有文档…</option>
                            <?php echo $arc_select_html; ?>
                        </select>
                        &nbsp;<a href="javascript:void(0);" id="up_article" class="ncap-btn ncap-btn-green">一键生成</a>
                        <p class="notic"></p>
                    </dd>
                </dl>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        $(function(){
            
            //生成整站
            $("#up_site").click(function(){
                $.ajax({
                    url: "<?php echo url('Seo/handle', ['_ajax'=>1]); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#handlepost').serialize(),
                    beforeSend:function(){
                        layer_loading('正在处理');
                    },
                    success: function(res){
                        layer.closeAll();
                        if (0 == res.code) {
                            layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                        } else {
                            var url = eyou_basefile+"?m=admin&c=Seo&a=site&lang="+__lang__;
                            var index = layer.open({type: 2,title: '开始生成',area: ['500px', '300px'],fix: false, maxmin: false,content: url});
                        }
                    },
                    error: function(e){
                        layer.closeAll();
                        layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                    }
                });
            })

            //生成首页
            $("#up_index").click(function(){
                $.ajax({
                    url: "<?php echo url('Seo/handle', ['_ajax'=>1]); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#handlepost').serialize(),
                    beforeSend:function(){
                        layer_loading('正在处理');
                    },
                    success: function(res){
                        if (0 == res.code) {
                            layer.closeAll();
                            layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                        } else {
                            var timestamp1 = Date.parse(new Date());
                            $.ajax({
                                url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=buildIndex&lang="+__lang__,
                                type:'POST',
                                dataType:'json',
                                data: {_ajax:1},
                                beforeSend:function(){
                                    // layer_loading('正在处理');
                                    // var index = layer.load(0, {shade: false}); 
                                },
                                success:function(data){
                                    if(data.msg !== ""){
                                        layer.alert(data.msg, {icon: 5, title:false,closeBtn: 0 },function () {
                                            layer.closeAll();
                                        } );
                                    }else{
                                        layer.closeAll();
                                        var timestamp2 = Date.parse(new Date());
                                        var timestamp3 = (timestamp2 - timestamp1) / 1000;
                                        if (timestamp3 < 1) timestamp3 = 1; 
                                        layer.msg("生成完毕，共耗时：<font color='red'>"+timestamp3+"</font> 秒",{icon:1,time:2000}); 
                                    }
                                },
                                complete:function(){
                                    // layer.alert(1, {icon: 5, title:false});
                                },
                                error: function(e){
                                    layer.closeAll();
                                    layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                                }
                            });
                        }
                    },
                    error: function(e){
                        layer.closeAll();
                        layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                    }
                });
            })
            
            //生成栏目页
            $("#up_channel").click(function(){
                $.ajax({
                    url: "<?php echo url('Seo/handle', ['_ajax'=>1]); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#handlepost').serialize(),
                    beforeSend:function(){
                        layer_loading('正在处理');
                    },
                    success: function(res){
                        layer.closeAll();
                        if (0 == res.code) {
                            layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                        } else {
                            var typeid = $("#html_typeid").val();     //栏目id
                            var url = eyou_basefile+"?m=admin&c=Seo&a=channel&typeid="+typeid+"&lang="+__lang__;
                            var index = layer.open({type: 2,title: '开始生成',area: ['500px', '300px'],fix: false, maxmin: false,content: url});
                        }
                    },
                    error: function(e){
                        layer.closeAll();
                        layer.alert('生成失败，请先提交保存配置！', {icon: 5, title:false});
                    }
                });
            })

            //生成文章页
            $("#up_article").click(function(){
                $.ajax({
                    url: "<?php echo url('Seo/handle', ['_ajax'=>1]); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#handlepost').serialize(),
                    beforeSend:function(){
                        layer_loading('正在处理');
                    },
                    success: function(res){
                        layer.closeAll();
                        if (0 == res.code) {
                            layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                        } else {
                            var typeid = $("#html_arc_typeid").val();     //栏目id
                            var url = eyou_basefile+"?m=admin&c=Seo&a=article&typeid="+typeid+"&lang="+__lang__;
                            var index = layer.open({type: 2,title: '开始生成',area: ['500px', '300px'],fix: false, maxmin: false,content: url});
                        }
                    },
                    error: function(e){
                        layer.closeAll();
                        layer.alert('未知错误，生成失败！', {icon: 5, title:false});
                    }
                });
            })
        })
    </script>
</div>

<br/>
<div id="goTop">
    <a href="JavaScript:void(0);" id="btntop">
        <i class="fa fa-angle-up"></i>
    </a>
    <a href="JavaScript:void(0);" id="btnbottom">
        <i class="fa fa-angle-down"></i>
    </a>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#think_page_trace_open').css('z-index', 99999);
    });
</script>
</body>
</html>