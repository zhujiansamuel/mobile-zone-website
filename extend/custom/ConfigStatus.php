<?php
namespace custom;

class  ConfigStatus  {

    //入账
    const ENTRY = 0;
    //消费
    const CONSUME = 1;
    
    const DISABLED_YES = 1;

    const MONEY_DYNAMIC = [
        self::ENTRY => '入账',
        self::CONSUME => '消费',
    ];
    
    //支付方式:0=微信支付,1=服务次数
    const PAY_MOD_WX = 0;
    //服务次数
    const PAY_MOD_SERVICE = 1;
    //积分
    const PAY_MOD_SCORE = 2;

    const PAY_MOD_LIST = [
        self::PAY_MOD_WX => '微信支付',
        self::PAY_MOD_SERVICE => '服务次数',
        self::PAY_MOD_SCORE => '积分兑换',
    ];

    //服务类型  分类:1=团购订单,2=代扔垃圾,3=代取快递,4=积分兑换
    const SERVICE_TYPE_GROUP_BUY = 1;

    const SERVICE_TYPE_WASTE = 2;

    const SERVICE_TYPE_FASTMAIL = 3;

    const SERVICE_TYPE_EXCHANGE = 4;

    const SERVICE_TYPE_LIST = [
        self::SERVICE_TYPE_GROUP_BUY => '团购',
        self::SERVICE_TYPE_WASTE => '代扔垃圾',
        self::SERVICE_TYPE_FASTMAIL => '代取快递',
        self::SERVICE_TYPE_EXCHANGE => '积分兑换',
    ];

    const SERVICE_TYPE_WASTE_TEXT = 'waste';

    const SERVICE_TYPE_FASTMAIL_TEXT = 'fastmail';

    const SERVICE_TYPE_TEXT_VALUE = [
        self::SERVICE_TYPE_WASTE_TEXT => self::SERVICE_TYPE_WASTE,
        self::SERVICE_TYPE_FASTMAIL_TEXT => self::SERVICE_TYPE_FASTMAIL,
    ];

    //性别
    const GENDER_UNKNOWN = 0;

    const GENDER_MALE = 1;

    const GENDER_FEMALE = 2;

    const GENDER_LIST = [
        self::GENDER_MALE => '男',
        self::GENDER_FEMALE => '女',
    ];
    
 
    const ORDER_STATUS_1 = 1;

    const ORDER_STATUS_2 = 2;
  
    const ORDER_STATUS_3 = 3;
    const ORDER_STATUS_4 = 4;
    const ORDER_STATUS_5 = 5;
    const ORDER_STATUS_6 = 6;
    

    const ORDER_STATUS_LIST = [
        self::ORDER_STATUS_1 => '申し込み承認中',
        self::ORDER_STATUS_2 => '申し込み承認',
        self::ORDER_STATUS_3 => '查定進行中',
        self::ORDER_STATUS_4 => '取引完了',
        self::ORDER_STATUS_5 => '取引キャンセル申請',
        self::ORDER_STATUS_6 => '取引キャンセル',
        
    ];

    const ORDER_STATUS_ADMIN_1 = 1;
    const ORDER_STATUS_ADMIN_2 = 2;
    const ORDER_STATUS_ADMIN_3 = 3;
    const ORDER_STATUS_ADMIN_4 = 4;
    const ORDER_STATUS_ADMIN_5 = 5;
    const ORDER_STATUS_ADMIN_6 = 6;
    const ORDER_STATUS_ADMIN_7 = 7;
    const ORDER_STATUS_ADMIN_8 = 8;
    const ORDER_STATUS_ADMIN_9 = 9;
    const ORDER_STATUS_ADMIN_10 = 10;
    const ORDER_STATUS_ADMIN_11 = 11;
    const ORDER_STATUS_ADMIN_12 = 12;
    const ORDER_STATUS_ADMIN_13 = 13;
    const ORDER_STATUS_ADMIN_14 = 14;
    
    const ORDER_STATUS_ADMIN_LIST = [
        self::ORDER_STATUS_ADMIN_1 => '新規申込',
        self::ORDER_STATUS_ADMIN_2 => '申込交涉中',
        self::ORDER_STATUS_ADMIN_3 => '申込承認済および到着待ち中',
        self::ORDER_STATUS_ADMIN_4 => '商品を受け取った',
        self::ORDER_STATUS_ADMIN_5 => '查定交涉中',
        self::ORDER_STATUS_ADMIN_6 => '查定確認',
        self::ORDER_STATUS_ADMIN_7 => '入庫完了',
        self::ORDER_STATUS_ADMIN_8 => '入金完了',
        self::ORDER_STATUS_ADMIN_9 => '取引キャンセル交涉中',
        self::ORDER_STATUS_ADMIN_10 => '取引キャンセル返品中',
        self::ORDER_STATUS_ADMIN_11 => '取引キャンセル返金返品中',
        self::ORDER_STATUS_ADMIN_12 => '取引キャンセル返品完了返金中',
        self::ORDER_STATUS_ADMIN_13 => '取引キャンセル返金完了返品中',
        self::ORDER_STATUS_ADMIN_14 => '取引キャンセルプロセス完了',
    ];

