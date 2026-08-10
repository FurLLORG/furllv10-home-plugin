<?php
namespace addon\furll_home\model;

use think\Model;

/**
 * @title 官网首页轮播图模型
 * @desc 官网首页轮播图模型
 * @use addon\furll_home\model\FurllHomeBannerModel
 */
class FurllHomeBannerModel extends Model
{
    protected $name = 'addon_furll_home_banner';

    protected $schema = [
        'id'          => 'int',
        'title'       => 'string',
        'label'       => 'string',
        'description' => 'string',
        'image'       => 'string',
        'url'         => 'string',
        'button_text' => 'string',
        'sort'        => 'int',
        'hidden'      => 'int',
        'admin_id'    => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    /**
     * @title 轮播图列表
     * @param array param - 参数
     * @param string param.app - 前后台 home前台 admin后台
     * @return array list - 轮播图
     */
    public function furllHomeBannerList($param, $app = '')
    {
        $param['keywords'] = $param['keywords'] ?? '';

        $where = [];
        if(!empty($param['keywords'])){
            $where[] = ['title', 'like', "%{$param['keywords']}%"];
        }
        if($app == 'home'){
            $where[] = ['hidden', '=', 0];
        }

        $count = $this->where($where)->count();

        $list = $this->field('id,title,label,description,image,url,button_text,sort,hidden,create_time')
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
     * @title 轮播图详情
     * @param int id - 轮播图ID
     */
    public function furllHomeBannerDetail($id)
    {
        $banner = $this->find($id);
        if(empty($banner)){
            return (object)[];
        }
        return $banner;
    }

    /**
     * @title 添加轮播图
     */
    public function createFurllHomeBanner($param)
    {
        $this->startTrans();
        try {
            $this->create([
                'admin_id'     => get_admin_id(),
                'title'        => $param['title'],
                'label'        => $param['label'] ?? '',
                'description'  => $param['description'] ?? '',
                'image'        => $param['image'] ?? '',
                'url'          => $param['url'] ?? '',
                'button_text'  => $param['button_text'] ?? '',
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
     * @title 修改轮播图
     */
    public function updateFurllHomeBanner($param)
    {
        $banner = $this->find($param['id']);
        if(empty($banner)){
            return ['status' => 400, 'msg' => lang_plugins('banner_is_not_exist')];
        }

        $this->startTrans();
        try {
            $this->update([
                'admin_id'     => get_admin_id(),
                'title'        => $param['title'],
                'label'        => $param['label'] ?? '',
                'description'  => $param['description'] ?? '',
                'image'        => $param['image'] ?? '',
                'url'          => $param['url'] ?? '',
                'button_text'  => $param['button_text'] ?? '',
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
     * @title 删除轮播图
     */
    public function deleteFurllHomeBanner($id)
    {
        $banner = $this->find($id);
        if(empty($banner)){
            return ['status' => 400, 'msg' => lang_plugins('banner_is_not_exist')];
        }
        $this->where('id', $id)->delete();
        return ['status' => 200, 'msg' => lang_plugins('delete_success')];
    }
}
