
<form action="<?php echo $this->mainObj->url."translate/index.php"; ?>" method="get">
<table border="0" cellpadding="0" cellspacing="0" style="border:2px solid #000000; width:400px">
  <tr>
    <td colspan="2" class="winhead">Save as file</td>
  </tr>
  <tr>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left; vertical-align:top; width:50px">Format</td>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left">
      <input type="checkbox" name="oformat[]" value="ascii" /> ASCII File<br />
      <input type="checkbox" name="oformat[]" value="latex" /> LaTeX<br />
      <!-- <input type="checkbox" name="oformat[]" value="pdf" /> PDF<br /> -->
    </td>
  </tr>
  <tr>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left; vertical-align:top; width:50px">Filename</td>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left">
      <input type="text" name="ofilename" size="20"<?php if(isset($this->oFilename)) echo " value=\"".$this->oFilename."\""; ?> />
      <input type="hidden" name="title" value="<?php if(isset($this->title)) echo $this->title; ?>" />
      <input type="hidden" name="motif" value="<?php if(isset($this->motif)) echo $this->motif; ?>" />
      <input type="hidden" name="mode" value="<?php if(isset($this->mode)) echo $this->mode; ?>" />
      <input type="hidden" name="translated" value="<?php if(isset($this->translated)) echo implode("", $this->translated); ?>" />
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background-color:#DDDDDD; padding:5px; text-align:center">
      <input type="submit" name="saveas" value="Save as" />
    </td>
  </tr>
</table>
</form>
