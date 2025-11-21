<?php

// 公共助手函数

use think\exception\HttpResponseException;
use think\Response;

if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param string | array  $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name) {
            return $name;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }
}
if (!function_exists('orderStoreSendEmail')) {
    /**
     * 添加订单 -店头买取+现金支付 / 【邮送买取 +银行支付】
     */
    function orderStoreSendEmail($orderDate, $orderNo,$receiver='', $extend = [])
    {
        $order_details = '';
        $storeInfo = '';
        if(!empty($extend['order'])){
            foreach ($extend['order']['details'] as $key => $val) {
                $order_details .= $val['title'].'<br>';
                //$order_details .= ($val['type'] == 1 ? '新品' : '中古').'<br>';
                $order_details .=  $val['color']."&nbsp;".$val['specs_name'].'<br>';
                $order_details .=  '¥'.number_format($val['price']).'(税込)'.'<br>';
                $order_details .=  $val['num'].' 点<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            //类型:1=门店,2=邮寄
            if($extend['order']['type'] == 1){
                $storeInfo .= '買取方法:店頭買取<br>
                        来店場所:'.$extend['order']['store']['name'].'<br>
                        来店時間:'.$extend['order']['go_store_date'].' '.$extend['order']['go_store_time'].'<br>';
            }else{
                $storeInfo .= '買取方法:鄄送買取<br>';
                $storeInfo .= '配送情報:お客様情報と同じ<br>';
            }
            if($extend['order']['pay_mode'] == 1){
                $storeInfo .= 'お支払い方法:現金払い<br>';
            }else{
                $storeInfo .= 'お支払い方法:銀行振込<br>';
                $storeInfo .= '銀行:'.$extend['order']['bank'].'<br>';
                //$storeInfo .= '支店番号:'.$extend['bank_account_name'].'<br>';
                $storeInfo .= '支店:'.$extend['order']['bank_branch'].'<br>';
                $storeInfo .= '支店号:'.$extend['order']['bank_branch_no'].'<br>';
                $storeInfo .= '預金種目:'.($extend['order']['bank_account_type'] == 1 ? '普通預金' : '当座預金').'<br>';
                $storeInfo .= '振込口座番号:'.$extend['order']['bank_account'].'<br>';
                $storeInfo .= '振込口座名羲:'.$extend['order']['bank_account_name'].'<br>';
            }
            
        }
        $userInfo = '';
        if(!empty($extend['user'])){
            $userInfo .= $extend['user']['name'].'<br>';
            $userInfo .= ($extend['user']['persion_type'] == 1 ? '個人' : '法人').'<br>';
            $userInfo .= $extend['user']['email'].'<br>';
            $userInfo .= '〒'.$extend['user']['zip_code'].$extend['user']['address'];
            if(!empty($extend['email_type']) && $extend['email_type'] = 2){
                $userInfo .= '<br>電話番号:'.$extend['user']['mobile'].'<br>';
                $userInfo .= '生年月日:'.$extend['user']['birthday'];
            }

            $storeInfo .= '書類種別:'.getCategoryName($extend['user']['szb']).'<br>';
        }
        $html = 'このメールは自動送信です。<br>

この度、「Mobile Zone」でお申し込みしていただき、誠にありがとうでさいました。<br>

現在は「仮予約」の状態です。スタッフによる承認後、正式予約として確定いたします。<br>

買取金額変更の場合がごさいます、ご了承ください。<br>

お申し込み情報は下記となり、ご確認ください。マイページの「予約履歴」にもで確認していただきます。<br>

--------------------------------------------------------------<br>
お申し込み日:'.$orderDate.'<br>

ご予約番号:'.$orderNo.'<br>
--------------------------------------------------------------<br>

商品情報:<br>

'.$order_details.'<br>


--------------------------------------------------------------<br>
お客様情報:<br>
'.$userInfo.'<br>
'.$storeInfo.'<br>
--------------------------------------------------------------<br>
振込予定について<br>

商品到着後1~2日以内 にお振込みいたします。<br>

繁忙期の場合は 2~3日以内 に入金いたします。<br>

なお、振込完了後ので連絡はいたしませんので、各自でで自身のロ座にてで確認をお願いいたします。<br>
--------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページ URL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone';
        $result = sendEmail('この度は「Mobile Zone」にお申し込みいただき、誠にありがとうごさいます',$html,$receiver);
        return $result;
    }
}
if (!function_exists('orderStoreManualYuYueSendEmail')) {
    /**
     * 手动发送 - 预约邮件
     * 添加订单 -店头买取+现金支付 / 【邮送买取 +银行支付】
     */
    function orderStoreManualYuYueSendEmail($orderDate, $orderNo,$receiver='', $extend = [])
    {
        $order_details = '';
        $storeInfo = '';
        $orderMemo = '';
        if(!empty($extend['order'])){
            foreach ($extend['order']['details'] as $key => $val) {
                $order_details .= $val['title'].'<br>';
                //$order_details .= ($val['type'] == 1 ? '新品' : '中古').'<br>';
                $order_details .=  $val['color']."&nbsp;".$val['specs_name'].'<br>';
                $order_details .=  '¥'.number_format($val['price']).'(税込)'.'<br>';
                $order_details .=  $val['num'].' 点<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            //类型:1=门店,2=邮寄
            if($extend['order']['type'] == 1){
                $storeInfo .= '買取方法:店頭買取<br>
                        来店場所:'.$extend['order']['store']['name'].'<br>
                        来店時間:'.$extend['order']['go_store_date'].' '.$extend['order']['go_store_time'].'<br>';
            }else{
                $storeInfo .= '買取方法:鄄送買取<br>';
                $storeInfo .= '配送情報:お客様情報と同じ<br>';
            }
            if($extend['order']['pay_mode'] == 1){
                $storeInfo .= 'お支払い方法:現金払い<br>';
            }else{
                $storeInfo .= 'お支払い方法:銀行振込<br>';
                $storeInfo .= '銀行:'.$extend['order']['bank'].'<br>';
                //$storeInfo .= '支店番号:'.$extend['bank_account_name'].'<br>';
                $storeInfo .= '支店:'.$extend['order']['bank_branch'].'<br>';
                $storeInfo .= '支店号:'.$extend['order']['bank_branch_no'].'<br>';
                $storeInfo .= '預金種目:'.($extend['order']['bank_account_type'] == 1 ? '普通預金' : '当座預金').'<br>';
                $storeInfo .= '振込口座番号:'.$extend['order']['bank_account'].'<br>';
                $storeInfo .= '振込口座名羲:'.$extend['order']['bank_account_name'].'<br>';
            }
            $orderMemo = $extend['order']['memo'];
        }
        $userInfo = '';
        if(!empty($extend['user'])){
            $userInfo .= $extend['user']['name'].'<br>';
            $userInfo .= ($extend['user']['persion_type'] == 1 ? '個人' : '法人').'<br>';
            $userInfo .= $extend['user']['email'].'<br>';
            $userInfo .= '〒'.$extend['user']['zip_code'].$extend['user']['address'];
            if(!empty($extend['email_type']) && $extend['email_type'] = 2){
                $userInfo .= '<br>電話番号:'.$extend['user']['mobile'].'<br>';
                $userInfo .= '生年月日:'.$extend['user']['birthday'];
            }

            $storeInfo .= '書類種別:'.getCategoryName($extend['user']['szb']).'<br>';
        }
        $html = 'この度、「Mobile Zone」でお申し込みしていただき、誠にありがとうごさいました。<br>

このメール受信してから正式予約となります。お客様の梱包発送/ご来店お待ちしております。<br>

郵送金额保証:(到着時間指定必要ありません)<br>

東北、関東、中部、近畿、翌日着<br>

北海道、中国·四国、九州冲翌々日まで着<br>

来店金額保証:予約当日<br>

商品状態より買取査定金額変更の場合がでさいます、予めご了承ください。<br>

お申し込み情報は下記となり、ご確認ください。マイページの「予約履歴」にもで確認していただきます。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
お申し込み日:'.$orderDate.'<br>

ご予約番号:'.$orderNo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

商品情報:<br>

'.$order_details.'<br>


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
備考:<br>
'.$orderMemo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
お客様情報:<br>
'.$userInfo.'<br>
'.$storeInfo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

法人のお客様と個人事業者のお客様は適格請求書発行事業者登録番号が必須となります。<br>

※申込する際に 登録番号付きの請求書を発行ください。<br>

(単価が税込み表示、合計も税込みでその下の欄に消費税総額表示<br>

個人関しては変わりなく今まで通りご利用いただけます。<br>

買取申込書に適格請求書発行事業者ではない旨のチエック項目が追加されますのでチエックが必要になります。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
振込予定について<br>

商品到着後1~2日以内 にお振込みいたします。<br>

繁忙期の場合は 2~3日以内 に入金いたします。<br>

なお、振込完了後ので連絡はいたしませんので、各自でで自身のロ座にてで確認をお願いいたします。<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

郵送買取流れ:<br>

1、買取金額保証は予約完了連絡した日の翌日到着分となります。(北海道・中国・四国・九州・沖縄 翌々日まで着)<br>

週明けの月曜日到着分の買取値段は全て月曜日到着日の相場値段となります。大きな変動がなければそのまま送金となります。<br>

(単価 2000-3000 円超える変動がある場合は当社から連絡致します)<br>

(到着日、値段希望の場合買取依頼書に到着日值段を必ずで記载ください)<br>

2.買取依賴品物と添付書類を梱包して元払いでで送付ください。(着払い発送の場合は着払い送料を引いてから送金となります)<br>

3. 商品到着、品、入金<br>

ⓘ現金書留の場合は到着の翌日に郵便局で出します。(但し金・土曜日到着分は週明け郵便局始業日に出します)、お客様手元に屆くには 2-3日ほどかかります。<br>

間い合わせ番号は原則お知らせしませんが、発送からЗ日間以上経っても届かない場合は気軽にお間い合わせください。<br>

(50万円每手数料一律2,000 円お客様負担となります、50 万円を超えの場合は送金まで数日かかる場合でさいますので予めで了承ください)<br>

2)振込送金ご利用の場合:<br>

振込手数料当社負担<br>

振込送金は到着日の翌日まで振込致します。<br>

(できる限り当日振込手続きしますが、郵送買取依頼件数が増加しており、当日終業まで検品終わらない場合も多くなっており)。<br>

原則振込のご連絡は致しません。各自ご入金の確認をしてください。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページ URL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone';
        $result = sendEmail('予約完了のご案内「Mobile Zone」',$html,$receiver);
        return $result;
    }
}
if (!function_exists('orderStoreManualQueDingSendEmail')) {
    /**
     * 手动发送 - 查定邮件
     * 添加订单 -店头买取+现金支付 / 【邮送买取 +银行支付】
     */
    function orderStoreManualQueDingSendEmail($orderDate, $orderNo,$receiver='', $extend = [])
    {
        $order_details = '';
        $storeInfo = '';
        $orderMemo = '';
        if(!empty($extend['order'])){
            foreach ($extend['order']['details'] as $key => $val) {
                $order_details .= $val['title'].'<br>';
                //$order_details .= ($val['type'] == 1 ? '新品' : '中古').'<br>';
                $order_details .=  $val['color']."&nbsp;".$val['specs_name'].'<br>';
                $order_details .=  '¥'.number_format($val['price']).'(税込)'.'<br>';
                $order_details .=  $val['num'].' 点<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            //类型:1=门店,2=邮寄
            if($extend['order']['type'] == 1){
                $storeInfo .= '買取方法:店頭買取<br>
                        来店場所:'.$extend['order']['store']['name'].'<br>
                        来店時間:'.$extend['order']['go_store_date'].' '.$extend['order']['go_store_time'].'<br>';
            }else{
                $storeInfo .= '買取方法:鄄送買取<br>';
                $storeInfo .= '配送情報:お客様情報と同じ<br>';
            }
            if($extend['order']['pay_mode'] == 1){
                $storeInfo .= 'お支払い方法:現金払い<br>';
            }else{
                $storeInfo .= 'お支払い方法:銀行振込<br>';
                $storeInfo .= '銀行:'.$extend['order']['bank'].'<br>';
                //$storeInfo .= '支店番号:'.$extend['bank_account_name'].'<br>';
                $storeInfo .= '支店:'.$extend['order']['bank_branch'].'<br>';
                $storeInfo .= '支店号:'.$extend['order']['bank_branch_no'].'<br>';
                $storeInfo .= '預金種目:'.($extend['order']['bank_account_type'] == 1 ? '普通預金' : '当座預金').'<br>';
                $storeInfo .= '振込口座番号:'.$extend['order']['bank_account'].'<br>';
                $storeInfo .= '振込口座名羲:'.$extend['order']['bank_account_name'].'<br>';
            }
            $orderMemo = $extend['order']['determine_memo'];
        }
        $userInfo = '';
        if(!empty($extend['user'])){
            $userInfo .= $extend['user']['name'].'<br>';
            $userInfo .= ($extend['user']['persion_type'] == 1 ? '個人' : '法人').'<br>';
            $userInfo .= $extend['user']['email'].'<br>';
            $userInfo .= '〒'.$extend['user']['zip_code'].$extend['user']['address'];
            if(!empty($extend['email_type']) && $extend['email_type'] = 2){
                $userInfo .= '<br>電話番号:'.$extend['user']['mobile'].'<br>';
                $userInfo .= '生年月日:'.$extend['user']['birthday'];
            }

            $storeInfo .= '書類種別:'.getCategoryName($extend['user']['szb']).'<br>';
        }
        $html = '「Mobile Zone」をご利用いただき、誠にありがとうごさいました。<br>

この度はお申込みの買取内容の査定が完了致しました。<br>

査定結果をお知らせいたしますので、ご確認よろしくお願い申し上けます。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
お申し込み日:'.$orderDate.'<br>

ご予約番号:'.$orderNo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

「査定結果」:<br>
'.$orderMemo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

商品情報:<br>

'.$order_details.'<br>


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

お客様情報:<br>
'.$userInfo.'<br>
'.$storeInfo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

法人のお客様と個人事業者のお客様は適格請求書発行事業者登録番号が必須となります。<br>

※申込する際に 登録番号付きの請求書を発行ください。<br>

(単価が税込み表示、合計も税込みでその下の欄に消費税総額表示<br>

個人関しては変わりなく今まで通りご利用いただけます。<br>

買取申込書に適格請求書発行事業者ではない旨のチエック項目が追加されますのでチエックが必要になります。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
振込予定について<br>

商品到着後1~2日以内 にお振込みいたします。<br>

繁忙期の場合は 2~3日以内 に入金いたします。<br>

なお、振込完了後ので連絡はいたしませんので、各自でで自身のロ座にてで確認をお願いいたします。<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

商品到着、検品、入金<br>

ⓘ現金書留の場合は到着の翌日に郵便局で出します。(但し金·土曜日到着分は週明け郵便局始業日に出します)、お客様手元に届くには 2-3日ほどかかります。<br>

間い合わせ番号は原則お知らせしませんが、発送から3日間以上経っても届かない場合は気軽にお問い合わせください。<br>

(50万円每手数料一律2.000 円お客様負担となります、50 万円を超えの場合は送金まで数日かかる場合でさいますので予めで了承ください)<br>

2)振込送金ご利用の場合:<br>

振込手数料当社負担<br>

振込送金は到着日の翌日まで振込致します。<br>

(できる限り当日振込手続きしますが、郵送買取依賴件数が増加しており、当日終業まで検品終わらない場合も多くなっており)。

原則振込のご連絡は致しません。各自ご入金の確認をしてください。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページ URL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone';
        $result = sendEmail($extend['subject'],$html,$receiver);
        return $result;
    }
}
if (!function_exists('registerSendEmail')) {
    /**
     * 注册成功发送邮件
     */
    function registerSendEmail($username, $password,$receiver='')
    {
        $result = sendEmail('ア力ウント情報「Mobile Zone」',
                '「Mobile Zone」の会員を登録していただき、誠にありがとうごさいました。<br>

アカウント情報をお送りします。大切に保管いただきますようお願いいたします。<br>

--------------------------------------------------------------<br>
アカウント:<br>
'.$username.'<br>
パスワード:<br>
'.$password.'<br>
--------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページ URL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone'
            ,$receiver);
        return $result;
    }
}
if (!function_exists('orderStoreManualCancelSendEmail')) {
    /**
     * 手动发送 - 订单取消邮件
     */
    function orderStoreManualCancelSendEmail($orderDate, $orderNo,$receiver='', $extend = [])
    {
        $order_details = '';
        $storeInfo = '';
        $orderMemo = '';
        if(!empty($extend['order'])){
            foreach ($extend['order']['details'] as $key => $val) {
                $order_details .= $val['title'].'<br>';
                //$order_details .= ($val['type'] == 1 ? '新品' : '中古').'<br>';
                $order_details .=  $val['color']."&nbsp;".$val['specs_name'].'<br>';
                $order_details .=  '¥'.number_format($val['price']).'(税込)'.'<br>';
                $order_details .=  $val['num'].' 点<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            $orderMemo = $extend['order']['cancel_memo'];
        }
        $userName = '';
        if(!empty($extend['user'])){
            $userName = $extend['user']['name'];
        }
        $html = $userName.'  様<br>

ご利用いただき誠にありがとうごさいます。<br>

大変恐れ入りますが、ご注文をキャンセルとさせていただきます。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
お申し込み日:'.$orderDate.'<br>

ご予約番号:'.$orderNo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

「キャンセル理由」:<br>
'.$orderMemo.'<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

商品情報:<br>

'.$order_details.'<br>


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

またの機会がごさいましたらよるしくお願いいたします。<br>

どうでよるしくお願い申し上げます。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページ URL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone';
        $result = sendEmail($extend['subject'],$html,$receiver);
        return $result;
    }
}
if (!function_exists('registerSendEmail')) {
    /**
     * 注册成功发送邮件
     */
    function registerSendEmail($username, $password,$receiver='')
    {
        $result = sendEmail('ア力ウント情報「Mobile Zone」',
                '「Mobile Zone」の会員を登録していただき、誠にありがとうごさいました。<br>

アカウント情報をお送りします。大切に保管いただきますようお願いいたします。<br>

--------------------------------------------------------------<br>
アカウント:<br>
'.$username.'<br>
パスワード:<br>
'.$password.'<br>
--------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページURL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone'
            ,$receiver);
        return $result;
    }
}
if (!function_exists('sendEmail')) {
    /**
     * 发送邮件
     */
    function sendEmail($subject='',$messageTitle='', $receiver='')
    {
        $email = new \app\common\library\Email;
        
        $result = $email
            ->to($receiver)
            ->from(config('site.mail_from'), 'Mobile Zone')
            ->subject(__($subject))
            ->message($messageTitle)
            ->send();
        if(!$result){
            return $email->getError();
        }
        return $result;
    }
}
if (!function_exists('getTree')) {
    function getTree($items, $parentId = 0) {
        $branch = array();
     
        foreach ($items as $item) {
            if ($item['pid'] == $parentId) {
                $children = getTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
     
        return $branch;
    }
}
if (!function_exists('getConfigOther')) {
    function getConfigOther($name, $value) {
        return config('other.'.$name)[$value] ??'';
    }
}
if (!function_exists('getCategoryName')) {
    function getCategoryName($category_id) {
        return db('category')->where('id', $category_id)->value('name') ?: '';
    }
}
if (!function_exists('isValidChineseMobile')) {
    function isValidChineseMobile($mobile) {
        return preg_match('/^1[3-9]\d{9}$/', $mobile);
    }
}
if (!function_exists('hideMobile')) {
    /**
     * 隐藏手机号中间四位
     * @access public
     * @param  int $number 
     * @return bool
     */
    function hideMobile($mobile)
    {
       
        return preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $mobile);
    }
}
if (!function_exists('removeOnlyDomains')) {
    function removeOnlyDomains($url) {
        //$url = preg_replace('#^https?://[^/]+#i', '', $url);;
        $domainName = getProtocol();//config('other.WEB_DOMAIN_NAME');
        $url = str_replace($domainName, '', $url);
        return $url;
    }
}
if (!function_exists('getProtocol')) {

 
    function getProtocol()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
        return $protocol . '://' . request()->host();
    }
}
if (!function_exists('getExplodeDomainName')) {

 
    function getExplodeDomainName($data)
    {
        $data = explode(',', $data);
        $domainName = getProtocol();//config('other.WEB_DOMAIN_NAME');
        foreach ($data as $key => $val) {
            $data[$key] = $domainName . $val;
        }
        return $data;
    }
}
if (!function_exists('getDomainNames')) {

 
    function getDomainNames($data, $keyName='image')
    {
        $domainName = getProtocol();//config('other.WEB_DOMAIN_NAME');
        foreach ($data as $key => &$val) {
            $val[$keyName] = $val[$keyName] ? $domainName . $val[$keyName] : $val[$keyName];
        }
        return $data;
    }
}
if (!function_exists('getDomainName')) {

 
    function getDomainName($data, $keyName='image')
    {
        if(strpos($data, 'http') === false && $data){
            return getProtocol() . $data;
        }
        return $data;
    }
}
if (!function_exists('setContentImageDomainName')) {

  
    function setContentImageDomainName($content)
    {
        $domainName = getProtocol();//config('other.WEB_DOMAIN_NAME');
        if(strpos($content, 'http') !== false){
            return $content;
        }
        return preg_replace("/(<img .*?src=\")(\/.*?)(\".*?>)/is","\${1}".$domainName."\${2}\${3}",$content);
    }
}
if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int    $size      大小
     * @param string $delimiter 分隔符
     * @param int    $precision 小数位数
     * @return string
     */
    function format_bytes($size, $delimiter = '', $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . $delimiter . $units[$i];
    }
}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int    $time   时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }
}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time  时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }
}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string  $url    资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $regex = "/^((?:[a-z]+:)?\/\/|data:image\/)(.*)/i";
        $cdnurl = \think\Config::get('upload.cdnurl');
        if (is_bool($domain) || stripos($cdnurl, '/') === 0) {
            $url = preg_match($regex, $url) || ($cdnurl && stripos($url, $cdnurl) === 0) ? $url : $cdnurl . $url;
        }
        if ($domain && !preg_match($regex, $url)) {
            $domain = is_bool($domain) ? request()->domain() : $domain;
            $url = $domain . $url;
        }
        return $url;
    }
}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return true;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }
        fclose($fp);
        return true;
    }
}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname  目录
     * @param bool   $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }
}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest   目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }
}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items  数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields) {
            return $items;
        }
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = $v['field'] ?? $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = $v['display'] ?? str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = $v['primary'] ?? '';
            $v['column'] = $v['column'] ?? 'name';
            $v['model'] = $v['model'] ?? '';
            $v['table'] = $v['table'] ?? '';
            $v['name'] = $v['name'] ?? str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                // 优先判断使用table的配置
                $model = $v['table'] ? \think\Db::table($v['table']) : \think\Db::name($v['name']);
            }
            $primary = $v['primary'] ?: $model->getPk();
            $result[$v['field']] = isset($ids[$v['field']]) ? $model->where($primary, 'in', $ids[$v['field']])->column($v['column'], $primary) : [];
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $linedata = array_intersect_key($result[$n], $curr);
                    $v[$fieldsArr[$n]['display']] = $fieldsArr[$n]['column'] == '*' ? $linedata : implode(',', $linedata);
                }
            }
        }
        return $items;
    }
}

