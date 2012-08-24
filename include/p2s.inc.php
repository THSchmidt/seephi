<?php
function clean_pattern($motif) {
    return preg_replace("/-{2,}/", "-", $motif);
}



function detect_pattern($title, $motif, $mode, $login, &$messagesRef) {
    $patternArray = split("-", strtoupper($motif));
    $seefeldPattern = array();
    $i = 0;

    ### All Classes of the Seefeld-Nomenclature (in alphabetical order) ############
    $seefeldClasses = array("FILMVWY" => "%",
                            "FWY" => "@",
                            "DEHKNQRST" => "&",
                            "HKR" => "[+]",
                            "DE" => "[-]",
                            "ILMV" => "#",
                            "AGPS" => "~");
    ################################################################################


    foreach($patternArray as $residue) {
        $selectionArray = $patternArray;

        if(preg_match ("/^<?[A-Z]>?$/", $residue)) { # If the current pattern is just a single amino acid...
            $residue = preg_replace("/</", "fn", $residue);
            $residue = preg_replace("/>/", "fc", $residue);
            if(preg_match ("/([^fncABCDEFGHIKLMNPQRSTUVWXYZ])/", $residue, $resi)) { # If Pattern (single Character) is not allowed in IUBMB...
                array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                              "There is an unknown Residue (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($resi[0],ENT_QUOTES)."</span>) on position ".($i+1)."!<br />".
                                              "Residue will be assumed.</p>");
                array_push($seefeldPattern, $residue); # ...assume although amino acid is not allowed (IUBMB).
            }
            else { # All of the other Characters (amino acids) are written directly in $seefeldPattern
                array_push($seefeldPattern, preg_replace("/X/", "x", $residue));
            }
        }
        elseif(preg_match ("/^<?([A-Z])\(([0-9]+)\)>?$/", $residue, $resi)) { # If pattern is something like: F(3) (-> FFF)
            $resiName = $resi[1];
            $resiNum = $resi[2];

            if(preg_match ("/([^<>ABCDEFGHIKLMNPQRSTUVWXYZ])/", $resiName, $resi) and $resiNum > 0) { #  If Pattern (single Character) is not allowed in IUBMB...
                array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                                    "There is an unknown Residue (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($resiName,ENT_QUOTES)."</span>) on position ".($i+1)."!<br />".
                                                    "Residue will be assumed.</p>");
                $howMany = str_pad($resiName, $resiNum, $resiName); # Example: J(3) -> JJJ
                if(preg_match("/^</", $residue))
                    $howMany = "fn".$howMany;
                if(preg_match("/>$/", $residue))
                    $howMany .= "fc";
                array_push($seefeldPattern, $howMany); # ...assume although this amino acid is not allowed (IUBMB).
            }
            elseif($resiName == 'X' and $resiNum > 0) {
                $howMany = str_pad("x", $resiNum, "x"); # Example: X(3) -> xxx
                if(preg_match("/^</", $residue))
                    $howMany = "fn".$howMany;
                if(preg_match("/>$/", $residue))
                    $howMany .= "fc";
                array_push($seefeldPattern, $howMany);
            }
            elseif($resiNum > 0) {
                $howMany = str_pad($resiName, $resiNum, $resiName); # Example: J(3) -> JJJ
                if(preg_match("/^</", $residue))
                    $howMany = "fn".$howMany;
                if(preg_match("/>$/", $residue))
                    $howMany .= "fc";
                array_push($seefeldPattern, $howMany);
            }
        }
        elseif(preg_match ("/^([A-Z])\(([0-9]+)\s*,\s*([0-9]+)\)$/", $residue, $resi)) { // Example: D(2,7).
            $resiName = $resi[1];
            $resiNumStart = $resi[2];
            $resiNumEnd = $resi[3];

            if($resiNumStart >= $resiNumEnd) { # If the first value is bigger than the second one... (Example: D(4,2)).
                array_push($messagesRef["error"], "<p><div style=\"font-weight:bold\">ERROR</div>".
                                                     "Syntax-Error on Motif-Position ".($i+1)." (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($residue)."</span>).<br />".
                                                     "The first number of range (".$resiNumStart.") must be smaller than the second number (".$resiNumEnd.").<br />".
                                                     "Please check your Input-Motif on this Position.</p>");
            }
            else {
                if(preg_match ("/^([^ABCDEFGHIKLMNPQRSTUVWXYZ])$/", $residue, $resi)) { #  If Pattern (single Character) is not allowed in IUBMB...
                    array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                                        "There is an unknown Residue (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($resiName, ENT_QUOTES)."</span>) on position ".($i+1)."!<br />".
                                                        "Residue will be assumed.</p>");
                }

                array_push($messagesRef["select"], "<p><div style=\"font-weight:bold\">SELECT</div>".
                                                      "Expression ".htmlentities($residue)." on Position ".($i+1)." can not be well-defined!<br />".
                                                      "Please select one of the following Expressions to proceed with:</p>");

                if($resiName == "X") # If Character is 'X'...
                   $resiUpLow = "x"; # ...set 'X' to 'x'.
                else $resiUpLow = $resiName;

                array_push($messagesRef["select"], "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"get\">\n".
                                                      "  <input type=\"hidden\" name=\"title\" value=\"".$title."\" />\n".
                                                      "  <input type=\"hidden\" name=\"mode\" value=\"".$mode."\" />\n".
//                                                      "  <input type=\"hidden\" name=\"phiblast\" value=\"".$this->phiblast."\" />\n".
                                                      "  <input type=\"hidden\" name=\"login\" value=\"".$login."\" />\n");

                for($j=$resiNumStart; $j<=$resiNumEnd; $j++) {
                    $selectionArray[$i] = $resiUpLow."(".$j.")"; # Replace on the actual Position in the temporary $selectionArray the current value through the current possibility
                    $selectOption = implode("-",$selectionArray);
                    $howMany = str_pad("", $j, $resiUpLow);
                    if($j == $resiNumStart) # Mark the first Radiobutton as 'checked'
                        $checked = " checked=\"checked\"";
                    else $checked = NULL;

                    array_push($messagesRef["select"], "\n  <input type=\"radio\" name=\"motif\" value=\"$selectOption\"".$checked." />".$resiUpLow."($j) -> ".$howMany."<br />");
                }

                array_push($messagesRef["select"], "  <input type=\"submit\" name=\"select\" value=\"Select\" />\n".
                                                "</form>\n");
            }
        }
        elseif(preg_match ("/^<?\[[A-Z0-9<>]+\]>?$/", $residue, $resi)) { # Expression in squared brackets...
            if(preg_match("/\[([ABCDEFGHIKLMNPQRSTUVWXYZ0-9])\]/", $residue, $resi)) { # If there´s just one Character in brackets...
                $tmpStr = $resi[1];
                if(preg_match("/^</", $residue))
                    $tmpStr = "fn".$tmpStr;
                if(preg_match("/>$/", $residue))
                    $tmpStr .= "fc";
                array_push($seefeldPattern, preg_replace("/X/", "x", $tmpStr)); # Example: -[D]- -> D
            }
            elseif(preg_match ("/\[([ABCDEFGHIKLMNPQRSTUVWXYZ0-9<>]{2,})\]/", $residue, $resi)) { # If there are more than 1 Characters in brackets...
                $cleanResi = preg_replace("/[<>]/", "", $resi[1]);
                $splitResi = chunk_split($cleanResi,1,":");
                $resiSortArray = explode(":",$splitResi);
                asort($resiSortArray);
                $resiSort = implode("", $resiSortArray);

                $tmpStr = $resi[1];
                if(preg_match("/^</", $residue))
                    $tmpStr = "fn".$tmpStr;
                if(preg_match("/>$/", $residue))
                    $tmpStr .= "fc";

                if(isset($seefeldClasses[$resiSort])) {
                    array_push($seefeldPattern, preg_replace("/$resi[1]/", "$seefeldClasses[$resiSort]", $tmpStr));
                }
                else {
                    $newPattern = chunk_split($resi[1],1,"/");
                    $newPattern = "(".substr($newPattern,0,-1).")";
                    $newPattern = preg_replace("/</", "fn", $newPattern);
                    $newPattern = preg_replace("/>/", "fc", $newPattern);
                    array_push($seefeldPattern, preg_replace("/X/", "x", preg_replace("/$resi[1]/", $newPattern, $tmpStr)));
                }
            }
            elseif (preg_match ("/\[([A-Z0-9]{2,})\]/", $residue, $resi)) {
                array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                                    "There is an unknown Residue in your &quot;Possible-Residue-List&quot; (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($residue, ENT_QUOTES)."</span>) on position ".($i+1)."!<br />".
                                                    "Residue-List will be assumed.</p>");
                $newPattern = chunk_split($resi[1],1,"/");
                $newPattern = substr($newPattern,0,-1);
                array_push($seefeldPattern, "(".preg_replace("/X/", "x", $newPattern).")");
            }
        }
        elseif(preg_match ("/^<?\{[A-Z0-9<>]+\}>?$/", $residue, $resi)) { # Expression in curly brackets...
            if(preg_match("/\{([ABCDEFGHIKLMNPQRSTUVWXYZ0-9])\}/", $residue, $resi)) { # If there´s just one Character in brackets...
                $tmpStr = "^(".$resi[1].")";
                if(preg_match("/^</", $residue))
                    $tmpStr = "fn".$tmpStr;
                if(preg_match("/>$/", $residue))
                    $tmpStr .= "fc";
                array_push($seefeldPattern, preg_replace("/X/", "x", $tmpStr)); # Example: -[D]- -> D
            }
            elseif(preg_match ("/\{([ABCDEFGHIKLMNPQRSTUVWXYZ0-9<>]{2,})\}/", $residue, $resi)) { # If there are more than 1 Characters in brackets...
                $cleanResi = preg_replace("/[<>]/", "", $resi[1]);
                $splitResi = chunk_split($cleanResi,1,":");
                $resiSortArray = explode(":",$splitResi);
                asort($resiSortArray);
                $resiSort = implode("", $resiSortArray);

                $tmpStr = $resi[1];
                if(preg_match("/^</", $residue))
                    $tmpStr = "fn".$tmpStr;
                if(preg_match("/>$/", $residue))
                    $tmpStr .= "fc";

                if(isset($seefeldClasses[$resiSort])) {
                    array_push($seefeldPattern, preg_replace("/$resi[1]/", "^($seefeldClasses[$resiSort])", $tmpStr));
                }
                else {
                    $newPattern = chunk_split($resi[1],1,"/");
                    $newPattern = "^(".substr($newPattern,0,-1).")";
                    $newPattern = preg_replace("/</", "fn", $newPattern);
                    $newPattern = preg_replace("/>/", "fc", $newPattern);
                    array_push($seefeldPattern, preg_replace("/X/", "x", preg_replace("/$resi[1]/", $newPattern, $tmpStr)));
                }
            }
            elseif (preg_match ("/\{([A-Z0-9]{2,})\}/", $residue, $resi)) {
                array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                                    "There is an unknown Residue in your &quot;non-Possible-Residue-List&quot; (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($residue, ENT_QUOTES)."</span>) on position ".($i+1)."!<br />".
                                                    "Residue-List will be assumed.</p>");
                $newPattern = chunk_split($resi[1],1,"/");
                $newPattern = substr($newPattern,0,-1);
                array_push($seefeldPattern, "^(".preg_replace("/X/", "x", $newPattern).")");
            }
        }
        elseif (preg_match ("/^([A-Z]{2,})$/", $residue, $resi)) { # Example: -FJ- = -F-J- (?)
            array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                                "There is a Syntax-Error (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($residue)."</span>) in your Sequence on Position ".($i+1)."!<br />".
                                                "Residue will be assumed as &quot;x&quot;.</p>");
            array_push($seefeldPattern, "x"); # ...assume motif as 'x'.
        }
        elseif (preg_match ("/^([0-9]+)$/", $residue, $resi)) {
            array_push($messagesRef["warn"], "<p><div style=\"font-weight:bold\">WARNING</div>".
                                                "There is a Syntax-Error (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($residue)."</span>) in your Sequence on Position ".($i+1)."!<br />".
                                                "Residue (Number) will be assumed as &quot;".$resi[1]."&quot;.</p>");
            array_push($seefeldPattern, $resi[1]); # ...assume although amino acid is not allowed (IUBMB).
        }
        else {
            array_push($messagesRef["error"], "<p><div style=\"font-weight:bold\">ERROR</div>".
                                                 "Cannot detect pattern (<span style=\"font-style:italic; color:#DD0000\">".htmlentities($residue)."</span>) in your Sequence on Position ".($i+1)."!<br />".
                                                 "Residue will not be assumed.</p>");
        }

        $i++;
    }
    return $seefeldPattern;
}



