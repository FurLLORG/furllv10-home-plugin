<?php
namespace addon\furll_home\validate;

use think\Validate;

/**
 * 官网首页配置验证
 */
class FurllHomeValidate extends Validate
{
    protected $rule = [
        'id'          => 'require|integer|gt:0',
        'title'       => 'require|max:200',
        'label'       => 'max:100',
        'description' => 'max:500',
        'image'       => 'max:500',
        'url'         => 'max:500',
        'button_text' => 'max:100',
        'product_id'  => 'integer|egt:0',
        'name'        => 'require|max:200',
        'tag'         => 'max:100',
        'price'       => 'max:50',
        'unit'        => 'max:50',
        'wall'        => 'in:1,2',
        'sort'        => 'integer|egt:0',
        'hidden'      => 'in:0,1',
        'config'      => 'array',
    ];

    protected $message = [
        'id.require'       => 'id_error',
        'id.integer'       => 'id_error',
        'id.gt'            => 'id_error',
        'title.require'    => 'title_require',
        'title.max'        => 'title_max',
        'label.max'        => 'label_max',
        'description.max'  => 'description_max',
        'image.max'        => 'image_max',
        'url.max'          => 'url_max',
        'button_text.max'  => 'button_text_max',
        'product_id.integer' => 'product_id_error',
        'name.require'     => 'name_require',
        'name.max'         => 'name_max',
        'tag.max'          => 'tag_max',
        'price.max'        => 'price_max',
        'unit.max'         => 'unit_max',
        'wall.in'          => 'wall_error',
        'sort.integer'     => 'param_error',
        'sort.egt'         => 'param_error',
        'hidden.in'        => 'param_error',
        'config.array'     => 'param_error',
    ];

    protected $scene = [
        'create_banner'   => ['title', 'label', 'description', 'image', 'url', 'button_text', 'sort', 'hidden'],
        'update_banner'   => ['id', 'title', 'label', 'description', 'image', 'url', 'button_text', 'sort', 'hidden'],
        'create_recommend'=> ['product_id', 'name', 'description', 'tag', 'price', 'unit', 'url', 'sort', 'hidden'],
        'update_recommend'=> ['id', 'product_id', 'name', 'description', 'tag', 'price', 'unit', 'url', 'sort', 'hidden'],
        'create_partner'  => ['name', 'image', 'url', 'wall', 'sort', 'hidden'],
        'update_partner'  => ['id', 'name', 'image', 'url', 'wall', 'sort', 'hidden'],
        'delete'          => ['id'],
    ];
}
