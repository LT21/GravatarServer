<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 提供多个可选择的Gravatar头像服务器，同时提供默认头像设置。
 * 
 * @package Gravatar Server
 * @author LT21
 * @version 1.1.0
 * @link http://lt21.me
 */
class GravatarServer_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Comments')->gravatar = array('GravatarServer_Plugin', 'render');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 服务器 **/
        $server = new Typecho_Widget_Helper_Form_Element_Radio( 'server',  array(
                'http://cn.gravatar.com'        =>  'Gravatar CN （ http://cn.gravatar.com ）',
                'http://0.gravatar.com'         =>  'Gravatar 0 （ http://0.gravatar.com ）',
                'http://1.gravatar.com'         =>  'Gravatar 1 （ http://1.gravatar.com ）',
                'http://2.gravatar.com'         =>  'Gravatar 2 （ http://2.gravatar.com ）',
                'http://3.gravatar.com'         =>  'Gravatar 3 （ http://3.gravatar.com ）',
                'https://secure.gravatar.com'   =>  'Gravatar Secure （ https://secure.gravatar.com ）',
                'http://gravatar.duoshuo.com'   =>  '多说 Gravatar 镜像 （ http://gravatar.duoshuo.com ）'),
            'http://cn.gravatar.com', _t('选择服务器'), _t('替换Typecho使用的Gravatar头像服务器（ www.gravatar.com ）') );
        $form->addInput($server->multiMode());

        /** 默认头像 **/
        $default = new Typecho_Widget_Helper_Form_Element_Radio( 'default',  array(
                'mm'            =>  '<img src=http://cn.gravatar.com/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=mm height="32" width="32" /> 神秘人物',
                'blank'         =>  '<img src=http://cn.gravatar.com/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=blank height="32" width="32" /> 空白',
                ''				=>  '<img src=http://cn.gravatar.com/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d= height="32" width="32" /> Gravatar 标志',
                'identicon'     =>  '<img src=http://cn.gravatar.com/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=identicon height="32" width="32" /> 抽象图形（自动生成）',
                'wavatar'       =>  '<img src=http://cn.gravatar.com/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=wavatar height="32" width="32" /> Wavatar（自动生成）',
                'monsterid'     =>  '<img src=http://cn.gravatar.com/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=monsterid height="32" width="32" /> 小怪物（自动生成）'),
            'mm', _t('选择默认头像'), _t('当评论者没有设置Gravatar头像时默认显示该头像') );
        $form->addInput($default->multiMode());
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render($size, $rating, $default, $comments)
    {
        $default = Typecho_Widget::widget('Widget_Options')->plugin('GravatarServer')->default;
        $url = self::gravatarUrl($comments->mail, $size, $rating, $default, $comments->request->isSecure());
        echo '<img class="avatar" src="' . $url . '" alt="' . $comments->author . '" width="' . $size . '" height="' . $size . '" />';
    }

    /**
     * 获取gravatar头像地址 
     * 
     * @param string $mail 
     * @param int $size 
     * @param string $rating 
     * @param string $default 
     * @param bool $isSecure 
     * @return string
     */
    public static function gravatarUrl($mail, $size, $rating, $default, $isSecure = false)
    {
        $url = $isSecure ? 'https://secure.gravatar.com' : Typecho_Widget::widget('Widget_Options')->plugin('GravatarServer')->server;
        $url .= '/avatar/';

        if (!empty($mail)) {
            $url .= md5(strtolower(trim($mail)));
        }

        $url .= '?s=' . $size;
        $url .= '&amp;r=' . $rating;
        $url .= '&amp;d=' . $default;

        return $url;
    }
}