if (!function_exists('var_export_short')) {

    /**
     * 使用短标签打印或返回数组结构
     * @param mixed   $data
     * @param boolean $return 是否返回数据
     * @return string
     */
    function var_export_short($data, $return = true)
    {
        return var_export($data, $return);
    }
}

if (!function_exists('letter_avatar')) {
    /**
     * 首字母头像
     * @param $text
     * @return string
     */
    function letter_avatar($text)
    {
        $total = unpack('L', hash('adler32', $text, true))[1];
        $hue = $total % 360;
        list($r, $g, $b) = hsv2rgb($hue / 360, 0.3, 0.9);

        $bg = "rgb({$r},{$g},{$b})";
        $color = "#ffffff";
        $first = mb_strtoupper(mb_substr($text, 0, 1));
        $src = base64_encode('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="100" width="100"><rect fill="' . $bg . '" x="0" y="0" width="100" height="100"></rect><text x="50" y="50" font-size="50" text-copy="fast" fill="' . $color . '" text-anchor="middle" text-rights="admin" dominant-baseline="central">' . $first . '</text></svg>');
        $value = 'data:image/svg+xml;base64,' . $src;
        return $value;
    }
}

if (!function_exists('hsv2rgb')) {
    function hsv2rgb($h, $s, $v)
    {
        $r = $g = $b = 0;

        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f * $s);
        $t = $v * (1 - (1 - $f) * $s);

        switch ($i % 6) {
            case 0:
                $r = $v;
                $g = $t;
                $b = $p;
                break;
            case 1:
                $r = $q;
                $g = $v;
                $b = $p;
                break;
            case 2:
                $r = $p;
                $g = $v;
                $b = $t;
                break;
            case 3:
                $r = $p;
                $g = $q;
                $b = $v;
                break;
            case 4:
                $r = $t;
                $g = $p;
                $b = $v;
                break;
            case 5:
                $r = $v;
                $g = $p;
                $b = $q;
                break;
        }

        return [
            floor($r * 255),
            floor($g * 255),
            floor($b * 255)
        ];
    }
}

