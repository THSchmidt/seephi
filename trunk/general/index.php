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
      <h1>Contact</h1>
      <div style="font-weight:bold">SeePHI</div>
      <div>Converter for regular expressions<br />(Seefeld-Convention &lt;-&gt; PHI-BLAST/PROSITE-Nomenclature)</div>

      <br />

      <h3>Author</h3>
      <table>
        <tr>
          <td colspan="2" style="font-weight:bold; padding:2px; vertical-align:top">Thomas Schmidt</td>
        </tr>
        <tr>
          <td colspan="2" style="padding:2px; vertical-align:top">
            PhD student<br />
            Computational Structural Biology<br />
            Department of Life Science Informatics<br />
            B-IT, LIMES-Institute<br />
            University of Bonn
          </td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Postal address:</td>
          <td style="padding:2px; vertical-align:top">Dahlmannstr. 2<br />
              DE-53113 Bonn</td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Email:</td>
          <td style="padding:2px; vertical-align:top">
            <a href="javascript:location='mailto:'+'schmi'+ /* Just click to the link... */ 'dt@bit.uni-bonn.de?subj' + 'ect=SeePHI - Contact'">schmidt<span style="display:none;">-Remove this Item-</span>@<span style="display:inline;">bit.uni-bonn.de</span>
          </td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Website:</td>
          <td style="padding:2px; vertical-align:top"><a href="http://www.fh-bingen.de/">http://csb.bit.uni-bonn.de/schmidt.html</a></td>
        </tr>
        <tr>
          <td style="padding:2px; vertical-align:top">Project Page:</td>
          <td style="padding:2px; vertical-align:top"><a href="http://www.fh-bingen.de/">http://code.google.com/p/seephi</a></td>
        </tr>
      </table>

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
