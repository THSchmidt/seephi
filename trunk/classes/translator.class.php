<?php
class translator {
    var $mainObj;
    var $title = "";
    var $motif = "";
    var $mode = "s2p";
    var $login = "";

    var $oFilename;

    var $messages = array("error" => array(), # This is a two-dimensional array: $messages["warn"][0].
                          "warn" => array(),
                          "select" => array());

    var $translated = array();


    function translator($mainObj) {
        $this->mainObj = $mainObj;

        $this->check_parameters();

        if($this->messages["error"])
            return 0;

        if($this->mode == "s2p")
            $this->s2p();
        else $this->p2s();
    }



    function p2s() {
        include_once $this->mainObj->incPath."p2s.inc.php";
        $this->motif = clean_pattern($this->motif);
        $this->translated = detect_pattern($this->title, $this->motif, $this->mode, $this->login, $this->messages);
    }



    function s2p() {
        include_once $this->mainObj->incPath."s2p.inc.php";
        $this->translated = detect_pattern($this->motif);
    }



    function print_translated() {
        if(!empty($this->translated) and empty($this->messages["error"]) and empty($this->messages["select"]))
            print_translated($this->translated);
    }



    function print_motifpositions() {
        if(empty($this->messages["error"]))
            print_motifpositions($this->motif);
    }



    function print_translatepositions() {
        if(empty($this->messages["error"]) and empty($this->messages["select"])) {
            if($this->mode == "p2s")
                print_seefeldpositions($this->translated);
            else print_prositepositions($this->translated);
        }
    }



    function print_errors() {
        if(!empty($this->messages["error"]))
            echo implode("\n", $this->messages["error"]);
    }



    function print_warnings() {
        if(!empty($this->messages["warn"]))
            echo implode("\n", $this->messages["warn"]);
    }



    function print_selects() {
        if(!empty($this->messages["select"]))
            echo implode("\n", $this->messages["select"]);
    }



    function print_convform() {
        if(!empty($this->messages["error"]) or !empty($this->messages["warn"]) or !empty($this->messages["select"]))
            include_once $this->mainObj->incPath."translate.form.inc.php";
    }



    function print_save2fileform() {
        if(!empty($this->translated) and empty($this->messages["error"]) and empty($this->messages["select"]) and $this->mainObj->valid_login())
            include_once $this->mainObj->incPath."save2file.form.inc.php";
    }



    function print_send2ncbiform() {
        if(!empty($this->translated) and empty($this->messages["error"]) and empty($this->messages["select"]) and $this->mainObj->valid_login() and $this->mode == "s2p")
            include_once $this->mainObj->incPath."send2ncbi.form.inc.php";
    }



    function check_parameters() {
        if(isset($_GET["title"]) and !empty($_GET["title"]))
            $this->title = trim($_GET["title"]);
        else array_push($this->messages["error"], "<div class=\"error\">ERROR: Please set a title for your conversion.</div>");
        if(isset($_GET["motif"]) and !empty($_GET["motif"]))
            $this->motif = preg_replace("/\s/", "", $_GET["motif"]);
        else array_push($this->messages["error"], "<div class=\"error\">ERROR: Please set a motif for your conversion.</div>");
        if(isset($_GET["mode"]) and !empty($_GET["mode"]))
            $this->mode = $_GET["mode"];
        if(isset($_GET["output"]) and !empty($_GET["output"]))
            $this->output = $_GET["output"];
//        if(isset($_GET["login"]) and !empty($_GET["login"]))
//            $this->login = $_GET["login"];
        if(isset($_GET["saveas"]) and !empty($_GET["saveas"]))
            $this->saveas = $_GET["saveas"];
        if(isset($_GET["oformat"]) and !empty($_GET["oformat"]))
            $this->oFormat = $_GET["oformat"];
        if(isset($_GET["ofilename"]) and !empty($_GET["ofilename"]))
            $this->oFilename = $_GET["ofilename"];
        else $this->oFilename = $this->title;
    }



    function save2file() {
        $downloadFiles = array();
        if(isset($this->saveas) and !empty($this->translated) and empty($this->messages["error"]) and empty($this->messages["select"]) and $this->mainObj->valid_login()) {
            for($i=0; $i<2; $i++) {
                if(isset($this->oFormat[$i]) and $this->oFormat[$i] == "ascii")
                    array_push($downloadFiles, saveas_ascii($this->title, $this->motif, $this->translated, "/srv/www/vhosts/coniubix.com/subdomains/seephi/httpdocs/translate/odata/".$_SESSION["userid"]."/", $this->oFilename));
                if(isset($this->oFormat[$i]) and $this->oFormat[$i] == "latex")
                    array_push($downloadFiles, saveas_latex($this->title, $this->motif, $this->translated, "/srv/www/vhosts/coniubix.com/subdomains/seephi/httpdocs/translate/odata/".$_SESSION["userid"]."/", $this->oFilename));
//                if(isset($this->oFormat[$i]) and $this->oFormat[$i] == "pdf")
//                    array_push($downloadFiles, saveas_pdf($this->title, $this->motif, $this->translated, "/srv/www/vhosts/coniubix.com/subdomains/seephi/httpdocs/translate/odata/".$_SESSION["userid"]."/", $this->oFilename));
            }
        }

        if(isset($downloadFiles) and !empty($downloadFiles)) {
            echo "<h2>Your files...</h2>\n";
            foreach($downloadFiles as $currFile) {
                $fileLink = str_replace('/srv/www/vhosts/coniubix.com/subdomains/seephi/httpdocs/', $this->mainObj->url, $currFile);
                echo "<a href=\"$fileLink\">".basename($currFile)."</a><br />\n";
            }
            echo "<br />&nbsp;<hr />\n";
        }
    }
}
?>
