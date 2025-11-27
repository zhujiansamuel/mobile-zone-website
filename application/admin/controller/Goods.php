<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 商品管理
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{

    /**
     * Goodsモデルオブジェクト
     * @var \app\admin\model\Goods
     */
    protected $model = null;

    protected $selectpageFields = "id,jan,title,image,price,spec_info,color_id,color";

    protected $multiFields = 'recomm,status';
    //protected $selectpageFieldsJson = 'spec_info';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Goods;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    public function handleParams($params)
    {
        if(!empty($params['spec_info'])){
            $spec_info = json_decode($params['spec_info'], true);
            $price = [];
            foreach ($spec_info as $key => $value) {
                $price[] = $value['price'];
            }
            $params['price'] = $price ? max($price) : 0;
        }
        return $params;
    }

    /**
     * 批量价格管理页面
     */
    public function batch_price()
    {
        if ($this->request->isAjax()) {
            // 获取筛选条件
            $filter = $this->request->get("filter", '');
            $filter = json_decode($filter, true);

            $where = [];

            // 按分类筛选
            if (isset($filter['category_id']) && $filter['category_id']) {
                $where['category_id'] = $filter['category_id'];
            }

            // 按标题搜索
            if (isset($filter['title']) && $filter['title']) {
                $where['title'] = ['like', '%' . $filter['title'] . '%'];
            }

            // 按状态筛选
            if (isset($filter['status']) && $filter['status'] !== '') {
                $where['status'] = $filter['status'];
            }

            // 获取商品列表
            $list = $this->model
                ->where($where)
                ->order('id', 'desc')
                ->select();

            // 处理数据，展开spec_info
            $result = [];
            foreach ($list as $goods) {
                $spec_info = $goods['spec_info'] ? json_decode($goods['spec_info'], true) : [];

                // 添加基础价格
                $result[] = [
                    'id' => $goods['id'],
                    'goods_id' => $goods['id'],
                    'title' => $goods['title'],
                    'jan' => $goods['jan'],
                    'spec_name' => '基础价格',
                    'spec_index' => -1,
                    'price_type' => 'base',
                    'price' => $goods['price'],
                    'color' => '',
                    'image' => $goods['image'],
                ];

                // 添加规格价格
                if ($spec_info) {
                    foreach ($spec_info as $index => $spec) {
                        $result[] = [
                            'id' => $goods['id'] . '_spec_' . $index,
                            'goods_id' => $goods['id'],
                            'title' => $goods['title'],
                            'jan' => $goods['jan'],
                            'spec_name' => $spec['specs_name'] . ' - ' . $spec['color'],
                            'spec_index' => $index,
                            'price_type' => 'spec',
                            'price' => $spec['price'],
                            'color' => $spec['color'],
                            'image' => $goods['image'],
                        ];
                    }
                }
            }

            return json(['code' => 1, 'msg' => '', 'data' => $result, 'count' => count($result)]);
        }

        // 获取商品分类
        $categoryList = db('category')->where('type', 'goods')->order('weigh desc')->select();
        $this->view->assign('categoryList', $categoryList);

        return $this->view->fetch();
    }

    /**
     * 批量更新价格
     */
    public function batch_update_price()
    {
        if ($this->request->isPost()) {
            $prices = $this->request->post('prices/a');

            if (empty($prices)) {
                $this->error('没有价格数据');
            }

            db()->startTrans();
            try {
                foreach ($prices as $item) {
                    $goods_id = $item['goods_id'];
                    $price_type = $item['price_type'];
                    $new_price = $item['price'];

                    if ($price_type == 'base') {
                        // 更新基础价格
                        db('goods')->where('id', $goods_id)->update(['price' => $new_price]);
                    } elseif ($price_type == 'spec') {
                        // 更新规格价格
                        $spec_index = $item['spec_index'];
                        $goods = db('goods')->where('id', $goods_id)->find();
                        $spec_info = json_decode($goods['spec_info'], true);

                        if (isset($spec_info[$spec_index])) {
                            $spec_info[$spec_index]['price'] = $new_price;
                            db('goods')->where('id', $goods_id)->update(['spec_info' => json_encode($spec_info)]);
                        }
                    }
                }

                db()->commit();
                $this->success('价格更新成功');
            } catch (\Exception $e) {
                db()->rollback();
                $this->error('更新失败：' . $e->getMessage());
            }
        }

        $this->error('非法请求');
    }


}
