<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\admin\model\Goods as GoodsModel;

/**
 * 商品价格管理API
 * 提供商品规格价格的查询和批量更新功能
 */
class GoodsPrice extends Api
{
    // 所有接口都需要登录认证（需要提供token）
    // gettoken 和 listusers 方法不需要登录，用于获取测试token
    protected $noNeedLogin = ['gettoken', 'listusers'];
    protected $noNeedRight = '*';

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new GoodsModel();
    }

    /**
     * 获取商品价格列表（按规格展开）
     *
     * @ApiMethod (GET)
     * @ApiRoute  (/api/goodsprice/list)
     * @ApiHeaders (name=token, type=string, required=true, description="用户token")
     * @ApiParams (name="page", type="int", required=false, description="页码", default="1")
     * @ApiParams (name="limit", type="int", required=false, description="每页数量", default="50")
     * @ApiParams (name="title", type="string", required=false, description="商品标题（模糊搜索）")
     * @ApiParams (name="category_id", type="int", required=false, description="分类ID")
     * @ApiParams (name="status", type="int", required=false, description="状态：0=下架，1=上架")
     * @ApiParams (name="goods_id", type="int", required=false, description="商品ID（精确查询）")
     *
     * @ApiReturn ({
     *   "code": 1,
     *   "msg": "获取成功",
     *   "time": "1638000000",
     *   "data": {
     *     "total": 100,
     *     "per_page": 50,
     *     "current_page": 1,
     *     "last_page": 2,
     *     "data": [
     *       {
     *         "goods_id": 1,
     *         "title": "iPhone 13",
     *         "image": "/uploads/xxx.jpg",
     *         "category_name": "手机",
     *         "status": 1,
     *         "spec_index": 0,
     *         "spec_name": "128GB 黑色",
     *         "price": 5999.00
     *       }
     *     ]
     *   }
     * })
     */
    public function list()
    {
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 50);
        $title = $this->request->param('title', '');
        $categoryId = $this->request->param('category_id/d', 0);
        $status = $this->request->param('status', '');
        $goodsId = $this->request->param('goods_id/d', 0);

        // 构建查询条件
        $where = [];
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        if ($categoryId > 0) {
            $where[] = ['category_id', '=', $categoryId];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }
        if ($goodsId > 0) {
            $where[] = ['id', '=', $goodsId];
        }

        // 查询商品
        $list = $this->model
            ->with(['category'])
            ->where($where)
            ->order('id', 'desc')
            ->paginate($limit, false, ['page' => $page]);

        // 展开规格信息
        $expandedRows = [];
        foreach ($list->items() as $item) {
            $baseData = [
                'goods_id' => $item->id,
                'title' => $item->title,
                'image' => $item->image ? cdnurl($item->image, true) : '',
                'category_name' => isset($item->category) ? $item->category->name : '',
                'status' => $item->status,
                'status_text' => $item->status == 1 ? '上架' : '下架',
            ];

            // 解析规格信息
            $specInfo = json_decode($item->spec_info, true);
            if (!empty($specInfo) && is_array($specInfo)) {
                foreach ($specInfo as $specIndex => $spec) {
                    $expandedRows[] = array_merge($baseData, [
                        'spec_index' => $specIndex,
                        'spec_name' => isset($spec['name']) ? $spec['name'] : '',
                        'price' => isset($spec['price']) ? floatval($spec['price']) : 0,
                    ]);
                }
            } else {
                // 如果没有规格信息，显示商品本身的价格
                $expandedRows[] = array_merge($baseData, [
                    'spec_index' => 0,
                    'spec_name' => '',
                    'price' => floatval($item->price),
                ]);
            }
        }

        $result = [
            'total' => $list->total(),
            'per_page' => $list->listRows(),
            'current_page' => $list->currentPage(),
            'last_page' => $list->lastPage(),
            'data' => $expandedRows,
        ];

        $this->success('获取成功', $result);
    }

    /**
     * 获取单个商品的价格信息
     *
     * @ApiMethod (GET)
     * @ApiRoute  (/api/goodsprice/detail)
     * @ApiHeaders (name=token, type=string, required=true, description="用户token")
     * @ApiParams (name="goods_id", type="int", required=true, description="商品ID")
     *
     * @ApiReturn ({
     *   "code": 1,
     *   "msg": "获取成功",
     *   "time": "1638000000",
     *   "data": {
     *     "goods_id": 1,
     *     "title": "iPhone 13",
     *     "image": "/uploads/xxx.jpg",
     *     "specs": [
     *       {
     *         "spec_index": 0,
     *         "spec_name": "128GB 黑色",
     *         "price": 5999.00
     *       },
     *       {
     *         "spec_index": 1,
     *         "spec_name": "256GB 白色",
     *         "price": 6999.00
     *       }
     *     ]
     *   }
     * })
     */
    public function detail()
    {
        $goodsId = $this->request->param('goods_id/d', 0);

        if ($goodsId <= 0) {
            $this->error('商品ID不能为空');
        }

        $goods = $this->model->with(['category'])->find($goodsId);
        if (!$goods) {
            $this->error('商品不存在');
        }

        // 解析规格信息
        $specInfo = json_decode($goods->spec_info, true);
        $specs = [];
        if (!empty($specInfo) && is_array($specInfo)) {
            foreach ($specInfo as $specIndex => $spec) {
                $specs[] = [
                    'spec_index' => $specIndex,
                    'spec_name' => isset($spec['name']) ? $spec['name'] : '',
                    'price' => isset($spec['price']) ? floatval($spec['price']) : 0,
                ];
            }
        }

        $result = [
            'goods_id' => $goods->id,
            'title' => $goods->title,
            'image' => $goods->image ? cdnurl($goods->image, true) : '',
            'category_name' => isset($goods->category) ? $goods->category->name : '',
            'status' => $goods->status,
            'status_text' => $goods->status == 1 ? '上架' : '下架',
            'specs' => $specs,
        ];

        $this->success('获取成功', $result);
    }

    /**
     * 批量更新商品规格价格
     *
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goodsprice/update)
     * @ApiHeaders (name=token, type=string, required=true, description="用户token")
     * @ApiParams (name="prices", type="array", required=true, description="价格数据数组", sample="[{goods_id:1,spec_index:0,price:5999}]")
     *
     * @ApiReturnParams (name="code", type="integer", description="状态码")
     * @ApiReturnParams (name="msg", type="string", description="返回消息")
     * @ApiReturnParams (name="data", type="object", description="返回数据")
     *
     * @ApiReturn ({
     *   "code": 1,
     *   "msg": "成功更新了3个商品",
     *   "time": "1638000000",
     *   "data": {
     *     "updated_count": 3,
     *     "updated_goods": [1, 2, 3]
     *   }
     * })
     */
    public function update()
    {
        $prices = $this->request->param('prices');

        if (empty($prices) || !is_array($prices)) {
            $this->error('价格数据不能为空');
        }

        // 按商品ID分组价格数据
        $goodsPrices = [];
        foreach ($prices as $item) {
            if (!isset($item['goods_id']) || !isset($item['spec_index']) || !isset($item['price'])) {
                continue;
            }

            $goodsId = intval($item['goods_id']);
            $specIndex = intval($item['spec_index']);
            $price = floatval($item['price']);

            if (!isset($goodsPrices[$goodsId])) {
                $goodsPrices[$goodsId] = [];
            }
            $goodsPrices[$goodsId][$specIndex] = $price;
        }

        if (empty($goodsPrices)) {
            $this->error('没有有效的价格数据');
        }

        $count = 0;
        $updatedGoods = [];
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
                foreach ($specs as $specIndex => $price) {
                    if (!isset($specInfo[$specIndex])) {
                        $specInfo[$specIndex] = ['name' => '', 'price' => 0];
                    }
                    $specInfo[$specIndex]['price'] = $price;
                }

                // 计算最高价格作为商品的参考价格
                $prices = array_column($specInfo, 'price');
                $maxPrice = $prices ? max($prices) : 0;

                // 更新商品
                $goods->spec_info = json_encode($specInfo, JSON_UNESCAPED_UNICODE);
                $goods->price = $maxPrice;
                $goods->save();

                $count++;
                $updatedGoods[] = $goodsId;
            }
            $this->model->commit();

            $this->success('成功更新了' . $count . '个商品', [
                'updated_count' => $count,
                'updated_goods' => $updatedGoods,
            ]);
        } catch (\Exception $e) {
            $this->model->rollback();
            $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 批量设置价格（简化版）
     * 可以一次性设置一个商品的所有规格价格
     *
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goodsprice/setprices)
     * @ApiHeaders (name=token, type=string, required=true, description="用户token")
     * @ApiParams (name="goods_id", type="int", required=true, description="商品ID")
     * @ApiParams (name="prices", type="array", required=true, description="规格价格数组", sample="[5999, 6999, 7999]")
     *
     * @ApiReturn ({
     *   "code": 1,
     *   "msg": "价格更新成功",
     *   "time": "1638000000",
     *   "data": {
     *     "goods_id": 1,
     *     "updated_specs": 3
     *   }
     * })
     */
    public function setprices()
    {
        $goodsId = $this->request->param('goods_id/d', 0);
        $prices = $this->request->param('prices');

        if ($goodsId <= 0) {
            $this->error('商品ID不能为空');
        }

        if (empty($prices) || !is_array($prices)) {
            $this->error('价格数据不能为空');
        }

        $goods = $this->model->find($goodsId);
        if (!$goods) {
            $this->error('商品不存在');
        }

        $specInfo = json_decode($goods->spec_info, true);
        if (empty($specInfo)) {
            $this->error('商品没有规格信息');
        }

        $this->model->startTrans();
        try {
            // 更新规格价格
            $updatedCount = 0;
            foreach ($prices as $index => $price) {
                if (isset($specInfo[$index])) {
                    $specInfo[$index]['price'] = floatval($price);
                    $updatedCount++;
                }
            }

            // 计算最高价格作为商品的参考价格
            $allPrices = array_column($specInfo, 'price');
            $maxPrice = $allPrices ? max($allPrices) : 0;

            // 更新商品
            $goods->spec_info = json_encode($specInfo, JSON_UNESCAPED_UNICODE);
            $goods->price = $maxPrice;
            $goods->save();

            $this->model->commit();

            $this->success('价格更新成功', [
                'goods_id' => $goodsId,
                'updated_specs' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            $this->model->rollback();
            $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 获取测试Token（仅用于开发测试）
     *
     * @ApiMethod (GET)
     * @ApiRoute  (/api/goodsprice/gettoken)
     * @ApiParams (name="user_id", type="int", required=false, description="用户ID，默认为1")
     *
     * @ApiReturn ({
     *   "code": 1,
     *   "msg": "Token获取成功",
     *   "time": "1638000000",
     *   "data": {
     *     "token": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
     *     "user_id": 1,
     *     "expire_time": "2592000秒 (30天)"
     *   }
     * })
     */
    public function gettoken()
    {
        $userId = $this->request->param('user_id/d', 1);

        // 检查用户是否存在
        $user = \app\common\model\User::get($userId);
        if (!$user) {
            $this->error('用户不存在，请提供有效的 user_id');
        }

        if ($user->status != 'normal') {
            $this->error('用户账号已被锁定');
        }

        // 生成Token
        $token = \fast\Random::uuid();
        \app\common\library\Token::set($token, $userId, 2592000); // 30天有效期

        $this->success('Token获取成功', [
            'token' => $token,
            'user_id' => $userId,
            'username' => $user->username,
            'expire_time' => '2592000秒 (30天)',
            'usage' => '请在API请求时通过 HTTP Header "token: ' . $token . '" 或 URL参数 "?token=' . $token . '" 传递此token'
        ]);
    }

    /**
     * 列出所有可用用户（仅用于开发测试）
     *
     * @ApiMethod (GET)
     * @ApiRoute  (/api/goodsprice/listusers)
     *
     * @ApiReturn ({
     *   "code": 1,
     *   "msg": "获取成功",
     *   "time": "1638000000",
     *   "data": {
     *     "users": [
     *       {
     *         "id": 1,
     *         "username": "admin",
     *         "email": "admin@example.com",
     *         "status": "normal"
     *       }
     *     ]
     *   }
     * })
     */
    public function listusers()
    {
        $users = \app\common\model\User::where('status', 'normal')
            ->field('id, username, email, mobile, status')
            ->limit(10)
            ->select();

        if (!$users || count($users) == 0) {
            $this->error('数据库中没有可用用户，请先创建用户或联系管理员');
        }

        $this->success('获取成功', [
            'users' => $users,
            'tip' => '请使用任意用户的 id 访问 /api/goodsprice/gettoken?user_id=X 来获取token'
        ]);
    }
}
