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

namespace app\plugins\controller;
use think\Db;

class Minipro0001 extends Base
{
    // 小程序插件标识
    public $nid = '';
    // 模型对象
    public $modelObj = '';

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->nid = CONTROLLER_NAME;
        $this->modelObj = model('Minipro0001');
    }

    /**
     * 全局常量API
     */
    public function globals()
    {
        $data = $this->modelObj->getGlobalsConf();

        exit(json_encode($data));
    }

    /**
     * 首页API
     */
    public function index()
    {
        /*配置值*/
        $indexConfig = $this->modelObj->getHomeConf();
        /*--end*/

        /*幻灯片*/
        $aid = !empty($indexConfig['swipers']['aid']) ? $indexConfig['swipers']['aid'] : '';
        $swipersList = $this->modelObj->getSwipersList($aid);
        $swipersData = array(
            'list' => $swipersList,
        );
        /*--end*/

        /*栏目列表*/
        $cateData = array(
            'list' => !empty($indexConfig['cate']) ? $indexConfig['cate'] : '',
        );
        /*--end*/

        /*公告*/
        $noticeData = array(
            // 'title' => !empty($indexConfig['notice']['title']) ? $indexConfig['notice']['title'] : '',
            // 'icon' => !empty($indexConfig['notice']['icon']) ? $indexConfig['notice']['icon'] : $this->modelObj->getImgRealpath('notice.png'),
        );
        /*--end*/

        /*图集中心*/
        $map = array(
            'channel' => !empty($indexConfig['images']['channel']) ? $indexConfig['images']['channel'] : 0,
            'typeid' => !empty($indexConfig['images']['typeid']) ? $indexConfig['images']['typeid'] : 0,
        );
        $num = !empty($indexConfig['images']['num']) ? $indexConfig['images']['num'] : '';
        $imagesList = $this->modelObj->getArchivesList($map, 1, $num, 'aid,title,litpic');
        $imagesData = array(
            // 'title' => !empty($indexConfig['images']['title']) ? $indexConfig['images']['title'] : '',
            'list' => $imagesList['list'],
        );
        /*--end*/

        /*文章中心*/
        $map = array(
            'channel' => !empty($indexConfig['article']['channel']) ? $indexConfig['article']['channel'] : 0,
            'typeid' => !empty($indexConfig['article']['typeid']) ? $indexConfig['article']['typeid'] : 0,
        );
        $num = !empty($indexConfig['article']['num']) ? $indexConfig['article']['num'] : '';
        $articleList = $this->modelObj->getArchivesList($map, 1, $num, 'aid,title,litpic,seo_description,add_time');
        $articleData = array(
            // 'title' => !empty($indexConfig['article']['title']) ? $indexConfig['article']['title'] : '',
            'list' => $articleList['list'],
        );
        /*--end*/

        $globalData = $this->modelObj->getGlobalsConf();

        $data = array(
            'swipersData' => $swipersData,
            'cateData' => $cateData,
            'noticeData' => $noticeData,
            'articleData' => $articleData,
            'imagesData' => $imagesData,
            'miniproConfig' => $indexConfig,
            'globalData' => $globalData,
        );
        exit(json_encode($data));
    }

    /**
     * 全部栏目
     */
    public function arctype($typeid = '')
    {
        /*配置值*/
        $arctypeConfig = $this->modelObj->getValue('arctype');
        /*--end*/

        /*栏目列表*/
        $data = $this->modelObj->getArctype($typeid);
        if (is_array($data['row'])) {
            $data['row'] = array_merge(array(), $data['row']); // 重置数组键名
        }
        /*--end*/

        exit(json_encode($data));
    }

    /**
     * 文档列表
     */
    public function lists($typeid = '', $page = 1)
    {
        /*文档列表*/
        $map = array(
            'typeid' => $typeid,
        );
        $data = $this->modelObj->getArchivesList($map, $page);
        /*--end*/

        exit(json_encode($data));
    }

    /**
     * 文档详情（文章、产品、图集、下载）
     */
    public function view($aid)
    {
        $data = $this->modelObj->getArchivesView($aid);

        exit(json_encode($data));
    }

    /**
     * 单页栏目
     */
    public function single($typeid)
    {
        $data = $this->modelObj->getSingleView($typeid);

        exit(json_encode($data));
    }

    /**
     * 留言栏目
     */
    public function guestbook($typeid = '')
    {
        if (IS_POST) {
            $post = I('post.');
            $data = $this->modelObj->getGuestbookSubmit($post);
            exit(json_encode($data));
        } else {
            $data = $this->modelObj->getGuestbookForm($typeid);

            exit(json_encode($data)); 
        }

    }

    /**
     * 关于我们
     */
    public function about()
    {
        $data = $this->modelObj->getAbout();

        exit(json_encode($data));
    }
}