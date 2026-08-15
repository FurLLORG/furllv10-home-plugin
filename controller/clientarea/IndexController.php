<?php
namespace addon\furll_home\controller\clientarea;

use app\event\controller\PluginBaseController;
use addon\furll_home\model\FurllHomeBannerModel;
use addon\furll_home\model\FurllHomeRecommendModel;
use addon\furll_home\model\FurllHomePartnerModel;
use addon\furll_home\model\FurllHomeConfigModel;
use app\admin\model\PluginModel;
use think\facade\Db;
use think\facade\View;

/**
 * @title FurLL 官网首页配置(前台)
 * @desc FurLL 官网首页配置(前台)
 * @use addon\furll_home\controller\clientarea\IndexController
 */
class IndexController extends PluginBaseController
{
    /**
     * 官方 default 模板内容区的隔离样式。
     *
     * iframe 保留官方页面与模块的真实 HTML/CSS/JS；仅隐藏已由 FurLLV10 提供的
     * 顶栏和侧栏，避免双重导航。组件仍挂载，官方 goods.js/productdetail.js 的生命周期
     * 与依赖保持不变。
     */
    private function defaultContentShellStyle(): string
    {
        return <<<'CSS'
<style id="furll-default-content-shell">
html, body { height: 100%; margin: 0; overflow: hidden; }
.goods, .product_detail, .goods > .el-container, .product_detail > .el-container,
.goods > .el-container > .el-container, .product_detail > .el-container > .el-container { height: 100%; }
.goods > .el-container > .el-container > .el-main,
.product_detail > .el-container > .el-container > .el-main { height: 100%; margin: 0; padding: 0; border-radius: 0; }
/* Vue 挂载后 aside-menu/top-menu 分别替换为 .el-aside 与 .el-header。 */
.goods > .el-container > .el-aside,
.product_detail > .el-container > .el-aside,
.goods > .el-container > .el-container > div:first-child,
.product_detail > .el-container > .el-container > div:first-child { display: none !important; }
.goods > .el-container > .el-container,
.product_detail > .el-container > .el-container { margin-left: 0 !important; width: 100% !important; }
.goods .config-box, .product_detail .config-box { height: 100%; }
.goods .content, .product_detail .content { min-height: 100%; }
.goods .buy, .goods .add-cart, .goods .cart, .goods .buy-btn, .goods .f-btn,
.goods .f-order .right { display: none !important; }
</style>
CSS;
    }

    /**
     * @title 官方默认商品配置内容
     * @desc 返回官方 pc/default goods.php 的实际渲染结果，移除重复导航供 FurLLV10 iframe 嵌入
     * @url /console/v1/furll_home/default-cart-goods
     * @method GET
     */
    public function defaultCartGoods()
    {
        return $this->renderDefaultContent('goods', true);
    }

    /**
     * @title 官方默认产品详情内容
     * @desc 返回官方 pc/default productdetail.php 的实际渲染结果，移除重复导航供 FurLLV10 iframe 嵌入
     * @url /console/v1/furll_home/default-product-detail
     * @method GET
     */
    public function defaultProductDetail()
    {
        return $this->renderDefaultContent('productdetail', false);
    }

    /** 渲染官方 pc/default 的完整模板，再隐藏与 React 外壳重复的导航。 */
    private function renderDefaultContent(string $page, bool $isCart)
    {
        $defaultTheme = 'pc/default';
        $data = [
            'title'                  => '',
            'template_catalog'       => 'clientarea',
            'themes'                 => $defaultTheme,
            'public_themes'          => $defaultTheme,
            'clientarea_theme_color' => 'default',
            'system_version'         => configuration('system_version'),
        ];

        $PluginModel = new PluginModel();
        $addons = $PluginModel->plugins('addon');
        $data['addons'] = $addons['list'];
        $data = assign_clientarea_lang_config($data);

        $headerPath = IDCSMART_ROOT . 'public/clientarea/template/' . $defaultTheme . '/header.php';
        $footerPath = IDCSMART_ROOT . 'public/clientarea/template/' . $defaultTheme . '/footer.php';
        $header = View::fetch($headerPath, $data);
        $footer = View::fetch($footerPath, $data);

        if ($isCart) {
            $cartTheme = 'pc/default';
            $pageData = [
                'title'                 => '',
                'template_catalog'      => 'clientarea',
                'template_catalog_cart' => 'cart',
                'themes'                => $defaultTheme,
                'themes_cart'           => $cartTheme,
            ];
            View::config([
                'view_path' => '../public/cart/template/' . $cartTheme . '/',
            ]);
        } else {
            $pageData = $data;
            View::config([
                'view_path' => '../public/clientarea/template/' . $defaultTheme . '/',
            ]);
        }

        $content = $header . View::fetch('/' . $page, $pageData) . $footer;

        // 游客态剥离顶栏/侧栏组件：其 created() 钩子会请求需登录接口 /index，
        // 返回 401 后官方 utils/request.js 会把 iframe 重定向到登录页（iframe 地址
        // default-cart-goods 不在其 noNeedJwtUrlArr 免登录白名单内），导致配置区嵌入登录页。
        // 剥离后 Vue 不再挂载这两个组件，也不会触发需登录请求，游客可正常浏览配置表单。
        if (empty(get_client_id())) {
            $content = preg_replace(
                '#<(/)?(?:aside-menu|top-menu)(?:\s[^>]*)?>#i',
                '',
                $content
            );
        }

        return response(str_replace('</head>', $this->defaultContentShellStyle() . '</head>', $content), 200, [
            'Content-Type'  => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-store',
        ]);
    }

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
