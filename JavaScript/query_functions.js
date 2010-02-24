var count = 0;

function swapCSS (id, bg) {
    var item = document.getElementById(id)
    item.style.background = bg
}

function hidePic (id, bg) {
    removePicBox();
    swapCSS(id,bg);
}

function removePicBox () {
    if (id = document.getElementById('picBox')) {
        id.style.display = 'none';
        document.getElementById('main').removeChild(id);
    }
}

function showPic (id, bg, name, img, e) {
    swapCSS(id,bg);
    var posx = 0;
    var posy = 0;
    if(!e) {
        e = window.event;
    }
    if (e.pageX || e.pageY)     {
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY)     {
        posx = e.clientX;
        posy = e.clientY + document.documentElement.scrollTop;
    }
    var div = document.createElement('div');
    div.className = 'picBox';
    div.id = 'picBox';
    div.innerHTML = ['<div>', name, '</div>', '<img src="', img, '" alt="Student Name" />'].join('');
    div.style.position='absolute';
    div.style.top=(posy-120)+'px';
    div.style.left=posx+'px';
    document.getElementById('main').appendChild(div);
}

function checkBetween(list) {
    var span = document.getElementById('endbetween');
    if (list.options[list.selectedIndex].value == "BTW") {
        span.disabled = false;
        span.style.display = 'inline';
    } else {
        span.disabled = true;
        span.style.display = 'none';
    }
}

function compareOptionText(a,b) {
  /*
   * return >0 if a>b
   *         0 if a=b
   *        <0 if a<b
   */
  // textual comparison
  return a.text!=b.text ? a.text<b.text ? -1 : 1 : 0;
  // numerical comparison
  //  return a.text - b.text;
}

function sortOptions(list) {
  var items = list.options.length;

  // create array and make copies of options in list
  var tmpArray = new Array(items);
  for ( i=0; i<items; i++ )
      tmpArray[i] = new Option(list.options[i].text,list.options[i].value);

  // sort options using given function
  tmpArray.sort(compareOptionText);

  // make copies of sorted options back to list
  for ( i=0; i<items; i++ )
      list.options[i] = new Option(tmpArray[i].text,tmpArray[i].value);
}

function removeFilter (id, val, txt) {
    var div = document.getElementById('current_filters');
    var opt = document.createElement('option');
    var button = document.getElementById('filter_button');
    div.removeChild(document.getElementById(id));
    if (div.childNodes.length == 0) {
        button.disabled = true;
    }
    if (val != null) {
        var select = document.getElementById('filter_types');
        opt.text = txt;
        opt.value = val;
        try {
            select.add(opt,null);
        }
        catch (e) {
            select.add(opt);
        }
        sortOptions(select);

    }
    var divs = document.getElementsByTagName('div');
    for (i=0; i<divs.length; i++) {
        if (divs[i].id.match('filterItem') != null) {
            firstdiv = divs[i].id;
            var sep = document.getElementById('SEPARATOR_'+firstdiv);
            if (sep != null)
                sep.value = '';
            return;
        }
    }
}

function buildFilter(val, obj) {
    // If the selection list has been automatically changed back to '0'
    // by this function, ignore and exit.
    if (obj.selectedIndex == 0) {
        return
    }

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null) {
        alert ("Your browser does not support AJAX!");
        return;
    }

    // Select the element we want to populate with the form.
    var div = document.getElementById("current_filters");
    var button = document.getElementById("filter_button");
    var sep = '';

    var newchild = document.createElement('div');
    newchild.id = "filterItem" + (count);
    newchild.className = 'filterItem';
    var end = "<div class=\"filterItemRight\">";
    end += "<img name=\"Remove\" src=\"Images/close.jpg\" ";
    end += "onmouseover=\"this.src='Images/closeover.jpg'\" ";
    end += "onmouseout=\"this.src='Images/close.jpg'\" ";
    end += "onmousedown=\"this.src='Images/closedown.jpg'\" ";
    end += "onclick=\"removeFilter('";
    end += newchild.id+"', '"+obj.options[obj.selectedIndex].value+"', '"+obj.options[obj.selectedIndex].text;
    end += "');\"></div><div class=\"filterItemBottom\"></div>";
    newchild.innerHTML = ["<div class=\"filterItemLeft\">Loading values from the server...</div>", end].join('');
    div.appendChild(newchild);

    var url="?a=getpvalues&param="+val;
    try {
        xmlHttp.open("POST",url,false);
        xmlHttp.send(null);
    } catch (e) {
        alert(e);
        removeFilter(newchild.id, null, null);
        obj.selectedIndex = 0;    
        return;
    }
    // Add a number to the end of the option name, so that we can do multiple options
    // of the same sort.
    //var newval = val + '_' + count;
    count++;
    //var reg = new RegExp(val, "g");
    //var param = xmlHttp.responseText.replace(reg, newval);
    var param = xmlHttp.responseText;
    var error = "<span id=\""+newchild.id+"error\" class=\"warning\"></span>";

    newchild.innerHTML = ["<div class=\"filterItemLeft\">", sep, param, error, "</div>", end].join('');
    button.disabled = false;
    obj.remove(obj.selectedIndex);
    obj.selectedIndex = 0;    
}

