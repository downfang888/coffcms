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

namespace app\admin\controller;
use think\Db;
use think\Cache;
use think\Request;

class System extends Base
{
    public function index()
    {
        $this->redirect(url('System/web'));
    }

    /**
     * 网站设置
     */
    public function web()
    {
        $inc_type =  'web';
        $root_dir = ROOT_DIR; // 支持子目录

        if (IS_POST) {
            $param = input('post.');
            $param['web_keywords'] = str_replace('，', ',', $param['web_keywords']);
            $param['web_description'] = filter_line_return($param['web_description']);
            
            // 网站根网址
            $web_basehost = rtrim($param['web_basehost'], '/');
            if (!is_http_url($web_basehost) && !empty($web_basehost)) {
                $web_basehost = 'http://'.$web_basehost;
            }
            $param['web_basehost'] = $web_basehost;

            // 网站logo
            $web_logo_is_remote = !empty($param['web_logo_is_remote']) ? $param['web_logo_is_remote'] : 0;
            $web_logo = '';
            if ($web_logo_is_remote == 1) {
                $web_logo = $param['web_logo_remote'];
            } else {
                $web_logo = $param['web_logo_local'];
            }
            $param['web_logo'] = $web_logo;
            unset($param['web_logo_is_remote']);
            unset($param['web_logo_remote']);
            unset($param['web_logo_local']);

            // 浏览器地址图标
            if (!empty($param['web_ico']) && !is_http_url($param['web_ico'])) {
                $source = realpath(preg_replace('#^'.$root_dir.'/#i', '', $param['web_ico']));
                $destination = realpath('favicon.ico');
                if (file_exists($source) && @copy($source, $destination)) {
                    $param['web_ico'] = $root_dir.'/favicon.ico';
                }
            }

            tpCache($inc_type, $param);
            write_global_params(); // 写入全局内置参数
            $this->success('操作成功', U('System/web'));
            exit;
        }

        $config = tpCache($inc_type);
        // 网站logo
        if (is_http_url($config['web_logo'])) {
            $config['web_logo_is_remote'] = 1;
            $config['web_logo_remote'] = handle_subdir_pic($config['web_logo']);
        } else {
            $config['web_logo_is_remote'] = 0;
            $config['web_logo_local'] = handle_subdir_pic($config['web_logo']);
        }

        if (!empty($root_dir)) {
            $config['web_ico'] = preg_replace('#^(/[/\w]+)?(/)#i', $root_dir.'$2', $config['web_ico']); // 支持子目录
        }
        
        /*系统模式*/
        $web_cmsmode = isset($config['web_cmsmode']) ? $config['web_cmsmode'] : 2;
        $this->assign('web_cmsmode', $web_cmsmode);
        /*--end*/

        /*自定义变量*/
        $eyou_row = M('config_attribute')->field('a.attr_id, a.attr_name, a.attr_var_name, a.attr_input_type, b.value, b.id, b.name')
            ->alias('a')
            ->join('__CONFIG__ b', 'b.name = a.attr_var_name AND b.lang = a.lang', 'LEFT')
            ->where([
                'b.lang'    => $this->admin_lang,
                'a.inc_type'    => $inc_type,
                'b.is_del'  => 0,
            ])
            ->order('a.attr_id asc')
            ->select();
        // 支持子目录
        if (!empty($root_dir)) {
            foreach ($eyou_row as $key => $val) {
                $val['value'] = handle_subdir_pic($val['value'], 'html');
                $val['value'] = handle_subdir_pic($val['value']);
                $eyou_row[$key] = $val;
            }
        }
        $this->assign('eyou_row',$eyou_row);
        /*--end*/

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    /**
     * 核心设置
     */
    public function web2()
    {
        $this->language_access(); // 多语言功能操作权限

        $inc_type = 'web';

        if (IS_POST) {
            $param = input('post.');

            /*EyouCMS安装目录*/
            empty($param['web_cmspath']) && $param['web_cmspath'] = ROOT_DIR; // 支持子目录
            $web_cmspath = trim($param['web_cmspath'], '/');
            $web_cmspath = !empty($web_cmspath) ? '/'.$web_cmspath : '';
            $param['web_cmspath'] = $web_cmspath;
            /*--end*/
            /*插件入口*/
            $web_weapp_switch = $param['web_weapp_switch'];
            $web_weapp_switch_old = tpCache('web.web_weapp_switch');
            /*--end*/
            /*自定义后台路径名*/
            $adminbasefile = trim($param['adminbasefile']).'.php'; // 新的文件名
            $param['web_adminbasefile'] = ROOT_DIR.'/'.$adminbasefile; // 支持子目录
            $adminbasefile_old = trim($param['adminbasefile_old']).'.php'; // 旧的文件名
            unset($param['adminbasefile']);
            unset($param['adminbasefile_old']);
            if ('index.php' == $adminbasefile) {
                $this->error("新后台地址禁止使用index", null, '', 1);
            }
            /*--end*/
            $param['web_sqldatapath'] = '/'.trim($param['web_sqldatapath'], '/'); // 数据库备份目录
            $param['web_htmlcache_expires_in'] = intval($param['web_htmlcache_expires_in']); // 页面缓存有效期
            /*多语言入口*/
            $web_language_switch = $param['web_language_switch'];
            $web_language_switch_old = tpCache('web.web_language_switch');
            /*--end*/

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type,$param,$val['mark']);
                    write_global_params($val['mark']); // 写入全局内置参数
                }
            } else {
                tpCache($inc_type,$param);
                write_global_params(); // 写入全局内置参数
            }
            /*--end*/

