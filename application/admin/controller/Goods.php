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
            try {
                // 获取筛选参数
                $filter = $this->request->get("filter", '');
                $filter = (array)json_decode($filter, true);
                $categoryId = isset($filter['category_id']) ? $filter['category_id'] : null;

                // 如果有分类筛选，先从filter中移除，稍后单独处理
                if ($categoryId) {
                    unset($filter['category_id']);
                    // 重新设置filter参数
                    $this->request->get(['filter' => json_encode($filter)]);
                }

                // 启用关联查询模式
                list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, true);

                // 字段映射：前端使用goods_id，但数据库中是id
                // 需要处理带表别名的情况（goods.goods_id -> goods.id）
                if ($sort === 'goods_id') {
                    $sort = 'id';
                } elseif ($sort === 'goods.goods_id') {
                    $sort = 'goods.id';
                }

                $list = $this->model
                    ->with(['category', 'second', 'three'])
                    ->where($where)
                    ->where(function($query) use ($categoryId) {
                        if ($categoryId) {
                            // 分类筛选：查询三个分类字段中的任意一个匹配
                            $query->where('category_id', $categoryId)
                                  ->whereOr('category_second', $categoryId)
                                  ->whereOr('category_three', $categoryId);
                        }
                    })
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
            } catch (\Exception $e) {
                // 记录错误日志
                \think\Log::error('bulkprice error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                return json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()], 500);
            }
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
                \think\Log::write('批量更新价格参数错误：' . json_encode($params), 'error');
                $this->error('参数错误');
            }

            $count = 0;
            $this->model->startTrans();
            try {
                // 遍历每个商品的价格数据
                foreach ($params['prices'] as $goodsId => $priceData) {
                    // 查找商品
                    $goods = $this->model->find($goodsId);
                    if (!$goods) {
                        \think\Log::write('商品不存在：' . $goodsId, 'warning');
                        continue;
                    }

                    // 更新价格
                    if (isset($priceData['price']) && $priceData['price'] !== '') {
                        $newPrice = floatval($priceData['price']);
                        $goods->price = $newPrice;
                        $goods->save();
                        $count++;

                        \think\Log::write('更新商品 ' . $goodsId . ' 价格：' . $newPrice, 'info');
                    }
                }

                $this->model->commit();

                // 记录成功日志
                \think\Log::write('批量更新价格成功，更新了 ' . $count . ' 个商品', 'info');

                // 使用直接的中文消息，确保msg不为空
                $this->success('成功保存 ' . $count . ' 个商品的价格');
            } catch (\Exception $e) {
                $this->model->rollback();
                \think\Log::write('批量更新价格失败：' . $e->getMessage(), 'error');
                $this->error('保存失败：' . $e->getMessage());
            }
        }

        \think\Log::write('无效的请求方法', 'error');
        $this->error('无效的请求');
    }



}
