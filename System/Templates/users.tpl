{include file='System/Templates/header.tpl'}
{literal}
<script type="text/javascript">
    $(document).ready(function(){
        $(".accordion").accordion({active: 1, collapsible: true, autoHeight: false});
    });
</script>
{/literal}
<div class="accordion" style="margin: 10px 0;">
	<h3><a href="#">Add a New User</a></h3>
	<div>
	    <form action="?{$thisaction}" method="post" enctype="multipart/form-data">
	        <table>
	            <tr>
			        <td><label>Login Name</label><br /><input type="text" name="LoginName" value="" /></td>
			        <td><label>First Name</label><br /><input type="text" name="FirstName" value="" /></td>
	                <td><label>Last Name</label><br /><input type="text" name="LastName" value="" /></td>
	                <td><label>Password</label><br /><input type="password" name="Password" value="" /></td>
	                <td><label>Confirm Password</label><br /><input type="password" name="confirm" value="" /></td>
			    </tr>
	        </table>
	        <p>
	            <input class="input_button" type="submit" name="add" value="Add" />
	            <input class="input_button" type="reset" value="Reset" />
	            <input type="hidden" name="IsEnabled" value="on" />
	        </p>
	    </form>
	</div>
	<h3><a href="#">Find Users</a></h3>
	<div>
		<form id="filterform" action="javascript:verifyQueryData();" method="get">
		    <div><input id="tableName" type="hidden" value="test" /></div>
		    <fieldset class="filter" id="filter">
		        <legend>Add a Search Parameter:
		            <select id="filter_types" onChange="buildFilter(this.options[this.selectedIndex].value, this)">
		                <option></option>
		            </select>
		        </legend>
		        <div id="filterfield">
		            <div id="current_filters"></div>
		            <div id="filter_search">
		                <p>
		                  <input id="filter_button" class='input_button' type="button" value="Search" onclick="verifyQueryData();" />
		                  <span id="status"></span>
		                </p>
		            </div>
		        </div>
		    </fieldset>
		</form>
		{literal}
		<script type="text/javascript">
		    var div = document.getElementById("current_filters");
		    var button = document.getElementById("filter_button");
		    var tbl = document.getElementById('tableName');
		    if (div.childNodes.length == 0) {
		        button.disabled = true;
		    }
		    if (tbl.type == "hidden") {
		        loadAction('?a=setparams', 'filter_types', '');
		    }
		</script>
		{/literal}
		<div id="resultsList"></div>
		<div id="reset"></div>
	</div>        
</div>

{include file='System/Templates/footer.tpl'}
