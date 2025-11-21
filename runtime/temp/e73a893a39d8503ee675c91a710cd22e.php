<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:83:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/user/register.html";i:1763545360;s:84:"/home/xs942548/mobile-zone.jp/public_html/application/index/view/common/captcha.html";i:1763545360;}*/ ?>
<div id="content-container" class="container">
    <div class="user-section login-section">
        <div class="logon-tab clearfix"><a href="<?php echo url('user/login'); ?>?url=<?php echo htmlentities(urlencode($url ?? '') ?? ''); ?>"><?php echo __('Sign in'); ?></a> <a class="active"><?php echo __('Sign up'); ?></a></div>
        <div class="login-main">
            <form name="form1" id="register-form" class="form-vertical" method="POST" action="">
                <!--@IndexRegisterFormBegin-->
                <input type="hidden" name="invite_user_id" value="0"/>
                <input type="hidden" name="url" value="<?php echo htmlentities($url ?? ''); ?>"/>
                <?php echo token(); ?>
                <div class="form-group">
                    <label class="control-label required"><?php echo __('Email'); ?><span class="text-success"></span></label>
                    <div class="controls">
                        <input type="text" name="email" id="email" data-rule="required;email" class="form-control" placeholder="<?php echo __('Email'); ?>">
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo __('Username'); ?></label>
                    <div class="controls">
                        <input type="text" id="username" name="username" data-rule="required;username" class="form-control" placeholder="<?php echo __('Username must be 3 to 30 characters'); ?>">
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo __('Password'); ?></label>
                    <div class="controls">
                        <input type="password" id="password" name="password" data-rule="required;password" class="form-control" placeholder="<?php echo __('Password must be 6 to 30 characters'); ?>">
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo __('Mobile'); ?></label>
                    <div class="controls">
                        <input type="text" id="mobile" name="mobile" data-rule="required;mobile" class="form-control" placeholder="<?php echo __('Mobile'); ?>">
                        <p class="help-block"></p>
                    </div>
                </div>

                <!--@CaptchaBegin-->
                <?php if($captchaType): ?>
                <div class="form-group">
                    <label class="control-label"><?php echo __('Captcha'); ?></label>
                    <div class="controls">
                        <div class="input-group">
                            <!--@formatter:off-->
<?php if("text" == 'email'): ?>
    <input type="text" name="captcha" class="form-control" data-rule="required;length(<?php echo \think\Config::get('captcha.length'); ?>);digits;remote(<?php echo url('api/validate/check_ems_correct'); ?>, event=register, email:#email)" />
    <span class="input-group-btn" style="padding:0;border:none;">
        <a href="javascript:;" class="btn btn-info btn-captcha" data-url="<?php echo url('api/ems/send'); ?>" data-type="email" data-event="register">发送验证码</a>
    </span>
<?php elseif("text" == 'mobile'): ?>
    <input type="text" name="captcha" class="form-control" data-rule="required;length(<?php echo \think\Config::get('captcha.length'); ?>);digits;remote(<?php echo url('api/validate/check_sms_correct'); ?>, event=register, mobile:#mobile)" />
    <span class="input-group-btn" style="padding:0;border:none;">
        <a href="javascript:;" class="btn btn-info btn-captcha" data-url="<?php echo url('api/sms/send'); ?>" data-type="mobile" data-event="register">发送验证码</a>
    </span>
<?php elseif("text" == 'wechat'): if(get_addon_info('wechat')): ?>
        <input type="text" name="captcha" class="form-control" data-rule="required;length(<?php echo \think\Config::get('captcha.length'); ?>);remote(<?php echo addon_url('wechat/captcha/check'); ?>, event=register)" />
        <span class="input-group-btn" style="padding:0;border:none;">
            <a href="javascript:;" class="btn btn-info btn-captcha" data-url="<?php echo addon_url('wechat/captcha/send'); ?>" data-type="wechat" data-event="register">获取验证码</a>
        </span>
    <?php else: ?>
        请在后台插件管理中安装《微信管理插件》
    <?php endif; elseif("text" == 'text'): ?>
    <input type="text" name="captcha" class="form-control" data-rule="required;length(<?php echo \think\Config::get('captcha.length'); ?>)" />
    <span class="input-group-btn" style="padding:0;border:none;">
        <img src="<?php echo captcha_src(); ?>" width="100" height="32" onclick="this.src = '<?php echo captcha_src(); ?>?r=' + Math.random();"/>
    </span>
<?php endif; ?>
<!--@formatter:on-->

                        </div>
                        <p class="help-block"></p>
                    </div>
                </div>
                <?php endif; ?>
                <!--@CaptchaEnd-->

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block"><?php echo __('Sign up'); ?></button>
                    <a href="<?php echo url('user/login'); ?>?url=<?php echo htmlentities(urlencode($url ?? '') ?? ''); ?>" class="btn btn-default btn-lg btn-block mt-3 no-border"><?php echo __('Already have an account? Sign in'); ?></a>
                </div>
                <!--@IndexRegisterFormEnd-->
            </form>
        </div>
    </div>
</div>
