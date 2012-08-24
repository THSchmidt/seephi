<?php
class register {
	var $mainObj;

	function register($mainObj) {
		$this->mainObj = $mainObj;
	}

	
	function print_registration() {
		if(isset($_POST["registform_submit"])) {
			$this->regist_step();
		}
		elseif(isset($_GET["key"]) or isset($_POST["confirmform_submit"])) {
			$this->confirm_step();
		}
		else {
			$this->print_registform();
		}
	}


	function regist_step() {
		$errorMessage = $this->check_registform();
		if($errorMessage) {
			print $errorMessage;
			$this->print_registform();
		}
		else {
			print $this->new_regist();
		}
	}


	function confirm_step() {
		if(isset($_GET["key"])) {
			$errorMessage = $this->check_confirmkey($_GET["key"]);
			if($errorMessage) {
				print $errorMessage;
				$this->print_registform();
				return;
			}
			else {
				$this->print_confirmform($_GET["key"]);
			}
		}
		elseif(isset($_POST["confirmform_submit"])) {
			$errorMessage = $this->check_confirmkey($_POST["confirmform_key"]);
			if($errorMessage) {
				print $errorMessage;
				$this->print_registform();
				return;
			}

			$errorMessage = $this->check_confirmform();
			if($errorMessage) {
				print $errorMessage;
				$this->print_confirmform($_POST["confirmform_key"]);
			}
			else {
				$this->confirm_final();
			}
		}
	}



	### Regist-Step Methods #############################
	function print_registform() {
		include $this->mainObj->clsPath."numgen.class.php";
		$numgenObj = new numgen($this->mainObj);
		$registKey = $numgenObj->create_img();

		include $this->mainObj->incPath."regist.form1.inc.php";
	}


	function check_registform() {
		$_POST["registform_email1"] = preg_replace("/\s/", "", $_POST["registform_email1"]);
		$_POST["registform_email2"] = preg_replace("/\s/", "", $_POST["registform_email2"]);
		$_POST["registform_key"] = preg_replace("/\s/", "", $_POST["registform_key"]);

		$errorMessage = $this->check_email();
		if(md5($_POST["registform_key"]) != $_POST["registform_id"]) {
			$errorMessage .= "<div class=\"error\">Invalid Registration Key. Please try it once more.</div>\n";
		}
		return $errorMessage;
	}


	function new_regist() {
		$confirmPassword = $this->create_confirmpassw();
		$confirmKey = $this->create_confirmkey();

		$sqlQuery = "INSERT INTO ".$this->mainObj->dbObj->dbTables['register']." (reg_email, reg_password, reg_confirmkey)
        		     VALUES ('".$_POST['registform_email1']."', '".md5($confirmPassword)."', '$confirmKey')";
		$this->mainObj->dbObj->sql_query($sqlQuery);

		$this->send_confirmdata($confirmPassword, $confirmKey);

		return "<div>Registration (Step 1) successfull.</div>\n";
	}


    function check_email() {
        if(preg_match("/^[A-Za-z0-9](([_\.\-]?[A-Za-z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[A-Za-z0-9]+)*)\.([A-Za-z]{2,})$/", $_POST['registform_email1'])) {
            if($_POST['registform_email1'] == $_POST['registform_email2']) {
				$sqlQuery = "SELECT * FROM ".$this->mainObj->dbObj->dbTables['users']." WHERE usr_email = '".$_POST['registform_email1']."' OR usr_regemail = '".$_POST['registform_email1']."'";
				$qEmailValid = $this->mainObj->dbObj->sql_query($sqlQuery);
				if(mysql_num_rows($qEmailValid)) {
					return "<div class=\"error\">The Email-Address is already registered. Please put in another one.</div>\n";
				}
                return;
            }
            return "<div class=\"error\">The Email-Addresses are different. Please check your input.</div>\n";
        }
        return "<div class=\"error\">No valid Email-Adress. Please check your input.</div>\n";
    }


    function create_confirmkey() {
		$confirmKey = "";
	
        srand((double) microtime()*1000000);
		if(isset($_POST['registform_email1'])) {
	        $confirmKey = md5(rand().$_POST['registform_email1']);
		}
        return substr($confirmKey, 3, 14);
    }


