<?php
$pageTitle = "Validator";
$toWebRoot = "../";

### Build the Main-Object ###############################
include $toWebRoot."main.class.php";
$mainObj = new main($pageTitle, $toWebRoot);
#########################################################

### Build the Main-Object ###############################
include "validator.class.php";
$validObj = new validator($mainObj, "p2s2p"); # p2s2p or s2p2s
#########################################################

$mainObj->print_htmlhead();
?>

<body style="background-color:#CCCCCC">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td>&nbsp;</td>
	<td class="centerframe">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td colspan="2" style="width:800px">
<?php $mainObj->print_banner(); ?>
    </td>
  </tr>
  <tr>
    <td class="menu_left">&nbsp;</td>
    <td class="menuline">
<?php $mainObj->print_menu(); ?>
    </td>
  </tr>
  <tr>
    <td class="sidebarbgd">&nbsp;
<?php $mainObj->print_loginstatus(); ?>
      &nbsp;
    </td>
    <td class="maincontent">
      <h1><?php echo $pageTitle; ?></h1>
<?php $validObj->multi_test(500); ?>
    </td>
  </tr>
  <tr>
    <td class="version">
<?php $mainObj->print_version(); ?>
    </td>
    <td class="footline">
<?php $mainObj->print_footline(); ?>
    </td>
  </tr>
</table>

    </td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php
$mainObj->clicklog_end();
?>

</body>
</html>
