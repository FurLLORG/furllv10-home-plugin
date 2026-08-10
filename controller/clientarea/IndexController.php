<?php
namespace addon\furll_home\controller\clientarea;

use app\event\controller\PluginBaseController;
use addon\furll_home\model\FurllHomeBannerModel;
use addon\furll_home\model\FurllHomeRecommendModel;
use addon\furll_home\model\FurllHomePartnerModel;
use addon\furll_home\model\FurllHomeConfigModel;
use think\facade\Db;

/**
 * @title FurLL 官网首页配置(前台)
 * @desc FurLL 官网首页配置(前台)
 * @use addon\furll_home\controller\clientarea\IndexController
 */
class IndexController extends PluginBaseController
{
    /**
     * @title 官网首页配置
     * @desc 返回 FurLLV10 官网首页渲染所需的轮播图、推荐产品、合作伙伴 Logo 配置
     * @url /console/v1/furll_home/home
     * @method GET
     * @return array banners - desc:轮播图列表
     * @return int banners[].id - desc:轮播图ID
     * @return string banners[].title - desc:标题
     * @return string banners[].label - desc:导航标签
     * @return string banners[].description - desc:介绍
     * @return string banners[].image - desc:背景图
     * @return string banners[].url - desc:跳转链接
     * @return string banners[].button_text - desc:按钮文本
     * @return string recommend_enabled - desc:推荐产品开关 0关闭1开启
     * @return array recommends - desc:推荐产品列表
     * @return int recommends[].id - desc:推荐产品ID
     * @return int recommends[].product_id - desc:商品ID
     * @return string recommends[].name - desc:产品名称
     * @return string recommends[].description - desc:介绍
     * @return string recommends[].tag - desc:徽章
     * @return string recommends[].price - desc:价格
     * @return string recommends[].unit - desc:价格单位
     * @return string recommends[].url - desc:跳转链接
     * @return array partners - desc:合作伙伴Logo列表
     * @return int partners[].id - desc:合作伙伴ID
     * @return string partners[].name - desc:名称
     * @return string partners[].image - desc:Logo
     * @return string partners[].url - desc:跳转链接
     * @return int partners[].wall - desc:滚动行1/2
     */
    public function home()
    {
        $FurllHomeBannerModel = new FurllHomeBannerModel();
        $FurllHomeRecommendModel = new FurllHomeRecommendModel();
        $FurllHomePartnerModel = new FurllHomePartnerModel();
        $FurllHomeConfigModel = new FurllHomeConfigModel();

        $param = ['page' => 1, 'limit' => 1000];

        $banners = $FurllHomeBannerModel->furllHomeBannerList($param, 'home');
        $recommends = $FurllHomeRecommendModel->furllHomeRecommendList($param, 'home');
        $partners = $FurllHomePartnerModel->furllHomePartnerList($param, 'home');
        $recommendEnabled = $FurllHomeConfigModel->getConfigValue('recommend_enabled', '1');

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'banners'           => $banners['list'],
                'recommend_enabled' => (string)$recommendEnabled,
                'recommends'        => $recommends['list'],
                'partners'          => $partners['list'],
            ]
        ];
        return json($result);
    }

    /**
     * @title 账单月度统计
     * @desc 最近12个月已支付/未支付订单金额按月汇总（原 rtapi/bill_monthly.php 融合）
     * @url /console/v1/furll_home/bill_monthly
     * @method POST
     * @return array months - desc:最近12个月统计(month/paid/unpaid)
     */
    public function billMonthly()
    {
        $clientId = get_client_id();
        if (empty($clientId)){
            return json(['status' => 401, 'msg' => lang_plugins('not_logged_in')]);
        }

        $startTimestamp = strtotime(date('Y-m-01 00:00:00', strtotime('-12 months')));

        // 已支付：按 pay_time 分组
        $paid = Db::name('order')
            ->field("DATE_FORMAT(FROM_UNIXTIME(pay_time), '%Y-%m') AS month, SUM(amount) AS total_amount")
            ->where('client_id', $clientId)
            ->where('status', 'Paid')
            ->where('pay_time', '>=', $startTimestamp)
            ->group('month')
            ->column('total_amount', 'month');

        // 未支付：按 create_time 分组
        $unpaid = Db::name('order')
            ->field("DATE_FORMAT(FROM_UNIXTIME(create_time), '%Y-%m') AS month, SUM(amount) AS total_amount")
            ->where('client_id', $clientId)
            ->where('status', 'Unpaid')
            ->where('create_time', '>=', $startTimestamp)
            ->group('month')
            ->column('total_amount', 'month');

        // 构建完整12个月数据
        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = date('Y-m', strtotime("-{$i} months"));
            $result[] = [
                'month'  => $monthKey,
                'paid'   => number_format((float)($paid[$monthKey] ?? 0), 2, '.', ''),
                'unpaid' => number_format((float)($unpaid[$monthKey] ?? 0), 2, '.', ''),
            ];
        }

        return json([
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [
                'client_id' => $clientId,
                'months'    => $result,
            ],
        ]);
    }

    /**
     * @title 已安装扩展(addon)列表
     * @desc 返回已启用 addon 插件列表（原 rtapi/addons.php 融合）
     * @url /console/v1/furll_home/addons
     * @method GET
     * @return array addons - desc:已启用插件(id/name/title/url)
     */
    public function addons()
    {
        $clientId = get_client_id();

        $list = Db::name('plugin')
            ->field('id,name,title,url')
            ->where('module', 'addon')
            ->where('status', 1)
            ->orderRaw('`order` ASC, `id` ASC')
            ->select()
            ->toArray();

        return json([
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [
                'client_id' => $clientId,
                'addons'    => $list,
                'count'     => count($list),
            ],
        ]);
    }
}
