<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//判断是否启用登陆状态异步显示扩展
if(class_exists('XH_Social_Add_On_Login_Status_Async_Load')){
    $api=XH_Social_Add_On_Login_Status_Async_Load::instance();
    //判断是否启用
    if($api->get_option('enable')=='yes'){
        ?>
        <a style="display: none;" id="xh_social_login_btn" href="<?php echo wp_login_url(XH_Social_Helper_Uri::get_location_uri());?>">登录</a>
        <a style="display: none;" id="xh_social_login_user_profile" href="#" title="用户">用户头像</a>
        <a style="display: none;" id="xh_social_logout_btn" href="<?php echo wp_logout_url(XH_Social_Helper_Uri::get_location_uri())?>">退出</a>
        <script type="text/javascript">
            (function ($,win) {
                win.xh_social_login_status_init=function () {
                    $.ajax({
                        url: '<?php echo XH_Social::instance()->ajax_url(array('action'=>"xh_social_{$api->id}",'tab'=>'check'),true,true)?>',
                        type: 'post',
                        timeout: 60 * 1000,
                        async: true,
                        cache: false,
                        data: null,
                        dataType: 'json',
                        success: function(res) {
                            let mark=(res.code==='00000'?'success':'error');
                            win.set_xh_social_login_status_html(mark,res.data);
                        },
                        error:function(e){
                            win.set_xh_social_login_status_html();
                            console.error(e.responseText);
                        }
                    });
                };

                win.set_xh_social_login_status_html=function (mark,data) {
                    if(mark==='success'){
                        $('#xh_social_login_btn').hide();
                        $('#xh_social_login_user_profile').attr('href',data.profile_url);
                        $('#xh_social_login_user_profile').attr('title',data.display_name);
                        $('#xh_social_login_user_profile').html(data.avatar);
                        $('#xh_social_login_user_profile').show();
                        $('#xh_social_logout_btn').show();
                    }else {
                        $('#xh_social_login_btn').show();
                        $('#xh_social_login_user_profile').hide();
                        $('#xh_social_logout_btn').hide();
                    }
                };

                win.xh_social_login_status_init();
            })(jQuery,window);
        </script>
        <?php
        return;
    }
}

//没有启用登陆状态异步显示扩展
if(!is_user_logged_in()){
    $loginActive = XH_Social::instance()->get_available_addon('add_ons_login');
    ?>
     <a <?php echo $loginActive? 'onclick="window.wsocial_dialog_login_show();"':'';?> href="<?php echo $loginActive?'javascript:void(0);':wp_login_url(XH_Social_Helper_Uri::get_location_uri())?>">登录</a>
    <?php
}else{
    global $current_user;
    ?>
    <!-- <a href="<?php echo esc_url(get_edit_profile_url());/*用户中心链接*/ ?>" title="<?php echo esc_attr($current_user->display_name)?>">
     <?php  echo get_avatar(get_current_user_id(),35,'','',array(
                'class'=>'xh-Avatar'
            ));?>
     </a> -->
     <a href="<?php echo wp_logout_url(XH_Social_Helper_Uri::get_location_uri())?>">退出</a>
    <?php
}
?>
