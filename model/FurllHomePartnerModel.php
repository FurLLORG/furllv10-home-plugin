<?php
namespace addon\furll_home\model;

use think\Model;

/**
 * @title 官网首页合作伙伴模型
 * @desc 官网首页合作伙伴模型
 * @use addon\furll_home\model\FurllHomePartnerModel
 */
class FurllHomePartnerModel extends Model
{
    protected $name = 'addon_furll_home_partner';

    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'image'       => 'string',
        'url'         => 'string',
        'wall'        => 'string',
        'sort'        => 'int',
        'hidden'      => 'int',
        'admin_id'    => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    /**
     * @title 合作伙伴列表
     * @param array param - 参数
     * @param string param.app - 前后台 home前台 admin后台
     * @return array list - 合作伙伴
     */
    public function furllHomePartnerList($param, $app = '')
    {
        $param['keywords'] = $param['keywords'] ?? '';
        $param['wall'] = $param['wall'] ?? '';

        $where = [];
        if(!empty($param['keywords'])){
            $where[] = ['name', 'like', "%{$param['keywords']}%"];
        }
        if(isset($param['wall']) && $param['wall'] !== ''){
            $where[] = ['wall', $param['wall']];
        }
        if($app == 'home'){
            $where[] = ['hidden', '=', 0];
        }

        $count = $this->where($where)->count();

        $list = $this->field('id,name,image,url,wall,sort,hidden,create_time')
            ->where($where)
            ->order('wall asc,sort asc,id asc')
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
     * @title 合作伙伴详情
     * @param int id - 合作伙伴ID
     */
    public function furllHomePartnerDetail($id)
    {
        $partner = $this->find($id);
        if(empty($partner)){
            return (object)[];
        }
        return $partner;
    }

    /**
     * @title 添加合作伙伴
     */
    public function createFurllHomePartner($param)
    {
        $this->startTrans();
        try {
            $this->create([
                'admin_id'    => get_admin_id(),
                'name'        => $param['name'],
                'image'       => $param['image'] ?? '',
                'url'         => $param['url'] ?? '',
                'wall'        => (int)($param['wall'] ?? 1),
                'sort'        => (int)($param['sort'] ?? 0),
                'hidden'      => (int)($param['hidden'] ?? 0),
                'create_time' => time(),
                'update_time' => time(),
            ]);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('create_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('create_success')];
    }

    /**
     * @title 修改合作伙伴
     */
    public function updateFurllHomePartner($param)
    {
        $partner = $this->find($param['id']);
        if(empty($partner)){
            return ['status' => 400, 'msg' => lang_plugins('partner_is_not_exist')];
        }

        $this->startTrans();
        try {
            $this->update([
                'admin_id'    => get_admin_id(),
                'name'        => $param['name'],
                'image'       => $param['image'] ?? '',
                'url'         => $param['url'] ?? '',
                'wall'        => (int)($param['wall'] ?? 1),
                'sort'        => (int)($param['sort'] ?? 0),
                'hidden'      => (int)($param['hidden'] ?? 0),
                'update_time' => time(),
            ], ['id' => $param['id']]);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('update_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('update_success')];
    }

    /**
     * @title 删除合作伙伴
     */
    public function deleteFurllHomePartner($id)
    {
        $partner = $this->find($id);
        if(empty($partner)){
            return ['status' => 400, 'msg' => lang_plugins('partner_is_not_exist')];
        }
        $this->where('id', $id)->delete();
        return ['status' => 200, 'msg' => lang_plugins('delete_success')];
    }
}
