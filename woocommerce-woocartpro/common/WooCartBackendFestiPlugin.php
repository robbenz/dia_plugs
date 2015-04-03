<?php

class WooCartBackendFestiPlugin extends WooCartFestiPlugin
{
    protected $_menuOptions = array(
        'settings' => "Settings",
        'importExport' => "Import & Export",
        'help'   => "Help",
    );
    
    protected $_iconRgbColor = array(0, 0, 0);
    
    protected $_defaultMenuOption = 'settings';
    
    protected $_fileSystem = '';
    
    protected function onInit()
    {
        $this->addActionListener('admin_menu', 'onAdminMenuAction');
    } // end onInit
    
    public function _onFileSystemInstanceAction()
    {
        $this->_fileSystem = $this->getFileSystemInstance();
    } // end _onFileSystemInstanceAction
    
    public function onInstall($refresh = false)
    {        
        if (!$this->_fileSystem) {
            $this->_fileSystem = $this->getFileSystemInstance();
        }
        
        if ($this->_hasPermissionToCreateCacheFolder()) {
            $this->_fileSystem->mkdir($this->_pluginCachePath, 0777);
        }
        
        $customCssPath = $this->getCustomCssPath();

        if ($this->_hasPermissionToCreateCustomCssFolder($customCssPath)) {
            $this->_fileSystem->mkdir($customCssPath, 0777);
        }
        
        $this->installUserIcons('user/');

        $this->installUserIcons('user/on_hover/');
        
        if ($refresh) {
            return true;
        }
                
        $this->_doInitDefaultSettings();
        $widget = $this->onWidgetInitAction('instal');
        $this->_doInitDefaultOptions('widget_options', $widget);
        
        $this->_updateCookieCacheFile();       
    } // end onInstal
    
    private function _doInitDefaultSettings()
    {
        $this->_doInitDefaultOptions('settings');
        
        $file = $this->_pluginStaticPath.'default_options/settings.txt';

        if (file_exists($file)) {
            $content = $this->_fileSystem->get_contents($file);
            $this->doImportSettingsFromJson($content);
        }
    } // end doInitDefaultOptionsSettings
    
    public function installUserIcons($folder)
    {
        $userIconsPath = $this->getPluginIconsPath($folder);

        if ($this->_hasPermissionToCreateUserIconsFolder($userIconsPath)) {
            $result = $this->_fileSystem->mkdir($userIconsPath, 0777);
        }
        
        if ($this->_fileSystem->is_writable($userIconsPath)) {
            try {
                $this->_doInitCopyDefaultIconsToUserDir($folder);
            } catch (Exception $e) {
                echo $e->getMessage();
                exit();
            }
        }
    } // end installUserIcons
    
    private function _hasPermissionToCreateCacheFolder()
    {
        return ($this->_fileSystem->is_writable($this->_pluginPath)
               && !file_exists($this->_pluginCachePath));

    } // end _hasPermissionToCreateFolder
    
    private function _hasPermissionToCreateCustomCssFolder($customCssPath)
    {
        $frontendCssPath = $file = $this->getPluginCssPath('frontend/');
        
        return ($this->_fileSystem->is_writable($frontendCssPath)
               && !file_exists($customCssPath));
    } // end _hasPermissionToCreateCustomCssFolder

    private function _hasPermissionToCreateUserIconsFolder($userIconsPath)
    {
        $iconsPath = $this->getPluginIconsPath();
        
        return ($this->_fileSystem->is_writable($iconsPath)
               && !$this->_fileSystem->exists($userIconsPath));

    } // end _hasPermissionToCreateUserIconsFolder
    
    public function onUninstall($refresh = false)
    {
        delete_option('festi_cart_settings');
        delete_option('festi_cart_widget_options');
        delete_option('festi_cart_coockie');
    } // end onUninstall
    
    private function _doInitCopyDefaultIconsToUserDir($folder)
    {
        $iconPath = $this->getPluginIconsPath('default/');
        
        $files = $this->_getListFilesInDirectory($iconPath);
   
        if (!$files) {
            $message = __(
                "The catalog is not detected files that come bundled ". 
                "with the plugin.",
                $this->_languageDomain
            );
            $message .= PHP_EOL;
            $message .= __("Directory: ", $this->_languageDomain);
            $message .= $dirPath;
            
            throw new Exception($message);
        }
        
        $newIconPath = $this->getPluginIconsPath($folder);
        
        foreach ($files as $value) {
             $vars = array(
                'defaultIconPath' => $iconPath.$value,
                'userIconPath' => $newIconPath.$value
            ); 
            
            $this->doUpdateIconSize($vars);
        }
    } //end _doInitCopyDefaultIconsToUserDir
    
    public function doUpdateIconSize($vars, $colors = array(), $customType = '')
    {
        if ($vars) {
           extract($vars); 
        }
        
        list($width, $height, $mime) = getimagesize($defaultIconPath);
        
        $imageType = $this->_getImageType($mime);
        
        $methodName = 'imagecreatefrom'.$imageType;
        
        $img = $methodName($defaultIconPath);

        if ($colors) {
            $img = $this->_changeColorOfIcons($img, $colors);
        }
        
        if ($this->_isUploadCustomIconWithIconSize($customType)) {
            $size = array(
                'width'  => $_POST['customIconWidth'],
                'height' => $_POST['customIconHeight']
            );
        } else{
            $size = array(
                'width'  => $this->_defaultIconSize,
                'height' => $this->_defaultIconSize
            );
            
            $_POST['customIconWidth']  = $this->_defaultIconSize;
            $_POST['customIconHeight'] = $this->_defaultIconSize;
        }

        $newImage = $this->_doCreateNewImageWithUserSize($size);
        
        $vars = $size;
        $vars['originalWidth']  = $width;
        $vars['originalHeight'] = $height;
        $vars['originalImage']  = $img;
        $vars['newImage']       = $newImage;

        $newImage = $this->_doCopyOriginalImageToNewImage($vars);

        imagepng($newImage, $userIconPath);
    } //end doUpdateIconSize
    
    
    private function _changeColorOfIcons($img, $colors)
    {
        if ($colors) {
            extract($colors);
        }

        $repaintIndexColor = imagecolorclosestalpha(
            $img,
            $fromRgb[0],
            $fromRgb[0],
            $fromRgb[0],
            0
        );
              
        imagecolorset(
            $img, 
            $repaintIndexColor, 
            $toRgb[0], 
            $toRgb[1],  
            $toRgb[2]
        );
        
        return $img;
    } // end _changeColorOfIcons
    
    private function _getImageType($mime)
    {
        $imageType = image_type_to_mime_type($mime);
        
        $imageType = str_replace('image/', '', $imageType);
        
        return $imageType;
    } // end _getImageType
    
    private function _isUploadCustomIconWithIconSize($customType)
    {
        return $this->isUploadCustomIcon($customType)
               && $this->_hasCustomIconSizeInRequest();
    } // end _isUploadCustomIconWithIconSize
    
