<?php

// 共通ヘルパー関数

use think\exception\HttpResponseException;
use think\Response;

if (!function_exists('__')) {

    /**
     * 言語変数値の取得
     * @param string $name 言語変数名
     * @param string | array  $vars 動的変数値
     * @param string $lang 言語
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
     * 注文を追加 -店頭買取+現金払い / 【郵送買取 +銀行振込】
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
                $order_details .=  $val['num'].' 時<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            //タイプ:1=店舗,2=郵送
            if($extend['order']['type'] == 1){
                $storeInfo .= '買取方法:店頭買取<br>
                        来店場所:'.$extend['order']['store']['name'].'<br>
                        来店時間:'.$extend['order']['go_store_date'].' '.$extend['order']['go_store_time'].'<br>';
            }else{
                $storeInfo .= '買取方法:郵送買取<br>';
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
                $storeInfo .= '振込口座名義:'.$extend['order']['bank_account_name'].'<br>';
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

この度、「Mobile Zone」でお申し込みいただき、誠にありがとうございました。<br>

現在は「仮予約」の状態です。スタッフによる承認後、正式予約として確定いたします。<br>

買取金額が変更となる場合がございます、ご了承ください。<br>

お申し込み情報は下記の通りとなり、ご確認ください。マイページの「予約履歴」からもご確認いただけます。<br>

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

なお、振込完了後のご連絡はいたしませんので、各自ご自身の口座にてご確認をお願いいたします。<br>
--------------------------------------------------------------<br>

※このメールは送信専用のため返信はお受けできません。<br>

Mobile Zone<br>

ホームページ URL<br>

TEL:'.config('site.tel').'<br>

MAIL:'.config('site.mail').'<br>

2025 Mobile Zone';
        $result = sendEmail('この度は「Mobile Zone」にお申し込みいただき、誠にありがとうございます',$html,$receiver);
        return $result;
    }
}
if (!function_exists('orderStoreManualYuYueSendEmail')) {
    /**
     * 手動送信 - 予約メール
     * 注文を追加 -店頭買取+現金払い / 【郵送買取 +銀行振込】
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
                $order_details .=  $val['num'].' 時<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            //タイプ:1=店舗,2=郵送
            if($extend['order']['type'] == 1){
                $storeInfo .= '買取方法:店頭買取<br>
                        来店場所:'.$extend['order']['store']['name'].'<br>
                        来店時間:'.$extend['order']['go_store_date'].' '.$extend['order']['go_store_time'].'<br>';
            }else{
                $storeInfo .= '買取方法:郵送買取<br>';
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
                $storeInfo .= '振込口座名義:'.$extend['order']['bank_account_name'].'<br>';
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
        $html = 'この度、「Mobile Zone」でお申し込みいただき、誠にありがとうございました。<br>

このメールを受信してから正式予約となります。お客様の梱包発送/ご来店をお待ちしております。<br>

郵送金額保証:(到着時間指定は必要ありません)<br>

東北、関東、中部、近畿、翌日着<br>

北海道、中国·四国、九州沖々日までに到着<br>

来店買取金額保証:ご予約当日<br>

商品の状態により買取査定金額が変更となる場合がございます、あらかじめご了承ください。<br>

お申し込み情報は下記の通りとなり、ご確認ください。マイページの「予約履歴」からもご確認いただけます。<br>

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

法人のお客様と個人事業主のお客様は、適格請求書発行事業者登録番号のご入力が必須となります。<br>

※お申し込みの際は 登録番号付きの請求書を発行してください。<br>

(単価は税込み表示、合計も税込みで、その下の欄に消費税総額を表示<br>

個人のお客様は、従来どおり変更なくご利用いただけます。<br>

買取申込書に、適格請求書発行事業者ではない旨のチェック項目が追加されますので、チェックが必要となります。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
振込予定について<br>

商品到着後1~2日以内 にお振込みいたします。<br>

繁忙期の場合は 2~3日以内 に入金いたします。<br>

なお、振込完了後のご連絡はいたしませんので、各自ご自身の口座にてご確認をお願いいたします。<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

郵送買取の流れ:<br>

1、買取金額保証は、予約完了のご連絡をした日の翌々日までに到着日到着分となります。(北海道・中国・四国・九州・沖縄 翌々日までに到着々日までに到着)<br>

週明けの月曜日到着分の買取金額は、すべて月曜日到着日の相場金額となります。大きな変動がなければ、そのまま送金となります。<br>

(単価 2000-3000 円を超える変動がある場合は、当社からご連絡いたします)<br>

(到着日、到着日の金額をご希望の場合は、買取依頼書に到着日の金額を必ずご記載ください)<br>

2.買取依頼品と添付書類を梱包し、元払いでご送付ください。(着払いでの発送の場合は、着払い送料を差し引いてからのご送金となります)<br>

3. 商検品到着、検品、入金<br>

ⓘ現金書留の場合は、到着の翌日に郵便局から発送いたします。(ただし金・土曜日到着分は、週明けの郵便局営業日に発送いたします)、お客様のお手元に届くまでには 2-3日ほどかかります。<br>

お問い合わせ番号は原則お知らせいたしませんが、発送からЗ日間以上経っても届かない場合は、お気軽にお問い合わせください。<br>

(50万円ごとに手数料一律2,000 円はお客様ご負担となります、50 万円を超える場合は、送金まで数日かかる場合がございますので、あらかじめご了承ください)<br>

2)振込送金をご利用の場合:<br>

振込手数料は当社負担<br>

振込送金は、到着日の翌日までにお振込いたします。<br>

(可能な限り当日中に振込手続きをいたしますが、郵送買取のご依頼件数が増加しており、当日終業時間までに検品が終わらない場合も多くなっております)。<br>

原則として振込完了のご連絡はいたしません。各自で入金状況のご確認をお願いいたします。<br>

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
     * 手動送信 - 査定メール
     * 注文を追加 -店頭買取+現金払い / 【郵送買取 +銀行振込】
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
                $order_details .=  $val['num'].' 時<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            //タイプ:1=店舗,2=郵送
            if($extend['order']['type'] == 1){
                $storeInfo .= '買取方法:店頭買取<br>
                        来店場所:'.$extend['order']['store']['name'].'<br>
                        来店時間:'.$extend['order']['go_store_date'].' '.$extend['order']['go_store_time'].'<br>';
            }else{
                $storeInfo .= '買取方法:郵送買取<br>';
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
                $storeInfo .= '振込口座名義:'.$extend['order']['bank_account_name'].'<br>';
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
        $html = '「Mobile Zone」をご利用いただき、誠にありがとうございました。<br>

この度はお申込みいただいた買取内容の査定が完了いたしました。<br>

査定結果をお知らせいたしますので、ご確認のほどよろしくお願い申し上げます。<br>

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

法人のお客様と個人事業主のお客様は、適格請求書発行事業者登録番号のご入力が必須となります。<br>

※お申し込みの際は 登録番号付きの請求書を発行してください。<br>

(単価は税込み表示、合計も税込みで、その下の欄に消費税総額を表示<br>

個人のお客様は、従来どおり変更なくご利用いただけます。<br>

買取申込書に、適格請求書発行事業者ではない旨のチェック項目が追加されますので、チェックが必要となります。<br>

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>
振込予定について<br>

商品到着後1~2日以内 にお振込みいたします。<br>

繁忙期の場合は 2~3日以内 に入金いたします。<br>

なお、振込完了後のご連絡はいたしませんので、各自ご自身の口座にてご確認をお願いいたします。<br>
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>

商品到着、検品、入金<br>

ⓘ現金書留の場合は、到着の翌日に郵便局から発送いたします。(ただし·ただし金曜・土曜到着分は週明けの郵便局営業日に発送いたします)、お客様のお手元に届くまでには 2-3日ほどかかります。<br>

お問い合わせ番号は原則お知らせいたしませんが、発送から3日間以上経っても届かない場合はお気軽にお問い合わせください。<br>

(50万円ごとに手数料一律2.000 円はお客様ご負担となります、50 万円を超える場合は、送金まで数日かかる場合がございますので、あらかじめご了承ください)<br>

2)振込送金をご利用の場合:<br>

振込手数料は当社負担<br>

振込送金は、到着日の翌日までにお振込いたします。<br>

(可能な限り当日中に振込手続きをいたしますが、郵送買取のご依頼件数が増加しており、当日終業時間までに検品が終わらない場合も多くなっております)。

原則として振込完了のご連絡はいたしません。各自で入金状況のご確認をお願いいたします。<br>

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
     * 登録成功メール送信
     */
    function registerSendEmail($username, $password,$receiver='')
    {
        $result = sendEmail('アカウント情報「Mobile Zone」',
                '「Mobile Zone」の会員にご登録いただき、誠にありがとうございました。<br>

アカウント情報をお送りします。大切に保管くださいますようお願いいたします。<br>

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
     * 手動送信 - 注文キャンセルメール
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
                $order_details .=  $val['num'].' 時<br>';
            }
            $order_details .= '<br><br>合計金額:￥'.number_format($extend['order']['total_price']).'(税込)';
            $orderMemo = $extend['order']['cancel_memo'];
        }
        $userName = '';
        if(!empty($extend['user'])){
            $userName = $extend['user']['name'];
        }
        $html = $userName.'  様<br>

ご利用いただき誠にありがとうございます。<br>

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

またの機会がございましたらよろしくお願いいたします。<br>

どうぞよろしくお願い申し上げます。<br>

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
     * 登録成功メール送信
     */
    function registerSendEmail($username, $password,$receiver='')
    {
        $result = sendEmail('アカウント情報「Mobile Zone」',
                '「Mobile Zone」の会員にご登録いただき、誠にありがとうございました。<br>

アカウント情報をお送りします。大切に保管くださいますようお願いいたします。<br>

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
     * メール送信
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
     * 携帯番号の中央4桁を非表示にする
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
     * バイト数を読みやすいテキストに変換する
     * @param int    $size      サイズ
     * @param string $delimiter 区切り文字
     * @param int    $precision 小数点桁数
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
     * タイムスタンプを日時に変換する
     * @param int    $time   タイムスタンプ
     * @param string $format 日時フォーマット
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
     * セマンティックな時間表現を取得する
     * @param int $time  時間
     * @param int $local ローカル時刻
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }
}

if (!function_exists('cdnurl')) {

    /**
     * アップロードしたリソースのCDNのアドレス
     * @param string  $url    リソースの相対パス
     * @param boolean $domain ドメイン名を表示するかどうか またはドメイン名を直接渡す
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
     * ファイルまたはフォルダが書き込み可能か判定する
     * @param string $file ファイルまたはディレクトリ
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
     * フォルダを削除する
     * @param string $dirname  ディレクトリ
     * @param bool   $withself 自身を削除するかどうか
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
     * フォルダをコピーする
     * @param string $source ソースフォルダ
     * @param string $dest   ターゲットフォルダ
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
     * 関連フィールドデータを付加する
     * @param array $items  データリスト
     * @param mixed $fields レンダリング元フィールド
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
                // 優先的に使用を判定するtableの設定
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
     * ショートタグを使用して配列構造を出力または返却する
     * @param mixed   $data
     * @param boolean $return データを返すかどうか
     * @return string
     */
    function var_export_short($data, $return = true)
    {
        return var_export($data, $return);
    }
}

if (!function_exists('letter_avatar')) {
    /**
     * 頭文字アイコン
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
     * 会員センターナビがハイライトされているかを検出
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
     * クロスドメイン検出
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
                $response = Response::create('クロスドメイン検出が無効です', 'html', 403);
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
     * クリーニングXSS
     */
    function xss_clean($content, $is_image = false)
    {
        return \app\common\library\Security::instance()->xss_clean($content, $is_image);
    }
}

if (!function_exists('url_clean')) {
    /**
     * クリーニングURL
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
     * チェックIP許可するかどうか
     * @param string $ip IPアドレス
     */
    function check_ip_allowed($ip = null)
    {
        $ip = is_null($ip) ? request()->ip() : $ip;
        $forbiddenipArr = config('site.forbiddenip');
        $forbiddenipArr = !$forbiddenipArr ? [] : $forbiddenipArr;
        $forbiddenipArr = is_array($forbiddenipArr) ? $forbiddenipArr : array_filter(explode("\n", str_replace("\r\n", "\n", $forbiddenipArr)));
        if ($forbiddenipArr && \Symfony\Component\HttpFoundation\IpUtils::checkIp($ip, $forbiddenipArr)) {
            $response = Response::create('このリクエストにはアクセス権限がありません', 'html', 403);
            throw new HttpResponseException($response);
        }
    }
}

if (!function_exists('check_url_allowed')) {
    /**
     * チェックURL許可するかどうか
     * @param string $url URL
     * @return bool
     */
    function check_url_allowed($url = '')
    {
        //許可ホスト一覧
        $allowedHostArr = [
            strtolower(request()->host())
        ];

        if (empty($url)) {
            return true;
        }

        //サイト内の相対リンクであれば許可
        if (preg_match("/^[\/a-z][a-z0-9][a-z0-9\.\/]+((\?|#).*)?\$/i", $url) && substr($url, 0, 2) !== '//') {
            return true;
        }

        //サイト外リンクの場合は判定が必要HOST許可するかどうか
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
     * ファイル拡張子アイコンを生成
     * @param string $suffix サフィックス
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

if (!function_exists('sendSlackNotification')) {
    /**
     * Slack通知を送信
     * @param string $type 通知タイプ（contactus, order, register）
     * @param array $data 通知データ
     * @return bool
     */
    function sendSlackNotification($type, $data = [])
    {
        try {
            // Slack Webhook URLを設定から取得
            $webhookUrl = config('site.slack_webhook_url');

            // Webhook URLが設定されていない場合はログに記録して正常終了
            if (empty($webhookUrl)) {
                \think\Log::write('Slack Webhook URLが設定されていません。通知タイプ: ' . $type, 'notice');
                return true;
            }

            // 通知タイプに応じてメッセージを構築
            $message = buildSlackMessage($type, $data);

            // Slackにメッセージを送信
            $payload = json_encode([
                'text' => $message,
                'username' => 'Mobile Zone Bot',
                'icon_emoji' => ':bell:'
            ]);

            $ch = curl_init($webhookUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5秒タイムアウト

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                \think\Log::write('Slack通知送信失敗。HTTPコード: ' . $httpCode . ', タイプ: ' . $type, 'error');
                return false;
            }

            return true;
        } catch (\Exception $e) {
            // エラーをログに記録するが、例外をスローしない（主処理に影響を与えない）
            \think\Log::write('Slack通知送信エラー: ' . $e->getMessage(), 'error');
            return false;
        }
    }
}

if (!function_exists('buildSlackMessage')) {
    /**
     * Slack通知メッセージを構築
     * @param string $type 通知タイプ
     * @param array $data データ
     * @return string
     */
    function buildSlackMessage($type, $data)
    {
        $message = '';

        switch ($type) {
            case 'contactus':
                $message = ":email: *新規お問い合わせ*\n\n";
                $message .= "ID: " . ($data['id'] ?? '-') . "\n";
                $message .= "氏名: " . ($data['name'] ?? '-') . "\n";
                $message .= "カナ: " . ($data['katakana'] ?? '-') . "\n";
                $message .= "電話: " . ($data['tel'] ?? '-') . "\n";
                $message .= "メール: " . ($data['email'] ?? '-') . "\n";
                $message .= "郵便番号: " . ($data['zip_code'] ?? '-') . "\n";
                $message .= "住所: " . ($data['address'] ?? '-') . "\n";
                $message .= "内容: " . ($data['content'] ?? '-') . "\n";
                break;

            case 'order':
                $message = ":shopping_cart: *新規注文*\n\n";
                $message .= "注文ID: " . ($data['order_id'] ?? '-') . "\n";
                $message .= "ユーザー: " . ($data['username'] ?? '-') . "\n";
                $message .= "金額: ¥" . number_format($data['amount'] ?? 0) . "\n";
                break;

            case 'register':
                $message = ":bust_in_silhouette: *新規会員登録*\n\n";
                $message .= "ユーザーID: " . ($data['user_id'] ?? '-') . "\n";
                $message .= "ユーザー名: " . ($data['username'] ?? '-') . "\n";
                $message .= "メール: " . ($data['email'] ?? '-') . "\n";
                break;

            default:
                $message = ":bell: *通知*\n\n";
                $message .= "タイプ: " . $type . "\n";
                $message .= "データ: " . json_encode($data, JSON_UNESCAPED_UNICODE);
                break;
        }

        return $message;
    }
}