            $refresh = false;
            $gourl = request()->domain().ROOT_DIR.'/'.$adminbasefile; // 支持子目录
            /*更改自定义后台路径名*/
            if ($adminbasefile_old != $adminbasefile && eyPreventShell($adminbasefile_old)) {
                if (file_exists($adminbasefile_old)) {
                    if(rename($adminbasefile_old, $adminbasefile)) {
                        $refresh = true;
                    }
                } else {
                    $this->error("根目录{$adminbasefile_old}文件不存在！", null, '', 2);
                }
            }
            /*--end*/

            /*更改之后，需要刷新后台的参数*/
            if ($web_weapp_switch_old != $web_weapp_switch || $web_language_switch_old != $web_language_switch) {
                $refresh = true;
            }
            /*--end*/
            
            /*刷新整个后台*/
            if ($refresh) {
                $this->success('操作成功', $gourl, '', 1, [], '_parent');
            }
            /*--end*/

            $this->success('操作成功', U('System/web2'));
        }

        $config = tpCache($inc_type);
        //自定义后台路径名
        $baseFile = explode('/', $this->request->baseFile());
        $web_adminbasefile = end($baseFile);
        $adminbasefile = preg_replace('/^(.*)\.([^\.]+)$/i', '$1', $web_adminbasefile);
        $this->assign('adminbasefile', $adminbasefile);
        // 数据库备份目录
        $sqlbackuppath = config('DATA_BACKUP_PATH');
        $this->assign('sqlbackuppath', $sqlbackuppath);

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    /**
     * 附件设置
     */
    public function basic()
    {
        $inc_type =  'basic';

        if (IS_POST) {
            $param = input('post.');
            /*多语言*/
            if (is_language()) {
                $newParam['basic_indexname'] = $param['basic_indexname'];
                tpCache($inc_type,$newParam);

                $synLangParam = $param; // 同步更新多语言的数据
                unset($synLangParam['basic_indexname']);
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $synLangParam, $val['mark']);
                }
            } else {
                tpCache($inc_type,$param);
            }
            /*--end*/
            $this->success('操作成功', U('System/basic'));
        }

        $config = tpCache($inc_type);
        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    /**
     * 图片水印
     */
    public function water()
    {
        $this->language_access(); // 多语言功能操作权限

        $inc_type =  'water';

        if (IS_POST) {
            $param = input('post.');

            $mark_img_is_remote = !empty($param['mark_img_is_remote']) ? $param['mark_img_is_remote'] : 0;
            $mark_img = '';
            if ($mark_img_is_remote == 1) {
                $mark_img = $param['mark_img_remote'];
            } else {
                $mark_img = $param['mark_img_local'];
            }
            $param['mark_img'] = $mark_img;
            unset($param['mark_img_is_remote']);
            unset($param['mark_img_remote']);
            unset($param['mark_img_local']);

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type,$param);
            }
            /*--end*/
            $this->success('操作成功', U('System/water'));
        }

        $config = tpCache($inc_type);
        if (is_http_url($config['mark_img'])) {
            $config['mark_img_is_remote'] = 1;
            $config['mark_img_remote'] = handle_subdir_pic($config['mark_img']);
        } else {
            $config['mark_img_is_remote'] = 0;
            $config['mark_img_local'] = handle_subdir_pic($config['mark_img']);
        }

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    /**
     * 清空缓存 - 兼容升级没刷新后台，点击报错404，过1.2.5版本之后清除掉代码
     */
    public function clearCache()
    {
        return $this->clear_cache();
    }

    /**
     * 清空缓存
     */
    public function clear_cache()
    {
        if (IS_POST) {
            if (!function_exists('unlink')) {
                $this->error('php.ini未开启unlink函数，请联系空间商处理！');
            }

            $post = input('post.');

            if (!empty($post['clearHtml'])) { // 清除页面缓存
                $this->clearHtmlCache($post['clearHtml']);
            }

            if (!empty($post['clearCache'])) { // 清除数据缓存
                $this->clearSystemCache($post['clearCache']);
            }

            /*兼容每个用户的自定义字段，重新生成数据表字段缓存文件*/
            try {
                schemaTable('arctype');
            } catch (\Exception $e) {}
            try {
                schemaTable('article_content');
            } catch (\Exception $e) {}
            try {
                schemaTable('download_content');
            } catch (\Exception $e) {}
            try {
                schemaTable('images_content');
            } catch (\Exception $e) {}
            try {
                schemaTable('product_content');
            } catch (\Exception $e) {}
            try {
                schemaTable('single_content');
            } catch (\Exception $e) {}
            /*--end*/

            /*清除旧升级备份包，保留最后一个备份文件*/
            $backupArr = glob(DATA_PATH.'backup/v*_www');
            for ($i=0; $i < count($backupArr) - 1; $i++) { 
                delFile($backupArr[$i], true);
            }

            $backupArr = glob(DATA_PATH.'backup/*');
            foreach ($backupArr as $key => $filepath) {
                if (file_exists($filepath) && !stristr($filepath, '.htaccess') && !stristr($filepath, '_www')) {
                    if (is_dir($filepath)) {
                        delFile($filepath, true);
                    } else if (is_file($filepath)) {
                        @unlink($filepath);
                    }
                }
            }
            /*--end*/

            $request = Request::instance();
            $gourl = $request->baseFile();
            $lang = $request->param('lang/s');
            if (!empty($lang) && $lang != get_main_lang()) {
                $gourl .= "?lang={$lang}";
            }
            $this->success('操作成功', $gourl, '', 1, [], '_parent');
        }
        
        return $this->fetch();
    }

    /**
     * 清空数据缓存
     */
    public function fastClearCache($arr = array())
    {
        $this->clearSystemCache();
        $script = "<script>parent.layer.msg('操作成功', {time:3000,icon: 1});window.location='".U('Index/welcome')."';</script>";
        echo $script;
    }

    /**
     * 清空数据缓存
     */
    public function clearSystemCache($arr = array())
    {
        if (empty($arr)) {
            delFile(rtrim(RUNTIME_PATH, '/'), true);
        } else {
            foreach ($arr as $key => $val) {
                delFile(RUNTIME_PATH.$val, true);
            }
        }

        /*多语言*/
        if (is_language()) {
            $langRow = Db::name('language')->order('id asc')
                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                ->select();
            foreach ($langRow as $key => $val) {
                tpCache('global', '', $val['mark']);
            }
        } else { // 单语言
            tpCache('global');
        }
        /*--end*/

        return true;
    }

    /**
     * 清空页面缓存
     */
    public function clearHtmlCache($arr = array())
    {
        if (empty($arr)) {
            delFile(rtrim(HTML_ROOT, '/'), true);
        } else {
            foreach ($arr as $key => $val) {
                $fileList = glob(HTML_ROOT.'http*/'.$val.'*');
                if (!empty($fileList)) {
                    foreach ($fileList as $k2 => $v2) {
                        if (file_exists($v2) && is_dir($v2)) {
                            delFile($v2, true);
                        } else if (file_exists($v2) && is_file($v2)) {
                            @unlink($v2);
                        }
                    }
                }
                if ($val == 'index') {
                    foreach (['index.html','indexs.html'] as $sk1 => $sv1) {
                        $filename = ROOT_PATH.$sv1;
                        if (file_exists($filename)) {
                            @unlink($filename);
                        }
                    }
                }
            }
        }
    }
      
    /**
     * 发送测试邮件
     */
    public function send_email()
    {
        $param = input('post.');
        $res = send_email($param['smtp_test_eamil'],'易优CMS','易优CMS验证码:'.mt_rand(1000,9999), 1);
        exit(json_encode($res));
    }
      
    /**
     * 发送测试短信
     */
    public function send_mobile()
    {
        $param = input('post.');
        $res = sendSms(4,$param['sms_test_mobile'],array('content'=>mt_rand(1000,9999)));
        exit(json_encode($res));
    }

    /**
     * 新增自定义变量
     */
    public function customvar_add()
    {
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $configAttributeM = model('ConfigAttribute');

            $post_data = input('post.');
            $attr_input_type = isset($post_data['attr_input_type']) ? $post_data['attr_input_type'] : '';

            if ($attr_input_type == 3) {
                // 本地/远程图片上传的处理
                $is_remote = !empty($post_data['is_remote']) ? $post_data['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post_data['value_remote'];
                } else {
                    $litpic = $post_data['value_local'];
                }
                $attr_values = $litpic;
            } else {
                $attr_values = input('attr_values');
                // $attr_values = str_replace('_', '', $attr_values); // 替换特殊字符
                // $attr_values = str_replace('@', '', $attr_values); // 替换特殊字符
                $attr_values = trim($attr_values);
                $attr_values = isset($attr_values) ? $attr_values : '';
            }

            $savedata = array(
                'inc_type'    => $post_data['inc_type'],
                'attr_name' => $post_data['attr_name'],
                'attr_input_type'   => $attr_input_type,
                'attr_values'   => $attr_values,
                'update_time'   => getTime(),
            );

            // 数据验证            
            $validate = \think\Loader::validate('ConfigAttribute');
            if(!$validate->batch()->check($savedata))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $this->error($error_msg[0]);
            } else {
                $langRow = Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();

                $attr_var_name = '';
                foreach ($langRow as $key => $val) {
                    $savedata['add_time'] = getTime();
                    $savedata['lang'] = $val['mark'];
                    $insert_id = Db::name('config_attribute')->insertGetId($savedata);
                    // 更新变量名
                    if (!empty($insert_id)) {
                        if (0 == $key) {
                            $attr_var_name = $post_data['inc_type'].'_attr_'.$insert_id;
                        }
                        Db::name('config_attribute')->where([
                                'attr_id'   => $insert_id,
                                'lang'  => $val['mark'],
                            ])->update(array('attr_var_name'=>$attr_var_name));
                    }
                }
                adminLog('新增自定义变量：'.$savedata['attr_name']);

                // 保存到config表，更新缓存
                $inc_type = $post_data['inc_type'];
                $configData = array(
                    $attr_var_name  => $attr_values,
                );

                // 多语言
                if (is_language()) {
                    foreach ($langRow as $key => $val) {
                        tpCache($inc_type, $configData, $val['mark']);
                    }
                } else { // 单语言
                    tpCache($inc_type, $configData);
                }

                $this->success('操作成功');
            }  
        }

        $inc_type = input('param.inc_type/s', '');
        $this->assign('inc_type', $inc_type);

        return $this->fetch();
    }

    /**
     * 编辑自定义变量
     */
    public function customvar_edit()
    {
        if (IS_POST) {
            $configAttributeM = model('ConfigAttribute');

            $post_data = input('post.');
            $attr_input_type = isset($post_data['attr_input_type']) ? $post_data['attr_input_type'] : '';

            if ($attr_input_type == 3) {
                // 本地/远程图片上传的处理
                $is_remote = !empty($post_data['is_remote']) ? $post_data['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post_data['value_remote'];
                } else {
                    $litpic = $post_data['value_local'];
                }
                $attr_values = $litpic;
            } else {
                $attr_values = input('attr_values');
                // $attr_values = str_replace('_', '', $attr_values); // 替换特殊字符
                // $attr_values = str_replace('@', '', $attr_values); // 替换特殊字符
                $attr_values = trim($attr_values);
                $attr_values = isset($attr_values) ? $attr_values : '';
            }

            $savedata = array(
                'inc_type'    => $post_data['inc_type'],
                'attr_name' => $post_data['attr_name'],
                'attr_input_type'   => $attr_input_type,
                'attr_values'   => $attr_values,
                'update_time'   => getTime(),
            );

            // 数据验证            
            $validate = \think\Loader::validate('ConfigAttribute');
            if(!$validate->batch()->check($savedata))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $this->error($error_msg[0]);
            } else {
                $langRow = Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();

                $configAttributeM->data($savedata,true); // 收集数据
                $configAttributeM->isUpdate(true, [
                        'attr_id'   => $post_data['attr_id'],
                        'lang'  => $this->admin_lang,
                    ])->save(); // 写入数据到数据库  
                // 更新变量名
                $attr_var_name = $post_data['name'];
                adminLog('编辑自定义变量：'.$savedata['attr_name']);

                // 保存到config表，更新缓存
                $inc_type = $post_data['inc_type'];
                $configData = array(
                    $attr_var_name  => $attr_values,
                );

                tpCache($inc_type, $configData);

                $this->success('操作成功');
            }  
        }

        $field = array();
        $id = input('param.id/d', 0);
        $field = M('config')->field('a.id, a.value, a.name, b.attr_id, b.attr_name, b.attr_input_type')
            ->alias('a')
            ->join('__CONFIG_ATTRIBUTE__ b', 'a.name=b.attr_var_name AND a.lang=b.lang', 'LEFT')
            ->where([
                'a.id'    => $id,
                'a.lang'  => $this->admin_lang,
            ])->find();
        if ($field['attr_input_type'] == 3) {
            if (is_http_url($field['value'])) {
                $field['is_remote'] = 1;
                $field['value_remote'] = $field['value'];
            } else {
                $field['is_remote'] = 0;
                $field['value_local'] = $field['value'];
            }
        }
        $this->assign('field', $field);

        $inc_type = input('param.inc_type/s', '');
        $this->assign('inc_type', $inc_type);

        return $this->fetch();
    }

    /**
     * 删除自定义变量
     */
    public function customvar_del()
    {
        $this->language_access(); // 多语言功能操作权限

        $id = input('del_id/d');
        if(!empty($id)){
            $attr_var_name = M('config')->where([
                    'id'    => $id,
                    'lang'  => $this->admin_lang,
                ])->getField('name');

            $r = M('config')->where('name', $attr_var_name)->update(array('is_del'=>1, 'update_time'=>getTime()));
            if($r){
                M('config_attribute')->where('attr_var_name', $attr_var_name)->update(array('update_time'=>getTime()));
                adminLog('删除自定义变量：'.$attr_var_name);
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 恢复自定义变量
     */
    public function customvar_recovery()
    {
        $id = input('del_id/d');
        if(!empty($id)){
            $attr_var_name = M('config')->where([
                    'id'    => $id,
                    'lang'  => $this->admin_lang,
                ])->getField('name');

            $r = M('config')->where('name', $attr_var_name)->update(array('is_del'=>0, 'update_time'=>getTime()));
            if($r){
                adminLog('恢复自定义变量：'.$attr_var_name);
                respose(array('status'=>1, 'msg'=>'删除成功'));
            }else{
                respose(array('status'=>0, 'msg'=>'删除失败'));
            }
        }else{
            respose(array('status'=>0, 'msg'=>'参数有误'));
        }
    }

    /**
     * 自定义变量回收站列表
     */
    public function customvar_recycle()
    {
        $admin_lang = $this->admin_lang;
        $list = array();
        $condition = array();
        // 应用搜索条件
        $attr_var_names = M('config')->field('name')
            ->where([
                'is_del'    => 1,
                'lang'  => $admin_lang,
            ])->getAllWithIndex('name');
        $condition['a.attr_var_name'] = array('IN', array_keys($attr_var_names));
        $condition['a.lang']    = $admin_lang;

        $count = M('config_attribute')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new \think\Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = M('config_attribute')->alias('a')
            ->field('a.*, b.id')
            ->join('__CONFIG__ b', 'b.name = a.attr_var_name', 'LEFT')
            ->where($condition)
            ->order('a.update_time desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页对象

        return $this->fetch();
    }

    /**
     * 彻底删除自定义变量
     */
    public function customvar_del_thorough()
    {
        $id = input('del_id/d');
        if(!empty($id)){
            $attr_var_name = M('config')->where([
                    'id'    => $id,
                    'lang'  => $this->admin_lang,
                ])->getField('name');

            $r = M('config')->where('name',$attr_var_name)->delete();
            if($r){
                // 同时删除
                M('config_attribute')->where('attr_var_name',$attr_var_name)->delete();
                adminLog('彻底删除自定义变量：'.$attr_var_name);
                respose(array('status'=>1, 'msg'=>'删除成功'));
            }else{
                respose(array('status'=>0, 'msg'=>'删除失败'));
            }
        }else{
            respose(array('status'=>0, 'msg'=>'参数有误'));
        }
    }
}