if (!function_exists('check_nav_active')) {
    /**
     * 检测会员中心导航是否高亮
     */
    function check_nav_active($url, $classname = 'active')
    {
        $auth = \app\common\library\Auth::instance();
        $requestUrl = $auth->getRequestUri();
        $url = ltrim($url, '/');
        return $requestUrl === str_replace(".", "/", $url) ? $classname : '';
    }
}

if (!function_exists('check_cors_request')) {
    /**
     * 跨域检测
     */
    function check_cors_request()
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] && config('fastadmin.cors_request_domain')) {
            $info = parse_url($_SERVER['HTTP_ORIGIN']);
            $domainArr = explode(',', config('fastadmin.cors_request_domain'));
            $domainArr[] = request()->host(true);
            if (in_array("*", $domainArr) || in_array($_SERVER['HTTP_ORIGIN'], $domainArr) || (isset($info['host']) && in_array($info['host'], $domainArr))) {
                header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
            } else {
                $response = Response::create('跨域检测无效', 'html', 403);
                throw new HttpResponseException($response);
            }

            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');

            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                $response = Response::create('', 'html');
                throw new HttpResponseException($response);
            }
        }
    }
}

if (!function_exists('xss_clean')) {
    /**
     * 清理XSS
     */
    function xss_clean($content, $is_image = false)
    {
        return \app\common\library\Security::instance()->xss_clean($content, $is_image);
    }
}

