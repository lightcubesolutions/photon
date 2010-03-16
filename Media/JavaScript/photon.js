/**
 * photon.js
 * Common JavaScript functions for photon, e.g., AJAX controls and login.
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

var xmlHttp;    // The XML HTTP Request Object
var divId;      // Contains the HTML DOM object which will be updated with the returned results
var timeout;	// Whether or not to build in a slight delay and show a status div.
var ajaxcount = 0;

// Create a new XML HTTP Request Object fitting the current browser and assign it to the xmlHttp variable.
function GetXmlHttpObject()
{
    xmlHttp=null;
    try {
        // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    }
    catch (e) {
        //Internet Explorer
        try {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}

/// Monitor the XML HTTP Request Object state
/** When the object state has changed, analyze to see if there are parsable results
 *  and display them in the DOM object referenced in divId. */
function stateChanged()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
	    if (divId != null) {
	        if (timeout == true) {
	            setTimeout('processReturn()', 500);
	        } else {
	            processReturn();
	        }
	    }
	}
}

function processReturn()
{
    var d = document.getElementById(divId);
    var x = d.getElementsByTagName('script');
    if (d != null) {
        d.innerHTML = xmlHttp.responseText;
        if (x != '' && x != null) {
        	for(var i=0;i<x.length;i++) {
        		eval(x[i].text);
        	}
        }
    }
    ajaxcount--;
    if (ajaxcount == 0) {
    var m = document.getElementById('pmodal');
	    if (m != null && m != '') {
	        setTimeout('removeModal()', 300);
	    }
    }
}

// Begin the XML HTTP Request and set the value of divId
// This is the function that should be called externally to send an AJAX request to a specific URL
function loadAction(url, domObj, modal, msg)
{
    if (modal != '' && modal != null && modal != false) {
        newModal(msg);
    } else {
        timeout = false;
    }
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null) {
        alert ("Browser does not support HTTP Request");
        return;
    }
    divId = domObj;
    xmlHttp.onreadystatechange=stateChanged;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
    ajaxcount++;
}

function newModal (msg)
{
    var m = document.getElementById('pmodal');
    if (m != '' && m != null) {
    	if (msg != '' && msg != null) {
    		var mm = document.getElementById('pmodal_msg');
    		if (mm != '' && mm != null) {
    			mm.innerHTML = msg;
    		}
    	}
    } else {
	
	    if (typeof window.innerWidth != 'undefined') {
	        var h = window.innerHeight;
	        var w = window.innerWidth;
	    } else {
	        var h = document.documentElement.clientHeight;
	        var w = document.documentElement.clientWidth;
	    }
	    var t = (h / 2) - 15;
	    var l = (w / 2) - 15;
	    var i = document.createElement('img');
	    i.style.position = 'absolute';
	    i.style.top = t + 'px';
	    i.style.left = l + 'px';
	    i.style.backgroundColor = '#000000';
	    i.style.zIndex = '100001';
	    i.src='Media/Images/ajax-loader.gif';
	    var d = document.createElement('div');
	    d.id = 'pmodal';
	    d.style.position = 'fixed';
	    d.style.top = '0px';
	    d.style.left = '0px';
	    d.style.width = w +'px';
	    d.style.height = h +'px';
	    d.style.backgroundColor = '#000000';
	    d.style.opacity = 0.7;
	    d.style.filter = 'alpha(opacity=70)';
	    d.style.zIndex = '100000';
	    d.appendChild(i);
        var m = document.createElement('div');
        m.style.position = 'absolute';
        m.id = 'pmodal_msg';
        m.style.width = '200px';
        m.style.top = t + 50 + 'px';
        m.style.left = (w / 2) - 100 + 'px';
        m.style.textAlign = 'center';
        m.style.zIndex = '100002';
        m.style.fontSize = '1.2em';
        m.style.color = '#ffffff';
        d.appendChild(m);
		if (msg != '' && msg != null) {
	        m.innerHTML = msg;
		}
	    document.body.appendChild(d);
    }
}

function removeModal()
{
    var m = document.getElementById('pmodal');
    if (m != '' && m != null) {
        document.body.removeChild(m);
    }
}

function login() {
	var p = hex_sha1(document.getElementById("pass").value);
	var k = document.getElementById("key").value;
	var h = hex_sha1(k+p);
	var u = document.getElementById("user").value;
	loadAction("?a=login&h="+h+"&u="+u, "login_status", true);
}

function submitForm(task, id, formName)
{
	$("#"+formName).hide();	
	jQuery.each(['input','select','textarea'], function(){
		$("#"+id).find(''+this).each(function(){
			if (this.type != 'button') {
				var clone = $(this).clone();
				clone.val($(this).val());
				clone.appendTo("#"+formName);
			}
		});
	});
	$("#"+formName).append($('<input type="hidden" name="'+task+'" value="'+task+'" />'));
	$("#"+formName).submit();
}