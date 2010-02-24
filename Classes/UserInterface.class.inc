<?php 
/**
 * UserInterface class
 *
 * @version 1.0
 * @copyright LightCube Solutions, LLC. 2010
 * @author LightCube Solutions <info@lightcubesolutions.com>
 */

class UserInterface {
    
    /**
     * statusMsg function
     * generates a status message based on input
     * depends on jquery and jquery-ui
     * 
     * @param string $msg
     * @param string $type
     * @param boolean $usesmarty
     * @return unknown_type
     */
    function statusMsg($msg, $type = 'ok', $usesmarty = true)
    {
        switch(strtolower($type)) {
           case 'ok':
               $icon = '<div class="ui-icon ui-icon-circle-check" style="float:left; margin-right: 10px;"></div>';
               $class= '$("#statusmsg").addClass("ui-state-highlight");';
               break;
           case 'error':
               $icon = '<div class="ui-icon ui-icon-alert" style="float:left; margin-right: 10px;"></div>';
               $class = '$("#statusmsg").addClass("ui-state-error");';
               break;
        }

        $retval = "&nbsp;
        <script>
        $(document).ready(function(){
            function callback(){
                setTimeout(function(){
                    $('#statusmsg').fadeOut();
                }, 3000);
            };
            $class
            $('#statusmsg').html('$icon<div>$msg</div>');
            $('#statusmsg').show('highlight',500,callback);
           });
        </script>
        ";
        
        if ($usesmarty) {
            global $smarty;
            $smarty->assign('statusmsg', $retval);
        } else {
            echo $retval;
        }
    }
}

?>