function print_motifpositions($motif) {
    $patternArray = split("-", $motif);
    $resPositionIndex = 1; # Position-Index of the current amino acid (pattern).
    $positions = "";
    $showSeq = "";

    # Foreach-Loop for output the splitted pattern
    foreach($patternArray as $patternElement) {
        $lenPattElem = strlen($patternElement); # Length of the current pattern (Example: [ULP] => 5).
        $lenResPos = strlen($resPositionIndex); # Length of the current Position-Index (Example: 13 => 2).

        $positions .= str_pad($resPositionIndex, ($lenPattElem), "."); # Filling the spaces by longer Pattern-Elements with appropriate number of '.' (dots).
        $positions .= "&nbsp;"; # Add a HTML-Whitespace to the String $positions.

        $showSeq .= htmlentities($patternElement)."&nbsp;"; # Saves the current Pattern-Element to the String $showSeq.
        if($lenPattElem < $lenResPos) {
            for($space=1; $space<$lenResPos; $space++) {
                $showSeq .= "&nbsp;"; # Padding the String $showSeq with $space HTML-Whitespaces.
            } 
        }
        $resPositionIndex++;
    }

    echo "<h2>Detected motif</h2>\n";
    echo "<code><span style=\"font-weight:bold\">Positions:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$positions</code><br />\n                  ";
    echo "<code><span style=\"font-weight:bold\">Pattern-Elements:</span>&nbsp;$showSeq</code>\n";
    echo "<br />&nbsp;<hr />\n";
}



