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

namespace app\common\logic;

// load_trait('controller/Jump');

/**
 * Class WeappLogic
 * 插件逻辑
 */
class WeappLogic
{
    // use \traits\controller\Jump;
    /**
     * 构造函数
     */
    public function __construct() 
    {

    }

    /**
     * 更新插件到数据库
     * @param $weapp_list array 本地插件数组
     */
    public function insertWeapp(){
        $row = M('weapp')->field('code')->getAllWithIndex('code'); // 数据库
        $new_arr = array(); // 本地
        $addData = array(); // 数据存储变量
        $weapp_list = $this->scanWeapp();
        //  本地对比数据库
        foreach($weapp_list as $k=>$v){
            $code = isset($v['code']) ? $v['code'] : 'error_'.date('Ymd');
            /*初步过滤不规范插件*/
            if ($k != $code) {
                continue;
            }
            /*--end*/
            $tmp['code'] = $code;
            $new_arr[] = $tmp;
            // 对比数据库 本地有 数据库没有
            if(empty($row[$code])){
                $addData[] = array(
                    'code'          => $code,
                    'name'          => isset($v['name']) ? $v['name'] : '配置信息不完善',
                    'author'        => isset($v['author']) ? $v['author'] : '',
                    'description'   => isset($v['description']) ? $v['description'] : '插件制作不符合官方规范',
                    'scene'         => isset($v['scene']) ? $v['scene'] : '',
                    'config'        => empty($v) ? '' : json_encode($v),
                    'add_time'      => getTime(),
                );
            }
        }
        if (!empty($addData)) {
            M('weapp')->insertAll($addData);
        }
        //数据库有 本地没有
        foreach($row as $k => $v){
            if (!in_array($v, $new_arr)) {
                M('weapp')->where($v)->delete();
            }
        }
    }

    /**
     * 插件目录扫描
     * @return array 返回目录数组
     */
    private function scanWeapp(){
        $dir = rtrim(WEAPP_PATH, DS);
        $weapp_list = $this->dirscan($dir);
        
        foreach($weapp_list as $k=>$v){
            if (!file_exists(WEAPP_PATH.$v.'/config.php')) {
                unset($weapp_list[$k]);
            }
            else
            {
                $weapp_list[$v] = include(WEAPP_PATH.$v.'/config.php');
                unset($weapp_list[$k]);                    
            }
        }
        return $weapp_list;
    }

    /**
     * 获取插件目录列表
     * @param $dir
     * @return array
     */
    private function dirscan($dir){
        $dirArray = array();
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while ( false !== ($file = readdir ($handle)) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".." && !strpos($file,".")) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            //关闭句柄
            closedir($handle);
        }
        return $dirArray;
    }

    /**
     * 插件基类构造方法
     * sm：module        插件模块
     * sc：controller    插件控制器
     * sa：action        插件操作
     */
    public function checkInstall()
    {
        $msg = true;
        if(!array_key_exists("sm", request()->param())){
            $msg = '无效插件URL！';
        } else {
            $module = request()->param('sm');
            $module = $module ?: request()->param('sc');
            $row = M('Weapp')->field('code, status')
                ->where(array('code'=>$module))
                ->find();
            if (empty($row)) {
                $msg = "{$module}插件不存在！";
            } else {
                if ($row['status'] == -1) {
                    $msg = "请先启用{$module}插件！";
                } else if (intval($row['status']) == 0) {
                    $msg = "请先安装{$module}插件！";
                }
            }
        }

        return $msg;
    }
}