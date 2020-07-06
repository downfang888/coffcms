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

namespace app\api\controller;

class Rewrite extends Base
{
    /*
     * 初始化操作
     */
    
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 检测服务器是否支持URL重写隐藏应用的入口文件index.php
     */
    public function testing()
    {
        ob_clean();
        exit('Congratulations on passing');
    }

    /**
     * 设置隐藏index.php
     */
    public function setInlet()
    {
        $seo_inlet = input('param.seo_inlet/d', 1);
        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('seo', ['seo_inlet'=>$seo_inlet], $val['mark']);
            }
        } else { // 单语言
            tpCache('seo', ['seo_inlet'=>$seo_inlet]);
        }
        /*--end*/
        ob_clean();
        exit('Congratulations on passing');
    }
}