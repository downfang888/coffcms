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

namespace app\admin\logic;

use think\Model;
use think\Db;
/**
 * 文档逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
load_trait('controller/Jump');
class ArchivesLogic extends Model
{
    use \traits\controller\Jump;
    
    /**
     * 删除文档
     */
    public function del($del_id = array())
    {
        if (empty($del_id)) {
            $del_id = input('del_id/a');
        }

        $id_arr = eyIntval($del_id);
        if(!empty($id_arr)){
            /*分离并组合相同模型下的文档ID*/
            $row = db('archives')
                ->alias('a')
                ->field('a.channel,a.aid,b.ctl_name')
                ->join('__CHANNELTYPE__ b', 'a.channel = b.id', 'LEFT')
                ->where([
                    'a.aid' => ['IN', $id_arr],
                    'a.lang'    => get_admin_lang(),
                ])
                ->select();
            $data = array();
            foreach ($row as $key => $val) {
                $data[$val['channel']]['aid'][] = $val['aid'];
                $data[$val['channel']]['ctl_name'] = $val['ctl_name'];
            }
            /*--end*/

            $err = 0;
            foreach ($data as $key => $val) {
                $r = M('archives')->where('aid','IN',$val['aid'])->delete();
                if ($r) {
                    model($val['ctl_name'])->afterDel($val['aid']);
                    adminLog('删除文档-id：'.implode(',', $val['aid']));
                } else {
                    $err++;
                }
            }

            if (0 == $err) {
                $this->success('删除成功');
            } else if ($err < count($data)) {
                $this->success('删除部分成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }
}
