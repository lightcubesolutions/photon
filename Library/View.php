<?php
/**
 * View Class
 *
 * @package photon
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license FIXME: Determine license
 */

class View
{
    public $smarty;
    public $template;
    public $usetidy = false;
    
    protected $vars;

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
     * display function
     * 
     * @return unknown_type
     */
    function display()
    {
        if ($this->smarty) {
            require('Smarty/Smarty.class.php');
            $smarty = new Smarty;
            $smarty->template_dir = '.';
            $smarty->compile_dir = 'Smarty/compiled';
            $smarty->config_dir = 'Smarty/configs';

            foreach ($this->vars as $key=>$val) {
                $smarty->assign($key, $val);
            }
            
            // Render the HTML via Smarty
            ob_start();
            $view->display($this->template);
            $html = ob_get_contents();
            ob_end_clean();
            
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