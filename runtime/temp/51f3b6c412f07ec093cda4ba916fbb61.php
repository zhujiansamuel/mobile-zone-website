<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/order/daochupdf.html";i:1763545360;}*/ ?>
<div style="width: 100%;display: inline-flex;">
    <div style="float:left;width: 50%;"> 
        <h4 style="text-align: center;">お客様の情報 </h4>
        <table class="table table-striped" >
            <!-- <thead>
            <tr>
                <th><?php echo __('打卡位置'); ?></th>
                <th><?php echo __('拍照图片'); ?></th>
                <th><?php echo __('时间'); ?></th>
            </tr>
            </thead> -->
            <tbody>
        <?php if($user): ?>
            <tr>
                <th width="30%">お名前</th>
                <td><?php echo $user['name']; ?></td>
            </tr>
            <tr>
                <th width="30%">メールアドレス</th>
                <td><?php echo $user['email']; ?></td>
            </tr>
            <tr>
                <th width="30%">個人法人区分</th>
                <td><?php echo getConfigOther('persion_type_list', $user['persion_type']); ?></td>
            </tr>
            <tr>
                <th width="30%">お電話番号</th>
                <td><?php echo $user['mobile']; ?></td>
            </tr>
            <tr>
                <th width="30%">ご住所</th>
                <td><?php echo $user['address']; ?></td>
            </tr>
            <tr>
                <th width="30%">生年月日</th>
                <td><?php echo $user['birthday']; ?></td>
            </tr>
            <tr>
                <th width="30%">職業</th>
                <td><?php echo getCategoryName($user['occupation']); ?></td>
            </tr>
            <tr>
                <th width="30%">個人書類種別</th>
                <td><?php echo getCategoryName($user['szb']); ?></td>
            </tr>
        <?php else: ?>
            暂无数据
        <?php endif; ?>  

            </tbody>
        </table>
        <h4 style="text-align: center;">個人書類アップロード </h4>
        <div>
            <?php if(is_array($user['szb_image']) || $user['szb_image'] instanceof \think\Collection || $user['szb_image'] instanceof \think\Paginator): $i = 0; $__LIST__ = $user['szb_image'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                <img src="<?php echo getDomainName($item ?? ''); ?>" width="150">
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>  
    </div>
    <div style="float:left;width: 40%;margin-left: 20px;">
    
        <h4 style="text-align: center;">&nbsp;</h4>
        <table class="table table-striped" style="width:100%">
            <tbody>
                <tr>
                    <th width="50%">お支払い方法</th>
                    <td><?php echo getConfigOther('pay_mode_list', $row['pay_mode']); ?></td>
                </tr>
            <?php if($row['pay_mode'] == 2): ?>
                <tr>
                    <th width="50%">銀行</th>
                    <td><?php echo $row['bank']; ?></td>
                </tr>
                <tr>
                    <th width="50%">支店</th>
                    <td><?php echo $row['bank_branch']; ?></td>
                </tr>
                <tr>
                    <th width="50%">預金種目</th>
                    <td><?php echo getConfigOther('bank_account_type', $row['bank_account_type']); ?></td>
                </tr>
                <tr>
                    <th width="50%">振込口座番号</th>
                    <td><?php echo $row['bank_account']; ?></td>
                </tr>
                <tr>
                    <th width="50%">振込口座名義</th>
                    <td><?php echo $row['bank_account_name']; ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        
        <h4 style="text-align: center;">合计</h4>
        <table class="table table-striped"  style="width:100%">
      
            <tbody>
            <tr>
                <th width="40%">商品价格</th>
                <td>
                <?php echo $row['price']; ?>
                </td>
            </tr>
            <tr>
                <th width="40%">共</th>
                <td>
                <?php echo $row['total_price']; ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    
</div>

<table class="table table-striped" style="margin-top: 45px;">
    <thead>
    <tr>
        <th width="20%"><?php echo __('商品名称'); ?></th>
        <th><?php echo __('備考'); ?></th>
        <th><?php echo __('商品图片'); ?></th>
        <th><?php echo __('商品规格'); ?></th>
        <th><?php echo __('数量'); ?></th>
        <th><?php echo __('单价'); ?></th>
        <!-- <th><?php echo __('类型'); ?></th> -->
    </tr>
    </thead>
    <tbody>
<?php if(is_array($order_details) || $order_details instanceof \think\Collection || $order_details instanceof \think\Paginator): $i = 0; $__LIST__ = $order_details;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
    <tr>
        <td><?php echo $item['title']; ?></td>
        <td><?php echo $item['memo']; ?></td>
        <td><img src="<?php echo getDomainName($item['image'] ?? ''); ?>" width="100"></td>
        <td><?php echo $item['color']; ?><br><?php echo $item['specs_name']; ?></td>
        <td><?php echo $item['num']; ?></td>
        <td><?php echo $item['price']; ?></td>
        <!-- <td><?php echo getConfigOther('goods_type', $item['type']); ?></td> -->
    </tr>
  <?php endforeach; endif; else: echo "" ;endif; if(\think\Request::instance()->get('dialog')): endif; ?>
    </tbody>
</table>

<div style="width: 100%;display: inline-flex;">
    <div style="float:left;width: 50%;"> 
        
        
    </div>
    
</div>


