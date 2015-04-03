<?php
/**
 * Plugin Name: WooCommerce WooCart Pro
 * Plugin URI: http://festi.io/app/woocomerce-festicart-plugin/
 * Description:  WooCart Pro is powerful plugin for create and modify cart for your online shop. We try create flexible solution for you.
 * Version: 1.33
 * Author: Festi 
 * Author URI: http://festi.io/
 * License: You should have purchased a license from http://codecanyon.net/item/woocommerce-woocart-pro/7992078?ref=Festi_io
 * Copyright 2014  Festi  http://festi.io/
 */
 
if (!class_exists('FestiPlugin')) {
    require_once dirname(__FILE__).'/common/FestiPlugin.php';
}

if (!class_exists('FestiPluginChild')) {
    require_once dirname(__FILE__).'/common/FestiPluginChild.php';
}

class WooCartFestiPlugin extends FestiPluginChild
{
    protected $_version = '1.33';
    protected $_languageDomain = 'festi_cart';
    protected $_optionsPrefix = 'festi_cart_';   
    protected $_currentIconFolder = 'user';
    protected $_defaultIconSize = 20;
     
    protected function onInit()
    {
        $this->addActionListener('plugins_loaded', 'onLanguagesInitAction');

        if ($this->_isWoocommercePluginNotActiveWhenFestiCartPluginActive()) {
            $this->addActionListener(
                'admin_notices',
                'onDisplayInfoAboutDisabledWoocommerceAction' 
            );
            
            return false;
        }
        
        parent::onInit();
        
        $this->addActionListener('widgets_init', 'onWidgetInitAction');
    } // end onInit
    
    private function _isWoocommercePluginNotActiveWhenFestiCartPluginActive()
    {
        return $this->_isFestiCartPluginActive()
               && !$this->_isWoocommercePluginActive();
    } // end _isWoocommercePluginNotActiveWhenFestiCartPluginActive
    
    public function onInstall()
    {
        if (!$this->_isWoocommercePluginActive()) {
            $message = 'WooCommerce not active or not installed.';
            $this->displayError($message);
            exit();
        } 
         
        if (!$this->_isInstalationGD()) {
            $message = 'It looks like GD is not installed.';
            $this->displayError($message);
            exit();
        }
        
        $plugin = $this->onBackendInit();
        
        $plugin->onInstall();
    } // end onInstall
    
    public function onUninstall()
    {
        $plugin = $this->onBackendInit();
        
        $plugin->onUninstall();
    } // end onUnistall
    
    public function onBackendInit()
    {
        require_once $this->_pluginPath.'common/WooCartBackendFestiPlugin.php';
        $backend = new WooCartBackendFestiPlugin(__FILE__);
        return $backend;
    } // end onBackendInit
    
    protected function onFrontendInit()
    {
        require_once $this->_pluginPath.'common/WooCartFrontendFestiPlugin.php';
        $frontend = new WooCartFrontendFestiPlugin(__FILE__);
        return $frontend;
    } // end onFrontendIn
    
    public function onWidgetInitAction($action = '')
    {
        require_once $this->_pluginPath.'common/WooCartFestiWidget.php';
        if ($action) {
            $widget = new WooCartFestiWidget();
            return $widget;
        }
        register_widget('WooCartFestiWidget');
    } // end onWidgetInit
    
    public function onLanguagesInitAction()
    {
        load_plugin_textdomain(
            $this->_languageDomain,
            false,
            $this->_pluginLanguagesPath
        );
    } // end onLanguagesInitAction
    
    private function _isFestiCartPluginActive()
    {        
        return $this->isPluginActive('woocommerce-woocartpro/plugin.php'); 
    } // end _isFestiCartPluginActive
    
    private function _isWoocommercePluginActive()
    {        
        return $this->isPluginActive('woocommerce/woocommerce.php');
    } // end _isWoocommercePluginActive
    
    public function &getWoocommerceInstance()
    {
        return $GLOBALS['woocommerce'];
    } // end getWoocommerceInstance
    
    private function _isInstalationGD()
    {
        return (extension_loaded('gd') && function_exists('gd_info'));
    } // end _isInstalationGD
    
    public function getPluginIconsPath($dirname = '')
    {
        return $this->getPluginImagesPath('icons/'.$dirname);
    } // end getPluginIconsPath
    
    public function getPluginIconsUrl($dirname = '', $file)
    {
        return $this->getPluginImagesUrl('icons/'.$dirname.'/'.$file);
    } // end getPluginIconsUrl
    
    public function onDisplayInfoAboutDisabledWoocommerceAction()
    {        
        $message = 'WooCommerce WooCart Pro: ';
        $message .= 'WooCommerce not active or not installed.';
        $this->displayError($message);
    } //end onDisplayInfoAboutDisabledWoocommerceAction
    
    public function convertHexToRgb($hex)
    {
        $hex = str_replace("#", "", $hex);
  
        if (strlen($hex) == 3) {
           
              $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            
              $g = hexdec(substr($hex,1,1).substr($hex,1,1));
              
              $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
              $r = hexdec(substr($hex,0,2));
            
              $g = hexdec(substr($hex,2,2));
              
              $b = hexdec(substr($hex,4,2));
        }
        
        $rgb = array($r, $g, $b);
      
        return $rgb;
    } // end _convertHexToRgb
    
    protected function getFileNamesListOfCustomizeCart()
    {
        $list = array(
            'cart_customize_style',
            'dropdown_list_customize_style',
            'widget_customize_style',
            'popup_customize_style'
        );
        
        return $list;
    } // end getFileNamesListOfCustomizeCart
    
    protected function getCustomCssPath($fileName = '')
    {
        return $this->getPluginCssPath('frontend/customize/'.$fileName);
    } //end getCustomCssPath
}

$GLOBALS['wooCommerceFestiCart'] = new WooCartFestiPlugin(__FILE__);
