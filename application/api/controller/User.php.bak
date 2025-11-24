<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use think\Validate;
use app\common\model\Goods;
use think\Exception;
use think\Db;
use custom\ConfigStatus as CS;
/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'contactus'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }

    }

    /**
     * 联系我们
     *
     */
    public function contactus()
    {
        try {
            $post = $this->request->post();
            $validate = [
                'name' => 'require|max:50,名前を入力してください|50文字以内',
                'katakana' => 'require|max:50,お名前（カナ）を入力してください|50文字以内',
                //'tel' => 'require|max:50,電話番号を入力してください|30文字以内',
                'email' => 'require|max:150,メールアドレスを確認してください。|150文字以内',
                'zip_code' => 'require|number,郵便番号 を入力してください|フォーマットが正しくありません',
                'address' => 'require|max:150,ご住所 を入力してください|150文字以内',
                'content' => 'require|max:255,お問い合わせ内容 を入力してください|255文字以内',
            ];
            $result = $this->verify($post, $validate);
            $save = [];
            $save['name'] = $post['name'];
            $save['katakana'] = $post['katakana'];
            if(!empty($post['tel'])){
                $save['tel'] = $post['tel'];
            }
            $save['email'] = $post['email'];
            if(!empty($post['confirm'])){
                $save['confirm'] = $post['confirm'];
            }
            $save['zip_code'] = $post['zip_code'];
            $save['address'] = $post['address'];
            $save['content'] = $post['content'];
            $save['createtime'] = time();
            $res = db('contactus')->insertGetId($save);
            if(!$res){
                $this->error('失败');
            }

            $this->success(__('お問い合わせいただき、ありがとうございます。'));
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 加入购物车
     *
     */
    public function addshopping()
    {
        try {
            $post = $this->request->post();
            //dump($post);die;
            $goods_id = $this->request->post('goods_id');
            $spec = $this->request->post('spec');
            $color = $this->request->post('color');
            //$specs = json_decode(htmlspecialchars_decode($specs), true);

            $num = $this->request->post('num');

            //获取商品
            $goods = db('goods')->where('id', $goods_id)->find();
            if(!$goods){
                $this->error('商品は存在しません');
            }
         
            $spec_info = json_decode($goods['spec_info'], true);

            $spec_info = $spec_info[$spec] ?? [];
            if(!$spec_info){
                $this->error('商品は存在しません');
            }

            $specs_name = $spec_info['name'];
            $price = $spec_info['price'];

            $shopping = db('shopping')
               ->where('user_id', $this->auth->id)
               ->where('goods_id', $goods_id)
               ->where('specs_name', $specs_name)
               ->find();
            if($shopping){
                db('shopping')->where('id', $shopping['id'])->update([
                    'num' => $num,
                    'price' => $price,
                    'price_zg' => $goods['price_zg'],
                    'specs_name' => $specs_name,
                ]);
            }else{
                db('shopping')->insertGetId([
                    'user_id' => $this->auth->id,
                    'goods_id' => $goods_id,
                    'color' => $color,
                    'specs_name' => $specs_name,
                    'title' => $goods['title'],
                    'jan' => $goods['jan'] ?: '',
                    'memo' => $goods['memo'],
                    'image' => $goods['image'],
                    'price' => $price,
                    'price_zg' => $goods['price_zg'],
                    'type' => $goods['type'],
                    'num' => $num,
                    'createtime' => time()
                ]);
            }

            $this->success(__('カートに入りました。'));
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 修改购物车
     *
     */
    public function updateshopping()
    {
        try {
            $post = $this->request->post();
         
            $shopping_id = $this->request->post('shopping_id');
            $num = $this->request->post('num');
            $shopping = db('shopping')->where('id', $shopping_id)->find();
            if(!$shopping){
                $this->error(__('数据不存在'));
            }
            
            db('shopping')->where('id', $shopping_id)->update([
                'num' => $num,
            ]);
            //获取当前用户下购物车金额
            $shopping_list = db('shopping')->where('user_id', $shopping['user_id'])->select();
            $totalMoney = 0;
            foreach ($shopping_list as $key => $val) {
                $totalMoney += $val['num'] * $val['price'];
                //$totalMoney += $val['num'] * ($val['type'] == 1 ? $val['price'] : $val['price_zg']);
            }
            $totalMoney = number_format($totalMoney);
            $this->success(__('成功'), ['totalMoney' => $totalMoney]);
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 删除购物车
     *
     */
    public function delshopping()
    {
        try {
            $post = $this->request->post();
         
            $shopping_id = $this->request->post('shopping_id');
            $shopping = db('shopping')->where('id', $shopping_id)->find();
            if(!$shopping){
                $this->error(__('数据不存在'));
            }
            
            db('shopping')->where('id', $shopping_id)->delete();
            
            $this->success(__('成功'));
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 创建订单
     *
     */
    public function addOrder()
    {
        try {
            $post = $this->request->post();
         
            $where = [];
            $where['user_id'] = $this->auth->id;
            $shopping = db('shopping')->where($where)->select();
            if(!$shopping){
                //请添加商品后购买
                $this->error('商品を追加してご購入ください');
            }

            if(!$this->auth->name || !$this->auth->zip_code || !$this->auth->mobile){
                $this->error('会員登録情報を修正して提出してください');
            }

            // if(!$this->auth->name){
            //     //请设置氏名后提交
            //     $this->error('氏名を設定した後、送信してください');
            // }
            // if(!$this->auth->zip_code){
            //     //请设置郵便番号后提交
            //     $this->error('郵便番号を設定してから送信してください');
            // }

            // if(!$this->auth->mobile){
            //     //请设置お電話番号后提交
            //     $this->error('お電話番号を設定して提出してください');
            // }
            
            $save = [];
            
            $save['user_id'] = $this->auth->id;
            $save['no'] = date('YmdHis').rand(100,999);
            $save['createtime'] = time();
            $store = [];
            //类型:1=门店,2=邮寄
            if($post['type'] == 1){
                if(empty($post['store_id'])){
                    $this->error(__('请选择门店'));
                }
                if(empty($post['go_store_date'])){
                    $this->error(__('请选择来店日期'));
                }
                if(empty($post['go_store_time'])){
                    $this->error(__('请选择来店时间'));
                }
                //门店信息
                $store = db('store')->where('id', $post['store_id'])->find();
                $save['store_id'] = $post['store_id'];
                $save['store_name'] = $store['name'];
                $save['go_store_date'] = $post['go_store_date'];
                $save['go_store_time'] = $post['go_store_time'];
            }
            //支付方式:1=现金,2=银行
            if($post['pay_mode'] == 2){
                if(empty($post['bank_account_type'])){
                    $this->error(__('请选择银行账户类型'));
                }
                if(empty($post['bank'])){
                    $this->error(__('请输入银行名称'));
                }
                if(empty($post['bank_branch'])){
                    $this->error(__('请输入支行'));
                }
                if(empty($post['bank_branch_no'])){
                    $this->error(__('请输入支行号'));
                }
                if(empty($post['bank_account'])){
                    $this->error(__('请输入汇款账户号码'));
                }
                if(empty($post['bank_account_name'])){
                    $this->error(__('请输入汇款账户名称'));
                }
                $save['bank_account_type'] = $post['bank_account_type'];
                $save['bank'] = $post['bank'];
                $save['bank_branch'] = $post['bank_branch'];
                $save['bank_branch_no'] = $post['bank_branch_no'];
                $save['bank_account'] = $post['bank_account'];
                $save['bank_account_name'] = $post['bank_account_name'];
            }
            $order_details = [];
            $totalMoney = 0;
            foreach ($shopping as $key => $val) {
                //$totalMoney += $val['num'] * ($val['type'] == 1 ? $val['price'] : $val['price_zg']);
                $totalMoney += $val['num'] * $val['price'];
            }
            $save['price'] = $totalMoney;
            //手续费
            $procedures = 0;
            if($procedures){
                $totalMoney += $procedures;
            }
            $save['total_price'] = $totalMoney;
            $save['type'] = $post['type'];
            $save['pay_mode'] = $post['pay_mode'];
            // 启动事务
            Db::startTrans();
            try {
                $order_id = db('order')->insertGetId($save);
                if(!$order_id){
                    $this->error('失败');
                }

                foreach ($shopping as $key => $val) {
                    $order_details[] = [
                        'order_id' => $order_id,
                        'goods_id' => $val['goods_id'],
                        'title' => $val['title'],
                        'image' => $val['image'],
                        'specs_name' => $val['specs_name'],
                        'color' => $val['color'],
                        'jan' => $val['jan'],
                        'memo' => $val['memo'],
                        'num' => $val['num'],
                        'price' => ($val['type'] == 1 ? $val['price'] : $val['price']),
                        'type' => $val['type'],
                        'createtime' => time(),
                    ];
                }
                if($order_details){
                    db('order_details')->insertAll($order_details);
                }

                db('shopping')->where($where)->delete();

                $extend = [];
                $extend['user'] = $this->auth->getUserinfo();
                $save['store'] = $store;
                $save['details'] = $order_details;
                $extend['order'] = $save;
                //发送邮件
                //店头买取+现金支付
                //type 类型:1=门店,2=邮寄
                //支付方式:1=现金,2=银行
                if($post['type'] == 1 ){ //&& $post['pay_mode'] == 1
                    orderStoreSendEmail(
                        date('Y/m/d'),
                        $save['no'],
                        $this->auth->email,
                        $extend
                    );
                }else if($post['type'] == 2 ){ //&& $post['pay_mode'] == 2
                    $extend['email_type'] = 2;
                    orderStoreSendEmail(
                        date('Y/m/d'),
                        $save['no'],
                        $this->auth->email,
                        $extend
                    );
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                throw new \think\Exception($e->getMessage());
            }
            
            $this->success(__('成功'));
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 取消订单
     *
     */
    public function cancleOrder()
    {
        try {
            $post = $this->request->post();
         
            $order_id = $this->request->post('order_id');
            $order = db('order')->where('id', $order_id)->find();
            if(!$order){
                $this->error(__('数据不存在'));
            }
            
            db('order')->where('id', $order_id)->update([
                'status' => CS::ORDER_STATUS_5,
                'admin_status' => CS::ORDER_STATUS_ADMIN_9,
                //'deletetime' => time()
            ]);
            
            $this->success(__('成功'));
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @ApiParams (name="account", type="string", required=true, description="账号")
     * @ApiParams (name="password", type="string", required=true, description="密码")
     */
    public function login()
    {
        $id = $this->request->post('id');
        
        $account = $this->request->post('account');
        $password = $this->request->post('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $userinfo = $this->auth->getUserinfo();
            $data = ['userinfo' => $userinfo];
            \think\Cookie::set('token', $userinfo['token']);
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @ApiParams (name="username", type="string", required=true, description="用户名")
     * @ApiParams (name="password", type="string", required=true, description="密码")
     * @ApiParams (name="email", type="string", required=true, description="邮箱")
     * @ApiParams (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams (name="code", type="string", required=true, description="验证码")
     */
    public function register()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        $repassword = $this->request->post('repassword');
        $email = $this->request->post('email');
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if ($username && !Validate::is($username, "email")) {
            $this->error(__('Email is incorrect'));
        }
        $result = Ems::check($username, $captcha, 'register');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        if($password != $repassword){
            $this->error(__('两次密码不相同'));
        }
        $ret = $this->auth->register($username, $password, $username, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            registerSendEmail($username, $password, $username);
            $this->success(__('新規会員登録が完了しました。再度ログインをしてください。'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @ApiMethod (POST)
     * @ApiParams (name="avatar", type="string", required=true, description="头像地址")
     * @ApiParams (name="username", type="string", required=true, description="用户名")
     * @ApiParams (name="nickname", type="string", required=true, description="昵称")
     * @ApiParams (name="bio", type="string", required=true, description="个人简介")
     */
    public function profile()
    {
        try {
            $user = $this->auth->getUser();
            $post = $this->request->post();
            //$szb_image = $this->request->file('szb_image');
            $validate = [
                'persion_type' => 'require|number,個人法人区分 を入力してください|フォーマットが正しくありません',
                'name' => 'require|max:50,氏名を入力してください|50文字以内',
                'katakana' => 'require|max:50,氏名（カナ）を入力してください|50文字以内',
                'mobile' => 'require|max:50,電話番号を入力してください|30文字以内',
                //'email' => 'require|max:150,メールアドレス を入力してください|150文字以内',
                'zip_code' => 'require|number,郵便番号 を入力してください|郵便番号を確認してください。',
                'address' => 'require|max:150,ご住所 を入力してください|150文字以内',
                'occupation' => 'require|number,職業を入力してください|フォーマットが正しくありません',
                'gender' => 'require|number,性別を入力してください|フォーマットが正しくありません',
                'szb' => 'require|number,個人書類種別を入力してください|フォーマットが正しくありません',
                'szb_image' => 'require,個人書類写真をアップロードしてください。',
            ];
            $result = $this->verify($post, $validate);
            //dump($post);die;
       
            $user->persion_type = $post['persion_type'];
            $user->name = $post['name'];
            $user->katakana = $post['katakana'];
            $user->mobile = $post['mobile'];
            $user->zip_code = $post['zip_code'];
            $user->address = $post['address'];
            $user->occupation = $post['occupation'];
            $user->gender = $post['gender'];
            $user->szb = $post['szb'];
            $user->szb_image = is_array($post['szb_image']) ? join(',', $post['szb_image']) : $post['szb_image'];
            $user->birthday = $post['year'].'-'.$post['month'].'-'.$post['day'];
            $user->save();
            $this->success(__('成功'));
        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }

    /**
     * 修改邮箱
     *
     * @ApiMethod (POST)
     * @ApiParams (name="email", type="string", required=true, description="邮箱")
     * @ApiParams (name="captcha", type="string", required=true, description="验证码")
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->post('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @ApiParams (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams (name="newpassword", type="string", required=true, description="新密码")
     * @ApiParams (name="captcha", type="string", required=true, description="验证码")
     */
    public function resetpwd()
    {
        $type = $this->request->post("type", "mobile");
        $mobile = $this->request->post("mobile");
        $email = $this->request->post("username");
        $password = $this->request->post("password");
        $captcha = $this->request->post("captcha");
        if (!$password || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        //验证Token
        if (!Validate::make()->check(['password' => $password], ['password' => 'require|regex:\S{6,30}'])) {
            //$this->error(__('Password must be 6 to 30 characters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        $user = \app\common\model\User::getByEmail($email);
        if (!$user) {
            $this->error(__('User not found'));
        }
        $ret = Ems::check($email, $captcha, 'resetpwd');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        Ems::flush($email, 'resetpwd');
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($password, '', true);
        if ($ret) {
            $this->success(__('パスワードを変更しました。再度ログインをしてください。'));
        } else {
            $this->error($this->auth->getError());
        }
    }
}
