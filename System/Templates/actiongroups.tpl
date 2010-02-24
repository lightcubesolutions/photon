{include file='System/Templates/header.tpl'}
     <div>
        <h3>Add a ActionGroup</h3>
         <form action="?{$thisaction}" method="post" enctype="multipart/form-data">
             <table>
                 <tr>
                     <td><label>Action Group Name</label></td>
                     <td><label>Action Parent Group</label></td>
                     <td><label>Enabled</label></td>
                     <td><label>Actions</label></td>
                </tr>
                <tr>
                     <td><input type="text" name="GroupName" value="" /></td>
                     <td>
                          <select name="ParentGroupID"/>
                                {foreach from=$groups item="row" key=k  name="row"}
                                     <option>{$row.GroupName}</option>
                                {/foreach}
                          </select>
                     </td>
                     <td><input type="checkbox" name="IsEnabled" value="1" /></td>
                     <td>
                         <select name="Actions[]" multiple size=5/>
                              {foreach from=$actions item="row" key=k  name="row"}
                                    <option value={$row.ActionName}>{$row.ActionName}</option>
                               {/foreach}
                         </select>
                     </td>
                </tr>
            </table>
            <p>
                <input class="input_button" type="submit" name="add" value="Add" />
                <input class="input_button" type="reset" value="Reset" />
            </p>
        </form>
    </div>
    <div style="margin-top: 10px;">
        <h3>All Groups</h3>
        {literal}
        <script type="text/javascript">
        var oTable;
        $(document).ready(function(){
            $('#results tbody tr').hover( function() {
                $(this).addClass('highlighted');
            }, function() {
                $(this).removeClass('highlighted');
            });
            oTable = $("#results").dataTable({
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
                       <th>Action Group</th>
                       <th>Parent Action Group</th>
                       <th>IsEnabled</th>
                       <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody class='scrollContent'>
                  {foreach from=$groups item="row" key=k  name="row"}
                       <td>{$row.GroupName}</td>
                       <td>{$row.ParentGroupID}</td>
                       <td>{$row.IsEnabled}</td>
                       <td>{foreach from=$row.Actions item="arow"}{$arow}<br>{/foreach}</td>
                    </tr>
                  {/foreach}
             </tbody>
         </table>
        </div>
     </div>
{include file='System/Templates/footer.tpl'}
