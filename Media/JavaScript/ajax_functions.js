/**
 * ajax_functions.js
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2009
 */

var xmlHttp;    // The XML HTTP Request Object
var divId;      // Contains the HTML DOM object which will be updated with the returned results
var timeout;	// Whether or not to build in a slight delay and show a status div.

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
    var m = document.getElementById('cfmodal');
    if (m != null && m != '') {
        setTimeout('removeModal()', 500);
    }
}

// Begin the XML HTTP Request and set the value of divId
// This is the function that should be called externally to send an AJAX request to a specific URL
function loadAction(url, domObj, modal, msg)
{
    if (modal != '' && modal != null && modal != false) {
        timeout = true;
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
}

function newModal (msg)
{
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
    d.id = 'cfmodal';
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
    if (msg != '' && msg != null) {
        var m = document.createElement('div');
        m.style.position = 'absolute';
        m.style.width = '200px';
        m.style.top = t + 50 + 'px';
        m.style.left = (w / 2) - 100 + 'px';
        m.style.textAlign = 'center';
        m.style.zIndex = '100002';
        m.style.fontSize = '1.2em';
        m.style.color = '#ffffff';
        m.innerHTML = msg;
        d.appendChild(m);
    }
    document.body.appendChild(d);
}

function removeModal()
{
    var m = document.getElementById('cfmodal');
    if (m != '' && m != null) {
        document.body.removeChild(m);
    }
}