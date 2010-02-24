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
            <tr>
                <td class='label'>Congregation ID:</td>
                <td><input type="text" name="CongregationID" value="{$userdata.CongregationID}" /></td>
                <td class='label'>City:</td>
                <td><input type="text" name="ContactCity" value="{$userdata.ContactCity}" /></td>
            </tr>
            <tr>
                <td class='label'>Circuit ID:</td>
                <td><input type="text" name="CircuitID" value="{$userdata.CircuitID}" /></td>
                <td class='label'>State:</td>
                <td><input type="text" name="ContactState" value="{$userdata.ContactState}" /></td>
            </tr>
            <tr>
                <td class='label'>Language ID:</td>
                <td><input type="text" name="LanguageID" value="{$userdata.LanguageID}" /></td>
                <td class='label'>Contact Phone:</td>
                <td><input type="text" name="ContactPhone" value="{$userdata.ContactPhone}" /></td>
            </tr>
            <tr>
                <td class='label'>Alternate Phone:</td>
                <td><input type="text" name="PhoneAlternate" value="{$userdata.PhoneAlternate}" /></td>
                <td class='label'>Interview Date:</td>
                <td><input type="text" name="InterviewDate" value="{$userdata.InterviewDate}" /></td>
            </tr>
            <tr>
                <td class='label'>Cell Phone:</td>
                <td><input type="text" name="PhoneCell" value="{$userdata.PhoneCell}" /></td>
                <td class='label'>Baptism Date:</td>
                <td><input type="text" name="BaptismDate" value="{$userdata.BaptismDate}" /></td>
            </tr>
            <tr>
                <td class='label'>Fax:</td>
                <td><input type="text" name="PhoneFax" value="{$userdata.PhoneFax}" /></td>
                <td class='label'>Volunteer ID:</td>
                <td><input type="text" name="VolunteerID" value="{$userdata.VolunteerID}" /></td>
            </tr>
            <tr>
                <td class='label'>Badge Number:</td>
                <td colspan='3'><input type="text" name="BadgeNum" value="{$userdata.BadgeNum}" /></td>
            </tr>
            <tr>
                <td class='label'>Notes:</td>
                <td colspan=3'><textarea name="Notes" rows='5' style="width: 90%">{$userdata.Notes}</textarea></td>
            </tr>
            <tr>
                <td colspan='4'>
                    <input type="hidden" name="LoginName" value="{$userdata.LoginName}" />
                    <input class="input_button" type="submit" name="update" value="Update" />
                    <input class="input_button" type="reset" value="Reset" />
                </td>
            </tr>
        </table>
    </form>
</div>