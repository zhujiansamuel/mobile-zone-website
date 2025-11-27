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
     * Goods模型对象
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
    public function bulkprice()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['category', 'second', 'three'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 批量更新价格
     */
    public function bulkupdate()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if (empty($params['prices']) || !is_array($params['prices'])) {
                $this->error(__('Invalid parameters'));
            }

            $count = 0;
            $this->model->startTrans();
            try {
                foreach ($params['prices'] as $id => $priceData) {
                    $update = [];
                    if (isset($priceData['price']) && $priceData['price'] !== '') {
                        $update['price'] = floatval($priceData['price']);
                    }
                    if (isset($priceData['price_zg']) && $priceData['price_zg'] !== '') {
                        $update['price_zg'] = floatval($priceData['price_zg']);
                    }

                    if (!empty($update)) {
                        $this->model->where('id', $id)->update($update);
                        $count++;
                    }
                }
                $this->model->commit();
                $this->success(__('Updated %d items successfully', $count));
            } catch (\Exception $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
        }
        $this->error(__('Invalid request'));
    }


}