function print_seefeldpositions($seefeldPattern) {
    $patternArray = $seefeldPattern;
    $resPositionIndex = 1; # Position-Index of the current amino acid (pattern)
    $positions = "";
    $showSeq = "";

    # Foreach-Loop for output the splitted pattern
    foreach($patternArray as $patternElement) {
        $lenPattElem = strlen($patternElement); # Length of the current pattern (Example: [ULP] => 5)
        $lenResPos = strlen($resPositionIndex); # Length of the current Position-Index (Example: 13 => 2)

        $positions .= str_pad($resPositionIndex, ($lenPattElem), "."); # Filling the spaces by longer Pattern-Elements with appropriate number of '.' (dots)
        $positions .= "&nbsp;"; # Add a HTML-Whitespace to the String $positions

        $showSeq .= htmlentities($patternElement)."&nbsp;"; # saves the current Pattern-Element to the String $showSeq
        if($lenPattElem < $lenResPos) {
            for($space=1; $space<$lenResPos; $space++) {
                $showSeq .= "&nbsp;"; # Padding the String $showSeq with $space HTML-Whitespaces
            } 
        }
        $resPositionIndex++;
    }

    echo "<h2>Detected Seefeld pattern</h2>\n";
    echo "<code><span style=\"font-weight:bold\">Positions:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$positions</code><br />\n                  ";
    echo "<code><span style=\"font-weight:bold\">Pattern-Elements:</span>&nbsp;$showSeq</code>\n";
    echo "<br />&nbsp;<hr />\n";
}