function verifyQueryData() {
   // Find all the input tags within the current_filters div
   var div = document.getElementById('current_filters');
   var subdivs = document.getElementsByTagName('div');
   var tbl = document.getElementById('tableName');
   var table;
   if (tbl.type == "hidden") {
       table = tbl.value;
   } else if (tbl.type == "select") {
       table = tbl.options[tbl.selectedIndex].value;
   }
   var querystr = '';
   var begin;
   for (c = 0; c < subdivs.length; c++) {
        if (subdivs[c].className != 'filterItem') {
            continue;
        }

        var inputs = subdivs[c].getElementsByTagName('input');
        var num = inputs.length;
        
        // For each input tag we find...
        for (i = 0; i < num; i++) {
        
            // We try to set 'e' as the span that was appended to the div in buildFilter()
            // for the purpose of displaying errors. This formula will not be true for all
            // input tags, but it will be true of the ones we want.
            try {
                e = document.getElementById(inputs[i].parentNode.parentNode.id+'error');
                e.innerHTML = '';
            } catch (err) {
                continue;
            }
        
            var n = inputs[i].name;
        
            // Whichever of the case statements return 0 is the one we've currently got.
            switch (0) {
                // BTW is a special field - handled already under CMP, just skip to the next one if we found it
                case n.search("BTW_"):
                    break;
                // Don't need Remove button values
                case n.search("Remove"):
                    break;
                // Radio and checkboxes, just make sure one item is selected.
                case inputs[i].type.search("checkbox"):
                case inputs[i].type.search("radio"):
                    var checked = false;
                    var multistr = '';
                    for (x = i ; x < num ; x++) {
                        if (inputs[x].name != n) {
                            continue;
                        }
                        if (inputs[x].checked) {
                            checked = true;
                            begin = (multistr == '') ? "'" : "|'";
                            multistr += begin + inputs[x].value + "'";
                        }
                    }
                    if (checked == false) {
                        e.innerHTML = '<p>You must select at least one item</p>';                    
                        return;
                    } else {
                        begin = (querystr == '') ? '' : '&';
                        querystr = [querystr, begin, inputs[i].type, '_', n, '=', multistr].join('');
                        i = (x);
                    }
                    break;
        
                // CMP is special in that it could possibly have two fields.
                // First verify the one and if that's good, verify the second.
                case n.search("CMP_"):
                    suf = n.substring(4);
                    s = document.getElementById('CMPOP_'+suf);
                    x = inputs[i].value;
                    if (x == null || x == "" || isNaN(x)) {
                        e.innerHTML = '<p>Field must be a number</p>';
                        inputs[i].style.border = '1px solid red';
                        return;
                    } else {
                        begin = (querystr == '') ? '' : '&';
                        querystr = [querystr, begin, n, '=', x, '&', s.id, '=', s.options[s.selectedIndex].value].join('');
                    }
                    if (s.options[s.selectedIndex].value == 'BTW') {
                        x = inputs[(i+1)].value;
                        if (x == null || x == "" || isNaN(x) || x <= inputs[i].value) {
                            e.innerHTML = '<p>Field must be a number greater than the one on the left</p>';
                            inputs[(i+1)].style.border = '1px solid red';
                            return;
                        } else {
                            begin = (querystr == '') ? '' : '&';
                            var nn = inputs[(i+1)].name;
                            querystr = [querystr, begin, nn, '=', x].join('');
                        }
                    } 
                    break;
                
                case inputs[i].type.search("text"):
                    s = document.getElementById('TXTOP_'+n);
                    t = s.options[s.selectedIndex].value;
                    begin = (querystr == '') ? '' : '&';
                    querystr = [querystr, begin, inputs[i].type, '_', n, '=', inputs[i].value, '&', s.id, '=', t].join('');
                    break;
        
                default:
                    begin = (querystr == '') ? '' : '&';
                    querystr = [querystr, begin, inputs[i].type, '_', n, '=', inputs[i].value].join('');
                    break;
            }
        }

        // Find any select tags in this div, except compare operators, which are already covered.
        var sel = subdivs[c].getElementsByTagName('select');
        for (i = 0; i < sel.length; i++) {
            if (sel[i].id.search('CMPOP_') != 0 && sel[i].id.search('TXTOP_') != 0) {
                begin = (querystr == '') ? '' : '&';
                querystr = [querystr, begin, '&select_', sel[i].name, '=', sel[i].options[sel[i].selectedIndex].value].join('');
            }
        }
        begin = (querystr == '') ? '' : '&';
        querystr = [querystr, begin, 'table=', table].join('');
    }
   
    // All the fields have valid data, so our query should be valid in turn.
    // Submit the form.
    loadAction('?a=runquery&'+querystr, 'resultsList', 1, 'Searching');
}
