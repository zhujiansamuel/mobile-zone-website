<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:79:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/goods/add.html";i:1763545360;s:84:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/layout/default.html";i:1763545360;s:81:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/common/meta.html";i:1763545360;s:83:"/home/xs942548/mobile-zone.jp/public_html/application/admin/view/common/script.html";i:1763545360;}*/ ?>
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
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Category_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-category_id" data-order-by="weigh desc"  data-rule="required" min="0" data-source="category/selectpage" data-params='{"custom[type]":"goods","custom[pid]":0}' class="form-control selectpage" name="row[category_id]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Category_second'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-category_second" data-order-by="weigh desc"  min="0" class="form-control selectpage" data-source="category/selectpage" name="row[category_second]" type="text" value="0">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Category_three'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-category_three" data-order-by="weigh desc"  min="0" class="form-control selectpage" data-source="category/selectpage" name="row[category_three]" type="text" value="0">
        </div>
    </div>
    <div class="form-group hidden">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('IMEI'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-imei" class="form-control" name="row[imei]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('備考'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-memo" data-rule="required" class="form-control" name="row[memo]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="hidden" name="row[extendParams]" value="handleParams">
            <input id="c-title" data-rule="required" class="form-control" name="row[title]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Image'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-image" data-rule="required" class="form-control" size="50" name="row[image]" type="text">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-image" class="btn btn-danger faupload" data-input-id="c-image" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="false" data-preview-id="p-image"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-image" class="btn btn-primary fachoose" data-input-id="c-image" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-image"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-image"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('产品规格'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <dl class="list-unstyled fieldlist" data-name="row[color]" data-template="row[color]tpl">
                <dd>
                    <ins><?php echo __('名称'); ?></ins>
                </dd>
                <dd>
                    <ins><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></ins>
                </dd>
            </dl>

            <textarea name="row[color]" class="form-control hide" cols="30" rows="5"></textarea>
            <script id="row[color]tpl" type="text/html">
                <dd class="form-inline">
                    <ins><input type="text" name="<%=name%>[<%=index%>][value]" class="form-control" size="15" value="<%=row%>"/></ins>
                    <ins>
                        <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                        <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>
                    </ins>
                </dd>
            </script>
            <!-- <input id="c-color_id" data-order-by="weigh desc" data-source="category/selectpage" data-params='{"custom[type]":"color","custom[pid]":0}' data-multiple="true" class="form-control selectpage" name="row[color_id]" type="text" value=""> -->
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('规格/颜色等'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <table class="table fieldlist" data-name="row[spec_info]" data-template="row[spec_info]tpl">
                <tr>
                    <td><?php echo __('规格/颜色等'); ?></td>
                    <td><?php echo __('价格'); ?></td>
                    <td width="90"><?php echo __('Operate'); ?></td>
                </tr>
                <tr><td colspan="5">
                <a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a>
                <textarea name="row[spec_info]" class="form-control hide" cols="30" rows="5"></textarea>
                </td></tr>
            </table>
            <script type="text/html" id="row[spec_info]tpl">
                <tr>
                    <td><input type="text" name="<%=name%>[<%=index%>][name]" class="form-control" value="<%=row.name%>"/></td>
                    <td><input type="text" name="<%=name%>[<%=index%>][price]" class="form-control" value="<%=row.price%>"/></td>
                    <td width="90">
                        <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                        <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>
                    </td>
                </tr>
            </script>
        </div>
    </div>
    <div class="form-group hidden">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Price'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-price"  min="0" class="form-control" step="0.01" name="row[price]" type="number" value="0.00">
        </div>
    </div>
    <div class="form-group hidden">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Price_zg'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-price_zg"  min="0" class="form-control" step="0.01" name="row[price_zg]" type="number" value="0.00">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group hidden">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label for="row[type]-1"><input id="row[type]-1" name="row[type]" type="radio" value="1" <?php if(in_array((1), explode(',',"1"))): ?>checked<?php endif; ?> /> 新品</label> 
            <label for="row[type]-2"><input id="row[type]-2" name="row[type]" type="radio" value="2" /> 中古</label> 
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('权重排序'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-weigh" data-rule="required" class="form-control" name="row[weigh]" type="number" value="0">
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-primary btn-embossed disabled"><?php echo __('OK'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require.min.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version'] ?? ''); ?>"></script>

    </body>
</html>
