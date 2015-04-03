<?php

abstract class FestiPluginChild extends FestiPlugin
{    
    public function getOptions($optionName)
    {
        $options = array();
        
        if (!$this->_isWpmlActive()) {
            $options = $this->getCache($optionName);
        }

        if ($this->_isWpmlActive() || !$options) {
            $options = get_option($this->_optionsPrefix.$optionName);     
        }
        
        if ($this->isJson($options)) {
            $options = json_decode($options, true);
        } else {
            $options = unserialize($options);
        }
           
        return $options;
    } // end getOptions
    
    private function _isWpmlActive()
    {
        $path = 'sitepress-multilingual-cms/sitepress.php';
        return $this->isPluginActive($path);
    } // end _isWpmlActive
    
    public function updateOptions($optionName, $values = array())
    {
        $values = $this->doChangeSingleQuotesToSymbol($values);
        
        $value = serialize($values);

        $result = update_option($this->_optionsPrefix.$optionName, $value);
        
        if (!$this->_isWpmlActive()) {
            $result = $this->updateCacheFile($optionName, $value);
        }

        return $result;
    } // end updateOptions
    
    protected function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        
        $result = json_decode($string, true);

        return is_array($result);
    } // end isJson
    
    protected function doChangeSingleQuotesToSymbol($options = array())
    {
        foreach ($options as $key => $value) {
            if (!is_string($value)) {
                continue;
            } 
            
            $result = str_replace("'", '&#039;', $value);
            $options[$key] = stripslashes($result);
        }
        
        return $options;
    } // end doChangeSingleQuotesToSymbol
}