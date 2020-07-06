<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:43:"./application/admin/template/links\edit.htm";i:1574150877;s:75:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\layout.htm";i:1590023511;s:71:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\links\bar.htm";i:1563843926;s:69:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\seo\bar.htm";i:1590023511;s:75:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\footer.htm";i:1545615936;}*/ ?>
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
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    
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
<!--     <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>友情链接</h3>
                <h5></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a href="<?php echo url("Links/index"); ?>" class="tab <?php if(\think\Request::instance()->action() == 'index'): ?>current<?php endif; ?>"><span>友链列表</span></a></li>

                <?php if(is_check_access(CONTROLLER_NAME.'@add') == '1'): ?>
                <li><a href="<?php echo url("Links/add"); ?>" class="tab <?php if(in_array(\think\Request::instance()->action(), array('add','edit'))): ?>current<?php endif; ?>"><span><?php if('edit' == \think\Request::instance()->action()): ?>编辑链接<?php else: ?>新增链接<?php endif; ?></span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div> -->
    <form class="form-horizontal" id="post_form" action="<?php echo U('Links/edit'); ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><em>*</em>链接类型</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="typeid1" class="cb-enable <?php if($info['typeid'] == 1): ?>selected<?php endif; ?>">文字</label>
                        <label for="typeid2" class="cb-disable <?php if($info['typeid'] == 2): ?>selected<?php endif; ?>" >图片</label>
                        <input id="typeid1" name="typeid" value="1" <?php if($info['typeid'] == 1): ?>checked="checked"<?php endif; ?> type="radio">
                        <input id="typeid2" name="typeid"  value="2" <?php if($info['typeid'] == 2): ?>checked="checked"<?php endif; ?> type="radio">
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="url"><em>*</em>网址URL</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="url" placeholder="http://" value="<?php echo (isset($info['url']) && ($info['url'] !== '')?$info['url']:''); ?>" id="url" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="title"><em>*</em>网站名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="title" value="<?php echo (isset($info['title']) && ($info['title'] !== '')?$info['title']:''); ?>" id="title" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row typeid2 <?php if($info['typeid'] == 1): ?>none<?php endif; ?>">
                <dt class="tit">
                    <label for="logo"><em>*</em>上传Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show div_logo_local" <?php if($info['is_remote'] != '0'): ?>style="display: none;"<?php endif; ?>>
                        <span class="show">
                            <a id="img_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo (isset($info['logo_local']) && ($info['logo_local'] !== '')?$info['logo_local']:'javascript:void(0);'); ?>">
                                <i id="img_i" class="fa fa-picture-o" <?php if(!(empty($info['logo_local']) || (($info['logo_local'] instanceof \think\Collection || $info['logo_local'] instanceof \think\Paginator ) && $info['logo_local']->isEmpty()))): ?>onmouseover="layer_tips=layer.tips('<img src=<?php echo (isset($info['logo_local']) && ($info['logo_local'] !== '')?$info['logo_local']:''); ?> class=\'layer_tips_img\'>',this,{tips: [1, '#fff']});"<?php endif; ?> onmouseout="layer.close(layer_tips);"></i>
                            </a>
                        </span>
                        <span class="type-file-box">
                            <input type="text" id="logo_local" name="logo_local" value="<?php echo (isset($info['logo_local']) && ($info['logo_local'] !== '')?$info['logo_local']:''); ?>" class="type-file-text">
                            <input type="button" name="button" id="button1" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'','allimg','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo"
                                 title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                    </div>
                    <input type="text" id="logo_remote" name="logo_remote" value="<?php echo (isset($info['logo_remote']) && ($info['logo_remote'] !== '')?$info['logo_remote']:''); ?>" placeholder="http://" class="input-txt" <?php if($info['is_remote'] != '1'): ?>style="display: none;"<?php endif; ?>>
                    &nbsp;
                    <label><input type="checkbox" name="is_remote" id="is_remote" value="1" <?php if($info['is_remote'] == '1'): ?>checked="checked"<?php endif; ?> onClick="clickRemote(this, 'logo');">远程图片</label>
                    <span class="err"></span>
                    <p class="notic">建议尺寸 88 * 31 (像素) 的gif或jpg文件</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sort_order">排序</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="sort_order" value="<?php echo (isset($info['sort_order']) && ($info['sort_order'] !== '')?$info['sort_order']:'100'); ?>" id="sort_order" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">越小越靠前</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="email">站长Email</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="email" value="<?php echo (isset($info['email']) && ($info['email'] !== '')?$info['email']:''); ?>" id="email" class="input-txt">
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="intro">网站备注</label>
                </dt>
                <dd class="opt">
                    <textarea rows="5" cols="80" id="intro" name="intro" style="height:80px;" placeholder="备注一些站长联系方式、信息等内容"><?php echo (isset($info['intro']) && ($info['intro'] !== '')?$info['intro']:''); ?></textarea>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>新窗口打开</label>
                </dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="target1" class="cb-enable <?php if($info['target'] == 1): ?>selected<?php endif; ?>">是</label>
                        <label for="target0" class="cb-disable <?php if($info['target'] == 0): ?>selected<?php endif; ?>">否</label>
                        <input id="target1" name="target" value="1" type="radio" <?php if($info['target'] == 1): ?> checked="checked"<?php endif; ?>>
                        <input id="target0" name="target" value="0" type="radio" <?php if($info['target'] == 0): ?> checked="checked"<?php endif; ?>>
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" onclick="checkForm();" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    // 判断输入框是否为空
    function checkForm(){
        if($('input[name=url]').val() == ''){
            showErrorMsg('网址URL不能为空！');
            $('input[name=url]').focus();
            return false;
        }
        if($('input[name=title]').val() == ''){
            showErrorMsg('网站名称不能为空！');
            $('input[name=title]').focus();
            return false;
        }
        if ($('input[name=typeid]:checked').val() == 2) {
            if ($('input[name=is_remote]').is(':checked')) {
                if($('input[name=logo_remote]').val() == ''){
                    showErrorMsg('请上传网站Logo图片！');
                    $('input[name=logo_remote]').focus();
                    return false;
                }
            } else {
                if($('input[name=logo_local]').val() == ''){
                    showErrorMsg('请上传网站Logo图片！');
                    $('input[name=logo_local]').focus();
                    return false;
                }
            }
        }
        layer_loading('正在处理');
        $('#post_form').submit();
    }
    
    function img_call_back(fileurl_tmp)
    {
        $("#logo_local").val(fileurl_tmp);
        $("#img_a").attr('href', fileurl_tmp);
        $("#img_i").attr('onmouseover', "layer_tips=layer.tips('<img src="+fileurl_tmp+" class=\\'layer_tips_img\\'>',this,{tips: [1, '#fff']});");
    }

    $(function(){
        $('input[name=typeid]').click(function(){
            var val = $(this).val();
            if (val == 2) {
                $('.typeid2').show();
            } else {
                $('.typeid2').hide();
            }
        });
    });
</script>
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