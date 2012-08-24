
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td>Username:</td>
    <td style="padding-top:3px"><input name="login_username" type="text" value="<?php if(isset($_POST["login_username"])) { echo $_POST["login_username"]; } ?>" size="8" /></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td style="padding-top:3px"><input name="login_password" type="password" size="8" /></td>
  </tr>
  <tr>
    <td colspan="2" style="padding:5px"><input name="login_submit" type="submit" value="Login" /></td>
  </tr>
</table>
</form>

