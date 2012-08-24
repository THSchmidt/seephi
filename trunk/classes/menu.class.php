<?php
class menu {
	var $htmlMenu;

	function menu($links, $direction) {
		if ($direction == "v") {
			return $this->get_menuvertical($links);
		}
		return $this->get_menuhorizontal($links);
	}


	function get_menuvertical($links) {
		$this->htmlMenu = "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		foreach ($links as $linkName => $linkValue) {
			$this->htmlMenu .= "  <tr>\n    <td><a href=\"".$linkValue."\">".$linkName."</a></td>\n  </tr>\n";
		}
		$this->htmlMenu .= "</table>\n";
	}


	function get_menuhorizontal($links) {
		$this->htmlMenu = "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$this->htmlMenu .= "  <tr>\n";
		foreach ($links as $linkName => $linkValue) {
			$this->htmlMenu .= "    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
			$this->htmlMenu .= "    <td><a href=\"".$linkValue."\"  class=\"menu\">".$linkName."</a></td>\n";
		}
		$this->htmlMenu .= "  </tr>\n";
		$this->htmlMenu .= "</table>\n\n";
	}
}
?>
