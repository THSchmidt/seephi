<?php
$pageTitle = "Translate";
$toWebRoot = "../";

### Build the Main-Object ###############################
include $toWebRoot."main.class.php";
$mainObj = new main($pageTitle, $toWebRoot);
#########################################################

### Build the Translate-Object ##########################
include $mainObj->clsPath."translator.class.php";
$translateObj = new translator($mainObj);
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
<?php
if(isset($_GET["title"]) or isset($_GET["motif"])) {
    $translateObj->print_errors();
    $translateObj->print_motifpositions();
    $translateObj->print_warnings();
    $translateObj->print_selects();
    $translateObj->print_translatepositions();
    $translateObj->print_translated();
    $translateObj->save2file();
    $translateObj->print_convform();
    $translateObj->print_save2fileform();
    $translateObj->print_send2ncbiform();
}
else $translateObj->print_convform();
?>
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
