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
    public function bulkprice()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['category', 'second', 'three'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            // 展开规格信息，每个规格作为一行
            $expandedRows = [];
            foreach ($list->items() as $item) {
                $baseData = [
                    'goods_id' => $item->id,
                    'title' => $item->title ?? '',
                    'image' => $item->image ?? '',
                    'status' => $item->status ?? '',
                    'category' => ['name' => isset($item->category) && $item->category ? $item->category->name : ''],
                    'second' => ['name' => isset($item->second) && $item->second ? $item->second->name : ''],
                    'three' => ['name' => isset($item->three) && $item->three ? $item->three->name : ''],
                ];

                // 解析规格信息
                $specInfo = !empty($item->spec_info) ? json_decode($item->spec_info, true) : null;
                if (!empty($specInfo) && is_array($specInfo)) {
                    foreach ($specInfo as $specIndex => $spec) {
                        $expandedRows[] = array_merge($baseData, [
                            'id' => $item->id . '_' . $specIndex,  // 组合ID：商品ID_规格索引
                            'spec_index' => $specIndex,
                            'spec_name' => isset($spec['name']) ? $spec['name'] : '',
                            'price' => isset($spec['price']) ? $spec['price'] : 0,
                        ]);
                    }
                } else {
                    // 如果没有规格信息，显示空行
                    $expandedRows[] = array_merge($baseData, [
                        'id' => $item->id . '_0',
                        'spec_index' => 0,
                        'spec_name' => '',
                        'price' => $item->price ?? 0,
                    ]);
                }
            }

            $result = array("total" => $list->total(), "rows" => $expandedRows);
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

            // 按商品ID分组价格数据
            $goodsPrices = [];
            foreach ($params['prices'] as $compositeId => $priceData) {
                // 解析组合ID: goods_id_spec_index
                $parts = explode('_', $compositeId);
                if (count($parts) >= 2) {
                    $specIndex = array_pop($parts);  // 最后一部分是规格索引
                    $goodsId = implode('_', $parts); // 其余部分是商品ID

                    if (!isset($goodsPrices[$goodsId])) {
                        $goodsPrices[$goodsId] = [];
                    }
                    $goodsPrices[$goodsId][$specIndex] = $priceData;
                }
            }

            $count = 0;
            $this->model->startTrans();
            try {
                foreach ($goodsPrices as $goodsId => $specs) {
                    // 获取商品当前的spec_info
                    $goods = $this->model->find($goodsId);
                    if (!$goods) {
                        continue;
                    }

                    $specInfo = json_decode($goods->spec_info, true);
                    if (empty($specInfo)) {
                        $specInfo = [];
                    }

                    // 更新规格价格
                    foreach ($specs as $specIndex => $priceData) {
                        if (isset($priceData['price']) && $priceData['price'] !== '') {
                            if (!isset($specInfo[$specIndex])) {
                                $specInfo[$specIndex] = ['name' => '', 'price' => 0];
                            }
                            $specInfo[$specIndex]['price'] = floatval($priceData['price']);
                        }
                    }

                    // 计算最高价格作为商品的参考价格
                    $prices = array_column($specInfo, 'price');
                    $maxPrice = $prices ? max($prices) : 0;

                    // 更新商品
                    $goods->spec_info = json_encode($specInfo, JSON_UNESCAPED_UNICODE);
                    $goods->price = $maxPrice;
                    $goods->save();

                    $count++;
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
