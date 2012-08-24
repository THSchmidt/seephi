
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td style="font-size:11px; font-weight:bold; text-align:left">Username</td>
  </tr>
  <tr>
    <td style="font-size:11px"><?php echo $_SESSION["username"]; ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:11px; font-weight:bold; text-align:left">Registered since</td>
  </tr>
  <tr>
    <td style="font-size:11px"><?php echo $_SESSION["userregsince"]; ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:11px; font-weight:bold; text-align:left">Logins:
      <span style="font-weight:normal"><?php echo $_SESSION["userlogins"]; ?></span>
    </td>
  </tr>
</table>

