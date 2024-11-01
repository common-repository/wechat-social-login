<?php

class WSocial_Captcha_Gt4 extends Abstract_WSocial_Captcha{
    public $title="极验行为验4.0";
    public $id = 'captcha_gt4';

    public function clear_captcha(){}
    //生成表单验证码字段
    public function create_captcha_form($form_id, $data_name, $settings){
        $html_name = $data_name;
        $html_id = isset($settings['id']) ? $settings['id'] : ($form_id . "_" . $data_name);
        $html = '';
        if(!defined('xunhuweb_captcha_gt4')){
            define('xunhuweb_captcha_gt4', 1);
            ob_start();
            ?>
            <script src="http://static.geetest.com/v4/gt4.js"></script>
            <?php 
            $html = ob_get_clean();
        }
        $appid = XH_Social_Add_On_Captcha_GT4::instance()->get_option('captcha_id');
        ob_start();
        ?>
        <div class="xh-form-group">
            <div id="captcha">
            </div>
            <input type="hidden" name="<?php echo esc_attr($html_name); ?>" id="<?php echo esc_attr($html_id); ?>" />
        </div>
        <script type="text/javascript">
            (function ($) {
                initGeetest4({
                    captchaId: '<?php echo esc_attr($appid)?>',
                    product: 'popup'
                }, function (gt) {
                    window.gt = gt
                    gt.appendTo("#captcha")
                        .onSuccess(function (e) {
                            var result = gt.getValidate();
                            $('#<?php echo esc_attr($html_id); ?>').val(JSON.stringify(result));
                        })
                });
                $(document).bind('wsocial_action_after',function(e,callback){
                    if(callback.data.errcode==100210){
                    	window.gt.reset();
                    	callback.data.done = true;
                    }
                });
            })(jQuery);
        </script>
        <?php 
        XH_Social_Helper_Html_Form::generate_field_scripts($form_id, $html_name, $html_id);
        return $html.ob_get_clean();
    }
    //验证
    public function validate_captcha($name, $datas, $settings){
        //插件未启用，那么不验证
        $data = isset($_POST[$name]) ? trim($_POST[$name]) : '';
        if (empty($data)) {
            return new XH_Social_Error(100210,'请点击验证条，完成验证');
        }
        $data=json_decode(stripslashes($data),true);
        unset($data['captcha_id']);
        $captcha_id = XH_Social_Add_On_Captcha_GT4::instance()->get_option('captcha_id');
        $captcha_key = XH_Social_Add_On_Captcha_GT4::instance()->get_option('captcha_key');
        $api_server = "http://gcaptcha4.geetest.com";
        $sign_token = hash_hmac('sha256', $data['lot_number'], $captcha_key);
        $data['sign_token'] = $sign_token;
        $url = sprintf($api_server . "/validate" . "?captcha_id=%s", $captcha_id);
        $data = http_build_query($data);
        $options    = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded",
                'content' => $data,
                'timeout' => 5
            )
        );
        $context = stream_context_create($options);
        $result    = file_get_contents($url, false, $context);
        preg_match('/([0-9])\d+/',$http_response_header[0],$matches);
        $responsecode = intval($matches[0]);
        if($responsecode != 200){
            return new XH_Social_Error(100210,'验证失败,请重新点击验证!');
        }
        return $datas;
    }
}