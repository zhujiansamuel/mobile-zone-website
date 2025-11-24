<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:80:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/user/index.html";i:1763545360;s:81:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/common/meta.html";i:1763545360;s:81:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/common/head.html";i:1763545360;s:80:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/common/nav.html";i:1763545360;s:85:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/common/user_nav.html";i:1763545360;s:81:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/common/foot.html";i:1763545360;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
<title><?php echo htmlentities((isset($title) && ($title !== '')?$title:'') ?? ''); ?> – <?php echo htmlentities($site['name'] ?? ''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="msapplication-tap-highlight" content="no">
<meta name="format-detection" content="telephone=no" />
<?php if(isset($keywords)): ?>
<meta name="keywords" content="<?php echo htmlentities($keywords ?? ''); ?>">
<?php endif; if(isset($description)): ?>
<meta name="description" content="<?php echo htmlentities($description ?? ''); ?>">
<?php endif; ?>

<link rel="shortcut icon" href="/assets/img/favicon.ico" />

<link rel="stylesheet" href="/css/swiper.min.css" />
<link rel="stylesheet" href="/css/base.css" />
<link rel="stylesheet" href="/css/base_rel.css" />
<script src="/js/jquery.js"></script>
<script src="/js/swiper.min.js"></script>
<script src="/js/js.js"></script>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config: <?php echo json_encode($config ?? ''); ?>
    };