if (!function_exists('url_clean')) {
    /**
     * 清理URL
     */
    function url_clean($url)
    {
        if (!check_url_allowed($url)) {
            return '';
        }
        return xss_clean($url);
    }
}

if (!function_exists('check_ip_allowed')) {
    /**
     * 检测IP是否允许
     * @param string $ip IP地址
     */
    function check_ip_allowed($ip = null)
    {
        $ip = is_null($ip) ? request()->ip() : $ip;
        $forbiddenipArr = config('site.forbiddenip');
        $forbiddenipArr = !$forbiddenipArr ? [] : $forbiddenipArr;
        $forbiddenipArr = is_array($forbiddenipArr) ? $forbiddenipArr : array_filter(explode("\n", str_replace("\r\n", "\n", $forbiddenipArr)));
        if ($forbiddenipArr && \Symfony\Component\HttpFoundation\IpUtils::checkIp($ip, $forbiddenipArr)) {
            $response = Response::create('请求无权访问', 'html', 403);
            throw new HttpResponseException($response);
        }
    }
}

if (!function_exists('check_url_allowed')) {
    /**
     * 检测URL是否允许
     * @param string $url URL
     * @return bool
     */
    function check_url_allowed($url = '')
    {
        //允许的主机列表
        $allowedHostArr = [
            strtolower(request()->host())
        ];

        if (empty($url)) {
            return true;
        }

        //如果是站内相对链接则允许
        if (preg_match("/^[\/a-z][a-z0-9][a-z0-9\.\/]+((\?|#).*)?\$/i", $url) && substr($url, 0, 2) !== '//') {
            return true;
        }

        //如果是站外链接则需要判断HOST是否允许
        if (preg_match("/((http[s]?:\/\/)+((?>[a-z\-0-9]{2,}\.)+[a-z]{2,8}|((?>([0-9]{1,3}\.)){3}[0-9]{1,3}))(:[0-9]{1,5})?)(?:\s|\/)/i", $url)) {
            $chkHost = parse_url(strtolower($url), PHP_URL_HOST);
            if ($chkHost && in_array($chkHost, $allowedHostArr)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('build_suffix_image')) {
    /**
     * 生成文件后缀图片
     * @param string $suffix 后缀
     * @param null   $background
     * @return string
     */
    function build_suffix_image($suffix, $background = null)
    {
        $suffix = mb_substr(strtoupper($suffix), 0, 4);
        $total = unpack('L', hash('adler32', $suffix, true))[1];
        $hue = $total % 360;
        list($r, $g, $b) = hsv2rgb($hue / 360, 0.3, 0.9);

        $background = $background ? $background : "rgb({$r},{$g},{$b})";

        $icon = <<<EOT
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
            <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"/>
            <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"/>
            <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "/>
            <path style="fill:{$background};" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16 V416z"/>
            <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"/>
            <g><text><tspan x="220" y="380" font-size="124" font-family="Verdana, Helvetica, Arial, sans-serif" fill="white" text-anchor="middle">{$suffix}</tspan></text></g>
        </svg>
EOT;
        return $icon;
    }
}
