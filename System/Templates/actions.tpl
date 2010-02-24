{include file='System/Templates/header.tpl'}
<div>
    <p>The management of individual actions here is intended mainly for Developers.
    This allows a quick and easy way to configure the necessary data in the database to
    handle action mappings. Administrators will be more interested in assigning defined
    groups of actions (modules) to individual users or groups of users. This will be
    handled through a different interface.</p>
    <p>As we develop actions and action handlers, keep in mind that our setup and the
    tools we use does not require a 1:1:1 relationship between actions, handlers and
    template files. A single PHP handler could belong to several actions, since it can
    parse the value of the $_REQUEST['a'] field and handle accordingly. Templates can
    also be specified dynamically.</p>
	<div>
        <h3>Add a New Action</h3>
	    <form action="?{$thisaction}" method="post" enctype="multipart/form-data">
	        <table>
	            <tr>
			        <td><label>Action Name</label></td>
			        <td><label>Action Handler</label></td>
	                <td><label>Uses Smarty?</label></td>
	                <td><label>Enabled</label></td>
			    </tr>
			    <tr>
			    	<td><input type="text" name="ActionName" value="" /></td>
			    	<td><input type="text" name="Handler" value="" /></td>
			    	<td><input type="checkbox" name="IsSmarty" /></td>
			    	<td><input type="checkbox" name="IsEnabled" /></td>
			    </tr>
			    <tr>
			        <td colspan='4'>
		                <input class="input_button" type="submit" name="add" value="Add" />
		                <input class="input_button" type="reset" value="Reset" />			        
			        </td>
			    </tr>
	        </table>
	    </form>    
	</div>
	<div style="margin-top: 10px;">
        <h3>All Actions</h3>
		{literal}
	    <script type="text/javascript">
	    $(document).ready(function(){
	        $("#results").dataTable({
		        "bJQueryUI": true,
	            "sPaginationType": "full_numbers",
	            "bAutoWidth": false,
	            "bSortClasses": false
	        });
	    });
		</script>
		{/literal}
		
		<div id='qresults'>
			<table id='results'>
				<thead>
				  <tr>
					<th>Action</th>
					<th>Handler</th>
                    <th>Description</th>
					<th>Uses Smarty</th>
					<th>Enabled</th>
					<th></th>
				  </tr>
				</thead>
				<tbody>
                    {foreach from=$actions item="row" key=k  name="row"}
					<tr class='actioninfo' id='actioninfo_{$row.ActionName}'>
					   <form action="?{$thisaction}" method="post" enctype="multipart/form-data">
						<td><input type='text' name='ActionName' value='{$row.ActionName}' /></td>
                        <td><input type='text' name='Handler' value='{$row.Handler}' /></td>
                        <td><input type='text' name='Description' value='{$row.Description}' /></td>
						<td><input type='checkbox' name='IsSmarty' {if $row.IsSmarty == 1} checked="checked" {/if} /></td>
						<td><input type='checkbox' name='IsEnabled' {if $row.IsEnabled == 1} checked="checked" {/if} /></td>
						<td>
						  <input type='hidden' name='_id' value='{$row._id}' />
						  <input class='input_button' type='submit' name='update' value='Save' />&nbsp;
						  <input class='input_button' type='submit' name='del' value='Delete' />
						</td>
					   </form>
					</tr>
                    {/foreach}
				</tbody>
			</table>
		</div>
    </div>
</div>
{include file='System/Templates/footer.tpl'}
