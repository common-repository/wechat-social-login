<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class XH_Social_Add_On_Login_Status_Async_Load extends Abstract_XH_Social_Add_Ons{
    private static $_instance = null;

    public $dir;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct(){
        $this->id='add_ons_login_status_async_load';
        $this->title='登录状态异步显示';
        $this->description='异步加载登录状态样式。';
        $this->version='1.0.0';
        $this->min_core_version = '1.0.0';
        $this->author=__('xunhuweb',XH_SOCIAL);
        $this->author_uri='https://www.wpweixin.net';
        $this->setting_uri = admin_url('admin.php?page=social_page_default&section=menu_default_other&sub=add_ons_login_status_async_load');
        $this->dir= rtrim ( trailingslashit( dirname( __FILE__ ) ), '/' );

        $this->init_form_fields();
    }

    public function on_load(){
        add_filter('xh_social_ajax', array($this,'ajax'),10,1);
        add_filter("xh_social_admin_menu_menu_default_other",array($this,'register_menus'),10,1);
    }

    //注册管理菜单
    public function register_menus($menus){
        $menus []=$this;
        return $menus;
    }

    public function init_form_fields(){
        $this->form_fields=array(
            'enable'=>[
                'title'=>'是否启用',
                'type'=>'checkbox',
                'label'=>'启用',
                'default'=>'yes'
            ]
        );
    }

    //注册异步ajax
    public function ajax($shortcodes){
        $shortcodes["xh_social_{$this->id}"]=array($this,'do_ajax');
        return $shortcodes;
    }

    public function do_ajax(){
        $action ="xh_social_{$this->id}";
        $datas=shortcode_atts(array(
            'notice_str'=>null,
            'action'=>$action,
            $action=>null,
            'tab'=>null
        ), stripslashes_deep($_REQUEST));
        if(!XH_Social::instance()->WP->ajax_validate($datas,isset($_REQUEST['hash'])?$_REQUEST['hash']:null,true)){
            $this->output(['code'=>'99999', 'msg'=>'非法访问']);
        }
        switch ($datas['tab']){
            case 'check':
                global $current_user;
                if($current_user->ID){
                   $this->output(['data'=>[
                       'profile_url'=>get_edit_profile_url(),
                       'display_name'=>$current_user->display_name,
                       'avatar'=>get_avatar($current_user->ID,35,'','',['class'=>'xh-Avatar'])
                   ]]);
                }
                $this->output(['code'=>'99999','msg'=>'未登录...']);
        }
    }

    //输出
    private function output($args=[]){
        $data=['code'=>'00000', 'msg'=>'操作成功', 'data'=>null];
        $data=array_merge($data,$args);
        echo json_encode($data);
        exit;
    }


}

return XH_Social_Add_On_Login_Status_Async_Load::instance();
?>