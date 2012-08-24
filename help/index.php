<?php
$pageTitle = "Help";
$toWebRoot = "../";

### Build the Main-Object ###############################
include $toWebRoot."main.class.php";
$mainObj = new main($pageTitle, $toWebRoot);
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
      <h2>What is SeePHI?</h2>
      <p>SeePHI is a converter for regular expressions of the Seefeld-Convention to the PROSITE/PHI-BLAST-Nomenclature and back.</p>
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
