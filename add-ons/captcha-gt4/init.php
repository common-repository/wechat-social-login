<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once 'class-xh-social-captcha-gt4.php';

/**
 * 极验验证码
 * @author ranj
 * @since 1.0.0
 */
class XH_Social_Add_On_Captcha_GT4 extends Abstract_XH_Social_Add_Ons{
    /**
     * The single instance of the class.
     *
     * @since 1.0.0
     * @var XH_Social_Add_On_Login
     */
    private static $_instance = null;
    
    /**
     * 当前插件目录
     * @var string
     * @since 1.0.0
     */
    public $dir;
    
    /**
     * Main Social Instance.
     *
     * @since 1.0.0
     * @static
     * @return XH_Social_Add_On_Login
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    private function __construct(){
        $this->id='add_ons_captcha_gt4';
        $this->title='拖动验证码 - 极验GEETEST';
        $this->description='全新第四代行为式验证码。';
        $this->version='1.0.0';
        $this->min_core_version = '1.0.0';
        $this->author=__('xunhuweb',XH_SOCIAL);
        $this->author_uri='https://www.wpweixin.net';
        $this->setting_uri = admin_url('admin.php?page=social_page_default&section=menu_default_other&sub=add_ons_captcha_gt4');
        $this->dir= rtrim ( trailingslashit( dirname( __FILE__ ) ), '/' );
    
        //基础配置
        add_filter("xh_social_admin_menu_menu_default_other",array($this,'register_menus'),10,3);
        $this->init_form_fields();
        //生成拖动验证码的下拉选项
        add_filter('wsocil_captcha',array($this,'create_captcha'),10,1);
    }
    public function register_menus($menus){
        $menus []=$this;
        return $menus;
    }
    public function init_form_fields(){
        $this->form_fields=array(
            'captcha_id'=>[
                'title'=>'验证 ID',
                'type'=>'text',
                'description'=>'验证 ID申请地址:<a href="https://www.geetest.com/adaptive-captcha" target="_blank">极验GEETEST</a>'
            ],
            'captcha_key'=>[
                'title'=>'验证 Key',
                'type'=>'text',
            ]
        );
    }
    public function create_captcha($captcha){
        $captcha[]=new WSocial_Captcha_Gt4();
        return $captcha;
    }
}
return XH_Social_Add_On_Captcha_GT4::instance();
?>