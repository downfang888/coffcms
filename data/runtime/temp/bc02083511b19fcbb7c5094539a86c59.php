<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:44:"./application/admin/template/index\index.htm";i:1590023511;s:76:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\menubox.htm";i:1545615936;s:73:"D:\phpstudy_pro\WWW\www.a1.com\application\admin\template\public\left.htm";i:1590023511;}*/ ?>
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
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" media="screen"/>
<title><?php echo (isset($global['web_name']) && ($global['web_name'] !== '')?$global['web_name']:''); ?>-<?php if(!(empty($is_eyou_authortoken) || (($is_eyou_authortoken instanceof \think\Collection || $is_eyou_authortoken instanceof \think\Paginator ) && $is_eyou_authortoken->isEmpty()))): ?>易优CMS企业网站管理系统<?php endif; ?><?php echo $version; ?></title>
<script type="text/javascript">
    var eyou_basefile = "<?php echo \think\Request::instance()->baseFile(); ?>";
    var module_name = "<?php echo MODULE_NAME; ?>";
    var SITEURL = window.location.host + eyou_basefile + "/" + module_name;
    var GetUploadify_url = "<?php echo url('Uploadify/upload'); ?>";
    var __root_dir__ = "";
    var __lang__ = "<?php echo $admin_lang; ?>";
</script>

<link href="/public/static/admin/css/main.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css">
<link href="/public/static/admin/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<link href="/public/static/admin/font/css/font-awesome.min.css" rel="stylesheet" />
<script type="text/javascript" src="/public/static/admin/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/admin/js/common.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery.bgColorSelector.js"></script>
<script type="text/javascript" src="/public/static/admin/js/admincp.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/public/static/admin/js/jquery.validation.min.js"></script>

<!-- <script type="text/javascript" src="/public/plugins/layer/layer.js"></script> -->
<script type="text/javascript" src="/public/plugins/layer-v3.1.0/layer.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/public/static/admin/js/dialog/dialog.js?v=<?php echo $version; ?>" id="dialog_js"></script>
<script src="/public/static/admin/js/upgrade.js?v=<?php echo $version; ?>"></script>
<script src="/public/static/admin/js/global.js?v=<?php echo $version; ?>"></script>
<link href="/public/static/admin/css/diy_style.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css" />
</head>
<body>

  <script type="text/javascript">
  //固定层移动
  $(function(){
      //管理显示与隐藏
      $('img[tptype="admin_avatar"], #admin-manager-btn').click(function () {
          if ($(".manager-menu").css("display") == "none") {
              $(".manager-menu").css('display', 'block'); 
              $("#admin-manager-btn").attr("title","关闭快捷管理"); 
              $("#admin-manager-btn").removeClass().addClass("arrow-close");
          }
          else {
              $(".manager-menu").css('display', 'none');
              $("#admin-manager-btn").attr("title","显示快捷管理");
              $("#admin-manager-btn").removeClass().addClass("arrow");
          }           
      });
  });
  </script> 
