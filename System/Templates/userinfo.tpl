<div class="userdata">
    <p class="subheading">Profile for: {$userdata.LoginName}</p>
    <form action="?a=users" method="post" enctype="multipart/form-data">
	    <table style="width: 100%;">
            <tr>
                <td class='label'>First Name:</td>
                <td><input type="text" name="FirstName" value="{$userdata.FirstName}" /></td>
                <td class='label'>Application Date:</td>
                <td><input type="text" name="ApplicationDate" value="{$userdata.ApplicationDate}" /></td>
            </tr>
            <tr>
                <td class='label'>Last Name:</td>
                <td><input type="text" name="LastName" value="{$userdata.LastName}" /></td>
                <td class='label'>Date Of Birth:</td>
                <td><input type="text" name="DateOfBirth" value="{$userdata.DateOfBirth}" /></td>
            </tr>
            <tr>
                <td class='label'>Login Enabled:</td>
                <td><input type="checkbox" name="IsEnabled"
                {if $userdata.IsEnabled == 1}
                checked="checked"
                {/if} />
                </td>
                <td class='label'>Email Address:</td>
                <td><input type="text" name="Email" value="{$userdata.Email}" /></td>
            </tr>
            <tr>
                <td class='label'>Address:</td>
                <td><input type="text" name="Address1" value="{$userdata.Address1}" /></td>
                <td class='label'>Married</td>
                <td><input type="checkbox" name="Married" 
                {if $userdata.Married == 1}
                checked="checked" />
                {/if}
                </td>
            </tr>
            <tr>
                <td class='label'>City:</td>
                <td><input type="text" name="City" value="{$userdata.City}" /></td>
                <td class='label'>Gender:</td>
                <td>
                    <label>M</label><input type="radio" name="SEX" value="M" {if $userdata.SEX == 'M'}checked="checked"{/if} />
                    &nbsp; &nbsp;
                    <label>F</label><input type="radio" name="SEX" value="F" {if $userdata.SEX == 'F'}checked="checked"{/if} />
                </td>
            </tr>
            <tr>
                <td class='label'>State:</td>
                <td><input type="text" name="State" value="{$userdata.State}" /></td>
                <td class='label'>Contact Name:</td>
                <td><input type="text" name="ContactName" value="{$userdata.ContactName}" /></td>
            </tr>
            <tr>
                <td class='label'>Zip:</td>
                <td><input type="text" name="Zip" value="{$userdata.zip}" /></td>
                <td class='label'>Contact Relation:</td>
                <td><input type="text" name="ContactRel" value="{$userdata.ContactRel}" /></td>
            </tr>
            <tr>
                <td class='label'>Phone:</td>
                <td><input type="text" name="Phone" value="{$userdata.Phone}" /></td>
                <td class='label'>Contact Address:</td>
                <td><input type="text" name="ContactAddress1" value="{$userdata.ContactAddress1}" /></td>
            </tr>
        </table>
    </form>
</div>