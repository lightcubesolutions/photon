<?php
require('Smarty/Smarty.class.php');

/**
 * MySmarty Class - Provide configuration settings for Smarty
 *
 * @extends Smarty
 */
class MySmarty extends Smarty
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