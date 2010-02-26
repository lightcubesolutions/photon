<?php
/**
 * View Class
 *
 * @package photon
 * @version 1.0
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
    
    protected $vars; // An array of variables and values to pass to the template
    protected $media; // An array of JavaScript and CSS links, generated by the register function.

    private $_head;
    
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
     * @return unknown_type
     */
    function register($type, $path)
    {
        switch (strtolower($type)) {
            case 'js':
            case 'javascript':
            case 'script':
                $this->media['js'][] = '';
                break;

            case 'css':
            default:
                $this->media['css'][] = '';
                break;
       } 
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
            $smarty = new Smarty;
            $smarty->template_dir = '.';
            $smarty->compile_dir = 'Library/Smarty/compiled';
            $smarty->config_dir = 'Library/Smarty/configs';
            
            foreach ($this->vars as $key=>$val) {
                $smarty->assign($key, $val);
            }

            // Open the default view, if template is empty.
            if (empty($this->template)) {                
                $this->template = 'Modules/Home/Views/v_home.html';
            }
            
            // Render the HTML via Smarty
            ob_start();
            $smarty->display($this->template);
            $body = ob_get_contents();
            ob_end_clean();

            // Add in the HTML header and footer, if necessary.
            if ($this->fullhtml) {
                $this->_head = self::OPEN;
                $this->_head .= "<head>\n";
                
                if (empty($this->theme)) {
                    $this->theme = 'Default';
                }
                
                if (file_exists("Themes/$this->theme/theme.css")) {
                    $this->_head .= "<link rel='stylesheet' type='text/css' href='Themes/$this->theme/theme.css' />\n";
                }
                
                foreach ($this->media as $type=>$set) {
                    foreach($set as $key=>$val) {
                        if ($type = 'js') {
                            $this->_head .= "<script type='text/javascript' src='$val'></script>\n";
                        } else {
                            $this->_head .= "<link rel='stylesheet' type='text/css' href='$val' />\n";
                        }
                    }
                }
                
                $this->_head .= "<title>$this->pagetitle</title>\n";
                $this->_head .= "<head>\n";
                $html = $this->_head;
                
                if (file_exists("Themes/$this->theme/header.html")) {
                    ob_start();
                    $smarty->template_dir = "Themes/$this->theme";
                    $smarty->compile_dir = "Themes/$this->theme/.compiled";
                    if (!file_exists($smarty->compile_dir)) {
                        mkdir($smarty->compile_dir, 0770);
                    }
                    $smarty->display('header.html');
                    $html .= ob_get_contents();
                    ob_end_clean();
                }
                              
                $html .= $body;
                
                if (file_exists("Themes/$this->theme/footer.html")) {
                    ob_start();
                    $smarty->template_dir = "Themes/$this->theme";
                    $smarty->compile_dir = "Themes/$this->theme/.compiled";
                    if (!file_exists($smarty->compile_dir)) {
                        mkdir($smarty->compile_dir, 0770);
                    }
                    $smarty->display('footer.html');
                    $html .= ob_get_contents();
                    ob_end_clean();
                }
                
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