function print_translated($seefeldPattern) {
    echo "<h2>Result Seefeld pattern</h2>\n";
    echo "<code>".htmlentities(implode("", $seefeldPattern))."</code>";
    echo "<br />&nbsp;<hr />\n";
}



function saveas_ascii($title, $motif, $translated, $oPath, $oFilename) {
    if(!file_exists($oPath))
        mkdir($oPath, 0700);
    if(!preg_match("/\.\w{3,4}$/", $oFilename))
        $oFilename .= ".txt";

    $asciiStr =  "    _____           _____  _    _ _____\n";
    $asciiStr .= "   / ____|         |  __ \| |  | |_   _|\n"; 
    $asciiStr .= "  | (___   ___  ___| |__) | |__| | | |\n";
    $asciiStr .= "   \___ \ / _ \/ _ \  ___/|  __  | | |\n";
    $asciiStr .= "   ____) |  __/  __/ |    | |  | |_| |_\n";
    $asciiStr .= "  |_____/ \___|\___|_|    |_|  |_|_____|\n";
    $asciiStr .= "RESULTSRESULTSRESULTSRESULTSRESULTSRESULTS\n"; 
    
    $asciiStr .= "Title  $title\n";
    $asciiStr .= "Mode   P2S (PHI-BLAST/PROSITE to Seefeld)\n";
    $asciiStr .= "Date   ".date("Y-m-d H:i:s")."\n\n";
    $asciiStr .= "----\n";
    $asciiStr .= "PHI-BLAST-Pattern\n$motif\n";
    $asciiStr .= "----\n\n";
    $asciiStr .= "----\n";
    $asciiStr .= "Seefeld-Pattern\n".implode("", $translated)."\n";
    $asciiStr .= "----\n";

    $fHandle = fopen($oPath.$oFilename, "w");
               flock($fHandle, 2);
               fputs($fHandle, $asciiStr);
               flock($fHandle, 3);
               fclose($fHandle);

    return $oPath.$oFilename;
}