    const ORDER_STATUS_DY_ADMIN_LIST = [
        self::ORDER_STATUS_ADMIN_1 => self::ORDER_STATUS_1,
        self::ORDER_STATUS_ADMIN_2 => self::ORDER_STATUS_1,
        self::ORDER_STATUS_ADMIN_3 => self::ORDER_STATUS_2,
        self::ORDER_STATUS_ADMIN_4 => self::ORDER_STATUS_3,
        self::ORDER_STATUS_ADMIN_5 => self::ORDER_STATUS_3,
        self::ORDER_STATUS_ADMIN_6 => self::ORDER_STATUS_3,
        self::ORDER_STATUS_ADMIN_7 => self::ORDER_STATUS_3,
        self::ORDER_STATUS_ADMIN_8 => self::ORDER_STATUS_4,
        self::ORDER_STATUS_ADMIN_9 => self::ORDER_STATUS_5,
        self::ORDER_STATUS_ADMIN_10 => self::ORDER_STATUS_6,
        self::ORDER_STATUS_ADMIN_11 => self::ORDER_STATUS_6,
        self::ORDER_STATUS_ADMIN_12 => self::ORDER_STATUS_6,
        self::ORDER_STATUS_ADMIN_13 => self::ORDER_STATUS_6,
        self::ORDER_STATUS_ADMIN_14 => self::ORDER_STATUS_6,
    ];

    const ORDER_STATUS_SCORE_LIST = [
        self::ORDER_STATUS_NO_PAY => '待兑换',
        self::ORDER_STATUS_YES_PAY => '待发货',
        self::ORDER_STATUS_COMPLETE => '已完成',
    ];

    //用户充值/提现记录
    //状态 0待处理1成功 2失败
    const RECHARGE_STATUS_PENDING = 0;
    //成功
    const RECHARGE_STATUS_SUCCESS = 1;
    //失败
    const RECHARGE_STATUS_FAIL = 2;
    //正在处理
    const RECHARGE_STATUS_HANDLE = 3;

    const RECHARGE_STATUS_LIST = [
        self::RECHARGE_STATUS_PENDING => '待处理',
        self::RECHARGE_STATUS_SUCCESS => '成功',
        self::RECHARGE_STATUS_FAIL => '失败',
        self::RECHARGE_STATUS_HANDLE => '正在处理',
    ];


    /*支付方式*/
    //余额支付
    const BALANCE_PAYMENT = 0;
    //冻结余额支付
    const FROZEN_BALANCE_PAYMENT = 1;
    //其他支付
    const OTHER_PAYMENT = 2;
    //微信支付
    const WX_PAYMENT = 3;
    //快捷支付
    const QUICK_PAYMENT = 4;

    const PAYMENT_LIST = [
        self::BALANCE_PAYMENT => '余额支付',
        self::FROZEN_BALANCE_PAYMENT => '冻结余额支付',
        self::OTHER_PAYMENT => '支付宝支付',
        self::WX_PAYMENT => '微信支付',
        self::QUICK_PAYMENT => '快捷支付',
    ];

    /*余额类型*/
    const MONEY_TYPE_BALANCE = 0;

    const MONEY_TYPE_FROZEN_BALANCE = 1;


    /* 是否支付*/
    const YES_PAY = 1;

    const NOT_PAY = 0;

    const MONEY_LOG_TYPE_PROFIT = 0;

    const MONEY_LOG_TYPE_WITHDRAWAL =1;

    const MONEY_LOG_TYPE_EXPEND =2;

    const MONEY_LOG_TYPE_LIST = [
        self::MONEY_LOG_TYPE_PROFIT => '收益',
        self::MONEY_LOG_TYPE_WITHDRAWAL => '提现',
        self::MONEY_LOG_TYPE_EXPEND => '消费',
    ];

    const SCORE_LOG_TYPE_PROFIT = 0;

    const SCORE_LOG_TYPE_EXCHANGE =1;

    const SCORE_LOG_TYPE_EXPEND =2;

    const SCORE_LOG_TYPE_LIST = [
        self::MONEY_LOG_TYPE_PROFIT => '收益',
        self::SCORE_LOG_TYPE_EXCHANGE => '兑换',
        self::MONEY_LOG_TYPE_EXPEND => '消费',
    ];

   
    const IS_INCOME = 0;

    const IS_INCOME_YES = 1;

    //是否支付
    const IS_PAY_NO = 0;

    const IS_PAY_YES = 1;

    const IS_PAY_LIST = [
        self::IS_PAY_NO => '未支付',
        self::IS_PAY_YES => '已支付',
    ];

    

    const USER_AUTH_SUCCESS_MSG = '您的认证申请已审核通过,请进入骑手界面查看';

    const USER_AUTH_FAIL_MSG = '您的认证申请审核失败,原因:$text';

    const WITHDRAWAL_MSG_SUCCESS = '您的提现申请审核成功,已成功打款';

    const WITHDRAWAL_MSG_FAIL = '您的提现申请审核失败,金额已退回';

    const POINTSMALL_REFUND_MSG_REFUND = '您的退换货-退货申请审核成功,骑手将上门取件';

    const POINTSMALL_REFUND_MSG_CHANGE = '您的退换货-换货申请审核成功,骑手将上门取件';

    const POINTSMALL_REFUND_MSG_FAIL = '您的退换货申请审核失败';

    const POINTSMALL_REFUND_MSG_REFUND_RECEIVE = '您的退换货-退货成功,积分已退回';

    const RIDER_APPLYFOR_MSG_SUCCESS = '您的骑手申请已审核通过';

    const RIDER_APPLYFOR_MSG_FAIL = '您的骑手申请审核失败,原因:$text';

    const FEEDBACK_REPLY = '您反馈的【$title】,管理员已回复:$content';
    
 
}