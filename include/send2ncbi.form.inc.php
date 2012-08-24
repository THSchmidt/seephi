
<br /><hr />

<form action="http://www.ncbi.nlm.nih.gov/blast/Blast.cgi" method="get" target="_blank">
<table border="0" cellpadding="0" cellspacing="0" style="border:2px solid #000000; width:400px">
  <tr>
    <td colspan="2" class="winhead">Do NCBI PHI-BLAST</td>
  </tr>
  <tr>
    <td colspan="2" style="background-color:#DDDDDD; padding:5px; text-align:center">
      <input type="hidden" name="CMD" value="Web" />
      <input type="hidden" name="PAGE" value="Proteins" />
      <input type="hidden" name="PHI_PATTERN" value="<?php echo implode("-", $this->translated); ?>" />
      <input type="hidden" name="PROGRAM" value="blastp" />
      <input type="hidden" name="BLAST_PROGRAMS" value="phiBlast" />
      <input type="submit" name="PHIBLAST" value="Go to NCBI" />
    </td>
  </tr>
</table>
</form>

<br />
