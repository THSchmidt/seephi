<?php
class sidebar {
	var $sidebarHeader;
	var $contentType;
	var $content;

	function sidebar($sidebarHeader, $contentType, $content) {
		$this->sidebarHeader = $sidebarHeader;
		$this->contentType = $contentType;
		$this->content = $content;
	}

	
	function print_sidebarelem() {
		echo "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "  <tr>\n";
		echo "    <td class=\"sidebarheader\">".$this->sidebarHeader."</td>\n";
		echo "  </tr>\n";

		echo "  <tr>\n";
		echo "    <td class=\"sidebarcontent\">\n";

		if($this->contentType == "include") {
			include $this->content;
		}
		else {
			echo $this->content;
		}

		echo "    </td>\n";
		echo "  </tr>\n";
		echo "</table>\n\n";
		return;
	}
}
?>
