<?php
class validator {
    var $mainObj;
    var $validMode = "s2p2s";

    var $aminoAcids = array("A","B","C","D","E","F","G","H","I","K","L","M","N","P","Q","R","S","T","U","V","W","x","Y","Z");
    var $seefeldClasses = array("%","@","&","[+]","[-]","#","~");
    var $prositeClasses = array("FILMVWY","FWY","DEHKNQRST","HKR","DE","ILMV","AGPS");


    function validator($mainObj, $validMode) {
        $this->mainObj = $mainObj;
        $this->validMode = $validMode;
    }


    function multi_test($numOfTests) {
        $csvOutput = "#Test-ID;Test-Sequence;TranslStep1;TranslStep2;Length-Str1;Length-Str2;Length-Difference;Length-Difference_Percent;Similarity;Levenshtein-Distance;Levenshtein_Percent\n";

        if(isset($_POST["clear"])) {
            $_SESSION['testResults'] = NULL;
            echo "<h3>Cleared cache...</h3>";
            echo "<form method=\"POST\" action=\"".$_SERVER['PHP_SELF']."\">\n";
            echo "  <input type=\"submit\" name=\"step1\" value=\"Step 1\" />\n";
            echo "</form>\n";
            return;
        }

        for($i=1; $i<=$numOfTests; $i++) {
            echo "<h2>Test $i</h2>";
            if(!isset($_POST["step2"])) {
                $_SESSION['testResults'][$i] = $this->do_teststep1($i);
            }
            elseif(isset($_POST["step2"])) {
                $result = $this->do_teststep2($i);
                $csvOutput .= $i.";".$result["testSeq"].";".$result["translStep1"].";".$result["translStep2"].";".$result["similarity"]["lenstr1"].";".$result["similarity"]["lenstr2"].";".$result["similarity"]["lendiff"].";".$result["similarity"]["lendiffpercent"].";".$result["similarity"]["percent"].";".$result["similarity"]["levenshtein"].";".$result["similarity"]["levenshteinpercent"]."\n";
            }
            echo "<br />&nbsp;<hr />\n";
        }

        if(!isset($_POST["step2"])) {
            echo "<form method=\"POST\" action=\"".$_SERVER['PHP_SELF']."\">\n";
            echo "  <input type=\"submit\" name=\"step2\" value=\"Go to Step 2...\" />\n";
            echo "</form>\n";
        }
        elseif(isset($_POST["step2"])) {
            $oFilename = "valid_output/".$this->validMode.".csv";
            $fHandle = fopen($oFilename, "w");
                       flock($fHandle, 2);
                       fputs($fHandle, $csvOutput);
                       flock($fHandle, 3);
                       fclose($fHandle);
        }
        echo "<form method=\"POST\" action=\"".$_SERVER['PHP_SELF']."\">\n";
        echo "  <input type=\"submit\" name=\"clear\" value=\"Clear Cache\" />\n";
        echo "</form>\n";
    }