    private function _hasCustomIconSizeInRequest()
    {
        return array_key_exists('customIconWidth', $_POST) 
               && !empty($_POST['customIconWidth'])
               && array_key_exists('customIconHeight', $_POST) 
               && !empty($_POST['customIconHeight']);
    } // end _hasCustomIconSizeInRequest
    
    public function isUploadCustomIcon($type)
    {
        return array_key_exists($type, $_FILES) 
               && is_uploaded_file($_FILES[$type]['tmp_name']);
    } // end isUploadCustomIcon
    
    private function _doCreateNewImageWithUserSize($size = array())
    {
        if ($size) {
            extract($size);
        }
        
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        
        return $image;
    } // end _doCreateNewImageWithUserSize
    
    private function _doCopyOriginalImageToNewImage($vars = array())
    {
        if ($vars) {
            extract($vars);
        }
        
        imagecopyresampled(
            $newImage, 
            $originalImage, 
            0, 
            0, 
            0, 
            0, 
            $width, 
            $height, 
            $originalWidth, 
            $originalHeight
        );
        
        return $newImage;
    } // end _doCopyOriginalImageToNewImage
    
    private function _getListFilesInDirectory($dirName)
    {
        if (!$this->_fileSystem->is_readable($dirName)){
            $message = __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            $message .= $dirName;
            
            throw new Exception($message);
        }
        
        $files = $this->_fileSystem->dirlist($dirName);
        return array_keys($files); 
    } // end _getListFilesInDirectory
    
    private function _updateCookieCacheFile()
    {
        $time = time();
        $content = md5($time);
        $content = array($content);
        $this->updateOptions('cookie', $content);
    } // end _updateCookieCacheFile
    
    public function getPluginTemplatePath($fileName)
    {
        return $this->_pluginTemplatePath.'backend/'.$fileName;
    } // end getPluginTemplatePath
    
    public function getPluginCssUrl($fileName) 
    {
        return $this->_pluginCssUrl.'backend/'.$fileName;
    } // end getPluginCssUrl
    
    public function getPluginJsUrl($fileName)
    {
        return $this->_pluginJsUrl.'backend/'.$fileName;
    } // end getPluginJsUrl
    
    public function onAdminMenuAction() 
    {
        $page = add_menu_page(
            __('WooCart Pro Settings', $this->_languageDomain), 
            __('WooCart Pro', $this->_languageDomain), 
            'manage_options', 
            'festi-cart', 
            array(&$this, 'onDisplayOptionPage'), 
            $this->getPluginImagesUrl('icon_16x16.png')
        );
        
        $this->addActionListener(
            'admin_print_scripts-'.$page, 
            'onInitJsAction'
        );
        
        $this->addActionListener(
            'admin_print_styles-'.$page, 
            'onInitCssAction'
        );
        
        $this->addActionListener(
            'admin_head-'.$page,
            '_onFileSystemInstanceAction'
        );
    } // end onAdminMenuAction
    
    public function onInitJsAction()
    {
        $this->onEnqueueJsFileAction('jquery');
        $this->onEnqueueJsFileAction(
            'festi-cart-general',
            'general.js',
            'festi-cart-slider'
        );
        $this->onEnqueueJsFileAction(
            'festi-cart-colorpicker',
            'colorpicker.js',
            'jquery'
        );
        $this->onEnqueueJsFileAction(
            'festi-cart-tooltip',
            'tooltip.js',
            'festi-cart-colorpicker'
        );
        
        $this->onEnqueueJsFileAction(
            'festi-cart-slider',
            'slider.js',
            'festi-cart-tooltip'
        );
        
        $this->onEnqueueJsFileAction(
            'festi-cart-top-down-scroll-buttons',
            'top_down_scroll_buttons.js',
            'jquery'
        );
        
    } // end onInitJsAction
    
    public function onInitCssAction()
    {
        $this->onEnqueueCssFileAction(
            'festi-cart-styles',
            'style.css'
        );
        
        $this->onEnqueueCssFileAction(
            'festi-cart-colorpicker',
            'colorpicker.css'
        );
        
        $this->onEnqueueCssFileAction(
            'festi-cart-tooltip',
            'tooltip.css'
        );
        
        $this->onEnqueueCssFileAction(
            'festi-cart-slider',
            'slider.css'
        );
        
        $this->onEnqueueCssFileAction(
            'festi-cart-top-down-scroll-buttons',
            'top_down_scroll_buttons.css'
        );
    } // end onInitCssAction
    
    private function _doInitDefaultOptions($option, $instance = NULL)
    {
        $methodName = $this->getMethodName('load', $option);
        
        if (is_null($instance)) {
            $instance = $this;
        }

        $method = array($instance, $methodName);
        
        if (!is_callable($method)) {
            throw new Exception("Undefined method name: ".$methodName);
        }

        $options = call_user_func_array($method, array());
        foreach ($options as $ident => &$item) {
            if ($this->_hasDefaultValueInItem($item)) {
                $values[$ident] = $item['default'];
            }
        }
        unset($item);
        
        $this->updateOptions($option, $values);
    } // end _doInitDefaultOptions
    
    public function getMethodName($prefix, $option)
    {
        $option = explode('_', $option);
        
        $option = array_map('ucfirst', $option);
        
        $option = implode('', $option);
        
        $methodName = $prefix.$option;
        
        return $methodName;
    } // end getMethodName
    
    private function _hasDefaultValueInItem($item)
    {
        return isset($item['default']);
    } //end _hasDefaultValueInItem

    public function onDisplayOptionPage()
    {
        $menu = $this->fetch('menu.phtml');
        echo $menu;

        $methodName = 'fetchOptionPage';
        
        if ($this->hasOptionPageInRequest()) {
            $postfix = $_GET['tab'];
        } else {
            $postfix = $this->_defaultMenuOption;
        }
        $methodName.= ucfirst($postfix);
        
        $method = array(&$this, $methodName);
        
        if (!is_callable($method)) {
            throw new Exception("Undefined method name: ".$methodName);
        }
        
        call_user_func_array($method, array());
    } // end onDisplayOptionPage
    
