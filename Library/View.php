<?php
/**
 * View Class
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class View
{
    public $render = true;
    public $template;
    public $usetidy = false;
    public $fullhtml = true; // Should the opening and closing html, head, and body tags be rendered?
    public $pagetitle;
    public $theme;
    
    protected $vars  = array(); // An array of variables and values to pass to the template
    protected $media = array(); // An array of JavaScript and CSS links, generated by the register function.

    private $_head;
    private $_smarty;
    
    const OPEN = '
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
';
    const CLOSE ='
</body>
</html>
';

    /**
     * _compileDir function
     * ensures the Smarty compile directory exists
     * @return void
     */
    private function _compileDir($dir)
    {
        $this->_smarty->compile_dir = $dir;
        if (!file_exists($dir)) {
            mkdir($dir, 0770);
        }
    }
    
    private function _render($template)
    {
        ob_start();
        $this->_smarty->display($template);
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    /**
     * redirect function
     * @param $redirect
     */
    function redirect($redirect = '')
    {
        // It's odd, but IE needs something here before the script, so add a space char '&nbsp;'
        echo "&nbsp;
          <script>
            window.location = '?$redirect';
          </script>";
    }
    
    /**
     * assign function
     * 
     * @param $key
     * @param $val
     * @return void
     */
    function assign($key, $val)
    {
        $this->vars[$key] = $val;
    }
    
    /**
     * register function
     * 
     * @param $type
     * @param $path
     * @acces public
     * @return unknown_type
     */
    public function register($type, $path)
    {
        switch (strtolower($type)) {
            case 'js':
            case 'javascript':
            case 'script':
                $this->media["Media/JavaScript/$path"] = 'js';
                break;

            case 'css':
            default:
                $this->media["Media/CSS/$path"] = 'css';
                break;
       }
    }
    
    private function _generateMenus()
    {
        // 1. Find all enabled menus
        $model = new MenusModel;
        $menus = $model->getData('', array('IsEnabled'=>'1'));
        
        // 2. Build out each menu
        
    }
    
    /**
     * display function
     * 
     * @return unknown_type
     */
    function display()
    {
        if ($this->render) {
            
            require('Library/Smarty/Smarty.class.php');
            $this->_smarty = new Smarty;
            $this->_smarty->template_dir = '.';
            $this->_smarty->config_dir = 'Library/Smarty/configs';
                        
            foreach ($this->vars as $key=>$val) {
                $this->_smarty->assign($key, $val);
            }

            // Open the default view, if template is empty.
            if (empty($this->template)) {                
                $this->template = 'Modules/Home/Views/v_home.html';
            }
            
            $this->_compileDir('Library/Smarty/.compiled');
            
            // Render the HTML via Smarty
            $body = $this->_render($this->template);

            // Make sure that the theme has been specified and the directory for it exists.
            if (empty($this->theme) || !file_exists("Themes/$this->theme")) {
            	$this->theme = 'Default';
            }
            
            // Add in the HTML header and footer, if necessary.
            if ($this->fullhtml) {

                $this->_head = self::OPEN;
                $this->_head .= "<head>\n";
                
                if (file_exists("Themes/$this->theme/theme.css")) {
                    $this->_head .= "<link rel='stylesheet' href='Themes/$this->theme/theme.css' type='text/css' media='screen' />\n";
                }
                
                foreach ($this->media as $path=>$type) {
                    if ($type == 'js') {
                        $this->_head .= "<script type='text/javascript' src='$path'></script>\n";
                    } else {
                        $this->_head .= "<link rel='stylesheet' href='$path' type='text/css' media='screen' />\n";
                    }
                }
                
                $this->_head .= "<title>$this->pagetitle</title>\n";
                $this->_head .= "</head>\n";
                $html = $this->_head;

                $this->_smarty->template_dir = "Themes/$this->theme";
                $this->_compileDir("Themes/$this->theme/.compiled");
                $html .= $this->_render('header.html');
                $html .= $body;
                $html .= $this->_render('footer.html');                              
                
                $html .= self::CLOSE;
                
            } else {
                $html = $body;
            }
            
            // Attempt to Tidy the output.
            if ($this->usetidy && class_exists('tidy')) {
                $tidy = new tidy;
                $tidy->parseString($html, array(
                    'hide-comments' => TRUE,
                    'output-xhtml' => TRUE,
                    'indent' => TRUE,
                    'wrap' => 0
                ));
                $tidy->cleanRepair();
                echo tidy_get_output($tidy);
            } else {
                // Just dump it as is
                echo $html;
            }
        }
    }    

}
?>