</script>

	</head>
	<body>
		<div class="header">
	<div class="header1 contain">
		<div class="logo">
			<a href="/"><img src="/uploads/20251029/a0480341e272cdcb79c34cfcf78c3007.png" alt="" /></a>
		</div>
		<div class="nav">
	<ul>
		<li>
			<a href="/">ホーム</a>
		</li>
		<li>
			<a href="/goods">
				商品一覧
				<img class="arrowa" src="/img/arrowa.png" alt="" />
				<img class="arrow" src="/img/arrow.png" alt="" />
			</a>
			<div class="nav_sub">
			<?php if(is_array($goodsCategoryTree) || $goodsCategoryTree instanceof \think\Collection || $goodsCategoryTree instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsCategoryTree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
				<div class="nav_sub1">
					<a href="/goods/<?php echo $item['id']; ?>">
						<span><?php echo $item['name']; ?></span>
						
						<?php if(!empty($item['children'])): ?>
						<img class="arrowa" src="/img/arrow1a.png" alt="" />
						<img class="arrow" src="/img/arrow1.png" alt="" />
						<?php endif; ?>
					</a>
					<div class="nav_sub2">
					<?php if(!empty($item['children'])): if(is_array($item['children']) || $item['children'] instanceof \think\Collection || $item['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?>
						<div class="nav_sub1">
							<a href="/goods/<?php echo $item['id']; ?>/<?php echo $item2['id']; ?>">
								<span><?php echo $item2['name']; ?></span>
								
							<?php if(!empty($item2['children'])): ?>
								<img class="arrowa" src="/img/arrow1a.png" alt="" />
								<img class="arrow" src="/img/arrow1.png" alt="" />
							<?php endif; ?>
							</a>
						<?php if(!empty($item2['children'])): ?>
							<div class="nav_sub2">
								<div class="nav_sub3">
								<?php if(is_array($item2['children']) || $item2['children'] instanceof \think\Collection || $item2['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item2['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item3): $mod = ($i % 2 );++$i;?>
									<a href="/goods/<?php echo $item['id']; ?>/<?php echo $item2['id']; ?>/<?php echo $item3['id']; ?>">
										<span><?php echo $item3['name']; ?></span>
									</a>
								<?php endforeach; endif; else: echo "" ;endif; ?>
								</div>
							</div>
						<?php endif; ?>
						</div>
					<?php endforeach; endif; else: echo "" ;endif; endif; ?>
						
					</div>
				</div>
			<?php endforeach; endif; else: echo "" ;endif; ?>
				
			</div>
		</li>
		<li>
			<a href="/news">お知らせ</a>
		</li>
		<li>
			<a href="/shop">店舗紹介</a>
		</li>
		<li>
			<a href="/buy_way">買取方法</a>
		</li>
		<li>
			<a href="/guide">ご利用ガイド</a>
		</li>
	</ul>
</div>
		<div class="pdf">
			<div>
				<a class="pdf1" target="_blank" href="<?php echo $site['protector_consent_form']; ?>">保護者同意書(PDF)</a>
				<a class="pdf2" target="_blank" href="<?php echo $site['buy_application_form']; ?>">買取申し込み書(PDF)</a>
			</div>
		</div>
		<div class="icon_m">
			<a href="javascript:void(0)">
				<img src="/img/menu.png" alt="" />
				<img class="hide" src="/img/heclose.png" alt="" />
			</a>
		</div>
		<div class="butt">
		<?php if(empty($userInfo->id)): ?>
			<div class="butt1 login">
				<a href="javascript:void(0)">
					<img src="/img/login.png" alt="" />
					ログイン
				</a>
			</div>
			<div class="butt2 login">
				<a href="javascript:;">
					<img src="/img/cart.png" alt="" />
					カート
				</a>
			</div>
		<?php else: ?>
			<div class="butt1">
				<a href="/user">
					<img src="/img/login.png" alt="" />
					マイページ
				</a>
			</div>
			<div class="butt2">
				<a href="/user/shopping">
					<img src="/img/cart.png" alt="" />
					カート
				</a>
			</div>
		<?php endif; ?>

			
		</div>
	</div>
</div>
<div class="menu_m">
	<div class="menu_m1">
		<div class="menu_m2">
			<ul>
				<li><a href="/">ホーム</a></li>
				<li>
					<a href="javascript:;">
						商品一覧
						<img src="/img/hearrow.png" alt="" />
					</a>
				<?php if(is_array($goodsCategoryTree) || $goodsCategoryTree instanceof \think\Collection || $goodsCategoryTree instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsCategoryTree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
					<div class="menu_msub">
						<div class="menu_msub1">
							<a href="javascript:;"><?php echo $item['name']; if(!empty($item['children'])): ?>
							    <img src="/img/hearrow.png" onclick="event.preventDefault();" alt="" />
							<?php endif; ?>
							</a>
						<?php if(!empty($item['children'])): ?>
							<div class="menu_msub">
							<?php if(is_array($item['children']) || $item['children'] instanceof \think\Collection || $item['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?>
								<a href="/goods/<?php echo $item['id']; ?>/<?php echo $item2['id']; ?>">
								    <?php echo $item2['name']; if(!empty($item2['children'])): ?>
    							    <img src="/img/hearrow.png" onclick="event.preventDefault();" alt="" />
    							<?php endif; ?>
								</a>
								
    							<?php if(!empty($item2['children'])): ?>
        							<div class="menu_msub">
        							    <?php if(is_array($item2['children']) || $item2['children'] instanceof \think\Collection || $item2['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item2['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item3): $mod = ($i % 2 );++$i;?>
        							        <a href="/goods/<?php echo $item['id']; ?>/<?php echo $item2['id']; ?>/<?php echo $item3['id']; ?>"><?php echo $item3['name']; ?></a>
        							    <?php endforeach; endif; else: echo "" ;endif; ?>
        							</div>
    							<?php endif; endforeach; endif; else: echo "" ;endif; ?>
							</div>
						<?php endif; ?>
						</div>
					</div>
			    <?php endforeach; endif; else: echo "" ;endif; ?>
				</li>
				<li><a href="/news">お知らせ</a></li>
    			<li><a href="/shop">店舗紹介</a></li>
				<li><a href="/buy_way">買取方法</a></li>
				<li><a href="/guide">ご利用ガイド</a></li>
    			<li><a href="/faq">よくある質問</a></li>
				<li><a href="/contactus">お問い合わせ</a></li>
			</ul>
		</div>
		<div class="menu_m3">
		<?php if(empty($userInfo->id)): ?>
			<a href="javascript:;">
				ログイン
				<img src="/img/hearrow1.png" alt="" />
			</a>
		<?php else: ?>
		    <a href="/user">
				マイページ
				<img src="/img/hearrow1.png" alt="" />
			</a>
		<?php endif; ?>
		</div>
	</div>
</div>
		<div class="inTop">
			<div class="address mt20">
				<div class="contain address1">
					<a href="/">ホーム</a>
					&gt;
					<a href=""> マイページ</a>
				</div>
			</div>
			<div class="inTop1">
				マイページ
			</div>
		</div>
		<div class="reItemz">
		    <div class="contain reItemz1">
	<a <?php if($webController == 'User' && ($webAction == 'order' || $webAction == 'order_details')): ?> class="on" <?php endif; ?> href="/user/order">
		<img alt="" class="reImg" src="img/reImg.png">
		<img alt="" class="reImg1" src="img/reImga.png">
		予約履歴
	</a>
	<a <?php if($webController == 'User' && $webAction == 'index'): ?> class="on" <?php endif; ?> href="/user/index">
		<img alt="" class="reImg" src="img/reImg1.png">
		<img alt="" class="reImg1" src="img/reImg1a.png">
		会員登録情報
	</a>
	<a href="/logout">
		<img alt="" class="reImg" src="img/reImg2.png">
		<img alt="" class="reImg1" src="img/reImg2a.png">
		ログアウト
	</a>
</div>
		</div>
		<div class="record">
		<form name="form" id="edituser-form" class="form-vertical" method="POST" action="">
			<div class="vipMess_d">
				<div class="vipMess12">
					*個人情報をご入力の上、ご注文を行ってください。
				</div>
				<div class="vipMess">
					会員番号 <br>
					<?php echo $userInfo->id; ?> <br>
					メールアドレス <br>
					<?php echo $userInfo->email; ?>
				</div>
				<div class="vipMess">
					個人法人区分 <span>*</span>
				</div>
				<div class="vipMess1">
					<select name="persion_type" id="ptyx">
						<option value="">選択してください</option>
						<option value="1" <?php if($userInfo->persion_type == 1): ?> selected <?php endif; ?>>個人</option>
						<option value="2" <?php if($userInfo->persion_type == 2): ?> selected <?php endif; ?>>法人</option>
					</select>
				</div>
				<div class="vipMess">
					氏名 <span>*</span>
				</div>
				<div class="vipMess2">
					<input placeholder="例）中山太郎" type="text" name="name" value="<?php echo $userInfo->name; ?>">
				</div>
				<div class="vipMess">
					氏名（フリガナ）<code>（カナ）</code> <span>*</span>
				</div>
				<div class="vipMess2">
					<input placeholder="例）ナカヤマタロウ" type="text" name="katakana" value="<?php echo $userInfo->katakana; ?>">
				</div>
				<div class="vipMess">
					お電話番号 <span>*</span>
				</div>
				<div class="vipMess3">
					<input placeholder="" type="text" name="mobile" value="<?php echo $userInfo->mobile; ?>">
				</div>
				<div class="vipMess">
					郵便番号 <span>*</span>
				</div>
				<div class="vipMess3">
					<input placeholder="" type="text" name="zip_code" id="zipcode" value="<?php echo $userInfo->zip_code; ?>" >
				</div>
				<div class="vipMess">
					ご住所 <span>*</span>
				</div>
				<div class="vipMess2">
					<input type="text" name="address" value="<?php echo $userInfo->address; ?>" class="text" placeholder="">
				</div>
				<div class="vipMess">
					職業 <span>*</span>
				</div>
				<div class="vipMess1">
					<select name="occupation">
						<option value="">職業 *</option>
					<?php if(is_array($occupation) || $occupation instanceof \think\Collection || $occupation instanceof \think\Paginator): $i = 0; $__LIST__ = $occupation;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
						<option <?php if($userInfo->occupation == $item['id']): ?> selected <?php endif; ?> value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				</div>
				<div class="vipMess">
					性別 <span>*</span>
				</div>
				<div class="vipMess8">
					<select name="gender" required="">
						<option value="">指定なし</option>
						<option value="1" <?php if($userInfo->gender == 1): ?> selected <?php endif; ?>>男性</option>
						<option value="2" <?php if($userInfo->gender == 2): ?> selected <?php endif; ?>>女性</option>
					</select>
				</div>
				<div class="vipMess">
					生年月日 <span>*</span>
				</div>
				<div class="vipMess8">
					<select name="year">
					<?php $__FOR_START_274313613__=1;$__FOR_END_274313613__=50;for($i=$__FOR_START_274313613__;$i < $__FOR_END_274313613__;$i+=1){ ?>
						<option <?php if($year == date('Y', strtotime('-'.$i.' year'))): ?> selected <?php endif; ?> value="<?php echo date('Y', strtotime('-'.$i.' year')); ?>"><?php echo date('Y', strtotime('-'.$i.' year')); ?></option>
					<?php } ?>
					</select>
					<span>/</span>
					<select name="month">
					<?php $__FOR_START_1352935276__=1;$__FOR_END_1352935276__=13;for($i=$__FOR_START_1352935276__;$i < $__FOR_END_1352935276__;$i+=1){ ?>
						<option <?php if($month == $i): ?> selected <?php endif; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
					</select>
					<span>/</span>
					<select name="day">
					<?php $__FOR_START_874322121__=1;$__FOR_END_874322121__=32;for($i=$__FOR_START_874322121__;$i < $__FOR_END_874322121__;$i+=1){ ?>
						<option <?php if($days == $i): ?> selected <?php endif; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
						
					</select>
				</div>
				<div class="vipMess">
					個人書類種別 <span>*</span>
				</div>
				<div class="vipMess4">
				<?php if(is_array($szb) || $szb instanceof \think\Collection || $szb instanceof \think\Paginator): $i = 0; $__LIST__ = $szb;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
					<label for="szb<?php echo $i; ?>">
						<input id="szb<?php echo $i; ?>" <?php if($userInfo->szb == $item['id']): ?> checked <?php endif; ?> type="radio" value="<?php echo $item['id']; ?>" name="szb" />
						<?php echo $item['name']; ?>
					</label>
				<?php endforeach; endif; else: echo "" ;endif; ?>
					
				</div>
				<div class="vipMess">
					個人書類アップロード <span>*</span>
				</div>
				<!--<div class="vipMess6">-->
				
				<!--	<img id="img_src_show1" <?php if($userInfo->szb_image): ?>style="opacity:1;"<?php endif; ?> src="<?php echo $userInfo->szb_image; ?>" alt="" />-->

				<!--</div>-->
				<div class="orderLeft14">
				<?php if(!empty($userInfo->szb_image)): foreach($szb_image as $key=>$vo): ?> 
				    <span>
				        <img id="img_src_show1" style="opacity:1;" src="<?php echo $vo; ?>" alt="" />
				        <img class="newclose" alt="" src="/img/close.png">
				        <input type="hidden" name="szb_image[]" value="<?php echo $vo; ?>"/>
				    </span>
				<?php endforeach; endif; ?>
				</div>
				<div class="vipMess7">
					書類を追加する
					<input type="file" id="imgs_file"  />
					<!--<input type="hidden" name="szb_image" value="<?php echo $userInfo->szb_image; ?>">-->
				</div>
				<div class="vipMess10">
					<input type="checkbox" value="1" required="" checked="">
					<a class="qbButt1 qbButtc" href="javascript:void(0)">買取利用規約</a>
					に同意する
				</div>
				<div class="vipMess10">
					<input type="checkbox" value="2" required="" checked="">
					<a href="javascript:void(0)">プライバシーポリシー</a>
					に同意する
				</div>
				<div class="vipMess11">
					<input type="submit" class="edituser" value="登　録">
				</div>
			</div>
			</form>
		</div>
		
		<!-- 确认弹窗 -->
<!-- <div class="tips_tc">
	<div class="tips_tc1">
		告知
	</div>
	<div class="tips_tc2">
		<div class="tips_tc3">
			お申込みのキャンセルを確定されますか？
		</div>
		<div class="tips_tc4">
			<a class="tips_tc4a" href="javascript:void(0)">キャンセル</a>
			<a class="tips_tc4b" href="javascript:void(0)">確　　認</a>
		</div>
	</div>
</div> -->
<div class="footer">
			<div class="contain">
				<div class="footer1">
					<div class="footer2">
						<div class="footer3">
							<img src="/uploads/20251029/a0480341e272cdcb79c34cfcf78c3007.png" alt="" />
						</div>
						<div class="footer4">
							〒<?php echo $site['zip_code']; ?> <br />
							<?php echo $site['address']; ?> <br />
							TEL：<?php echo $site['tel']; ?>
						</div>
						<div class="footer5">
						<?php if(is_array($sns) || $sns instanceof \think\Collection || $sns instanceof \think\Paginator): $i = 0; $__LIST__ = $sns;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
							<a href="<?php echo !empty($item['url'])?$item['url']: 'javascript:;'; ?>"><img src="<?php echo $item['image']; ?>" alt="" /></a>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					</div>
					<div class="footer6 pc">
						<a href="/">ホーム</a>
						<a href="/goods">商品一覧</a>
						<a href="/news">お知らせ</a>
						<a href="/shop">店舗紹介</a>
						<a href="/buy_way">買取方法</a>
						<a href="/guide">ご利用ガイド</a>
						<a href="/faq">よくある質問</a>
						<a href="/contactus">お問い合わせ</a>
						<!-- <a href="">ご注意事項</a> -->
						<a href="/use_terms">ご利用規約</a>
						<a href="/privacy_policy">プライバシー</a>
						<a href="/trading_law">特定商取引法</a>
					</div>
					<div class="footer6 wap">
						<a href="/guide">ご利用ガイド</a>
						<a href="/shop">店舗紹介</a>
						<a href="/buy_way">買取方法</a>
						<a href="/contactus">お問い合わせ</a>
					</div>
				</div>
			</div>
		</div>
		<div class="beian">
			<div class="beian1">
				<!-- <a href="">ご注意事項</a> -->
				<a href="/use_terms">ご利用規約</a>
				<a href="/privacy_policy">プライバシー</a>
				<a href="/trading_law">特定商取引法</a>
			</div>
			<?php echo $site['copyright']; ?>
		</div>
		<div class="goTop">
			<a href="javascript:void(0)"><img src="/img/goTop.png" alt="" /></a>
		</div>
		<div class="login_tc">
			<div class="login_tc1">
			<form name="form" id="login-form" class="form-vertical" method="POST" action="">
				<div class="login_tc2">
					<div class="close">
						<img alt="" src="/img/close.png"/>
					</div>
					<div class="login_tc3">
						<img alt="" src="/img/logo.png"/>
					</div>
					<div class="login_tc4">
						ログイン
					</div>
					<div class="login_tc5">
						<div class="login_tc6">
							メールアドレス *
						</div>
						<div class="login_tc7">
							<input type="text" name="account" id="username"/>
						</div>
						<div class="login_tc6">
							パスワード *
						</div>
						<div class="login_tc7">
							<input type="password" name="password" id="password"/>
							<img alt="" class="eye" src="/img/eye.png"/>
							<img alt="" class="eye1" src="/img/eye1.png"/>
						</div>
					</div>
					<div class="login_tc8">
						<input type="submit" class="submitlogin" value="ログイン"/>
					</div>
					<div class="login_tc9" style="line-height:2;">
						<a class="forget" >パスワードをお忘れですか？</a>
					</div>
					<div class="login_tc10">
						<a class="register" >新規会員登録</a>
					</div>
					<div class="login_tc11">
						<a href="/use_terms">買取利用規約</a>
						<a href="/privacy_policy">プライバシー</a>
						<a href="/shop">店舗紹介</a>
					</div>
					<div class="login_tc12">
						 <?php echo $site['copyright']; ?>
					</div>
		        </div>
		      </form>
		    </div>
		</div>
		<div class="register_tc">
			<div class="login_tc1">
			<form name="form" id="register-form" class="form-vertical" method="POST" action="">
				<div class="login_tc2">
					<div class="close">
						<img alt="" src="/img/close.png"/>
					</div>
					<div class="login_tc3">
						<img alt="" src="/img/logo.png"/>
					</div>
					<div class="register_tc1">
						新規会員登録
					</div>
					<div class="register_tc2">
						認証コードを配信するため、メールアドレスをご入力ください。
					</div>
					<div class="login_tc5">
						<div class="login_tc6">
							メールアドレス  *
						</div>
						<div class="login_tc7">
							<input type="text" name="username" class="email" placeholder="your@email.com"/>
						</div>
						<div class="login_tc6">
							認証コード *
						</div>
						<div class="login_tc7">
							<input placeholder="認証コードを入力してください。" id="email2" name="captcha" value="" type="text"/>
							<a data-event="register" class="sendEms" href="javascript:;">配信</a>
						</div>
						<div class="login_tc6">
							パスワード *
						</div>
						<div class="login_tc7">
							<input type="password" name="password" id="zpass1"/>
							<img alt="" class="eye" src="/img/eye.png"/>
							<img alt="" class="eye1" src="/img/eye1.png"/>
						</div>
						<div class="login_tc6">
							パスワードを再入力 *
						</div>
						<div class="login_tc7">
							<input type="password" name="repassword" id="zpass2"/>
							<img alt="" class="eye" src="/img/eye.png"/>
							<img alt="" class="eye1" src="/img/eye1.png"/>
						</div>
					</div>
					<div class="login_tc8">
						<input type="submit" class="submitregister" value="新規会員登録"/>
					</div>
					<div class="login_tc11">
						<a href="/use_terms">買取利用規約</a>
						<a href="/privacy_policy">プライバシーポリシー</a>
						<a href="">店舗紹介</a>
					</div>
					<div class="login_tc12">
						 <?php echo $site['copyright']; ?>
					</div>
		        </div>
		       </form>
		    </div>
		</div>
		<div class="forget_tc">
			<div class="login_tc1">
			<form name="form" id="resetpwd-form" class="form-vertical" method="POST" action="">
				<div class="login_tc2">
					<div class="close">
						<img alt="" src="/img/close.png"/>
					</div>
					<div class="login_tc3">
						<img alt="" src="/img/logo.png"/>
					</div>
					<div class="register_tc1">
						パスワード再設定
					</div>
					<div class="register_tc2">
						認証コードを配信するため、登録されたメールアドレスをご入力ください。
					</div>
					<div class="login_tc5">
						<div class="login_tc6">
							メールアドレス *
						</div>
						<div class="login_tc7">
							<input type="text" name="username" class="email" placeholder="your@email.com"/>
						</div>
						<div class="login_tc6">
							認証コード *
						</div>
						<div class="login_tc7">
							<input placeholder="認証コードを入力してください。" type="text" name="captcha" id="zhcode" />
							<a data-event="resetpwd" class="sendEms" href="javascript:;">配信</a>
						</div>
						<div class="login_tc6">
							パスワード *
						</div>
						<div class="login_tc7">
							<input type="password" name="password" id="zhpass1"/>
							<img alt="" class="eye" src="/img/eye.png"/>
							<img alt="" class="eye1" src="/img/eye1.png"/>
						</div>
						<div class="login_tc6">
							パスワードを再入力 *
						</div>
						<div class="login_tc7">
							<input type="password" name="repassword" id="zhpass2">
							<img alt="" class="eye" src="/img/eye.png"/>
							<img alt="" class="eye1" src="/img/eye1.png"/>
						</div>
					</div>
					<div class="login_tc8">
						<input type="submit" class="submitresetpwd" value="パスワード変更" />
					</div>
					<div class="login_tc11">
						<a href="/use_terms">買取利用規約</a>
						<a href="/privacy_policy">プライバシー</a>
						<a href="/shop">店舗紹介</a>
					</div>
					<div class="login_tc12">
						<?php echo $site['copyright']; ?>
					</div>
		        </div>
		      </form>
		    </div>
		</div>
		<!-- 公共弹窗 -->
		<div class="tips_tc">
			<div class="tips_tc1">
				
			</div>
			<div class="tips_tc2">
				<div class="tips_tc3">
					お申込みをキャンセルしました。
				</div>
				<div class="tips_tc4 querenbox" style="display:none;">
					<a href="javascript:;" class="tips_tc4a yesCancel">はい</a>
					<a href="javascript:;" class="tips_tc4b noCancel">いいえ</a>
				</div>
			</div>
		</div>
		
	</body>
</html>
