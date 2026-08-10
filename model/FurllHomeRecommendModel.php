<?php
namespace addon\furll_home\model;

use think\Model;

/**
 * @title 官网首页推荐产品模型
 * @desc 官网首页推荐产品模型
 * @use addon\furll_home\model\FurllHomeRecommendModel
 */
class FurllHomeRecommendModel extends Model
{
    protected $name = 'addon_furll_home_recommend';

    protected $schema = [
        'id'          => 'int',
        'product_id'  => 'int',
        'name'        => 'string',
        'description' => 'string',
        'tag'         => 'string',
        'price'       => 'string',
        'unit'        => 'string',
        'url'         => 'string',
        'sort'        => 'int',
        'hidden'      => 'int',
        'admin_id'    => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    /**
     * @title 推荐产品列表
     * @param array param - 参数
     * @param string param.app - 前后台 home前台 admin后台
     * @return array list - 推荐产品
     */
    public function furllHomeRecommendList($param, $app = '')
    {
        $param['keywords'] = $param['keywords'] ?? '';

        $where = [];
        if(!empty($param['keywords'])){
            $where[] = ['name', 'like', "%{$param['keywords']}%"];
        }
        if($app == 'home'){
            $where[] = ['hidden', '=', 0];
        }

        $count = $this->where($where)->count();

        $list = $this->field('id,product_id,name,description,tag,price,unit,url,sort,hidden,create_time')
            ->where($where)
            ->order('sort asc,id asc')
            ->page($param['page'] ?? 1, $param['limit'] ?? 1000)
            ->select()
            ->toArray();

        if($app == 'home'){
            foreach ($list as $key => $value) {
                unset($list[$key]['hidden'], $list[$key]['sort']);
            }
        }

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 推荐产品详情
     * @param int id - 推荐产品ID
     */
    public function furllHomeRecommendDetail($id)
    {
        $recommend = $this->find($id);
        if(empty($recommend)){
            return (object)[];
        }
        return $recommend;
    }

    /**
     * @title 添加推荐产品
     */
    public function createFurllHomeRecommend($param)
    {
        $this->startTrans();
        try {
            $this->create([
                'admin_id'     => get_admin_id(),
                'product_id'   => $param['product_id'] ?? 0,
                'name'         => $param['name'],
                'description'  => $param['description'] ?? '',
                'tag'          => $param['tag'] ?? '',
                'price'        => $param['price'] ?? '',
                'unit'         => $param['unit'] ?? '',
                'url'          => $param['url'] ?? '',
                'sort'         => (int)($param['sort'] ?? 0),
                'hidden'       => (int)($param['hidden'] ?? 0),
                'create_time'  => time(),
                'update_time'  => time(),
            ]);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('create_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('create_success')];
    }

    /**
     * @title 修改推荐产品
     */
    public function updateFurllHomeRecommend($param)
    {
        $recommend = $this->find($param['id']);
        if(empty($recommend)){
            return ['status' => 400, 'msg' => lang_plugins('recommend_is_not_exist')];
        }

        $this->startTrans();
        try {
            $this->update([
                'admin_id'     => get_admin_id(),
                'product_id'   => $param['product_id'] ?? 0,
                'name'         => $param['name'],
                'description'  => $param['description'] ?? '',
                'tag'          => $param['tag'] ?? '',
                'price'        => $param['price'] ?? '',
                'unit'         => $param['unit'] ?? '',
                'url'          => $param['url'] ?? '',
                'sort'         => (int)($param['sort'] ?? 0),
                'hidden'       => (int)($param['hidden'] ?? 0),
                'update_time'  => time(),
            ], ['id' => $param['id']]);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('update_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('update_success')];
    }

    /**
     * @title 删除推荐产品
     */
    public function deleteFurllHomeRecommend($id)
    {
        $recommend = $this->find($id);
        if(empty($recommend)){
            return ['status' => 400, 'msg' => lang_plugins('recommend_is_not_exist')];
        }
        $this->where('id', $id)->delete();
        return ['status' => 200, 'msg' => lang_plugins('delete_success')];
    }
}
