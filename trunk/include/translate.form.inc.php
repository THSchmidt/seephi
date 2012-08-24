
<form action="<?php echo $this->mainObj->url."translate/index.php"; ?>" method="get">
<table border="0" cellpadding="0" cellspacing="0" style="border:2px solid #000000; width:400px">
  <tr>
    <td colspan="2" class="winhead">Translate</td>
  </tr>
  <tr>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left; vertical-align:top; width:50px">Title</td>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left">
	  <input type="text" name="title" size="20"<?php if(isset($this->title)) echo "value=\"".$this->title."\""; ?> />
	</td>
  </tr>
  <tr>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left; vertical-align:top; width:50px">Motif</td>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left">
	  <textarea cols="35" rows="3" name="motif"><?php if(isset($this->motif)) echo $this->motif; ?></textarea>
	</td>
  </tr>
  <tr>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left; vertical-align:top; width:50px">Mode</td>
    <td style="background-color:#DDDDDD; padding:8px; text-align:left">
<?php
        if(isset($this->mode) and $this->mode == "p2s") {
            echo "<input type=\"radio\" name=\"mode\" value=\"s2p\" /> Seefeld -&gt; PhiBLAST<br />
            <input type=\"radio\" name=\"mode\" value=\"p2s\" checked=\"checked\" /> PhiBLAST -&gt; Seefeld";
        }
        else {
            echo "<input type=\"radio\" name=\"mode\" value=\"s2p\" checked=\"checked\" /> Seefeld -&gt; PhiBLAST<br />
            <input type=\"radio\" name=\"mode\" value=\"p2s\" /> PhiBLAST -&gt; Seefeld";
        }
            "<input type=\"hidden\" name=\"login\" value=\"".$_SESSION['userid']."\" />
          </td>
        </tr>";
?>
  <tr>
    <td colspan="2" style="background-color:#DDDDDD; padding:5px; text-align:center">
      <input type="submit" name="translate" value="Translate" />
    </td>
  </tr>
</table>
</form>