    public function fetchOptionPageSettings()
    {
        $vars = array();

        if ($this->_isRefreshPlugin()) {
            $this->onRefreshPlugin();

            $message = __(
                'Success update plugin',
                $this->_languageDomain
            );
            
            $this->displayUpdate($message);
        }
        
        $this->_displayFoldersAccessErrors();
        
        if ($this->_isDeleteCostumIcon()) {
            $this->onDeleteCustomIcon();

            $message = __(
                'Success custom icon remove',
                $this->_languageDomain
            );
            
            $this->displayUpdate($message);
        }
        
        if ($this->isUpdateOptions('save')) {
            try {
                $this->_doUpdateOptions($_POST);
                           
                $message = __(
                    'Success update settings',
                    $this->_languageDomain
                );
                
                $this->displayUpdate($message);               
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }

        $options = $this->getOptions('settings');
        
        $vars['fieldset'] = $this->getOptionsFieldSet();        
        $vars['currentValues'] = $options;
        
        echo $this->fetch('settings_page.phtml', $vars);  
    } // end fetchOptionPageSettings
    
    private function _isRefreshPlugin()
    {
        return array_key_exists('refresh_plugin', $_GET);
    } // end _isRefreshPlugin
    
    public function onRefreshPlugin()
    {
        //$this->onUninstall(true);
        $this->onInstall(true);
    } // end onRefreshPlugin
    
    private function _doUpdateOptions($newSettings = array())
    {
        if ($this->isUploadCustomIcon('customIcon')) {
            $this->_doUploadCustomIcon('customIcon');
            $newSettings = array_merge($newSettings, $_POST);
        }
        
        if ($this->isUploadCustomIcon('customIconOnHover')) {
            $this->_doUploadCustomIcon('customIconOnHover');
            $newSettings = array_merge($newSettings, $_POST);
        }
        
        $options = $this->getOptions('settings');

        $this->updateColorOfDefaultIcons('iconColor', $options, $newSettings);
        
        $this->updateColorOfDefaultIcons(
            'iconColorOnHover',
            $options,
            $newSettings
        );

        $this->updateOptions('settings', $newSettings);
             
        $this->_doCreateCustomCssFiles($newSettings);
        
        $this->_updateCookieCacheFile();
    } // end _doUpdateOptions

    private function _hasPermissionToCreateCustomCssFile()
    {
        $path = $this->getCustomCssPath();
        
        return $this->_fileSystem->exists($path)
               && $this->_fileSystem->is_writable($path);
    } // end _hasPermissionToCreateCustomCssFile
    
    private function _doCreateCustomCssFiles($options)
    {
        if (!$this->_hasPermissionToCreateCustomCssFile()) {
            return false;
        }
        
        $filesNamesList = $this->getFileNamesListOfCustomizeCart();

        $vars = array(
            'settings' => $options,
            'woocommerce'=> $this->getWoocommerceInstance(),
        );
        
        foreach ($filesNamesList as $fileName) {
            $cssContent = $this->_fetchFrontendCustomizeTemplate(
                $fileName.'.phtml',
                $vars
            );
            
            $cssContent = str_replace('<style>', '', $cssContent);
            $cssContent = str_replace('</style>', '', $cssContent);
            
            $customCssFile = $this->getCustomCssPath($fileName.'.css');
            $this->_fileSystem->put_contents($customCssFile, $cssContent, 0777);
        }
    } // end _doCreateCustomCssFiles
    
    private function _fetchFrontendCustomizeTemplate($fileName, $vars)
    {
        return  $this->fetch('../frontend/customize/'.$fileName, $vars);
    } // end _fetchFrontendCustomizeTemplate
    
    public function updateColorOfDefaultIcons($type, $options, $newSettings)
    {
        $iconsFolders = array(
            'iconColor'        => 'user/',
            'iconColorOnHover' =>  'user/on_hover/'
        );
        
        $currentIconColor = $options[$type];
        
        $colors['from'] = $currentIconColor;
        
        if ($this->_hasIconColorInRequest($newSettings)) {
           $colors['to'] = $newSettings[$type]; 
        }
        
        if (!$this->isIconColorChanged($colors)) {
            $this->updateIconsColor($colors['to'], $iconsFolders[$type]);
        }
    }  // end updateColorOfDefaultIcons
    
    public function updateIconsColor($color, $folder)
    {        
        $styles = $this->loadSettings();
        
        $iconList = $styles['iconList']['images'];
        
        $defaultIconsPath = $this->getPluginIconsPath('default/');
        
        if (!$this->_fileSystem->is_readable($defaultIconsPath)){
            $message = __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            $message .= $defaultIconsPat;
            
            throw new Exception($message);
        }
        
        $userIconsPath = $this->getPluginIconsPath($folder);

        $colors = array(
            'fromRgb' => $this->_iconRgbColor,
            'toRgb' => $this->convertHexToRgb($color)
        );

        foreach ($iconList as $value) {
            $icons = array(
                'defaultIconPath' => $defaultIconsPath.$value,
                'userIconPath' =>  $userIconsPath.$value
            );
                        
            $this->doUpdateIconSize($icons, $colors);
        }
    } // end updateIconsColor
    
    private function _hasIconColorInRequest($settings = array())
    {
        return array_key_exists('iconColor', $settings) 
               && !empty($settings['iconColor']);
    } // end _hasIconColorInRequest
    
    public function isIconColorChanged($colors = array())
    {
         extract($colors);
         return $from == $to;
    } //end isIconColorChanged
    
    private function _displayFoldersAccessErrors()
    {        
        $caheFolderErorrs = $this->_detectTheCacheFolderAccessErrors();
        
        $userFolderErorrs = $this->_detectTheUserIconsFolderAccessErrors();
        
        $customCssFolderErorrs = $this->_detectTheCustomCssFolderAccessErrors();
        
        if ($caheFolderErorrs || $userFolderErorrs || $customCssFolderErorrs) {
            echo $this->fetch('refresh.phtml');
        }
    } // end _displayFoldersAccessErrors
    
    private function _detectTheCacheFolderAccessErrors()
    {
        if (!$this->_fileSystem->is_writable($this->_pluginCachePath)) {
            
            $message = __(
                "Caching does not work! ",
                $this->_languageDomain
            );
            
            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            
            $path = $this->_pluginCachePath;
            
            if (!$this->_fileSystem->exists($path)) {
                $path = $this->_pluginPath;
            }
            
            $message .= $path;
            $message .= $this->fetch('manual_url.phtml');
            
            $this->displayError($message);
            
            return true;
        }
        
        return false;
    } // end _detectTheCacheFolderAccessErrors
    
    private function _detectTheUserIconsFolderAccessErrors()
    {
        $userIconsPath = $this->getPluginIconsPath('user/');
        if (!$this->_fileSystem->is_writable($userIconsPath)) {
            
            $this->_currentIconFolder = 'default';
            
            $message = __(
                "Available only standard icons! ",
                $this->_languageDomain
            );
            
            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            
            $path = $userIconsPath;
            
            if (!$this->_fileSystem->exists($userIconsPath)) {
                $path = $this->getPluginIconsPath();
            }
            
            $message .= $path;
            $message .= $this->fetch('manual_url.phtml');
            
            $this->displayError($message);
            
            return true;
        }
        
        return false;   
    } // end _detectTheUserIconsFolderAccessErrors
    
    private function _detectTheCustomCssFolderAccessErrors()
    {
        $customCssPath = $this->getCustomCssPath();
        if (!$this->_fileSystem->is_writable($customCssPath)) {

            $message = __(
                "Styles for Customizing of cart will not added in css file! ",
                $this->_languageDomain
            );
            
            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            
            $path = $customCssPath;

            if (!$this->_fileSystem->exists($customCssPath)) {
                $path = $this->_pluginCssPath.'frontend/';
            }
            
            $message .= $path;
            
            $this->displayError($message);
            
            return true;
        }
        
        return false;   
    } // end _detectTheUserIconsFolderAccessErrors
    
    public function isUpdateOptions($action)
    {
        return array_key_exists('__action', $_POST)
               && $_POST['__action'] == $action;
    } // end isUpdateOptions
    
    public function getOptionsFieldSet()
    {
        $fildset = array(
            'general' => array(
                'legend' => __('General', $this->_languageDomain),
                'display' => true
            ),
            'menu' => array(
                'legend' => __('Menu', $this->_languageDomain)
            ),
            'window' => array(
                'legend' => __('Cart in Fixed Location', $this->_languageDomain)
            ),
            'cartCustomization' => array(
                'legend' => __(
                    'Customization for Cart Container',
                    $this->_languageDomain
                )
            ),
            'dropdownListCustomization' => array(
                'legend' => __(
                    'Customization for Product List',
                    $this->_languageDomain
                )
            ),
            'popup' => array(
                'legend' => __(
                    ' Lightbox Popup for Add to Cart action',
                    $this->_languageDomain
                )
            ),
        );
        
        $settings = $this->loadSettings();
        
        if ($settings) {
            foreach ($settings as $ident => &$item) {
                if (array_key_exists('fieldsetKey', $item)) {
                   $key = $item['fieldsetKey'];
                   $fildset[$key]['filds'][$ident] = $settings[$ident];
                }
            }
            unset($item);
        }
        
        return $fildset;
    } // end getOptionsFieldSet
    
    public function loadSettings()
    {
        $settings = array(
            'displayMenu' => array(
                'caption' => __('Cart in Menu', $this->_languageDomain),
                'lable' => __(
                    'Enable displaying cart in menu',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'menu',
                'backlight' => 'light'
            ),
            
            'menuList' => array(
                'caption' => __('Displaying in Menu', $this->_languageDomain),
                'hint' => __(
                    'Select where you want to show cart ', 
                    $this->_languageDomain
                ),
                'type' => 'select',
                'attr' => 'multiple',
                'default' => array(),
                'eventClasses' => 'displayMenu',
                'fieldsetKey' => 'menu'
            ),
            
            'menuCartPosition' => array(
                'caption' => __('Position in Menu', $this->_languageDomain),
                'hint' => __(
                    'Select the location of cart in the menu',
                    $this->_languageDomain
                ),
                'type' => 'select',
                'values' => array(
                    0 => __('Left', $this->_languageDomain),
                    1 => __('Right', $this->_languageDomain)
                ),
                'default' => 1,
                'eventClasses' => 'displayMenu',
                'fieldsetKey' => 'menu'
            ),
            'customizeCartInMenu' => array(
                'caption' => __(
                    'Enable Customization for Cart In Menu',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'eventClasses' => 'displayMenu',
                'fieldsetKey' => 'menu'
            ),
            'hideCart' => array(
                'caption' => __('Hide Empty cart', $this->_languageDomain),
                'lable' => __(
                    'Enable hide cart option',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'fieldsetKey' => 'general',
            ),
            'cartIconDivider' => array(
                'caption' => __('Icon', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'general'
            ),   
            'displayIcon' => array(
                'caption' => __('Cart Icon', $this->_languageDomain),
                'lable' => __('Enable cart icon', $this->_languageDomain),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),

            'iconPosition' => array(
                'caption' => __(
                    'Icon Position',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change Icon position',
                    $this->_languageDomain
                ),
                'values' => array(
                    0 => __('Left', $this->_languageDomain),
                    1 => __('Right', $this->_languageDomain)
                ),
                'type' => 'select',
                'default' => 0,
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),
            'iconList' => array(
                'caption' => __('Icons', $this->_languageDomain),
                'type'    => 'icon_list',
                'images'  => array(
                    1  => 'icon1.png',
                    2  => 'icon2.png',
                    3  => 'icon3.png',
                    4  => 'icon4.png',
                    5  => 'icon5.png',
                    6  => 'icon6.png',
                    7  => 'icon7.png',
                    8  => 'icon8.png',
                    9  => 'icon9.png',
                    10 => 'icon10.png',
                    11 => 'icon11.png',                 
                ),
                'default' => 5,
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),
            'iconColor' => array(
                'type' => 'skip',
                'default' => '#000000'
            ),
            'iconColorOnHover' => array(
                'caption' => __('Icon Color on Hover', $this->_languageDomain),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'hide' => 'ifDefaultFolder',
                'type'    => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'general',
                'eventClasses' => 'displayIcon',
            ),
            'customIconWidth' => array(
                'caption' => __(
                    'Width for Custom Icon',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'input_size',
                'default' => $this->_defaultIconSize,
                'eventClasses' => 'displayIcon',
                'class' => 'festi-cart-custom-icon-size',
                'fieldsetKey' => 'general'
            ),
            
            'customIconHeight' => array(
                'caption' => __(
                    'Height for Custom Icon',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'input_size',
                'default' => $this->_defaultIconSize,
                'eventClasses' => 'displayIcon',
                'class' => 'festi-cart-custom-icon-size',
                'fieldsetKey' => 'general'
            ),        
            'customIcon' => array(
                'caption' => __('Custom Icon', $this->_languageDomain),
                'hint' => __(
                    'Upload your own cart image',
                    $this->_languageDomain
                ),
                'type' => 'custom_icon',
                'default' => '',
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),
            'customIconOnHover' => array(
                'caption' => __('Custom Icon on Hover', $this->_languageDomain),
                'hint' => __(
                    'Upload your own cart image',
                    $this->_languageDomain
                ),
                'type' => 'custom_icon_on_hover',
                'default' => '',
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),
            'cartDropdownListDivider' => array(
                'caption' => __('Dropdown List', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'general'
            ),        
            'dropdownAction' => array(
                'caption' => __(
                    'Dropdown Product List',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select action to show dropdown product list', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'disable' => __('Disable', $this->_languageDomain),
                    'hover' => __('On Hover', $this->_languageDomain),
                    'click' => __('On Click', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'click',
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),         
            'cartDropdownListAligment' => array(
                'caption' => __(
                    'Alignment Relative to Cart',
                    $this->_languageDomain
                ),
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'left',
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'general',
            ),         
            'dropdownArrow' => array(
                'caption' => __(
                    'Dropdown Arrow',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select for Show dropdown arrow', 
                    $this->_languageDomain
                ),
                'values' => array(
                    0 => __('Hide', $this->_languageDomain),
                    1 => __('Left', $this->_languageDomain),
                    2 => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 1,
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'general'
            ),
            'dropdownListAmountProducts' => array(
                'caption' => __(
                    'Set Maximum Number of Products',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'general'
            ),
            'cartQuantityDivider' => array(
                'caption' => __('Product Quantity', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'general'
            ),   
            'displayCartQuantity' => array(
                'caption' => __('Product Quantity', $this->_languageDomain),
                'lable' => __(
                    'Enable total products amount in cart',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
                   
            'textBeforeQuantity' => array(
                'caption' => __('Text Before Quantity', $this->_languageDomain),
                'hint' => __(
                    'Change Text Before Quantity',
                    $this->_languageDomain
                ),
                'type' => 'input_double_text',
                'default' => '',
                'fieldsetKey' => 'general',
            ),
            'textBeforeQuantityPlural' => array(
                'type'    => 'skip',
                'default' => '',
            ),
            
            'textAfterQuantity' => array(
                'caption' => __('Text After Quantity', $this->_languageDomain),
                'hint' => __(
                    'Change Text After Quantity',
                    $this->_languageDomain
                ),
                'type' => 'input_double_text',
                'default' => 'Item',
                'fieldsetKey' => 'general',
            ),
            'textAfterQuantityPlural' => array(
                'type'    => 'skip',
                'default' => 'Items',
            ),
            'cartTotalDivider' => array(
                'caption' => __('Total Price', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'general'
            ),   
            'displayCartTotal' => array(
                'caption' => __('Cart Total Price', $this->_languageDomain),
                'lable' => __(
                    'Enable Cart Total Price in cart',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
            'textBeforeTotal' => array(
                'caption' => __(
                    'Text Before Total Price',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change Text Before Total',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => '',
                'fieldsetKey' => 'general',
            ),
            
            'textAfterTotal' => array(
                'caption' => __(
                    'Text After Total Price', 
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change Text After Total',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => '',
                'fieldsetKey' => 'general',
            ),       
            'windowCart' => array(
                'caption' => __(
                    'Show Cart',
                    $this->_languageDomain
            ),
                'lable' => __(
                    'Enable displaying cart in browser window',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'window',
                'backlight' => 'light'
            ),
            
            'windowCartHorizontalPosition' => array(
                'caption' => __(
                    'Horizontal Location in Window',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the horizontal location of cart in the window',
                    $this->_languageDomain
                ),
                'type' => 'select',
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'center' => __('Center', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain)
                ),
                'default' => 'right',
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),
            
            'windowCartVerticalPosition' => array(
                'caption' => __(
                    'Vertical Location in Window',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the vertical location of cart in the window',
                    $this->_languageDomain
                ),
                'type' => 'select',
                'values' => array(
                    'top' => __('Top', $this->_languageDomain),
                    'middle' => __('Middle', $this->_languageDomain)
                ),
                'default' => 'top',
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),
    
            'windowCartMarginTop' => array(
                'caption' => __(
                    'Margin Top for Cart',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 50,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),
            
            'windowCartMarginLeft' => array(
                'caption' => __(
                    'Margin Left for Cart',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),
            
            'windowCartMarginRight' => array(
                'caption' => __(
                    'Margin Right for Cart',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 50,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),
            'windowCartFixedPosition' => array(
                'caption' => __(
                    'Scrolling Cart',
                    $this->_languageDomain
            ),
                'lable' => __(
                    'Enable scrolling for cart',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window',
            ),
            'popup' => array(
                'caption' => __(
                    'Show',
                    $this->_languageDomain
                ),
                'lable' => __(
                    'Enable displaying popup after adding product',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'popup',
                'backlight' => 'light'
            ),
            'popupWidth' => array(
                'caption' => __(
                    'Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 400,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 100,
                'max' => 1000,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupPadding' => array(
                'caption' => __(
                    'Padding',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackAroundDivider' => array(
                'caption' => __(
                    'Blacked out background',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupAroundBackColor' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupAroundBackOpacity' => array(
                'caption' => __(
                    'Opacity',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackgroundDivider' => array(
                'caption' => __('Background Window', $this->_languageDomain),
                'type'    => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackgroundColor' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackgroundOpacity' => array(
                'caption' => __(
                    'Opacity',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowDivider' => array(
                'caption' => __('Shadow', $this->_languageDomain),
                'type'    => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowColor' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the shadow color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#5e5e5e',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowWidth' => array(
                'caption' => __(
                    'Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 500,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowBlur' => array(
                'caption' => __(
                    'Blur',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBorderDivider' => array(
                'caption' => __('Border', $this->_languageDomain),
                'type'    => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBorderWidth' => array(
                'caption' => __(
                    'Border Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 3,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupBorderRadius' => array(
                'caption' => __(
                    'Border Radius',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupBorderColor' => array(
                'caption' => __(
                    'Border Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#00a8ca',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupProductsListDivider' => array(
                'caption' => __(
                    'Products List',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupProductsListScroll' => array(
                'caption' => __(
                    'Scrollbar',
                    $this->_languageDomain
                ),
                'lable' => __(
                    'Enable scrollbar for products list',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'event' => 'visible',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup',
                'backlight' => 'light'
            ),
            'popupProductsListScrollHeight' => array(
                'caption' => __(
                    'Height',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 200,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 100,
                'max' => 1000,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup'
            ),
            'popupHeaderTextDivider' => array(
                'caption' => __(
                    'Header Text Font Styles',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderText' => array(
                'caption' => __(
                    'Text',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the text',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => __(
                    'Item Added to your Cart!',
                    $this->_languageDomain
                ),
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextAlign' => array(
                'caption' => __(
                    'Alignment',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the location of text in popup header', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'center' => __('Center', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'center',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup'
            ),
            'popupHeaderTextFontSize' => array(
                'caption' => __('Font Size', $this->_languageDomain),
                'lable' => 'px',
                'hint' => __(
                    'Change font size', 
                    $this->_languageDomain
                ),
                'type' => 'input_size',
                'default' => 20,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextColor' => array(
                'caption' => __('Font Color', $this->_languageDomain),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#5b9e2b',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginTop' => array(
                'caption' => __(
                    'Margin Top',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
             'popupHeaderTextMarginBottom' => array(
                'caption' => __(
                    'Margin Bottom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 20,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginLeft' => array(
                'caption' => __(
                    'Margin Left',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginRight' => array(
                'caption' => __(
                    'Margin Right',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonDivider' => array(
                'caption' => __(
                    'Close button',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'displayPopupCloseButton' => array(
                'caption' => __('Display', $this->_languageDomain),
                'lable' => __(
                    'Enable close button',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
                'backlight' => 'light'
            ),
            'popupCloseButtonSize' => array(
                'caption' => __(
                    'Size',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 30,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 5,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#00a8ca',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonHoverColor' => array(
                'caption' => __(
                    'Font Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#72ddf2',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonMarginTop' => array(
                'caption' => __(
                    'Margin Top',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonMarginRight' => array(
                'caption' => __(
                    'Margin Right',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonDivider' => array(
                'caption' => __(
                    'Continue Shopping Button',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'displayPopupContinueButton' => array(
                'caption' => __('Continue Shopping Button', $this->_languageDomain),
                'lable' => __(
                    'Enable Continue Shopping button',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
                'backlight' => 'light'
            ),
            'popupContinueButtonText' => array(
                'caption' => __(
                    'Title',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the button text',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => __('Continue Shopping', $this->_languageDomain),
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonFontSize' => array(
                'caption' => __('Font Size', $this->_languageDomain),
                'lable' => 'px',
                'hint' => __(
                    'Change font size', 
                    $this->_languageDomain
                ),
                'type' => 'input_size',
                'default' => 20,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonAlign' => array(
                'caption' => __(
                    'Alignment',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the location of button in popup footer', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'center' => __('Center', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'center',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonWidthType' => array(
                'caption' => __(
                    'Width Type',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select width type', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'auto' => __('Auto', $this->_languageDomain),
                    'full' => __('Full Width', $this->_languageDomain),
                    'custom' => __('Custom', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'auto',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonWidth' => array(
                'caption' => __('Custom Width', $this->_languageDomain),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 50,
                'max' => 1000,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonPaddingTop' => array(
                'caption' => __(
                    'Padding Top',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonPaddingBottom' => array(
                'caption' => __(
                    'Padding Bottom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBackground' => array(
                'caption' => __(
                    'Background Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonHoverBackground' => array(
                'caption' => __(
                    'Background Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#00a8ca',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonHoverFontColor' => array(
                'caption' => __(
                    'Font Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#72ddf2',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBorderWidth' => array(
                'caption' => __(
                    'Border Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBorderRadius' => array(
                'caption' => __(
                    'Border Radius',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBorderColor' => array(
                'caption' => __(
                    'Border Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonHoverBorderColor' => array(
                'caption' => __(
                    'Border Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'responsiveCartWidth' => array(
                'caption' => __(
                    'Disable Responsive Width',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'cartCustomization',
                'backlight' => 'light'
            ),
            'cartWidth' => array(
                'caption' => __(
                    'Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 20,
                'max' => 1000,
                'eventClasses' => 'responsiveCartWidth',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartPadding' => array(
                'caption' => __(
                    'Padding',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartContentAlign' => array(
                'caption' => __(
                    'Content Alignment',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the location of content in cart container', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'center' => __('Center', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'left',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartOpacity' => array(
                'caption' => __(
                    'Opacity for Cart',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                //'fieldsetKey' => 'cartCustomization'
            ),
            'cartHoverOpacity' => array(
                'caption' => __(
                    'Cart Opacity for Hover Action',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                //'fieldsetKey' => 'cartCustomization'
            ),
            'cartFontDivider' => array(
                'caption' => __('Font Styles', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'cartCustomization'
            ),
            'fontSize' => array(
                'caption' => __('Font Size', $this->_languageDomain),
                'lable' => 'px',
                'hint' => __(
                    'Change font size', 
                    $this->_languageDomain
                ),
                'type' => 'input_size',
                'default' => 14,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'cartCustomization'
            ),
            
            'textColor' => array(
                'caption' => __('Font Color', $this->_languageDomain),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'cartCustomization'
            ),
            'textHoverColor' => array(
                'caption' => __('Font Color on Hover', $this->_languageDomain),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBackgroundDivider' => array(
                'caption' => __('Background', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBackground' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartHoverBackground' => array(
                'caption' => __(
                    'Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBackgroundOpacity' => array(
                'caption' => __(
                    'Opacity',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 6,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartHoverBackgroundOpacity' => array(
                'caption' => __(
                    'Opacity on Hover',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 8,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderDivider' => array(
                'caption' => __('Border', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderWidth' => array(
                'caption' => __(
                    'Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderColor' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#a39da3',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderHoverColor' => array(
                'caption' => __(
                    'Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#b3afb3',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusTopLeft' => array(
                'caption' => __(
                    'Radius for Top Left Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusTopRight' => array(
                'caption' => __(
                    'Radius for Top Right Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusBottomRight' => array(
                'caption' => __(
                    'Radius for Bottom Right Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusBottomLeft' => array(
                'caption' => __(
                    'Radius for Bottom Right Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'productListPadding' => array(
                'caption' => __(
                    'Padding',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'responsiveProductList' => array(
                'caption' => __(
                    'Disable Responsive Width',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productListWidth' => array(
                'caption' => __(
                    'Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 170,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 150,
                'max' => 1000,
                'eventClasses' => 'responsiveProductList',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListFontSize' => array(
                'caption' => __('Font Size', $this->_languageDomain),
                'lable' => 'px',
                'hint' => __(
                    'Change font size', 
                    $this->_languageDomain
                ),
                'type' => 'input_size',
                'default' => 13,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyCartDivider' => array(
                'caption' => __('Empty Cart', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyText' => array(
                'caption' => __(
                    'Text for Empty Cart',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change Dropdown List Text for Empty Cart',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => 'There are no products',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'productListEmptyPaddingTop' => array(
                'caption' => __(
                    'Padding Top',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyPaddingBottom' => array(
                'caption' => __(
                    'Padding Bottom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
           'productListEmptyFontColor' => array(
                'caption' => __(
                    'Text Font Color for Empty Cart',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color for empty product list text',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#111111',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBackgroundDivider' => array(
                'caption' => __('Background', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBackground' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBackgroundOpacity' => array(
                'caption' => __(
                    'Opacity',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderDivider' => array(
                'caption' => __('Border', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderWidth' => array(
                'caption' => __(
                    'Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization'
            ),            
            'borderColor' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#ccc7c3',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'borderArrow' => array(
                'caption' => __(
                    'Show arrow on top of border',
                    $this->_languageDomain
                ),
                'lable' => __(
                    'Enable dropdown arrow',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'borderArrowColor' => array(
                'caption' => __(
                    'Arrow Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#ccc7c3',
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusTopLeft' => array(
                'caption' => __(
                    'Radius for Top Left Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusTopRight' => array(
                'caption' => __(
                    'Radius for Top Right Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusBottomRight' => array(
                'caption' => __(
                    'Radius for Bottom Right Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusBottomLeft' => array(
                'caption' => __(
                    'Radius for Bottom Left Corner',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListOpacity' => array(
                'caption' => __(
                    'Opacity for Product List',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                //'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productTitleDivider' => array(
                'caption' => __('Product Title', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductTitle' => array(
                'caption' => __('Display', $this->_languageDomain),
                'lable' => __(
                    'Enable product title',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#00497d',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductTitle'
            ),
            'productHoverFontColor' => array(
                'caption' => __(
                    'Font Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#8094ed',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductTitle'
            ),
            'productAmountDivider' => array(
                'caption' => __('Amount and Price', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductTotalPrice' => array(
                'caption' => __('Display', $this->_languageDomain),
                'lable' => __(
                    'Enable amount and price',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productTotalPriceFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#1f1e1e',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductTotalPrice'
            ),
            'productListSubtotalDivider' => array(
                'caption' => __('Subtotal', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductListTotal' => array(
                'caption' => __('Display', $this->_languageDomain),
                'lable' => __(
                    'Enable Subtotal',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productListTotalText' => array(
                'caption' => __(
                    'Title',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the text',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => __('Subtotal', $this->_languageDomain),
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalTextAlign' => array(
                'caption' => __(
                    'Text Position',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the location of text in subtotal container', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'center' => __('Center', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'right',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBackground' => array(
                'caption' => __(
                    'Background Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#eeeeee',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBorderWidth' => array(
                'caption' => __(
                    'Border Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBorderColor' => array(
                'caption' => __(
                    'Border Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color for total price in product list',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e6e6e6',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBorderRadius' => array(
                'caption' => __(
                    'Border Radius',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 7,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListButtonsDivider' => array(
                'caption' => __(
                    'View Cart & Checkout Buttons',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListButtonsFontWeight' => array(
                'caption' => __(
                    'Font Weight',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select font weight for Buttons', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'normal' => __('Normal', $this->_languageDomain),
                    'bold' => __('Bold', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'normal',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'productListButtonsQueue' => array(
                'caption' => __(
                    'Display the First in Queue',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select Display the button to display the first', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'viewCart' => __('View Cart', $this->_languageDomain),
                    'checkout' => __('Checkout', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'viewCart',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'displayViewCartButton' => array(
                'caption' => __('View Cart Button', $this->_languageDomain),
                'lable' => __(
                    'Enable View Cart button',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'viewCartButtonText' => array(
                'caption' => __(
                    'Title',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the button text',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => __('View Cart', $this->_languageDomain),
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton'
            ),
            'viewCartButtonWidthType' => array(
                'caption' => __(
                    'Width Type',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select width type', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'auto' => __('Auto', $this->_languageDomain),
                    'full' => __('Full Width', $this->_languageDomain),
                    'custom' => __('Custom', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'auto',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton'
            ),
            'viewCartButtonWidth' => array(
                'caption' => __('Custom Width', $this->_languageDomain),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 50,
                'max' => 1000,
                'eventClasses' => 'displayViewCartButton',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'viewCartButtonPaddingTop' => array(
                'caption' => __(
                    'Padding Top',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'viewCartButtonPaddingBottom' => array(
                'caption' => __(
                    'Padding Bottom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'viewCartButtonBackground' => array(
                'caption' => __(
                    'Background Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#eeeeee',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonHoverBackground' => array(
                'caption' => __(
                    'Background Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#6caff7',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonHoverFontColor' => array(
                'caption' => __(
                    'Font Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonBorderWidth' => array(
                'caption' => __(
                    'Border Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonBorderRadius' => array(
                'caption' => __(
                    'Border Radius',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 7,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonBorderColor' => array(
                'caption' => __(
                    'Border Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonHoverBorderColor' => array(
                'caption' => __(
                    'Border Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'displayCheckoutButton' => array(
                'caption' => __('Checkout Button', $this->_languageDomain),
                'lable' => __(
                    'Enable Checkout button',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'checkoutButtonText' => array(
                'caption' => __(
                    'Title',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the button text',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'default' => __('Checkout', $this->_languageDomain),
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton'
            ),
            'checkoutButtonWidthType' => array(
                'caption' => __(
                    'Width Type',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select width type', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'auto' => __('Auto', $this->_languageDomain),
                    'full' => __('Full Width', $this->_languageDomain),
                    'custom' => __('Custom', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'auto',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton'
            ),
            'checkoutButtonWidth' => array(
                'caption' => __('Custom Width', $this->_languageDomain),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 50,
                'max' => 1000,
                'eventClasses' => 'displayCheckoutButton',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'checkoutButtonPaddingTop' => array(
                'caption' => __(
                    'Padding Top',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'checkoutButtonPaddingBottom' => array(
                'caption' => __(
                    'Padding Bottom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'checkoutButtonBackground' => array(
                'caption' => __(
                    'Background Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#eeeeee',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonHoverBackground' => array(
                'caption' => __(
                    'Background Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the background color',
                    $this->_languageDomain
                ),
                'type' => 'color_picker',
                'default' => '#6caff7',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonHoverFontColor' => array(
                'caption' => __(
                    'Font Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonBorderWidth' => array(
                'caption' => __(
                    'Border Width',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonBorderRadius' => array(
                'caption' => __(
                    'Border Radius',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 7,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonBorderColor' => array(
                'caption' => __(
                    'Border Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonHoverBorderColor' => array(
                'caption' => __(
                    'Border Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'productListDeleteButtonDivider' => array(
                'caption' => __(
                    'Delete Product Button',
                    $this->_languageDomain
                ),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayDeleteButton' => array(
                'caption' => __('Display', $this->_languageDomain),
                'lable' => __(
                    'Enable Delete Product Button',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'deleteButtonPosition' => array(
                'caption' => __(
                    'Position',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the location of button in dropdown list', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'left' => __('Left', $this->_languageDomain),
                    'right' => __('Right', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'left',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonVerticalAlignment' => array(
                'caption' => __(
                    'Vertical Alignment',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Select the vertical alignment of button in dropdown list', 
                    $this->_languageDomain
                ),
                'values' => array(
                    'top' => __('Top', $this->_languageDomain),
                    'middle' => __('Middle', $this->_languageDomain),
                    'bottom' => __('Bottom', $this->_languageDomain),
                ),
                'type' => 'select',
                'default' => 'top',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonSize' => array(
                'caption' => __(
                    'Size',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 18,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 5,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonFontColor' => array(
                'caption' => __(
                    'Font Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonHoverFontColor' => array(
                'caption' => __(
                    'Font Color on Hover',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#807878',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'productListDelimiterDivider' => array(
                'caption' => __('Divider for products', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'delimiterPositionsWidth' => array(
                'caption' => __(
                    'Height',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'delimiterPositionsColor' => array(
                'caption' => __(
                    'Color',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Change the Color',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'default' => '#e8e4e3',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productsPicturesDivider' => array(
                'caption' => __('Products Thumbnails', $this->_languageDomain),
                'type'    => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductsPictures' => array(
                'caption' => __('Display', $this->_languageDomain),
                'lable' => __(
                    'Enable product picture',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productDefaultThumbnail' => array(
                'caption' => __('Use Default Thumbnails', $this->_languageDomain),
                'lable' => __(
                    'Enable option',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Will use default  WooCommerce Product Thumbnails',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductsPictures'
            ),
            'productImageMaxWidth' => array(
                'caption' => __(
                    'Max Width for Custom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 40,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 500,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductsPictures'
            ),
            'productImageMaxHeight' => array(
                'caption' => __(
                    'Max Height for Custom',
                    $this->_languageDomain
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 500,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductsPictures'
            ),
        );
        
        $menus = $this->_getListWordpressMenu();
        if ($menus) {
            foreach ($menus as $menu) {
                $settings['menuList']['values'][$menu->slug] = $menu->name;
            }
        }
        
        $values = $this->getOptions('settings');
        if ($values) {
            foreach ($settings as $ident => &$item) {
                if (array_key_exists($ident, $values)) {
                    $item['value'] = $values[$ident];
                }
            }
            unset($item);
        }

        $currentIconValue = $values['iconList'];

        if ($this->_isSelectedCustomIcon($currentIconValue)) {
            $settings['customIcon']['selected'] = 1; 
        }
        
        return $settings;
    } // end loadSettings
    
    private function _doUploadCustomIcon($type)
    {
        $iconsFolders = array(
            'customIcon'        => 'user/',
            'customIconOnHover' =>  'user/on_hover/'
        );
        
        if (!$this->_isAllowedCustomIconExtension($type)) {
            $message = __(
                "Wrong Image Format",
                $this->_languageDomain
            );
            
            throw new Exception($message);
        }
        
        $iconName = 'custom_icon.png';
        $iconPath = $this->getPluginIconsPath($iconsFolders[$type].$iconName);
        
        $variables = array(
            'defaultIconPath' => $_FILES[$type]["tmp_name"],
            'userIconPath' => $iconPath
        );

        $this->doUpdateIconSize($variables, array(), $type);

        $_POST[$type] =  $iconName;
        
        if ($type == 'customIcon') {
            $_POST['iconList'] = 0;
        }
    } // end _doUploadCustomIcon
    
    private function _isAllowedCustomIconExtension($type)
    {
        $ext = pathinfo($_FILES[$type]['name'], PATHINFO_EXTENSION);

        return in_array($ext, array('png', 'gif', 'jpg', 'jpeg'));
    } // end _isAllowedCustomIconExtension
    
    public function onDeleteCustomIcon()
    {
        $options = $this->getOptions('settings');
                
        $options['customIcon'] = '';
        $options['customIconOnHover'] = '';
        
        $value = $options['iconList'];
        
        if ($this->_isWasMainIcon($value)) {
            $settings = $this->loadSettings();
            $options['iconList'] = $settings['iconList']['default'];
        }
        
        $this->updateOptions('settings', $options);
        
        unset($_GET['delete_custom_icon']);
        
    } // end onDeleteCustomIconAction
    
    private function _isWasMainIcon($var)
    {
        return $var == 0;
    } // end _isWasMainIcon
    
    public function getSelectorClassForDisplayEvent($class)
    {
        $selector = $class.'-visible';
        
        $options = $this->getOptions('settings');
                
        if (!isset($options[$class]) || $options[$class] == 'disable') {
            $selector.=  ' festi-cart-hidden ';
        }
        
        return $selector;
    } // end getSelectorClassForDisplayEvent
    
    private function _getListWordpressMenu()
    {
        return get_terms('nav_menu', array('hide_empty' => false));
    } //end _getListWordpressMenu    

    private function _isSelectedCustomIcon($value)
    {
       return $value == 0; 
    } // end _isSelectedCustomIcon
       
    protected function hasOptionPageInRequest()
    {
        return array_key_exists('tab', $_GET)
               && array_key_exists($_GET['tab'], $this->_menuOptions);
    } // end hasOptionPageInRequest
    
    private function _isDeleteCostumIcon()
    {
        return array_key_exists('delete_custom_icon', $_GET);
    } // end _isDeleteCostumIcon
    
    public function fetchOptionPageHelp()
    {
        echo $this->fetch('help_page.phtml');
    } // end fetchOptionPageManual
    
    public function fetchOptionPageImportExport()
    {
        if ($this->isUpdateOptions('import')) {
            try {
                $this->doImportSettingsFromJson($_POST['importSettings']);
                           
                $message = __(
                    'Success update settings',
                    $this->_languageDomain
                );
                
                $this->displayUpdate($message);               
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }
        
        $vars = array(
            'jsonCode' => $this->getJsonForExport()
        );
        
        echo $this->fetch('import_export_page.phtml', $vars);  
    } // end fetchOptionPageImportExport
    
    public function getJsonForExport()
    {
        $options = $this->getOptionsWithDisabledValues();
        
        $options = json_encode($options);
        
        return $options;
    } // end getJsonForExport
    
    public function getOptionsWithDisabledValues()
    {
        $options = $this->getOptions('settings');
        
        $settings = $this->loadSettings();
        
        $diff = array_diff_key($settings, $options);
        
        $disabledOptions = array();
        
        foreach ($diff as $key => $value) {
            if ($this->_isSwitchOption($value)) {
                $disabledOptions[$key] = 'false';
            }
        }

        $options = array_merge($options, $disabledOptions);
        
        return $options;
        
    } // end getOptionsWithDisabledValues
    
    private function _isSwitchOption($value)
    {
        return $value['type'] == 'input_checkbox';
    } // end _isSwitchOption
    
    public function doImportSettingsFromJson($json = '')
    {
        if(!$json) {
            $message = __(
                'You need to insert JSON',
                $this->_languageDomain
            );
            throw new Exception($message);
        }
        
        $importSettings = stripcslashes($json);
        $importSettings = json_decode($importSettings, true);
        
        if (!is_array($importSettings)) {
            $message = __(
                'Not true format settings',
                $this->_languageDomain
            );
            throw new Exception($message);
        }
        
        $importSettings = $this->getOnlySupportedKeys($importSettings);

        $newOptions = $this->getNewOptions($importSettings);

        $this->_doUpdateOptions($newOptions); 
    } // end doImportSettingsFromJson
    
    public function getOnlySupportedKeys($importSettings)
    {
        $settings = $this->loadSettings();
        
        $diff = array_diff_key($importSettings, $settings);
        $importSettings = array_diff_key($importSettings, $diff);

        if (empty($importSettings)) {
            $message = __(
                'These settings are not supported',
                $this->_languageDomain
            );
            throw new Exception($message);
        }
        
        return $importSettings;

    } // end getOnlySupportedKeys
    
    public function getNewOptions($importSettings)
    {
        $options = $this->getOptions('settings');

        $newOptions = array_merge($options, $importSettings);

        $newOptions = $this->deleteOptionsOfDisabledValue($newOptions);
        
        return $newOptions;
    } // end getNewOptions
    
    public function deleteOptionsOfDisabledValue($options)
    {
        $diff = array_keys($options, 'false');
        
        $diff = array_fill_keys($diff, '');

        $options = array_diff_key($options, $diff);

        return $options;   
    } // end deleteOptionsOfDisabledValue
}