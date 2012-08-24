<?php
class htmlhead {
	var $dbObj;
	var $htmlHeader;

	function htmlhead($dbObj, $pageTitle, $cssFile, $toWebRoot) {
		$this->dbObj = $dbObj;

		$this->htmlHeader = $this->get_doctype();
		$this->htmlHeader .= "<head>\n";
		$this->htmlHeader .= $this->get_metadata();
		$this->htmlHeader .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"".$cssFile."\" />\n";
		$this->htmlHeader .= "  <link rel=\"shortcut icon\" href=\"".$toWebRoot."favicon.ico\" />\n";
		$this->htmlHeader .= "  <title>".$pageTitle."</title>\n";
		$this->htmlHeader .= "</head>\n\n";
	}

	function get_doctype() {
		$docType = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$docType .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$docType .= "        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n\n";
		$docType .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
		return $docType;
	}

	function get_metadata() {
		$metaData = "";
	
		$sqlCmd = "SELECT * FROM ".$this->dbObj->dbTables["metadata"];
		$allMetadata = $this->dbObj->sql_query($sqlCmd);
		while($metaTag = mysql_fetch_array($allMetadata)) {
		    $metaData .= "  <meta ".$metaTag["metakey"]." "."".$metaTag["metastring"]." />\n";
		}
		return $metaData;
	}
}
?>
