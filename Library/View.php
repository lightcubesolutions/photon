<?php
require('Smarty/Smarty.class.php');

/**
 * View Class
 *
 * @extends Smarty
 */
class View extends Smarty
{
   function __construct()
   {
        $this->Smarty();
        $this->template_dir = '.';
        $this->compile_dir  = 'Smarty/compiled';
        $this->config_dir   = 'Smarty/configs';
   }
}
?>