    function create_confirmpassw() {
        srand((double) microtime()*1000000);
        $confirmPassword = md5(rand());
        return substr($confirmPassword, 17, 8);
    }


    function send_confirmdata($confirmPassword, $confirmKey) {
		if(isset($_POST['registform_email1'])) {
	        $mailTo = $_POST['registform_email1'];
    	    $mailSubj = "Registration to SeePHI (Step 2)";
        	$mailHeader = "From:seephi@coniubix.com";

	        $mailText = "Thanks for registration to SeePHI.\n\n";
    	    $mailText .= "To complete click on the following Link ";
        	$mailText .= "and put in your Password \"$confirmPassword\" on the opening website.\n\n";
	        $mailText .= $_SERVER['HTTP_REFERER']."?key=$confirmKey";
#			print "<code>".nl2br($mailText)."</code>\n";
        	mail($mailTo, $mailSubj, $mailText, $mailHeader);
		}
    }
	#####################################################



	### Confirm-Step Methods #############################
	function check_confirmkey($confirmKey) {
		$sqlQuery = "SELECT * FROM ".$this->mainObj->dbObj->dbTables['register']."
					 WHERE reg_confirmkey = '$confirmKey'";
		$validKey = $this->mainObj->dbObj->sql_query($sqlQuery);
		
		if(!mysql_num_rows($validKey)) {
			return "<div class=\"error\">Invalid Confirmation-Key.</div>\n";
		}
	}


	function print_confirmform($confirmKey) {
		include $this->mainObj->incPath."regist.form2.inc.php";
	}


	function check_confirmform() {
		if(strlen($_POST["confirmform_passw"]) < 8) {
			return "<div class=\"error\">The password is too short. Please put in once more.</div>\n";
		}
		
		if(strlen($_POST["confirmform_newpassw1"]) < 4) {
			return "<div class=\"error\">Your new password must be longer than 3 characters.</div>\n";
		}
		
		if($_POST["confirmform_newpassw1"] != $_POST["confirmform_newpassw2"]) {
			return "<div class=\"error\">Your new passwords are different. Please put in once more.</div>\n";
		}
		
		$sqlQuery = "SELECT * FROM ".$this->mainObj->dbObj->dbTables['register']."
					 WHERE reg_confirmkey = '".$_POST["confirmform_key"]."' and reg_password = '".md5($_POST["confirmform_passw"])."'";
		$validPassword = $this->mainObj->dbObj->sql_query($sqlQuery);
		if(!mysql_num_rows($validPassword)) {
			return "<div class=\"error\">Invalid Confirmation-Password.</div>\n";
		}
	}


	function confirm_final() {
		$sqlQuery = "SELECT reg_email FROM ".$this->mainObj->dbObj->dbTables['register']." WHERE reg_confirmkey = '".$_POST['confirmform_key']."' AND reg_password = '".md5($_POST['confirmform_passw'])."'";
		$qChkKey = $this->mainObj->dbObj->sql_query($sqlQuery);
		
		$fChkKey = mysql_fetch_array($qChkKey);
		$sqlQuery = "DELETE FROM ".$this->mainObj->dbObj->dbTables['register']." WHERE reg_confirmkey = '".$_POST['confirmform_key']."' and reg_email = '".$fChkKey['reg_email']."'";
		$this->mainObj->dbObj->sql_query($sqlQuery); # Delete dataset.


		$sqlQuery = "INSERT INTO ".$this->mainObj->dbObj->dbTables['users']." (usr_name, usr_pass, usr_firstlogin, usr_lastlogin, usr_numlogins, usr_email, usr_regemail)
					 VALUES ('".$fChkKey['reg_email']."', '".md5($_POST["confirmform_newpassw1"])."', '".$this->mainObj->now."', '".$this->mainObj->now."', 1, '".$fChkKey['reg_email']."', '".$fChkKey['reg_email']."')";
		$this->mainObj->dbObj->sql_query($sqlQuery);

		$_SESSION['username'] = $fChkKey['reg_email'];
		$_SESSION['userip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['usernumlogins'] = 1;

		print "<div>Registration successfull.<br />Welcome to BibLib. You are logged in as User: &quot;".$_SESSION['username']."&quot;.</div>\n";
	}
	#####################################################
}
?>
