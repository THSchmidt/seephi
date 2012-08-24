
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="padding:5px">Email:</td>
	<td style="padding:5px"><input name="registform_email1" type="text" value="<?php if(isset($_POST["registform_email1"])) { echo $_POST["registform_email1"]; } ?>" size="12" /></td>
  </tr>
  <tr>
    <td style="padding:5px">Confirm Email:</td>
	<td style="padding:5px"><input name="registform_email2" type="text" value="<?php if(isset($_POST["registform_email2"])) { echo $_POST["registform_email2"]; } ?>" size="12" /></td>
  </tr>
  <tr>
    <td style="padding:5px; vertical-align:top">Registration Key:</td>
	<td style="padding:5px">
	  <img src="<?php echo $this->mainObj->imgPath."numgen/".$registKey.".jpg"; ?>" alt="Registration Key" /><br />
	  <input name="registform_key" type="text" size="5" maxlength="5" />
	</td>
  </tr>
  <tr>
    <td colspan="2"><input name="registform_submit" type="submit" value="Register (Step 1)" />
	  <input name="registform_id" type="hidden" value="<?php echo $registKey; ?>" /></td>
  </tr>
</table>
</form>

