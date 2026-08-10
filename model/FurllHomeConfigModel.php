<?php
namespace addon\furll_home\model;

use think\Model;

/**
 * @title 官网首页配置模型
 * @desc 官网首页配置模型
 * @use addon\furll_home\model\FurllHomeConfigModel
 */
class FurllHomeConfigModel extends Model
{
    protected $name = 'addon_furll_home_config';

    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'value'       => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    /**
     * @title 获取配置值
     * @param string name - 配置名
     */
    public function getConfigValue($name, $default = '')
    {
        $value = $this->where('name', $name)->value('value');
        return ($value === null || $value === '') ? $default : $value;
    }

    /**
     * @title 设置配置值
     * @param string name - 配置名
     * @param string value - 配置值
     */
    public function setConfigValue($name, $value)
    {
        $config = $this->where('name', $name)->find();
        $now = time();
        if(empty($config)){
            $this->create([
                'name'        => $name,
                'value'       => $value,
                'create_time' => $now,
                'update_time' => $now,
            ]);
        }else{
            $this->update([
                'value'       => $value,
                'update_time' => $now,
            ], ['id' => $config['id']]);
        }
        return true;
    }

    /**
     * @title 全部配置
     */
    public function getConfigList()
    {
        $list = $this->field('name,value')->select()->toArray();
        $data = [];
        foreach ($list as $item) {
            $data[$item['name']] = $item['value'];
        }
        return $data;
    }
}
