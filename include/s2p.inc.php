<?php
function detect_pattern($pattern) {
    $prositePattern = array();

    ### All Classes of the Seefeld-Nomenclature (in alphabetical order) ############
    $seefeldClasses = array("%"   => "-[FILMVWY]-",
                            "@"   => "-[FWY]-",
                            "&"   => "-[DEHKNQRST]-",
                            "+" => "-[HKR]-",
                            "-" => "-[DE]-",
                            "#"   => "-[ILMV]-",
                            "~"   => "-[AGPS]-");
    ################################################################################

    $pattern = preg_replace("/fn/", "<", $pattern);
    $pattern = preg_replace("/fc/", ">", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:po[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:su[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:gl[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:me[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:sme[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:ame[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:ac[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/[\[\{]([A-Za-z])\:hy[\]\}]/", "$1", $pattern);
    $pattern = preg_replace("/\(beta\s*\d+\)\d+/", "", $pattern);
    $pattern = preg_replace("/\(alpha\s*\d+\)\d+/", "", $pattern);
    $pattern = preg_replace("/\//", "", $pattern);
    $pattern = preg_replace("/\^\(([A-Za-z<>\[\]%@&#~+-]+?)\)/", '-{$1}-', $pattern);
    $pattern = preg_replace("/\(/", "-[", $pattern);
    $pattern = preg_replace("/\)/", "]-", $pattern);

    $pattern = preg_replace("/%/", $seefeldClasses["%"], $pattern);
    $pattern = preg_replace("/@/", $seefeldClasses["@"], $pattern);
    $pattern = preg_replace("/&/", $seefeldClasses["&"], $pattern);
    $pattern = preg_replace("/\[\+\]/", $seefeldClasses["+"], $pattern);
    $pattern = preg_replace("/\[-\]/", $seefeldClasses["-"], $pattern);
    $pattern = preg_replace("/#/", $seefeldClasses["#"], $pattern);
    $pattern = preg_replace("/~/", $seefeldClasses["~"], $pattern);

    $pattern = preg_replace("/-{2,}/", "-", $pattern);
    $pattern = preg_replace("/^<-/", "<", $pattern);
    $pattern = preg_replace("/->$/", ">", $pattern);
    $pattern = preg_replace("/\{-/", "{", $pattern);
    $pattern = preg_replace("/-\}/", "}", $pattern);

//    echo "<br /><br /><code style=\"color:red\">Temp. String:&nbsp;".htmlentities($pattern)."</code>\n";

    $tmpArray = split("-", $pattern);
    foreach($tmpArray as $residue) {
        if(preg_match ("/^<?[A-Za-z]+>?$/", $residue)) {
            $lastResidue = substr($residue, 0, 1);
            $tmpResCount = 1;
            $prefix = "";
            for($i=0; $i<strlen($residue); $i++) {
                if($i>0)
                    $lastResidue = substr($residue, ($i-1), 1);
                $tmpResidue = substr($residue, $i, 1);
                $nextResidue = substr($residue, ($i+1), 1);

                if($tmpResidue == "<") {
                    $prefix = "<";
                }
                elseif($tmpResidue == ">") {
                    $prositePattern[count($prositePattern)-1] .= ">";
                }
                elseif($lastResidue == $tmpResidue and $tmpResidue == $nextResidue)
                    $tmpResCount++;
                elseif($tmpResidue == $nextResidue)
                    $tmpResCount++;
                elseif($lastResidue == $tmpResidue and $tmpResCount > 1) {
                    array_push($prositePattern, $prefix.$tmpResidue."(".$tmpResCount.")");
                    $prefix = "";
                    $tmpResCount = 1;
                }
                elseif(!preg_match("/[<>]/", $tmpResidue)) {
                    array_push($prositePattern, $prefix.$tmpResidue);
                    $prefix = "";
                }
//                else array_push($prositePattern, $prefix.$tmpResidue.$postfix);
            }
        }
        elseif(preg_match("/^<?\[(.+)\]>?$/", $residue, $string)) {
//            $pattern = preg_replace("/[\[\]\{\}]/", "", $string[0]); # Cleanup.
//            echo "<p>".$string[1]."</p>";
//            array_push($prositePattern, "[".$pattern."]");
            array_push($prositePattern, $residue);
        }
        elseif(preg_match("/^<?\{(.+)\}>?$/", $residue, $string)) {
//            $pattern = "{".preg_replace("/[\[\]\{\}]/", "", $string[0])."}";
//            if(preg_match("/^</", $residue))
//                $pattern = "<".$pattern;
//            if(preg_match("/>$/", $residue))
//                $pattern .= ">";
//            array_push($prositePattern, $pattern);
            array_push($prositePattern, $residue);
        }
        elseif(preg_match("/^<?(.+)>?$/", $residue, $string)) {
            array_push($prositePattern, $residue);
        }
    }
    return $prositePattern;
}



function print_motifpositions($motif) {
    echo "<h2>Input Seefeld pattern</h2>\n";
    echo "<code>".htmlentities($motif)."</code>\n";
    echo "<br />&nbsp;<hr />\n";
}



function print_prositepositions($patternArray) {
    $resPositionIndex = 1; # Position-Index of the current amino acid (pattern).
    $positions = "";
    $showSeq = "";

    # Foreach-Loop for output the splitted pattern
    foreach($patternArray as $patternElement) {
        $lenPattElem = strlen($patternElement); # Length of the current pattern (Example: [ULP] => 5).
        $lenResPos = strlen($resPositionIndex); # Length of the current Position-Index (Example: 13 => 2).

        $positions .= str_pad($resPositionIndex, ($lenPattElem), "."); # Filling the spaces by longer Pattern-Elements with appropriate number of '.' (dots).
        $positions .= "&nbsp;"; # Add a HTML-Whitespace to the String $positions

        $showSeq .= htmlentities($patternElement)."&nbsp;"; # saves the current Pattern-Element to the String $showSeq.
        if($lenPattElem < $lenResPos) {
            for($space=1; $space<$lenResPos; $space++) {
                $showSeq .= "&nbsp;"; # Padding the String $showSeq with $space HTML-Whitespaces.
            } 
        }
        $resPositionIndex++;
    }

    echo "<h2>Detected PROSITE pattern</h2>\n";
    echo "<code><span style=\"font-weight:bold\">Positions:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$positions</code><br />\n                  ";
    echo "<code><span style=\"font-weight:bold\">Pattern-Elements:</span>&nbsp;$showSeq</code>\n";
    echo "<br />&nbsp;<hr />\n";
}



function print_translated($translated) {
    echo "<h2>Result PROSITE pattern</h2>\n";
    echo "<code>".htmlentities(implode("-", $translated))."</code>";
    echo "<br />&nbsp;<hr />\n";
}



function saveas_ascii($title, $motif, $translated, $oPath, $oFilename) {
    if(!file_exists($oPath))
        mkdir($oPath, 0777);
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
    $asciiStr .= "Mode   S2P (Seefeld to PHI-BLAST/PROSITE)\n";
    $asciiStr .= "Date   ".date("Y-m-d H:i:s")."\n\n";
    $asciiStr .= "----\n";
    $asciiStr .= "Seefeld-Pattern\n$motif\n";
    $asciiStr .= "----\n\n";
    $asciiStr .= "----\n";
    $asciiStr .= "PHI-BLAST-Pattern\n".implode("-", $translated)."\n";
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
        mkdir($oPath, 0777);
    if(!preg_match("/(\.tex$)|(\.latex$)/", $oFilename))
        $oFilename .= ".tex";

    $latexStr =  "\documentclass{article}\n";
    $latexStr .= "\begin{document}\n\n";
    $latexStr .= "\section*{SeePHI-Results}\n\n";
    $latexStr .= "\begin{description}\n";
    $latexStr .= "  \item[Title] Test\n";
    $latexStr .= "  \item[Mode] \\texttt{S2P} (Seefeld to PHI-BLAST/PROSITE)\n";
    $latexStr .= "  \item[Date] ".date("Y-m-d H:i:s")."\n";
    $latexStr .= "\end{description}\n\n";
    $latexStr .= "\vspace{2mm}\n\n";
    $latexStr .= "\subsubsection*{Seefeld-Pattern}\n";
    $latexStr .= "\begin{verbatim}\n";
    $latexStr .= $motif."\n";
    $latexStr .= "\end{verbatim}\n\n";
    $latexStr .= "\vspace{4mm}\n\n";
    $latexStr .= "\subsubsection*{PHI-BLAST-Pattern}\n";
    $latexStr .= "\begin{verbatim}\n";
    $latexStr .= implode("-", $translated)."\n";
    $latexStr .= "\end{verbatim}\n\n";
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
        mkdir($oPath, 0777);
    preg_replace("/\.pdf$/", "", $oFilename);

    $latexFilename = saveas_latex($title, $motif, $translated, "/tmp/seephi/", md5(microtime()).".tex");
    exec("pdflatex -output-directory=$oPath -jobname='$oFilename' '$latexFilename'");

    return $oPath.$oFilename.".pdf";
}
?>
