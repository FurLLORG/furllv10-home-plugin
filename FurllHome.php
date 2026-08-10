<?php
namespace addon\furll_home;

use app\common\lib\Plugin;
use think\facade\Db;

require_once __DIR__ . '/common.php';

/**
 * FurLL 官网首页配置
 * @desc 配置 FurLLV10 官网首页轮播图、推荐产品、合作伙伴 Logo
 * @copyright Copyright (c) 2026 FurLLCN (官网 furll.cn)
 */
class FurllHome extends Plugin
{
    # 插件基本信息
    public $info = array(
        'name'        => 'FurllHome', //插件英文名,作为插件唯一标识
        'title'       => 'FurLL 官网首页配置',
        'description' => '配置 FurLLV10 官网首页：轮播图、推荐产品、合作伙伴 Logo',
        'author'      => 'FurLLCN',  //开发者
        'version'     => '1.0.0',      // 版本号
    );

    # 插件安装
    public function install()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_banner`",
            "CREATE TABLE `idcsmart_addon_furll_home_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '轮播图ID',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '标题',
  `label` varchar(100) NOT NULL DEFAULT '' COMMENT '导航标签',
  `description` varchar(500) NOT NULL DEFAULT '' COMMENT '介绍',
  `image` varchar(500) NOT NULL DEFAULT '' COMMENT '背景图',
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `button_text` varchar(100) NOT NULL DEFAULT '' COMMENT '按钮文本',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:显示1:隐藏',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='官网首页轮播图'",
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_recommend`",
            "CREATE TABLE `idcsmart_addon_furll_home_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐产品ID',
  `product_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '产品名称',
  `description` varchar(500) NOT NULL DEFAULT '' COMMENT '介绍',
  `tag` varchar(100) NOT NULL DEFAULT '' COMMENT '徽章',
  `price` varchar(50) NOT NULL DEFAULT '' COMMENT '价格',
  `unit` varchar(50) NOT NULL DEFAULT '' COMMENT '价格单位',
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:显示1:隐藏',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='官网首页推荐产品'",
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_partner`",
            "CREATE TABLE `idcsmart_addon_furll_home_partner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '合作伙伴ID',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '名称',
  `image` varchar(500) NOT NULL DEFAULT '' COMMENT 'Logo',
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `wall` tinyint(1) NOT NULL DEFAULT '1' COMMENT '滚动行1/2',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:显示1:隐藏',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `wall` (`wall`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='官网首页合作伙伴Logo'",
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_config`",
            "CREATE TABLE `idcsmart_addon_furll_home_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '配置名',
  `value` text NOT NULL COMMENT '配置值',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='官网首页配置'",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }

        # 默认配置：推荐产品开关
        $now = time();
        Db::name('addon_furll_home_config')->insertAll([
            ['name' => 'recommend_enabled', 'value' => '1', 'create_time' => $now, 'update_time' => $now],
        ]);

        # 默认轮播图（与 FurLLV10 首页内置 BANNERS 保持一致）
        $banners = [
            ['title' => '高性能云服务器', 'label' => '高性能云', 'description' => '优质稳定网络，满血性能释放，高防不惧攻击', 'image' => '/images/home/banners/banner-1.png', 'url' => '', 'button_text' => '立即购买', 'sort' => 0],
            ['title' => '锐驰带宽 轻装上阵', 'label' => '锐驰带宽', 'description' => '提供高性价比的大带宽云服务器解决方案', 'image' => '/images/home/banners/banner-2.png', 'url' => '', 'button_text' => '立即购买', 'sort' => 1],
            ['title' => 'FurLL 易上云', 'label' => '易上云', 'description' => '简单易上手 高性价比的香港云服务器方案', 'image' => '/images/home/banners/banner-3.png', 'url' => '', 'button_text' => '快速上云', 'sort' => 2],
            ['title' => 'AI 智防引擎', 'label' => '智防引擎', 'description' => '提供完善高性价比的安全边缘分发服务', 'image' => '/images/home/banners/banner-4.png', 'url' => '', 'button_text' => '立即体验', 'sort' => 3],
        ];
        foreach ($banners as $key => $value) {
            $banners[$key]['hidden'] = 0;
            $banners[$key]['admin_id'] = 0;
            $banners[$key]['create_time'] = $now;
            $banners[$key]['update_time'] = $now;
        }
        Db::name('addon_furll_home_banner')->insertAll($banners);

        # 默认推荐产品（与 FurLLV10 首页内置 HERO_RECOMMENDS 保持一致）
        $recommends = [
            ['product_id' => 0, 'name' => '海外加速白银版', 'description' => '网站类', 'tag' => '限时推荐', 'price' => '4.20', 'unit' => '/ 月', 'url' => '', 'sort' => 0],
            ['product_id' => 0, 'name' => '高质量云电脑 2核 2GB A型', 'description' => '可选 Windows / Linux', 'tag' => '热卖', 'price' => '3.60', 'unit' => '/ 月', 'url' => '', 'sort' => 1],
            ['product_id' => 0, 'name' => '香港精品CN2 2核 2GB', 'description' => '25Mbps · 双向500G', 'tag' => '', 'price' => '40.00', 'unit' => '/ 月', 'url' => '', 'sort' => 2],
            ['product_id' => 0, 'name' => '美国轻量云服务器 A型', 'description' => '不限流量 · 回国优化', 'tag' => '', 'price' => '16.00', 'unit' => '/ 月', 'url' => '', 'sort' => 3],
        ];
        foreach ($recommends as $key => $value) {
            $recommends[$key]['hidden'] = 0;
            $recommends[$key]['admin_id'] = 0;
            $recommends[$key]['create_time'] = $now;
            $recommends[$key]['update_time'] = $now;
        }
        Db::name('addon_furll_home_recommend')->insertAll($recommends);

        # 默认合作伙伴 Logo（仅保留 FurLL）
        $partners = [
            ['name' => 'FurLL', 'image' => '/plugins/addon/furll_home/template/admin/img/partners/furll.png', 'url' => '', 'wall' => 1, 'sort' => 0],
        ];
        foreach ($partners as $key => $value) {
            $partners[$key]['hidden'] = 0;
            $partners[$key]['admin_id'] = 0;
            $partners[$key]['create_time'] = $now;
            $partners[$key]['update_time'] = $now;
        }
        Db::name('addon_furll_home_partner')->insertAll($partners);

        return true;
    }

    # 插件卸载
    public function uninstall()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_banner`",
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_recommend`",
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_partner`",
            "DROP TABLE IF EXISTS `idcsmart_addon_furll_home_config`",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }
        return true;
    }

    # 插件升级
    public function upgrade()
    {
        return true;
    }
}
