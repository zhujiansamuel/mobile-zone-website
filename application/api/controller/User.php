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
 * 会員API
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
     * お問い合わせ
     *
     */
    public function contactus()
    {
        try {
            $post = $this->request->post();
            $validate = [
                'name' => 'require|max:50,氏名を入力してください|50文字以内',
                'katakana' => 'require|max:50,お名前（カナ）を入力してください|50文字以内',
                //'tel' => 'require|max:50,電話番号を入力してください|30文字以内',
                'email' => 'require|max:150,メールアドレスをご確認ください。|150文字以内',
                'zip_code' => 'require|number,郵便番号 を入力してください|形式が正しくありません',
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
                $this->error('失敗');
            }

            $this->success(__('お問い合わせいただき、ありがとうございます。'));
        }catch (Exception $e){
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * カートに追加
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

            //商品を取得
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
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * ショッピングカートを修正
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
                $this->error(__('データが存在しません'));
            }
            
            db('shopping')->where('id', $shopping_id)->update([
                'num' => $num,
            ]);
            //現在のユーザーのカート金額を取得
            $shopping_list = db('shopping')->where('user_id', $shopping['user_id'])->select();
            $totalMoney = 0;
            foreach ($shopping_list as $key => $val) {
                $totalMoney += $val['num'] * $val['price'];
                //$totalMoney += $val['num'] * ($val['type'] == 1 ? $val['price'] : $val['price_zg']);
            }
            $totalMoney = number_format($totalMoney);
            $this->success(__('成功'), ['totalMoney' => $totalMoney]);
        }catch (Exception $e){
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * カートから削除
     *
     */
    public function delshopping()
    {
        try {
            $post = $this->request->post();
         
            $shopping_id = $this->request->post('shopping_id');
            $shopping = db('shopping')->where('id', $shopping_id)->find();
            if(!$shopping){
                $this->error(__('データが存在しません'));
            }
            
            db('shopping')->where('id', $shopping_id)->delete();
            
            $this->success(__('成功'));
        }catch (Exception $e){
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * 注文を作成
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
                //商品を追加してからご購入ください
                $this->error('商品を追加してご購入ください');
            }

            if(!$this->auth->name || !$this->auth->zip_code || !$this->auth->mobile){
                $this->error('会員登録情報を修正して提出してください');
            }

            // if(!$this->auth->name){
            //     //氏名を設定してから送信してください
            //     $this->error('氏名を設定した後、送信してください');
            // }
            // if(!$this->auth->zip_code){
            //     //郵便番号を設定してから送信してください
            //     $this->error('郵便番号を設定してから送信してください');
            // }

            // if(!$this->auth->mobile){
            //     //お電話番号を設定してから送信してください
            //     $this->error('お電話番号を設定して提出してください');
            // }
            
            $save = [];
            
            $save['user_id'] = $this->auth->id;
            $save['no'] = date('YmdHis').rand(100,999);
            $save['createtime'] = time();
            $store = [];
            //タイプ:1=店舗,2=郵送
            if($post['type'] == 1){
                if(empty($post['store_id'])){
                    $this->error(__('店舗を選択してください'));
                }
                if(empty($post['go_store_date'])){
                    $this->error(__('来店日を選択してください'));
                }
                if(empty($post['go_store_time'])){
                    $this->error(__('来店時間を選択してください'));
                }
                //店舗情報
                $store = db('store')->where('id', $post['store_id'])->find();
                $save['store_id'] = $post['store_id'];
                $save['store_name'] = $store['name'];
                $save['go_store_date'] = $post['go_store_date'];
                $save['go_store_time'] = $post['go_store_time'];
            }
            //支払方法:1=現金,2=銀行
            if($post['pay_mode'] == 2){
                if(empty($post['bank_account_type'])){
                    $this->error(__('銀行口座の種類を選択してください'));
                }
                if(empty($post['bank'])){
                    $this->error(__('銀行名を入力してください'));
                }
                if(empty($post['bank_branch'])){
                    $this->error(__('支店名を入力してください'));
                }
                if(empty($post['bank_branch_no'])){
                    $this->error(__('支店番号を入力してください'));
                }
                if(empty($post['bank_account'])){
                    $this->error(__('振込口座番号を入力してください'));
                }
                if(empty($post['bank_account_name'])){
                    $this->error(__('振込口座名義を入力してください'));
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
            //手数料
            $procedures = 0;
            if($procedures){
                $totalMoney += $procedures;
            }
            $save['total_price'] = $totalMoney;
            $save['type'] = $post['type'];
            $save['pay_mode'] = $post['pay_mode'];
            // トランザクションを開始
            Db::startTrans();
            try {
                $order_id = db('order')->insertGetId($save);
                if(!$order_id){
                    $this->error('失敗');
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
                //メール送信
                //店頭買取+現金払い
                //type タイプ:1=店舗,2=郵送
                //支払方法:1=現金,2=銀行
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
                // トランザクションをコミットする
                Db::commit();
            } catch (\Exception $e) {
                // トランザクションをロールバック
                Db::rollback();
                throw new \think\Exception($e->getMessage());
            }
            
            $this->success(__('成功'));
        }catch (Exception $e){
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * 注文をキャンセル
     *
     */
    public function cancleOrder()
    {
        try {
            $post = $this->request->post();
         
            $order_id = $this->request->post('order_id');
            $order = db('order')->where('id', $order_id)->find();
            if(!$order){
                $this->error(__('データが存在しません'));
            }
            
            db('order')->where('id', $order_id)->update([
                'status' => CS::ORDER_STATUS_5,
                'admin_status' => CS::ORDER_STATUS_ADMIN_9,
                //'deletetime' => time()
            ]);
            
            $this->success(__('成功'));
        }catch (Exception $e){
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * 会員ログイン
     *
     * @ApiMethod (POST)
     * @ApiParams (name="account", type="string", required=true, description="アカウント")
     * @ApiParams (name="password", type="string", required=true, description="パスワード")
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
     * 会員登録
     *
     * @ApiMethod (POST)
     * @ApiParams (name="username", type="string", required=true, description="ユーザー名")
     * @ApiParams (name="password", type="string", required=true, description="パスワード")
     * @ApiParams (name="email", type="string", required=true, description="メールアドレス")
     * @ApiParams (name="mobile", type="string", required=true, description="携帯番号")
     * @ApiParams (name="code", type="string", required=true, description="認証コード")
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
            $this->error(__('2 回入力したパスワードが一致しません'));
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
     * ログアウト
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
     * 会員個人情報を修正
     *
     * @ApiMethod (POST)
     * @ApiParams (name="avatar", type="string", required=true, description="アバターアドレス")
     * @ApiParams (name="username", type="string", required=true, description="ユーザー名")
     * @ApiParams (name="nickname", type="string", required=true, description="ニックネーム")
     * @ApiParams (name="bio", type="string", required=true, description="自己紹介")
     */
    public function profile()
    {
        try {
            $user = $this->auth->getUser();
            $post = $this->request->post();
            //$szb_image = $this->request->file('szb_image');
            $validate = [
                'persion_type' => 'require|number,個人／法人区分 を入力してください|形式が正しくありません',
                'name' => 'require|max:50,氏名を入力してください|50文字以内',
                'katakana' => 'require|max:50,氏名（カナ）を入力してください|50文字以内',
                'mobile' => 'require|max:50,電話番号を入力してください|30文字以内',
                //'email' => 'require|max:150,メールアドレス を入力してください|150文字以内',
                'zip_code' => 'require|number,郵便番号 を入力してください|郵便番号を確認してください。',
                'address' => 'require|max:150,ご住所 を入力してください|150文字以内',
                'occupation' => 'require|number,職業を入力してください|形式が正しくありません',
                'gender' => 'require|number,性別を入力してください|形式が正しくありません',
                'szb' => 'require|number,個人書類種別を入力してください|形式が正しくありません',
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
            //例外をキャッチ
            $this->error($e->getMessage());
        }
    }

    /**
     * メールアドレスを変更
     *
     * @ApiMethod (POST)
     * @ApiParams (name="email", type="string", required=true, description="メールアドレス")
     * @ApiParams (name="captcha", type="string", required=true, description="認証コード")
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
     * パスワードをリセット
     *
     * @ApiMethod (POST)
     * @ApiParams (name="mobile", type="string", required=true, description="携帯番号")
     * @ApiParams (name="newpassword", type="string", required=true, description="新しいパスワード")
     * @ApiParams (name="captcha", type="string", required=true, description="認証コード")
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
        //検証Token
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
        //ログインを一度シミュレート
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($password, '', true);
        if ($ret) {
            $this->success(__('パスワードを変更しました。再度ログインをしてください。'));
        } else {
            $this->error($this->auth->getError());
        }
    }
}