function saveas_latex($title, $motif, $translated, $oPath, $oFilename) {
    if(!file_exists($oPath))
        mkdir($oPath, 0700);
    if(!preg_match("/(\.tex$)|(\.latex$)/", $oFilename))
        $oFilename .= ".tex";

    $latexPattern = ascii2latex($translated);

    $latexStr =  "\documentclass{article}\n";
    $latexStr .= "\begin{document}\n\n";
    $latexStr .= "\section*{SeePHI-Results}\n\n";
    $latexStr .= "\begin{description}\n";
    $latexStr .= "  \item[Title] Test\n";
    $latexStr .= "  \item[Mode] \\texttt{P2S} (PHI-BLAST/PROSITE to Seefeld)\n";
    $latexStr .= "  \item[Date] ".date("Y-m-d H:i:s")."\n";
    $latexStr .= "\end{description}\n\n";
    $latexStr .= "\vspace{2mm}\n\n";
    $latexStr .= "\subsubsection*{PHI-BLAST-Pattern}\n";
    $latexStr .= "\begin{verbatim}\n";
    $latexStr .= $motif."\n\n";
    $latexStr .= "\end{verbatim}\n\n";
    $latexStr .= "\vspace{4mm}\n\n";
    $latexStr .= "\subsubsection*{Seefeld-Pattern}\n";
    $latexStr .= implode("", $latexPattern)."\n\n";
    $latexStr .= "\end{document}";

    $fHandle = fopen($oPath.$oFilename, "w");
               flock($fHandle, 2);
               fputs($fHandle, $latexStr);
               flock($fHandle, 3);
               fclose($fHandle);

    return $oPath.$oFilename;
}



function saveas_pdf($title, $motif, $translated, $oPath, $oFilename) {
    if(!file_exists($oPath))
        mkdir($oPath, 0700);
    preg_replace("/\.pdf$/", "", $oFilename);

    $latexFilename = saveas_latex($title, $motif, $translated, "/tmp/seephi/", md5(microtime()).".tex");
    exec("pdflatex -output-directory=$oPath -jobname=\'$oFilename\' \'$latexFilename\'");

    return $oPath.$oFilename.".pdf";
}


function ascii2latex($translated) {
    $converted = array();
    $convArray = array("%" => "$\Phi$",
                       "@" => "$\Omega$",
                       "&" => "$\zeta$",
                       "[+]" => "[+]",
                       "[-]" => "[-]",
                       "#" => "$\Psi$",
                       "~" => "$\pi$");

    foreach($translated as $val) {
        if(isset($convArray[$val]))
            array_push($converted, $convArray[$val]);
        else array_push($converted, "\\texttt{".$val."}");
    }
    return $converted;
}
?>
