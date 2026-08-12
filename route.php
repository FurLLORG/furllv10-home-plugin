<?php
/**
 * FurLL 官网首页配置插件路由
 * @copyright Copyright (c) 2026 FurLLCN (官网 furll.cn)
 */
use think\facade\Route;

# 前台（官网首页配置，无需登录）
Route::group('console/v1',function (){
    # 官网首页配置
    Route::get('furll_home/home', "\\addon\\furll_home\\controller\\clientarea\\IndexController@home")
        ->append(['_plugin'=>'furll_home','_controller'=>'index','_action'=>'home']);

    # 官方 default 模板内容壳（供 FurLLV10 iframe 使用，外层导航由 React 提供）
    Route::get('furll_home/default-cart-goods', "\\addon\\furll_home\\controller\\clientarea\\IndexController@defaultCartGoods")
        ->append(['_plugin'=>'furll_home','_controller'=>'index','_action'=>'default_cart_goods']);
    Route::get('furll_home/default-product-detail', "\\addon\\furll_home\\controller\\clientarea\\IndexController@defaultProductDetail")
        ->append(['_plugin'=>'furll_home','_controller'=>'index','_action'=>'default_product_detail']);

    # 账单月度统计（原 rtapi/bill_monthly.php 融合，需登录）
    Route::post('furll_home/bill_monthly', "\\addon\\furll_home\\controller\\clientarea\\IndexController@billMonthly")
        ->append(['_plugin'=>'furll_home','_controller'=>'index','_action'=>'bill_monthly']);

    # 已安装扩展列表（原 rtapi/addons.php 融合，需登录）
    Route::get('furll_home/addons', "\\addon\\furll_home\\controller\\clientarea\\IndexController@addons")
        ->append(['_plugin'=>'furll_home','_controller'=>'index','_action'=>'addons']);
})
    ->allowCrossDomain([
        'Access-Control-Allow-Origin'        => $origin,
        'Access-Control-Allow-Credentials'   => 'true',
        'Access-Control-Max-Age'             => 600,
    ])
    ->middleware(\app\http\middleware\Check::class)
    ->middleware(\app\http\middleware\ParamFilter::class);

# 后台（官网首页配置管理）
Route::group(DIR_ADMIN . '/v1',function (){
    # 轮播图
    Route::get('furll_home/banner', "\\addon\\furll_home\\controller\\AdminIndexController@bannerList")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'banner_list']);
    Route::get('furll_home/banner/:id', "\\addon\\furll_home\\controller\\AdminIndexController@bannerDetail")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'banner_detail']);
    Route::post('furll_home/banner', "\\addon\\furll_home\\controller\\AdminIndexController@createBanner")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'create_banner']);
    Route::put('furll_home/banner/:id', "\\addon\\furll_home\\controller\\AdminIndexController@updateBanner")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'update_banner']);
    Route::delete('furll_home/banner/:id', "\\addon\\furll_home\\controller\\AdminIndexController@deleteBanner")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'delete_banner']);

    # 推荐产品
    Route::get('furll_home/recommend', "\\addon\\furll_home\\controller\\AdminIndexController@recommendList")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'recommend_list']);
    Route::get('furll_home/recommend/:id', "\\addon\\furll_home\\controller\\AdminIndexController@recommendDetail")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'recommend_detail']);
    Route::post('furll_home/recommend', "\\addon\\furll_home\\controller\\AdminIndexController@createRecommend")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'create_recommend']);
    Route::put('furll_home/recommend/:id', "\\addon\\furll_home\\controller\\AdminIndexController@updateRecommend")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'update_recommend']);
    Route::delete('furll_home/recommend/:id', "\\addon\\furll_home\\controller\\AdminIndexController@deleteRecommend")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'delete_recommend']);

    # 合作伙伴
    Route::get('furll_home/partner', "\\addon\\furll_home\\controller\\AdminIndexController@partnerList")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'partner_list']);
    Route::get('furll_home/partner/:id', "\\addon\\furll_home\\controller\\AdminIndexController@partnerDetail")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'partner_detail']);
    Route::post('furll_home/partner', "\\addon\\furll_home\\controller\\AdminIndexController@createPartner")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'create_partner']);
    Route::put('furll_home/partner/:id', "\\addon\\furll_home\\controller\\AdminIndexController@updatePartner")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'update_partner']);
    Route::delete('furll_home/partner/:id', "\\addon\\furll_home\\controller\\AdminIndexController@deletePartner")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'delete_partner']);

    # 配置
    Route::get('furll_home/config', "\\addon\\furll_home\\controller\\AdminIndexController@configDetail")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'config_detail']);
    Route::put('furll_home/config', "\\addon\\furll_home\\controller\\AdminIndexController@configUpdate")
        ->append(['_plugin'=>'furll_home','_controller'=>'admin_index','_action'=>'config_update']);
})
    ->allowCrossDomain([
        'Access-Control-Allow-Origin'        => $origin,
        'Access-Control-Allow-Credentials'   => 'true',
        'Access-Control-Max-Age'             => 600,
    ])
    ->middleware(\app\http\middleware\CheckAdmin::class)
    ->middleware(\app\http\middleware\ParamFilter::class);
