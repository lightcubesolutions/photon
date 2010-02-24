<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
    <head>
        <title>{$title}</title>
        <meta name="Robots" content="noindex, nofollow"/>
        <link rel="Shortcut icon" href="Images/icon.ico"/>

		<link href="CSS/corestyles.css" rel="stylesheet" type="text/css"/>
		<!--[if IE 7]><link href="CSS/ie7styles.css" media="screen" rel="stylesheet" type="text/css"/><![endif]-->
		<!--[if lte IE 6]><link href="CSS/ie6styles.css" media="screen" rel="stylesheet" type="text/css"/><![endif]-->
		        
        {$login_script}
        <script type="text/javascript" src="JavaScript/ajax_functions.js"></script>
        <script type="text/javascript" src="JavaScript/sha1.js"></script>
        <script type="text/javascript" src="JavaScript/query_functions.js"></script>
        <script type="text/javascript" src="JavaScript/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="JavaScript/jquery-ui-1.7.2.custom.min.js"></script>
        <script type="text/javascript" src="JavaScript/jquery.colorbox-min.js"></script>
        <script type="text/javascript" src="JavaScript/jquery.dataTables.min.js"></script>
    </head>

	<body> 
		<div id="m_megawrap"> 
			<div id="m_headwrap"> 
				<h1 id="m_headlogo"><a href="#" title="Return to Home">photon</a></h1> 
				<div id="m_logwrap"> 
                    {if $loggedin}
                    {include file='System/Templates/logmenu.tpl'}
                    {else}
                    {include file='System/Templates/login.tpl'}
                    {/if}
				</div> 
			</div>
			<ul id="m_navwrap"> 
				<li id="home"><a href="?a=home" title="Return to Home">Home</a></li> 
			</ul> 
	        <div id="m_bodywrap">
	            <div id="m_contentwrap">
	               <div id="statusmsg" class="ui-widget-content ui-corner-all">{$statusmsg}</div>
