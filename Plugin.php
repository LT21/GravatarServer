<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 提供多个可选择的服务器，用以替换Typecho所使用的Gravatar官方服务器（www.gravatar.com）。避免在Gravatar官方服务器被墙后，评论中用户头像无法加载。
 * 
 * @package Gravatar Server
 * @author LT21
 * @version 1.0.0
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
        /** 分类名称 */
        $server = new Typecho_Widget_Helper_Form_Element_Radio( 'server',  array(
                'http://cn.gravatar.com'        =>  'Gravatar CN （ http://cn.gravatar.com ）',
                'http://0.gravatar.com'         =>  'Gravatar 0 （ http://0.gravatar.com ）',
                'http://1.gravatar.com'         =>  'Gravatar 1 （ http://1.gravatar.com ）',
                'http://2.gravatar.com'         =>  'Gravatar 2 （ http://2.gravatar.com ）',
                'http://3.gravatar.com'         =>  'Gravatar 3 （ http://3.gravatar.com ）',
                'https://secure.gravatar.com'   =>  'Gravatar Secure （ https://secure.gravatar.com ）',
                'http://gravatar.duoshuo.com'   =>  '多说 Gravatar 镜像 （ http://gravatar.duoshuo.com ）'),
            'http://cn.gravatar.com', _t('选择服务器'), _t('选择服务器替换原Gravatar头像服务器') );
        $form->addInput($server->multiMode());
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