<style>
.scroll-wrapper {   
  height: 100%;
  -webkit-overflow-scrolling: touch;   
  overflow-y: auto;   
} 
</style>
<div class="admincp-container unfold">
<div class="eycms_cont_left hidden-xs">
    <dl class="eylogo">
        <?php if(!(empty($is_eyou_authortoken) || (($is_eyou_authortoken instanceof \think\Collection || $is_eyou_authortoken instanceof \think\Paginator ) && $is_eyou_authortoken->isEmpty()))): ?>
        <a href="<?php echo \think\Request::instance()->url(); ?>"><img id="web_adminlogo" src="/public/static/admin/images/logo_ey.png?v=<?php echo time(); ?>" alt="点击刷新" title="点击刷新"></a>
        <?php else: ?>
        <a href="<?php echo \think\Request::instance()->url(); ?>"><img id="web_adminlogo" src="/public/static/admin/images/logo.png?v=<?php echo time(); ?>" alt="点击刷新" title="点击刷新"></a>
        <?php endif; ?>
    </dl>
    <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <dl style="<?php if($vo['is_menu'] == 0): ?>display: none;<?php endif; ?>">
            <?php if(!(empty($vo['name']) || (($vo['name'] instanceof \think\Collection || $vo['name'] instanceof \think\Paginator ) && $vo['name']->isEmpty()))): ?><dt><?php echo $vo['name']; ?></dt><?php endif; ?>
            <dd class="sub-menu">
            <?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): $k2 = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($k2 % 2 );++$k2;if(empty($v2['child']) || empty($v2['is_subshowmenu'])): ?>
                    <a data-child='0' id="<?php echo $v2['controller']; ?>_<?php echo $v2['action']; ?>" href="<?php echo (isset($v2['url']) && ($v2['url'] !== '')?$v2['url']:'javascript:void(0);'); ?>" <?php if(empty($v2['url']) || (($v2['url'] instanceof \think\Collection || $v2['url'] instanceof \think\Paginator ) && $v2['url']->isEmpty())): ?>data-param="<?php echo $v2['controller']; ?>|<?php echo $v2['action']; ?>"<?php endif; ?> target="<?php echo $v2['target']; ?>" style="<?php if($v2['is_menu'] == 0): ?>display: none;<?php endif; ?>"><i class="<?php echo (isset($v2['icon']) && ($v2['icon'] !== '')?$v2['icon']:'fa fa-minus'); ?>"></i><?php echo $v2['name']; ?></a>
                <?php else: ?>
                    <dl class="jslist" style="border-bottom:none;<?php if($v2['is_menu'] == 0): ?>display: none;<?php endif; ?>">
                        <dt><i class="<?php echo (isset($v2['icon']) && ($v2['icon'] !== '')?$v2['icon']:'fa fa-minus'); ?>"></i><?php echo $v2['name']; ?><i class="fa fa-angle-right"></i></dt>
                        <dd style="display: none;">
                            <?php if(is_array($v2['child']) || $v2['child'] instanceof \think\Collection || $v2['child'] instanceof \think\Paginator): $k3 = 0; $__LIST__ = $v2['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v3): $mod = ($k3 % 2 );++$k3;?>
                                <a data-child='1' id="<?php echo $v3['controller']; ?>_<?php echo $v3['action']; ?>" href="<?php echo (isset($v3['url']) && ($v3['url'] !== '')?$v3['url']:'javascript:void(0);'); ?>" <?php if(empty($v3['url']) || (($v3['url'] instanceof \think\Collection || $v3['url'] instanceof \think\Paginator ) && $v3['url']->isEmpty())): ?>data-param="<?php echo $v3['controller']; ?>|<?php echo $v3['action']; ?>"<?php endif; ?> target="<?php echo $v3['target']; ?>" style="<?php if($v3['is_menu'] == 0): ?>display: none;<?php endif; ?>"><i class="<?php echo (isset($v3['icon']) && ($v3['icon'] !== '')?$v3['icon']:'fa fa-minus'); ?>"></i><?php echo $v3['name']; if($v3['controller'] == 'Upgrade' and !empty($upgradeMsg)): endif; ?></a>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </dd>
                    </dl>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </dd>
        </dl>
    <?php endforeach; endif; else: echo "" ;endif; ?>
</div>

  <div class="admincp-container-right">
    <div class="admincp-header">
      <div class="wraper">
        <div class="bgSelector"></div>
        <div id="foldSidebar"><i  title="展开/收起侧边导航"></i></div>
        <div class="admincp-name" id="foldSidebar2">
        </div>
        <div class="admincp-header-r">
          <div class="manager">
            <dl>
              <dt class="name"><?php echo $admin_info['user_name']; ?></dt>
              <dd class="group"><?php echo $admin_info['role_name']; ?></dd>
            </dl>
            <div class="btn-group pull-left ey-tool">
              <a class="btn btn-default dropdown-toggle" target="_blank" href="<?php echo $home_url; ?>">
                <i class="fa fa-home"></i>
                <span class="hidden-xs">网站首页</span>
              </a>
              <?php if(is_check_access('Index@switch_map') == '1'): ?>
              <a class="btn btn-default dropdown-toggle" href="javascript:void(0);" onclick="switch_map();">
                <i class="fa fa-toggle-on"></i>
                <span class="hidden-xs">功能开关</span>
              </a>
              <?php endif; if(!(empty($is_eyou_authortoken) || (($is_eyou_authortoken instanceof \think\Collection || $is_eyou_authortoken instanceof \think\Paginator ) && $is_eyou_authortoken->isEmpty()))): ?>
              <!-- 商业授权 -->   
              <em class="eyou_tool em_authortoken" data-expanded="close">
                <a class="btn btn-default dropdown-toggle" title="购买后可去除所有版权提示" href="javascript:void(0);" onclick="valide(this);">
                  <i class="fa fa-bookmark"></i>
                  <span class="hidden-xs">购买授权</span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="shouquan">
                    <li class="ey-tool-list text-center"><a target="_blank" class="liaojie" href="http://www.eyoucms.com/buy/">了解商业授权</a></li>
                    <li class="ey-tool-list text-center">
                      <input class="btn btn-primary" type="button" onclick="$('.em_authortoken').toggleClass('open');openItem('Index|authortoken');" value="检测是否正版" />
                    </li>
                </ul>
               </em>
              <!-- 商业授权 -->   
              <?php endif; ?>

              <!-- 多语言 -->
              <em id="Language_index" class="eyou_tool em_lang" data-expanded="close" <?php if(empty($web_language_switch)): ?>style="display: none;"<?php endif; ?>>
                <a class="btn btn-default dropdown-toggle" title="支持多语言切换" href="javascript:void(0);" onclick="valide(this);">
                  <i class="fa fa-globe"></i>
                  <span class="hidden-xs" id="language_title"><?php echo (isset($languages[$admin_lang]['title']) && ($languages[$admin_lang]['title'] !== '')?$languages[$admin_lang]['title']:'简体中文'); ?></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="shouquan">
                  <?php if(is_array($languages) || $languages instanceof \think\Collection || $languages instanceof \think\Paginator): $i = 0; $__LIST__ = $languages;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li class="ey-tool-list text-center lang"><a class="liaojie" href="?lang=<?php echo $vo['mark']; ?>"><?php echo $vo['title']; ?></a></li>
                  <?php endforeach; endif; else: echo "" ;endif; ?>
                    <li class="ey-tool-list text-center" id="addlang">
                      <button class="btn btn-primary" type="button" onclick="$('.em_lang').toggleClass('open');openItem('Language|index');" /><i class="fa fa-globe"></i>&nbsp;多语言设置</button>
                    </li>
                </ul>
               </em>
              <!-- 多语言 -->   

              <!-- 小程序 start --> 
              <?php if(is_check_access('Diyminipro@page_edit') == '1'): ?>
              <a id="Diyminipro_theme_index" class="btn btn-default dropdown-toggle" href="<?php echo url('Diyminipro/page_edit'); ?>" target="_blank" <?php if($web_diyminipro_switch == -1): ?> style="display: none;" <?php endif; ?> title="可视化小程序">
                <i class="fa fa-compass"></i>
                <span class="hidden-xs">小程序</span>
              </a>
              <?php endif; ?>
              <!-- 小程序 end --> 

              <!-- 服务器升级 -->   
              <div id="upgrade_filelist" style="display:none;"></div> 
              <div id="upgrade_intro" style="display:none;"></div> 
              <div id="upgrade_notice" style="display:none;"></div> 
              <a class="btn btn-default dropdown-toggle" style="display: none;color:#F00;" title="不升级可能有安全隐患" href="javascript:void(0);" id="a_upgrade" data-version="" data-max_version="" data-iframe="parent" data-tips_url="<?php echo url('Upgrade/setPopupUpgrade'); ?>" data-upgrade_url="<?php echo url('Upgrade/OneKeyUpgrade'); ?>" data-check_authority="<?php echo url('Upgrade/check_authority'); ?>">
                <i class="fa fa-info-circle"></i>
                <span class="hidden-xs">系统更新</span>
              </a>
              <!-- 服务器升级 end -->
            </div>
            
            <div class="admin_user_dropup">
              <div class="admin_user_dropup_bt">
                <img id="admin_head_pic" tptype="admin_avatar" src="<?php echo get_head_pic($admin_info['head_pic']); ?>" style="cursor: pointer;width: 34px;height: 34px;">
                <i class="fa fa-angle-down" aria-hidden="true"></i>
              </div>
              <div class="admin_user_dropup_con">
                <ul>
                  <li><a href="javascript:void(0);" onclick="openItem('Admin|admin_edit|id|<?php echo $admin_info['admin_id']; ?>');">个人信息</a></li>
                  <li><a href="<?php echo url('Admin/logout'); ?>">安全退出</a></li>
                </ul>
              </div>
            </div>
            <script type="text/javascript">
              $(".admin_user_dropup").mouseover(function(){
                 $(".admin_user_dropup_con").show();
                 $(".admin_user_dropup_bt").children('i').removeClass('fa-angle-down').addClass('fa-angle-up');
              });
              $(".admin_user_dropup").mouseout(function(){
                 $(".admin_user_dropup_con").hide();
                 $(".admin_user_dropup_bt").children('i').removeClass('fa-angle-up').addClass('fa-angle-down');
              });
            </script>
            
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
    <div class="top-border"></div>
    <div class="scroll-wrapper">
      <iframe src="" id="workspace" name="workspace" style="overflow-y: auto" frameborder="0" width="100%" height="95%" scrolling="yes" onload="window.parent"></iframe>
    </div>
  </div>
</div>
<script type="text/javascript">

  function valide(obj)
  {
    var cls = $(obj).parent().attr('class');
    $('.eyou_tool').removeClass('open');
    if(cls.indexOf("open") > 0) {
      $(obj).parent().addClass('open');
    }
    $(obj).parent().toggleClass('open');
  }

  // 功能地图
  function switch_map()
  {
      var url = "<?php echo url('Index/switch_map'); ?>";
      workspace.window.location.href = url;
  }
</script>
</body>
</html>