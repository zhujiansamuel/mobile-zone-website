<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/user/user/details.html";i:1763545360;s:84:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/layout/default.html";i:1763545360;s:81:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/common/meta.html";i:1763545360;s:83:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/common/script.html";i:1763545360;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">
<meta name="robots" content="noindex, nofollow">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo htmlentities(\think\Config::get('site.version') ?? ''); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo htmlentities(\think\Config::get('fastadmin.adminskin') ?? ''); ?>.css?v=<?php echo htmlentities(\think\Config::get('site.version') ?? ''); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config ?? ''); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo htmlentities($vo['url'] ?? ''); ?>"><?php echo htmlentities($vo['title'] ?? ''); ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <div style="width: 100%;display: inline-flex;">
    <div style="float:left;width: 40%;"> 
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
                <th width="30%">氏名（フリガナ）（カナ）</th>
                <td><?php echo $user['katakana']; ?></td>
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
                <th width="30%">郵便番号</th>
                <td><?php echo $user['zip_code']; ?></td>
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
                <th width="30%">性別</th>
                <td><?php echo $user['gender']==1?'男性' : '女性'; ?></td>
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

    </div>
    <div class="" style="float:left;width: 30%;margin-left: 20px;">
    
        <h4 style="text-align: center;">個人書類アップロード</h4>
        <table class="table table-striped" >
            <tbody>
                <?php if(is_array($user['szb_image']) || $user['szb_image'] instanceof \think\Collection || $user['szb_image'] instanceof \think\Paginator): $i = 0; $__LIST__ = $user['szb_image'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                <tr>
                    <th width="50%"></th>
                    <td>
                        <img data-width="60%" data-tips-image src="<?php echo $item; ?>" width="100">
                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        
    </div>

</div>

<div style="width: 100%;display: inline-flex;">
    <div style="float:left;width: 50%;"> 
        
        
    </div>
    
</div>

<div class="hide layer-footer">
    <label class="control-label col-xs-12 col-sm-2"></label>
    <div class="col-xs-12 col-sm-8">
        <button type="reset" class="btn btn-primary btn-embossed btn-close" onclick="Layer.closeAll();"><?php echo __('Close'); ?></button>
    
    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require.min.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version'] ?? ''); ?>"></script>

    </body>
</html>
