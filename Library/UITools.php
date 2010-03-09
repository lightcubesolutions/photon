<?php 
/**
 * UITools class
 *
 * @version 1.0-a
 * @copyright LightCube Solutions, LLC. 2010
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @license http://www.lightcubesolutions.com/LICENSE
 */
class UITools
{    
    /**
     * statusMsg function
     * generates a status message based on input
     * depends on jquery and jquery-ui
     * 
     * @param string $msg
     * @param string $type
     * @param boolean $render
     * @return unknown_type
     */
    function statusMsg($msg, $type = 'ok', $render = true)
    {
        switch(strtolower($type)) {
           case 'ok':
               $icon = '<div class="ui-icon ui-icon-circle-check" style="float:left; margin-right: 10px;"></div>';
               $class= 'ui-state-highlight';
               break;
           case 'error':
               $icon = '<div class="ui-icon ui-icon-alert" style="float:left; margin-right: 10px;"></div>';
               $class = 'ui-state-error';
               break;
        }

        $retval = "&nbsp;
        <script>
        $(document).ready(function(){
            function callback(){
                setTimeout(function(){
                    $('#statusmsg').hide('slide',500).fadeOut()
                }, 3000);
            };
            $('#statusmsg').addClass('$class');
            $('#statusmsg').html('$icon<div>$msg</div>');
            $('#statusmsg').show('slide',500,callback);
           });
        </script>
        ";
        
        // Render through the view object?
        if ($render) {
            global $view;
            $view->assign('statusmsg', $retval);
        } else {
            echo $retval;
        }
    }
}

?>