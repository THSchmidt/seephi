<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td>Confirmation Password:</td>
	<td><input name="confirmform_passw" type="password" /></td>
  </tr>
  <tr>
    <td>New Password:</td>
	<td><input name="confirmform_newpassw1" type="password" /></td>
  </tr>
  <tr>
    <td>Confirm new Password:</td>
	<td><input name="confirmform_newpassw2" type="password" /></td>
  </tr>
  <tr>
    <td colspan="2"><input name="confirmform_submit" type="submit" value="Confirm Registration (Step 2)" />
	  <input name="confirmform_key" type="hidden" value="<?php echo $confirmKey; ?>" /></td>
  </tr>
</table>
</form>
