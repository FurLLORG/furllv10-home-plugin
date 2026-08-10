<?php
namespace addon\furll_home\controller;

use app\event\controller\PluginAdminBaseController;
use addon\furll_home\model\FurllHomeBannerModel;
use addon\furll_home\model\FurllHomeRecommendModel;
use addon\furll_home\model\FurllHomePartnerModel;
use addon\furll_home\model\FurllHomeConfigModel;
use addon\furll_home\validate\FurllHomeValidate;

/**
 * @title FurLL 官网首页配置(后台)
 * @desc FurLL 官网首页配置(后台)
 * @use addon\furll_home\controller\AdminIndexController
 */
class AdminIndexController extends PluginAdminBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new FurllHomeValidate();
    }

    /**
     * @title 轮播图列表
     * @desc 轮播图列表
     * @author FurLLCN
     * @version v1
     * @url /admin/v1/furll_home/banner
     * @method GET
     * @param string keywords - desc:关键字,搜索范围:标题 validate:optional
     * @return array list - desc:轮播图
     */
    public function bannerList()
    {
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        $FurllHomeBannerModel = new FurllHomeBannerModel();
        $data = $FurllHomeBannerModel->furllHomeBannerList($param, 'admin');

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * @title 轮播图详情
     * @desc 轮播图详情
     * @url /admin/v1/furll_home/banner/:id
     * @method GET
     * @param int id - desc:轮播图ID validate:required
     */
    public function bannerDetail()
    {
        $param = $this->request->param();

        $FurllHomeBannerModel = new FurllHomeBannerModel();
        $banner = $FurllHomeBannerModel->furllHomeBannerDetail($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'banner' => $banner
            ]
        ];
        return json($result);
    }

    /**
     * @title 添加轮播图
     * @desc 添加轮播图
     * @url /admin/v1/furll_home/banner
     * @method POST
     * @param string title - desc:标题 validate:required
     * @param string label - desc:导航标签 validate:optional
     * @param string description - desc:介绍 validate:optional
     * @param string image - desc:背景图 validate:optional
     * @param string url - desc:跳转链接 validate:optional
     * @param string button_text - desc:按钮文本 validate:optional
     * @param int sort - desc:排序 validate:optional
     * @param int hidden - desc:0显示1隐藏 validate:optional
     */
    public function createBanner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('create_banner')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        // 显示状态的轮播图最多4个
        if (($param['hidden'] ?? 0) == 0){
            $visibleCount = (new FurllHomeBannerModel())->where('hidden', 0)->count();
            if ($visibleCount >= 4){
                return json(['status' => 400, 'msg' => lang_plugins('banner_max_tip')]);
            }
        }

        $FurllHomeBannerModel = new FurllHomeBannerModel();
        $result = $FurllHomeBannerModel->createFurllHomeBanner($param);

        return json($result);
    }

    /**
     * @title 修改轮播图
     * @desc 修改轮播图
     * @url /admin/v1/furll_home/banner/:id
     * @method PUT
     * @param int id - desc:轮播图ID validate:required
     * @param string title - desc:标题 validate:required
     * @param string label - desc:导航标签 validate:optional
     * @param string description - desc:介绍 validate:optional
     * @param string image - desc:背景图 validate:optional
     * @param string url - desc:跳转链接 validate:optional
     * @param string button_text - desc:按钮文本 validate:optional
     * @param int sort - desc:排序 validate:optional
     * @param int hidden - desc:0显示1隐藏 validate:optional
     */
    public function updateBanner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('update_banner')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        // 显示状态的轮播图最多4个（排除当前记录）
        $banner = (new FurllHomeBannerModel())->find($param['id']);
        $newHidden = isset($param['hidden']) ? $param['hidden'] : ($banner['hidden'] ?? 0);
        if ($newHidden == 0){
            $visibleCount = (new FurllHomeBannerModel())->where('hidden', 0)->where('id', '<>', $param['id'])->count();
            if ($visibleCount >= 4){
                return json(['status' => 400, 'msg' => lang_plugins('banner_max_tip')]);
            }
        }

        $FurllHomeBannerModel = new FurllHomeBannerModel();
        $result = $FurllHomeBannerModel->updateFurllHomeBanner($param);

        return json($result);
    }

    /**
     * @title 删除轮播图
     * @desc 删除轮播图
     * @url /admin/v1/furll_home/banner/:id
     * @method DELETE
     * @param int id - desc:轮播图ID validate:required
     */
    public function deleteBanner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('delete')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        $FurllHomeBannerModel = new FurllHomeBannerModel();
        $result = $FurllHomeBannerModel->deleteFurllHomeBanner($param['id']);

        return json($result);
    }

    /**
     * @title 推荐产品列表
     * @desc 推荐产品列表
     * @url /admin/v1/furll_home/recommend
     * @method GET
     * @param string keywords - desc:关键字,搜索范围:名称 validate:optional
     * @return array list - desc:推荐产品
     */
    public function recommendList()
    {
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        $FurllHomeRecommendModel = new FurllHomeRecommendModel();
        $data = $FurllHomeRecommendModel->furllHomeRecommendList($param, 'admin');

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * @title 推荐产品详情
     * @desc 推荐产品详情
     * @url /admin/v1/furll_home/recommend/:id
     * @method GET
     * @param int id - desc:推荐产品ID validate:required
     */
    public function recommendDetail()
    {
        $param = $this->request->param();

        $FurllHomeRecommendModel = new FurllHomeRecommendModel();
        $recommend = $FurllHomeRecommendModel->furllHomeRecommendDetail($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'recommend' => $recommend
            ]
        ];
        return json($result);
    }

    /**
     * @title 添加推荐产品
     * @desc 添加推荐产品
     * @url /admin/v1/furll_home/recommend
     * @method POST
     * @param int product_id - desc:商品ID validate:optional
     * @param string name - desc:产品名称 validate:required
     * @param string description - desc:介绍 validate:optional
     * @param string tag - desc:徽章 validate:optional
     * @param string price - desc:价格 validate:optional
     * @param string unit - desc:价格单位 validate:optional
     * @param string url - desc:跳转链接 validate:optional
     * @param int sort - desc:排序 validate:optional
     * @param int hidden - desc:0显示1隐藏 validate:optional
     */
    public function createRecommend()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('create_recommend')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        // 显示状态的推荐产品最多4个
        if (($param['hidden'] ?? 0) == 0){
            $visibleCount = (new FurllHomeRecommendModel())->where('hidden', 0)->count();
            if ($visibleCount >= 4){
                return json(['status' => 400, 'msg' => lang_plugins('recommend_max_tip')]);
            }
        }

        $FurllHomeRecommendModel = new FurllHomeRecommendModel();
        $result = $FurllHomeRecommendModel->createFurllHomeRecommend($param);

        return json($result);
    }

    /**
     * @title 修改推荐产品
     * @desc 修改推荐产品
     * @url /admin/v1/furll_home/recommend/:id
     * @method PUT
     * @param int id - desc:推荐产品ID validate:required
     * @param int product_id - desc:商品ID validate:optional
     * @param string name - desc:产品名称 validate:required
     * @param string description - desc:介绍 validate:optional
     * @param string tag - desc:徽章 validate:optional
     * @param string price - desc:价格 validate:optional
     * @param string unit - desc:价格单位 validate:optional
     * @param string url - desc:跳转链接 validate:optional
     * @param int sort - desc:排序 validate:optional
     * @param int hidden - desc:0显示1隐藏 validate:optional
     */
    public function updateRecommend()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('update_recommend')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        // 显示状态的推荐产品最多4个（排除当前记录）
        $recommend = (new FurllHomeRecommendModel())->find($param['id']);
        $newHidden = isset($param['hidden']) ? $param['hidden'] : ($recommend['hidden'] ?? 0);
        if ($newHidden == 0){
            $visibleCount = (new FurllHomeRecommendModel())->where('hidden', 0)->where('id', '<>', $param['id'])->count();
            if ($visibleCount >= 4){
                return json(['status' => 400, 'msg' => lang_plugins('recommend_max_tip')]);
            }
        }

        $FurllHomeRecommendModel = new FurllHomeRecommendModel();
        $result = $FurllHomeRecommendModel->updateFurllHomeRecommend($param);

        return json($result);
    }

    /**
     * @title 删除推荐产品
     * @desc 删除推荐产品
     * @url /admin/v1/furll_home/recommend/:id
     * @method DELETE
     * @param int id - desc:推荐产品ID validate:required
     */
    public function deleteRecommend()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('delete')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        $FurllHomeRecommendModel = new FurllHomeRecommendModel();
        $result = $FurllHomeRecommendModel->deleteFurllHomeRecommend($param['id']);

        return json($result);
    }

    /**
     * @title 合作伙伴列表
     * @desc 合作伙伴列表
     * @url /admin/v1/furll_home/partner
     * @method GET
     * @param string keywords - desc:关键字,搜索范围:名称 validate:optional
     * @param int wall - desc:滚动行1/2 validate:optional
     * @return array list - desc:合作伙伴
     */
    public function partnerList()
    {
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        $FurllHomePartnerModel = new FurllHomePartnerModel();
        $data = $FurllHomePartnerModel->furllHomePartnerList($param, 'admin');

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * @title 合作伙伴详情
     * @desc 合作伙伴详情
     * @url /admin/v1/furll_home/partner/:id
     * @method GET
     * @param int id - desc:合作伙伴ID validate:required
     */
    public function partnerDetail()
    {
        $param = $this->request->param();

        $FurllHomePartnerModel = new FurllHomePartnerModel();
        $partner = $FurllHomePartnerModel->furllHomePartnerDetail($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'partner' => $partner
            ]
        ];
        return json($result);
    }

    /**
     * @title 添加合作伙伴
     * @desc 添加合作伙伴
     * @url /admin/v1/furll_home/partner
     * @method POST
     * @param string name - desc:名称 validate:required
     * @param string image - desc:Logo validate:optional
     * @param string url - desc:跳转链接 validate:optional
     * @param int wall - desc:滚动行1/2 validate:optional
     * @param int sort - desc:排序 validate:optional
     * @param int hidden - desc:0显示1隐藏 validate:optional
     */
    public function createPartner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('create_partner')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        $FurllHomePartnerModel = new FurllHomePartnerModel();
        $result = $FurllHomePartnerModel->createFurllHomePartner($param);

        return json($result);
    }

    /**
     * @title 修改合作伙伴
     * @desc 修改合作伙伴
     * @url /admin/v1/furll_home/partner/:id
     * @method PUT
     * @param int id - desc:合作伙伴ID validate:required
     * @param string name - desc:名称 validate:required
     * @param string image - desc:Logo validate:optional
     * @param string url - desc:跳转链接 validate:optional
     * @param int wall - desc:滚动行1/2 validate:optional
     * @param int sort - desc:排序 validate:optional
     * @param int hidden - desc:0显示1隐藏 validate:optional
     */
    public function updatePartner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('update_partner')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        $FurllHomePartnerModel = new FurllHomePartnerModel();
        $result = $FurllHomePartnerModel->updateFurllHomePartner($param);

        return json($result);
    }

    /**
     * @title 删除合作伙伴
     * @desc 删除合作伙伴
     * @url /admin/v1/furll_home/partner/:id
     * @method DELETE
     * @param int id - desc:合作伙伴ID validate:required
     */
    public function deletePartner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('delete')->check($param)){
            return json(['status' => 400, 'msg' => lang_plugins($this->validate->getError())]);
        }

        $FurllHomePartnerModel = new FurllHomePartnerModel();
        $result = $FurllHomePartnerModel->deleteFurllHomePartner($param['id']);

        return json($result);
    }

    /**
     * @title 获取配置
     * @desc 获取配置
     * @url /admin/v1/furll_home/config
     * @method GET
     * @return string recommend_enabled - desc:推荐产品开关 0关闭1开启
     */
    public function configDetail()
    {
        $FurllHomeConfigModel = new FurllHomeConfigModel();
        $config = $FurllHomeConfigModel->getConfigList();

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'config' => $config
            ]
        ];
        return json($result);
    }

    /**
     * @title 保存配置
     * @desc 保存配置
     * @url /admin/v1/furll_home/config
     * @method PUT
     * @param int recommend_enabled - desc:推荐产品开关 0关闭1开启 validate:optional
     */
    public function configUpdate()
    {
        $param = $this->request->param();

        $FurllHomeConfigModel = new FurllHomeConfigModel();

        if (isset($param['recommend_enabled'])){
            $FurllHomeConfigModel->setConfigValue('recommend_enabled', $param['recommend_enabled']);
        }

        return json(['status' => 200, 'msg' => lang_plugins('update_success')]);
    }
}
