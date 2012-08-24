<?php
$pageTitle = "Contact/Imprint";
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
      <h1><a name="imprint" />Imprint</a></h1>
      <div style="font-weight:bold">SeePHI</div>
      <div>Converter for regular expressions<br />(Seefeld-Convention &lt;-&gt; PHI-BLAST/PROSITE-Nomenclature)</div>

      <br />

      <h3>Author</h3>
      <table>
        <tr>
          <td colspan="2" style="font-weight:bold; padding:2px; vertical-align:top">Thomas Schmidt</td>
        </tr>
        <tr>
          <td colspan="2" style="padding:2px; vertical-align:top">Student of Bioinformatics</td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Email:</td>
          <td style="padding:2px; vertical-align:top">
            <a href="javascript:location='mailto:'+'seep'+ /* Just click to the link... */ 'hi@fh-bingen.de?subj' + 'ect=SeePHI - Contact'">seephi<span style="display:none;">-Remove this Item-</span>@<span style="display:inline;">fh-bingen.de</span>
          </td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Postal address:</td>
          <td style="padding:2px; vertical-align:top">Richard-Wagner-Stra&szlig;e 2<br />
              DE-68165 Mannheim</td>
        </tr>
      </table>

      <br />

      <h3>FH Bingen - University of Applied Sciences</h3>
      <table>
        <tr>
          <td style="padding:2px; vertical-align:top">Postal address:</td>
          <td style="padding:2px; vertical-align:top">Berlinstra&szlig;e 107<br />
              DE-55411 Bingen am Rhein</td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Website:</td>
          <td style="padding:2px; vertical-align:top"><a href="http://www.fh-bingen.de/">http://www.fh-bingen.de/</a></td>
        </tr>
      </table>

      <br /><hr />

      <h1><a name="contact" />Contact</a></h1>
      <table>
        <tr>
          <td colspan="2" style="font-weight:bold; padding:2px; vertical-align:top">Thomas Schmidt</td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Email:</td>
          <td style="padding:2px; vertical-align:top">
            <a href="javascript:location='mailto:'+'thomas'+ /* Just click to the link... */ 's@fh-b' + 'ingen.de'">thomass<span style="display:none;">-Remove this Item-</span>@<span style="display:inline;">fh-bingen.de</span>
          </td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Website:</td>
          <td style="padding:2px; vertical-align:top"><a href="http://www.t-schmidt.name/">http://www.t-schmidt.name/</a></td>
        </tr>
      </table>

      <br />
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