    function do_teststep1($testID) {
        $results = array();
        $outSeparator = "&thinsp;";

        if($this->validMode == "p2s2p") {
            ### Build sequence ##########################
            $results["testSeq"] = $this->build_prositeseq();
            echo "<h3>PROSITE-Testsequence</h3>\n";
            echo "<code>".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["testSeq"], 87, "\n")))."</code>";
            #############################################

            ### Translation: first step #################
            $messages = array("error" => array(),
                              "warn" => array(),
                              "select" => array());

            include_once "../include/p2s.inc.php";
            $motif = clean_pattern($results["testSeq"]);
            $results["translStep1"] = implode("", detect_pattern("Test $testID Step 1", $motif, "p2s", "", $messages));
            echo "<h3>Translated Seefeld pattern</h3>\n";
            echo "<code>".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["translStep1"], 87, "\n")))."</code>";
            #############################################
        }
        elseif($this->validMode == "s2p2s") {
            ### Build sequence ##########################
            $results["testSeq"] = $this->build_seefeldseq();
            echo "<h4>Seefeld-Testsequence</h4>\n";
            echo "<code>".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["testSeq"], 87, "\n")))."</code>";
            #############################################

            ### Translation: first step #################
            include_once "../include/s2p.inc.php";
            $results["translStep1"] = implode("-", detect_pattern($results["testSeq"]));
            echo "<h3>Translated PROSITE pattern</h3>\n";
            echo "<code>".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["translStep1"], 87, "\n")))."</code>";
            #############################################
        }
        return $results;
    }



    function do_teststep2($testID) {
        $results = array("testSeq" => $_SESSION["testResults"][$testID]["testSeq"],
                         "translStep1" => $_SESSION["testResults"][$testID]["translStep1"]);
        $outSeparator = "&thinsp;";

        if($this->validMode == "p2s2p") {
            ### Build sequence ##########################
            echo "<h3>PROSITE-Testsequence</h3>\n";
            echo "<code style=\"color:#FF0000\">".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["testSeq"], 87, "\n")))."</code>";
            #############################################

            ### Translation: first step #################
            echo "<h3>Translated Seefeld pattern</h3>\n";
            echo "<code>".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["translStep1"], 87, "\n")))."</code>";
            #############################################

            ### Translation: second step ################
            include_once "../include/s2p.inc.php";
            $results["translStep2"] = implode("-", detect_pattern($results["translStep1"]));
            echo "<h3>Re-Translated PROSITE pattern</h3>\n";
            echo "<code style=\"color:#FF0000\">".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["translStep2"], 87, "\n")))."</code>";
            #############################################

            ### Similarity ##############################
            $similarity = $this->str_compare($results["testSeq"], $results["translStep2"]);
            $results["similarity"] = $similarity;
            echo "<h3>Similarity between the PROSITE patterns</h3>\n";
            echo "<div>Length-Difference: ".$similarity["lendiff"]."</div>";
            echo "<div>Similarity: ".$similarity["percent"]."%</div>";
            echo "<div>Levenshtein-Distance: ".$similarity["levenshtein"]." (".$similarity["levenshteinpercent"]."%)</div>";
            #############################################
        }
        elseif($this->validMode == "s2p2s") {
            ### Build sequence ##########################
            $testSeq = $this->build_seefeldseq();
            echo "<h3>Seefeld-Testsequence</h3>\n";
            echo "<code style=\"color:#FF0000\">".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["testSeq"], 87, "\n")))."</code>";
            #############################################

            ### Translation: first step #################
            echo "<h3>Translated PROSITE pattern</h3>\n";
            echo "<code>".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["translStep1"], 87, "\n")))."</code>";
            #############################################

            ### Translation: second step ################
            $messages = array("error" => array(),
                              "warn" => array(),
                              "select" => array());

            include_once "../include/p2s.inc.php";
            $motif = clean_pattern($results["translStep1"]);
            $results["translStep2"] = implode("", detect_pattern("Test $testID Step 2", $motif, "p2s", "", $messages));
            echo "<h3>Translated Seefeld pattern</h3>\n";
            echo "<code style=\"color:#FF0000\">".preg_replace("/\n/", $outSeparator, htmlentities(chunk_split($results["translStep2"], 87, "\n")))."</code>";
            #############################################

            ### Similarity ##############################
            $similarity = $this->str_compare($results["testSeq"], $results["translStep2"]);
            $results["similarity"] = $similarity;
            echo "<h3>Similarity between the Seefeld patterns</h3>\n";
            echo "<div>Length-Difference: ".$similarity["lendiff"]."</div>";
            echo "<div>Similarity: ".$similarity["percent"]."%</div>";
            echo "<div>Levenshtein-Distance: ".$similarity["levenshtein"]." (".$similarity["levenshteinpercent"]."%)</div>";
            #############################################
        }
        return $results;
    }



    function str_compare($str1, $str2) {
        $similarity = array();

        similar_text($str1, $str2, $percent);
        $similarity["percent"] = sprintf("%01.2f", $percent);
        $similarity["levenshtein"] = levenshtein($str1, $str2);
        $similarity["levenshteinpercent"] = sprintf("%01.2f", 100-(100*$similarity["levenshtein"]/strlen($str1)));
        $similarity["lenstr1"] = strlen($str1);
        $similarity["lenstr2"] = strlen($str2);
        $similarity["lendiff"] = $similarity["lenstr2"] - $similarity["lenstr1"];
        $similarity["lendiffpercent"] = sprintf("%01.2f", $similarity["lendiff"]*100/$similarity["lenstr1"]);

        return $similarity;
    }



    function build_prositeseq() {
        $prositeArray = array();

        srand(microtime()*1000000);
        $seqLen = rand(5,30);

        for($i=0; $i<$seqLen; $i++) {
            srand(microtime()*1000000);
            $addThis = rand(0,5);

            switch($addThis) {
                case 0:
                    ### Add a single amino acid #########
                    array_push($prositeArray, $this->get_randomaa());
                    #####################################
                    break;
                case 1:
                    ### Add an amino acid class #########
                    srand(microtime()*1000000);
                    $tmpStr = chunk_split($this->prositeClasses[rand(0,(count($this->prositeClasses)-1))],1,":");
                    $tmpArray = explode(":", $tmpStr);
                    shuffle($tmpArray);
                    array_push($prositeArray, "[".implode("", $tmpArray)."]");
                    #####################################
                    break;
                case 2:
                    ### Add a set of amino acids ########
                    $tmpArray = array();
                    srand(microtime()*1000000);
                    $howMany = rand(2,10);
                    for($j=0; $j<$howMany; $j++) {
                        array_push($tmpArray, $this->get_randomaa());
                    }
                    $tmpArray = array_unique($tmpArray);
                    array_push($prositeArray, "[".implode("", $tmpArray)."]");
                    #####################################
                    break;
                case 3:
                    ### Add an inverse set of AAs #######
                    $tmpArray = array();
                    srand(microtime()*1000000);
                    $howMany = rand(1,10);
                    for($j=0; $j<$howMany; $j++) {
                        array_push($tmpArray, $this->get_randomaa());
                    }
                    $tmpArray = array_unique($tmpArray);
                    array_push($prositeArray, "{".implode("", $tmpArray)."}");
                    #####################################
                    break;
                case 4:
                    ### The same AA behind e.o. #########
                    srand(microtime()*1000000);
                    $howMany = rand(1,5);
                    $randAA = $this->get_randomaa();
                    array_push($prositeArray, $randAA."($howMany)");
                    #####################################
                    break;
                case 5:
                    ### More then one x behind e.o. #####
                    srand(microtime()*1000000);
                    $howMany = rand(1,5);
                    array_push($prositeArray, "x($howMany)");
                    #####################################
                    break;
            }
        }
        shuffle($prositeArray); # Randomize the whole array.

        ### Terminal postions ###########################
        if(preg_match("/^\[/", $prositeArray[0])) {
            $workStr = preg_replace("/[\[\]]/", "", $prositeArray[0]);
            srand(microtime()*1000000);
            $switch = rand(1,2);
            if($switch == 1) {
                $tmpStr = chunk_split($workStr,1,":");
                $splittedArray = explode(":", $tmpStr);
                array_push($splittedArray, "<");
                shuffle($splittedArray);
                $prositeArray[0] = "[".implode("", $splittedArray)."]";
            }
        }
        elseif(preg_match("/^\{/", $prositeArray[0])) {
            $workStr = preg_replace("/[\{\}]/", "", $prositeArray[0]);
            srand(microtime()*1000000);
            $switch = rand(1,2);
            if($switch == 1) {
                $tmpStr = chunk_split($workStr,1,":");
                $splittedArray = explode(":", $tmpStr);
                array_push($splittedArray, "<");
                shuffle($splittedArray);
                $prositeArray[0] = "{".implode("", $splittedArray)."}";
            }
        }

        srand(microtime()*1000000);
        $switch = rand(1,2);
        if($switch == 1) {
            $prositeArray[0] = "<".$prositeArray[0];
        }

        $lastElement = count($prositeArray)-1;
        if(preg_match("/\]$/", $prositeArray[$lastElement])) {
            $workStr = preg_replace("/[\[\]]/", "", $prositeArray[$lastElement]);
            srand(microtime()*1000000);
            $switch = rand(1,2);
            if($switch == 1) {
                $tmpStr = chunk_split($workStr,1,":");
                $splittedArray = explode(":", $tmpStr);
                array_push($splittedArray, ">");
                shuffle($splittedArray);
                $prositeArray[$lastElement] = "[".implode("", $splittedArray)."]";
            }
        }
        elseif(preg_match("/\}$/", $prositeArray[$lastElement])) {
            $workStr = preg_replace("/[\{\}]/", "", $prositeArray[$lastElement]);
            srand(microtime()*1000000);
            $switch = rand(1,2);
            if($switch == 1) {
                $tmpStr = chunk_split($workStr,1,":");
                $splittedArray = explode(":", $tmpStr);
                array_push($splittedArray, ">");
                shuffle($splittedArray);
                $prositeArray[$lastElement] = "{".implode("", $splittedArray)."}";
            }
        }

        srand(microtime()*1000000);
        $switch = rand(1,2);
        if($switch == 1) {
            $seefeldArray[$lastElement] = $prositeArray[$lastElement]."fc";
        }
        #################################################

//        echo "<code>".implode("-", $prositeArray)."</code>";
        return implode("-", $prositeArray);        
    }


    function build_seefeldseq() {
        $seefeldArray = array();

        srand(microtime()*1000000);
        $seqLen = rand(5,30);

        for($i=0; $i<$seqLen; $i++) {
            srand(microtime()*1000000);
            $addThis = rand(0,5);

            switch($addThis) {
                case 0:
                    ### Add a single amino acid #########
                    array_push($seefeldArray, $this->get_randomaa());
                    #####################################
                    break;
                case 1:
                    ### Add an amino acid class #########
                    srand(microtime()*1000000);
                    array_push($seefeldArray, $this->seefeldClasses[rand(0,(count($this->seefeldClasses)-1))]);
                    #####################################
                    break;
                case 2:
                    ### Add a set of amino acids ########
                    $tmpArray = array();
                    srand(microtime()*1000000);
                    $howMany = rand(2,10);
                    for($j=0; $j<$howMany; $j++) {
                        array_push($tmpArray, $this->get_randomaa());
                    }
                    $tmpArray = array_unique($tmpArray);
                    array_push($seefeldArray, "(".implode("/", $tmpArray).")");
                    #####################################
                    break;
                case 3:
                    ### Add an inverse set of AAs #######
                    $tmpArray = array();
                    srand(microtime()*1000000);
                    $howMany = rand(1,10);
                    for($j=0; $j<$howMany; $j++) {
                        array_push($tmpArray, $this->get_randomaa());
                    }
                    $tmpArray = array_unique($tmpArray);
                    array_push($seefeldArray, "^(".implode("/", $tmpArray).")");
                    #####################################
                    break;
                case 4:
                    ### The same AA behind e.o. #########
                    srand(microtime()*1000000);
                    $howMany = rand(1,5);
                    $randAA = $this->get_randomaa();
                    array_push($seefeldArray, str_pad($randAA, $howMany, $randAA));
                    #####################################
                    break;
                case 5:
                    ### More then one x behind e.o. #####
                    srand(microtime()*1000000);
                    $howMany = rand(1,5);
                    array_push($seefeldArray, str_pad("x", $howMany, "x"));
                    #####################################
                    break;
            }
        }
        shuffle($seefeldArray); # Randomize the whole array.

        ### Terminal postions ###########################
        if(preg_match("/^(\(|\^\()/", $seefeldArray[0])) {
            $workStr = preg_replace("/[\^\(\)]/", "", $seefeldArray[0]);
            srand(microtime()*1000000);
            $switch = rand(1,2);
            if($switch == 1) {
                $splittedArray = explode("/", $workStr);
                array_push($splittedArray, "fn");
                shuffle($splittedArray);
                $seefeldArray[0] = "(".implode("/", $splittedArray).")";
            }
        }

        srand(microtime()*1000000);
        $switch = rand(1,2);
        if($switch == 1) {
            $seefeldArray[0] = "fn".$seefeldArray[0];
        }

        $lastElement = count($seefeldArray)-1;
        if(preg_match("/\)$/", $seefeldArray[$lastElement])) {
            $workStr = preg_replace("/[\^\(\)]/", "", $seefeldArray[$lastElement]);
            srand(microtime()*1000000);
            $switch = rand(1,2);
            if($switch == 1) {
                $splittedArray = explode("/", $workStr);
                array_push($splittedArray, "fc");
                shuffle($splittedArray);
                $seefeldArray[$lastElement] = "(".implode("/", $splittedArray).")";

                srand(microtime()*1000000);
                $switch = rand(1,2);
                if($switch == 2) {
                    $seefeldArray[$lastElement] = "^".$seefeldArray[$lastElement];
                }
            }
        }

        srand(microtime()*1000000);
        $switch = rand(1,2);
        if($switch == 1) {
            $seefeldArray[$lastElement] = $seefeldArray[$lastElement]."fc";
        }
        #################################################

//        echo "<code>".implode("-", $seefeldArray)."</code>";
        return implode("", $seefeldArray);
    }


    function get_randomaa() {
        srand(microtime()*1000000);
        return $this->aminoAcids[rand(0,(count($this->aminoAcids)-1))];
    }
}
?>