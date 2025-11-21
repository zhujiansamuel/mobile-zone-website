<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use custom\Data;
use custom\ConfigStatus as CS;
use think\Db;
use addons\epay\library\Service;
use app\common\model\User;
/**
 * 支付
 */
class Pay extends Api
{
    //use \traits\Shares, \traits\Redis;

    protected $noNeedLogin = ['notify','ys_notify','ys_df_notify','cardnotify'];
    protected $noNeedRight = ['*'];


    /**
     * 支付
     *
     */
    public function index()
    {
        try {
            $request = $this->request->post();
            $type = $this->request->post('type');
            $no = $request['no'] ?? '';
            
            if(!$no){
                throw new Exception('缺少订单编号');
            }

            if(!$this->auth->openid){
                throw new Exception('支付失败,请授权');
            }

            $response = [];
            if(strpos($no, 'R') !== false){
                $OrderMsg = '充值';
                $info = db('user_recharge')->where('no', $no)->find();
                $type = 'recharge';
            }else{
                $info = db('order')->where('no', $no)->find();
                $OrderMsg = '订单';
                $type = 'order';
                $info['money'] = $info['total_money'];
            }

            $no = $info['no'] . '-' . rand(100,999);
            
            if($info['money'] > 0){
                $notifyurl = getProtocol().'/notify/'.$type;
                $returnurl = getProtocol().'/returnx/'.$type;
                $response = Service::submitOrder(
                    $info['money'], $no, 'wechat', $OrderMsg, $notifyurl, $returnurl, 'miniapp', $this->auth->openid
                );
            }else{
                $data = [
                    'out_trade_no' =>   $no,
                    'total_fee' => $info['money'] * 100
                ];
                switch ($type) {
                    case 'recharge':
                        $this->updateRecharge($data);
                        break;
                    default:
                        $this->updateOrder($data);
                        break;
                }
            }
            
            
            $this->success('成功！', $response);

        }catch (Exception $e){
            //捕获异常
            $this->error($e->getMessage());
        }
    }


    /**
     * 支付成功，仅供开发测试
     */
    public function notify()
    {
        $type = $this->request->param('type');

        $pay = Service::checkNotify('wechat');
        $xinpaopaoPayNotify = json_decode(cache('xinpaopaoPayNotify'), true) ?? [];
        if (!$pay) {
            array_push($xinpaopaoPayNotify, '签名错误');
            cache('xinpaopaoPayNotify', json_encode($xinpaopaoPayNotify, JSON_UNESCAPED_UNICODE), 3600);
            return;
        }
        $data = $pay->verify();
        try {
            array_push($xinpaopaoPayNotify, json_encode($data, JSON_UNESCAPED_UNICODE));
            
            // 设置缓存数据
            cache('xinpaopaoPayNotify', json_encode($xinpaopaoPayNotify, JSON_UNESCAPED_UNICODE), 3600);

            switch ($type) {
                case 'recharge':
                    $this->updateRecharge($data);
                    break;
                default:
                    $this->updateOrder($data);
                    break;
            }
            

        } catch (Exception $e) {
        }

        //下面这句必须要执行,且在此之前不能有任何输出
        return $pay->success()->send();
    }

    private function updateOrder($data)
    {
        list($no, $rand) = explode('-', $data['out_trade_no']);
        $payamount = $data['total_fee'] / 100;
        $transaction_id = $data['transaction_id'] ?? '';
        
        $order = db('order')->where('no', $no)->find();
        //获取订单用户信息
        $orderUser = db('user')->where('id', $order['user_id'])->find();

        // 启动事务
        Db::startTrans();
        try {
            db('order')->where('no', $no)->update([
                'status' => CS::ORDER_STATUS_YES_PAY,
                'is_pay' => 1,
                'transaction_id' => $transaction_id,
                'pay_time' => time(),
            ]);

            if($orderUser && $orderUser['pid']){
                $income = round((config('site.distributionLevel1') / 100) * $order['total_money'], 2);
                User::income($income, $orderUser['pid'], '收益', CS::MONEY_LOG_TYPE_PROFIT, $order['id']);
                //获取上级用户信息
                $subUserPid = db('user')->where('id', $orderUser['pid'])->value('pid');
                if($subUserPid){
                    $income2 = round((config('site.distributionLevel2') / 100) * $order['total_money'], 2);
                    User::income($income2, $subUserPid, '收益', CS::MONEY_LOG_TYPE_PROFIT, $order['id']);
                }
            }

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
    }

    private function updateRecharge($data)
    {
        list($no, $rand) = explode('-', $data['out_trade_no']);
        $payamount = $data['total_fee'] / 100;
        $transaction_id = $data['transaction_id'] ?? '';
        $user_recharge = db('user_recharge')->where('no', $no)->find();
        
        // 启动事务
        Db::startTrans();
        try {
            db('user_recharge')->where('no', $no)->update([
                'is_pay' => 1,
                'pay_time' => time(),
                'transaction_id' => $transaction_id
            ]);

            User::money($user_recharge['money'] + $user_recharge['deliver'], $user_recharge['user_id'], '充值', CS::MONEY_LOG_TYPE_RECHARGE, $user_recharge['id']);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            //$this->error($e->getMessage());
        }
    }
    
    
    /**
     * 支付返回，仅供开发测试
     */
    public function returnx()
    {
        $paytype = $this->request->param('paytype');
        $out_trade_no = $this->request->param('out_trade_no');
        // $pay = Service::checkReturn($paytype);
        // if (!$pay) {
        //     $this->error('签名错误', '');
        // }

        //你可以在这里通过out_trade_no去验证订单状态
        //但是不可以在此编写订单逻辑！！！

        $this->success("请返回网站查看支付结果", 'https://szhswy.cn/#/pages/tabbar/home/pay/paySuccess/paySuccess');
    }
    
    
    public function ceshi()
    {
        $no = $this->request->post('no');
        $order_info['total_fee'] = 50;
        $order_info['out_trade_no'] = $no.'-556';
        if(strpos($no, 'R') !== false){
            $this->updateRecharge($order_info);
        }else{
            $this->updateOrder($order_info);
        }
        
        // $xinpaopaoPayNotify = json_decode(cache('xinpaopaoPayNotify'), true) ?? [];
        // dump($xinpaopaoPayNotify);
        // die;
       //Data::name('order', $order_info)->order_info(true,true)->verifi('payStatus')->getProject()->updateOrder();
        die;
    }
    
    /*
     * 给微信发送确认订单金额和签名正确，SUCCESS信息 -xzz0521
     */
    private function return_success(){
        $return['return_code'] = 'SUCCESS';
        $return['return_msg'] = 'OK';
        $xml_post = '<xml>
                    <return_code>'.$return['return_code'].'</return_code>
                    <return_msg>'.$return['return_msg'].'</return_msg>
                    </xml>';
        echo $xml_post;exit;
    }
    
    
    /**
     * 将xml转为array
     * @param string $xml
     * return array
     */
    public function xml_to_array($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }
    


}
