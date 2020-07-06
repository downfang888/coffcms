<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:44:"./application/admin/template/tools\index.htm";i:1574150878;s:75:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\layout.htm";i:1590023511;s:71:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\tools\bar.htm";i:1554199440;s:75:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\footer.htm";i:1545615936;}*/ ?>
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
<body class="bodystyle" style="cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>备份还原</h3>
                <h5></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a href="<?php echo url("Tools/index"); ?>" class="tab <?php if(\think\Request::instance()->action() == 'index'): ?>current<?php endif; ?>"><span>数据备份</span></a></li>
                <li><a href="<?php echo url("Tools/restore"); ?>" class="tab <?php if(in_array(\think\Request::instance()->action(), array('restore'))): ?>current<?php endif; ?>"><span>数据还原</span></a></li>
            </ul>
        </div>
    </div>
    <!-- 操作说明 -->
    <div id="explanation" class="explanation" style="color: rgb(44, 188, 163); background-color: rgb(237, 251, 248); width: 99%; height: 100%;">
        <div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
            <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
            <span title="收起提示" id="explanationZoom" style="display: block;"></span>
        </div>
        <ul>
            <li>数据备份功能根据你的选择备份全部数据或指定数据，导出的数据文件可用“数据恢复”功能或 phpMyAdmin 导入</li>
            <li>建议定期备份数据库</li>
        </ul>
    </div>
    <div class="flexigrid">
        <div class="mDiv">
            <div class="ftitle">
                <h3>数据库表列表</h3>
                <h5>(共<?php echo $tableNum; ?>条数据，共计<?php echo $total; ?>)</h5>
            </div>
            <div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
        </div>
        <div class="hDiv">
            <div class="hDivBox">
                <table cellspacing="0" cellpadding="0" style="width: 100%">
                    <thead>
                    <tr>
                        <th class="sign w40" axis="col0">
                            <div class="tc">选择</div>
                        </th>
                        <th abbr="article_title" axis="col3">
                            <div style="padding-left: 10px;" class="">数据库表</div>
                        </th>
                        <th abbr="ac_id" axis="col4" class="w80">
                            <div class="tc">记录条数</div>
                        </th>
                        <th abbr="article_show" axis="col5" class="w80">
                            <div class="tc">占用空间</div>
                        </th>
                        <th abbr="article_time" axis="col6" class="w120">
                            <div class="tc">编码</div>
                        </th>
                        <th abbr="article_time" axis="col6" class="w160">
                            <div class="tc">创建时间</div>
                        </th>
                        <th abbr="article_time" axis="col6" class="w80">
                            <div class="tc">备份状态</div>
                        </th>
                        <th axis="col1" class="w80">
                            <div class="tc">操作</div>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="bDiv" style="height: auto;">
            <div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
                <form  method="post" id="export-form" action="<?php echo url('Tools/export'); ?>">
                    <table id="tb_flexigrid" style="width: 100%">
                        <tbody>
                        <?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?>
                            <tr>
                                <td class="no-data" align="center" axis="col0" colspan="50">
                                    <i class="fa fa-exclamation-circle"></i>没有符合条件的记录
                                </td>
                            </tr>
                        <?php else: if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $k=>$vo): ?>
                            <tr data-id="<?php echo $vo['Name']; ?>">
                                <td class="sign">
                                    <div class="w40 tc"><input type="checkbox" name="tables[]" value="<?php echo $vo['Name']; ?>" checked="checked"></div>
                                </td>
                                <td style="width: 100%">
                                    <div style="padding-left: 10px;"><?php echo $vo['Name']; ?></div>
                                </td>
                                <td>
                                    <div class="w80 tc"><?php echo $vo['Rows']; ?></div>
                                </td>
                                <td>
                                    <div class="w80 tc"><?php echo format_bytes($vo['Data_length']); ?></div>
                                </td>
                                <td>
                                    <div class="w120 tc"><?php echo $vo['Collation']; ?></div>
                                </td>
                                <td>
                                    <div class="w160 tc"><?php echo $vo['Create_time']; ?></div>
                                </td>
                                <td>
                                    <div class="info w80 tc">未备份</div>
                                </td>
                                <td>
                                    <div class="w80 tc">
                                        <?php if(is_check_access(CONTROLLER_NAME.'@optimize') == '1'): ?>
                                        <!-- <a href="<?php echo url('Tools/optimize',array('tablename'=>$vo['Name'])); ?>" class="btn blue"><i class="fa fa-magic"></i>优化</a> -->
                                        <?php endif; if(is_check_access(CONTROLLER_NAME.'@repair') == '1'): ?>
                                        <a class="btn green" href="<?php echo url('Tools/repair',array('tablename'=>$vo['Name'])); ?>"><i class="fa fa-wrench"></i>修复</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                               
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="iDiv" style="display: none;"></div>
        </div>
        <div class="tDiv">
            <div class="tDiv2">
                <div class="fbutton checkboxall">
                    <input type="checkbox" onclick="javascript:$('input[name*=tables]').prop('checked',this.checked);" checked="checked">
                </div>
                <?php if(is_check_access(CONTROLLER_NAME.'@export') == '1'): ?>
                <div class="fbutton">
                    <a id="ing_btn">
                        <div class="add" title="数据备份">
                            <span><i class="fa fa-book"></i><span id="export">数据备份</span></span>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // 表格行点击选中切换
        $('#tb_flexigrid >tbody >tr').click(function(){
            $(this).toggleClass('trSelected');
        });

        // 点击刷新数据
        $('.fa-refresh').click(function(){
            location.href = location.href;
        });

    });

    (function($){
        var $form = $("#export-form"), $export = $("#export"), tables
        $export.click(function(){
            if($("input[name^='tables']:checked").length == 0){
                layer.alert('请选中要备份的数据表', {icon: 2, title:false});
                return false;
            }
            $export.addClass("disabled");
            $export.html("正在发送备份请求...");
            $.post(
                "<?php echo url('Tools/export', ['_ajax'=>1]); ?>",
                $form.serialize(),
                function(res){
                    if(res.status){
                        tables = res.tables;
                        var loading = layer.msg('正在备份表(<font id="upgrade_backup_table">'+res.tab.table+'</font>)……<font id="upgrade_backup_speed">0.01</font>%', 
                        {
                            icon: 1,
                            time: 3600000, //1小时后后自动关闭
                            shade: [0.2] //0.1透明度的白色背景
                        });
                        $export.html(res.info + "开始备份，请不要关闭本页面！");
                        backup(res.tab);
                        window.onbeforeunload = function(){ return "正在备份数据库，请不要关闭！" }
                    } else {
                        layer.alert(res.info, {icon: 2, title:false});
                        $export.removeClass("disabled");
                        $export.html("立即备份");
                    }
                },
                "json"
            );
            return false;
        });

        function backup(tab, status){
            status && showmsg(tab.id, "开始备份……(0%)");
            $.post("<?php echo url('Tools/export', ['_ajax'=>1]); ?>", tab, function(data){
                if(data.status){
                    if (tab.table) {
                        showmsg(tab.id, data.info);
                        $('#upgrade_backup_table').html(tab.table);
                        $('#upgrade_backup_speed').html(tab.speed);
                        $export.html('初始化成功！正在备份表('+tab.table+')……'+tab.speed+'%，请不要关闭本页面！');
                    } else {
                        $export.html('初始化成功！开始备份……，请不要关闭本页面！');
                    }
                    if(!$.isPlainObject(data.tab)){
                        var loading = layer.msg('备份完成……100%，请不要关闭本页面！', 
                        {
                            icon: 1,
                            time: 2000, //1小时后后自动关闭
                            shade: [0.2] //0.1透明度的白色背景
                        });
                        $export.removeClass("disabled");
                        $export.html("备份完成……100%，点击重新备份");
                        setTimeout(function(){
                            layer.closeAll();
                            layer.alert('备份成功！', {icon: 6, title:false});
                        }, 1000);
                        window.onbeforeunload = function(){ return null }
                        return;
                    }
                    backup(data.tab, tab.id != data.tab.id);
                } else {
                    layer.closeAll();
                    $export.removeClass("disabled");
                    $export.html("立即备份");
                }
            }, "json");
        }

        function showmsg(id, msg){
            $form.find("input[value=" + tables[id] + "]").closest("tr").find(".info").html(msg);
        }
    })(jQuery);
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