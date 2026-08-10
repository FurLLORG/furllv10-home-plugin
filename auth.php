<?php
/*
 *  定义权限
 */
return [
    [
        'title' => 'auth_site_management_furll_home',
        'url' => '',
        'description' => 'FurLL 官网首页配置', # 权限描述
        'parent' => 'auth_site_management', # 父权限
        'child' => [
            [
                'title' => 'auth_site_management_furll_home_view',
                'url' => 'index',
                'auth_rule' => [
                    'addon\furll_home\controller\AdminIndexController::bannerList',
                    'addon\furll_home\controller\AdminIndexController::recommendList',
                    'addon\furll_home\controller\AdminIndexController::partnerList',
                    'addon\furll_home\controller\AdminIndexController::configDetail',
                ],
                'description' => '查看页面',
            ],
            [
                'title' => 'auth_site_management_furll_home_banner',
                'url' => '',
                'auth_rule' => [
                    'addon\furll_home\controller\AdminIndexController::createBanner',
                    'addon\furll_home\controller\AdminIndexController::updateBanner',
                    'addon\furll_home\controller\AdminIndexController::deleteBanner',
                ],
                'description' => '轮播图管理',
            ],
            [
                'title' => 'auth_site_management_furll_home_recommend',
                'url' => '',
                'auth_rule' => [
                    'addon\furll_home\controller\AdminIndexController::createRecommend',
                    'addon\furll_home\controller\AdminIndexController::updateRecommend',
                    'addon\furll_home\controller\AdminIndexController::deleteRecommend',
                    'addon\furll_home\controller\AdminIndexController::configUpdate',
                ],
                'description' => '推荐产品管理',
            ],
            [
                'title' => 'auth_site_management_furll_home_partner',
                'url' => '',
                'auth_rule' => [
                    'addon\furll_home\controller\AdminIndexController::createPartner',
                    'addon\furll_home\controller\AdminIndexController::updatePartner',
                    'addon\furll_home\controller\AdminIndexController::deletePartner',
                ],
                'description' => '合作伙伴管理',
            ],
        ]
    ],
];
