       <form action="javascript:login()" method="post" >
         <fieldset>
           <input type="hidden" id="key" value="{$key}" />
           <ul>
             <li id="login_status" class="nolog">Welcome! Please Log in.</li>
             <li class="text"><label>Username: </label><input type="text" id="user" tabindex="1" size="15" /></li>
             <li class="text"><label>Password: </label><input type="password" id="pass" tabindex="2" size="15" /></li>
             <li><input type="submit" value="Login" tabindex="3" id="subbtn" title="Log in Now"/></li>
           </ul>
         </fieldset>
        </form>
