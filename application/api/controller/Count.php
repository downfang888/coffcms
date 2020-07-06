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

class Count extends Base
{
    /*
     * 初始化操作
     */
    
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 内容页浏览量的自增
     */
    public function view()
    {
        $aid = I('aid/d', 0);
        $click = 0;
        if (empty($aid)) {
            echo($click);
            exit;
        }

        if ($aid > 0) {
            M('archives')->where(array('aid'=>$aid))->setInc('click'); 
            $click = M('archives')->where(array('aid'=>$aid))->getField('click');
        }

        echo($click);
        exit;
    }
}