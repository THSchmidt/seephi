<?php
class main {
	var $parseStart;
	var $now;

	var $url = "http://192.168.0.50/seephi7/";
	var $homepage;
	var $toWebRoot;

	var $pageTitle = "SeePHI 0.7b";
	var $clsPath = "classes/";
	var $incPath = "include/";
	var $styPath = "styles/";
	var $imgPath = "images/";

	var $dbObj;
	var $screenStyle = "default";
	var $links;
	var $loginMessage;

	var $dbTables = array("clicklog" => "clicklog",
						  "register" => "register",
						  "users" => "users",
						  "metadata" => "metadata");


	function main($pageTitle, $toWebRoot) {
		$this->parseStart = microtime();
		$this->now = date("Y-m-d H:i:s");

		$this->homepage = $this->url."index.php";
		$this->toWebRoot = $toWebRoot;

		$this->pageTitle = $this->pageTitle." :: ".$pageTitle;
		$this->clsPath = $toWebRoot.$this->clsPath;
		$this->incPath = $toWebRoot.$this->incPath;
		$this->styPath = $toWebRoot.$this->styPath;
		$this->imgPath = $toWebRoot.$this->imgPath;

		include $this->clsPath."db.class.php";

		### Database Connect ############################
		$this->dbObj = new db("localhost", "seephiadmin", "KRz&b1#", $this->dbTables);
		$this->dbObj->select_db("seephi");
		#################################################


		### Click-Statistics ############################
		$this->clicklog_start($this->dbObj->dbTables['clicklog']);
		#################################################


		### Build default Links #########################
		$this->links = array("Home" => $this->homepage,
                             "Translation" => $this->url."translate/index.php",
							 "Registration" => $this->url."register.php",
							 "Help" => $this->url."help/index.php");
		#################################################


		### Check Login-Data ############################
		session_start(); # HTML-Header IS ALWAYS the first Information of each Page.
		$this->check_login();
		#################################################
	}


	function print_htmlhead() {
		include $this->clsPath."htmlhead.class.php";
		$htmlHeadObj = new htmlhead($this->dbObj, $this->pageTitle, $this->styPath.$this->screenStyle."/screen.css", $this->toWebRoot);
		print $htmlHeadObj->htmlHeader;
	}


	function print_banner() {
		include $this->incPath."banner.inc.php";
	}


	function print_menu() {
		if(isset($_SESSION["userid"])) { # If User is logged in...
			$this->links = array("Home" => $this->homepage,
								 "Translation" => $this->url."translate/index.php",
								 "Logout" => $_SERVER['PHP_SELF']."?logout=1",
								 "Help" => $this->url."help/index.php"); # Build by runtime.
		}
		include $this->clsPath."menu.class.php";
		$htmlMenuObj = new menu($this->links, "h");
		print $htmlMenuObj->htmlMenu;
	}


	function print_footline() {
		include $this->incPath."footline.inc.php";
	}


	function print_version() {
		include $this->incPath."version.inc.php";
	}


	### Login-Methods ###################################
	function check_login() {
		if(isset($_POST['login_submit']) and !isset($_SESSION["username"])) {
			$this->login();
		}
		elseif(isset($_GET["logout"])) {
			$this->logout();
		}
	}


	function login() {
		$errorMessage = NULL;

		$_POST["login_username"] = trim($_POST["login_username"]);
		$_POST["login_password"] = trim($_POST["login_password"]);

		if(!$_POST["login_username"] or strlen($_POST["login_username"]) < 4) {
			$errorMessage = "<div class=\"error\">Please put in your Username (min. 4 characters).</div>\n";
		}
		if(!$_POST["login_password"]) {
			$errorMessage .= "<div class=\"error\">Please put in your Password.</div>\n";
		}
		if($errorMessage) {
			$this->loginMessage["title"] = "Error";
			$this->loginMessage["message"] = $errorMessage;
			return;
		}

		
		$sqlQuery = "UPDATE ".$this->dbObj->dbTables['users']." SET usr_lastlogin = '".$this->now."', usr_numlogins = usr_numlogins + 1
        		     WHERE usr_name = '".$_POST["login_username"]."' and usr_pass = '".md5($_POST["login_password"])."'";
		$this->dbObj->sql_query($sqlQuery);
		
		$sqlQuery = "SELECT * FROM ".$this->dbObj->dbTables["users"]." WHERE usr_name = '".$_POST["login_username"]."' and usr_pass = '".md5($_POST["login_password"])."'";
		$qUser = $this->dbObj->sql_query($sqlQuery);

		if(mysql_num_rows($qUser)) {
			$fUser = mysql_fetch_array($qUser);
			$_SESSION["userid"] = $fUser["usr_id"];
			$_SESSION["userip"] = $_SERVER['REMOTE_ADDR'];

			$_SESSION["username"] = $fUser["usr_name"];
			$_SESSION["userlogins"] = $fUser["usr_numlogins"];
			$_SESSION["userregsince"] = $fUser["usr_firstlogin"];
			$_SESSION["useremail"] = $fUser["usr_email"];

			$this->loginMessage["title"] = "Welcome";
			$this->loginMessage["message"] = "Nice to meet you, ".$_SESSION["username"]."...\n";
			return;
		}
		$this->loginMessage["title"] = "Error";
		$this->loginMessage["message"] = "<div class=\"error\">".$_POST["login_username"]." is not a registered User.</div>\n";
	}


	function valid_login() {
		if(isset($_SESSION["userid"]) and $_SESSION["userip"] == $_SERVER['REMOTE_ADDR'])
			return 1;
		return 0;
	}


	function print_loginstatus() {
		include_once $this->clsPath."sidebar.class.php";

		### Login message ################################
		if($this->loginMessage) {
			$sidebarElementObj = new sidebar($this->loginMessage["title"], "text", $this->loginMessage["message"]);
			$sidebarElementObj->print_sidebarelem();
			print "&nbsp;\n";
		}
		##################################################

		### Login infos ##################################
		if($this->valid_login()) {
			$sidebarElementObj = new sidebar("User Data", "include", $this->incPath."login_infos.inc.php");
			$sidebarElementObj->print_sidebarelem();
			return;
		}
		##################################################

		### Login form ###################################
		$sidebarElementObj = new sidebar("Login", "include", $this->incPath."login.form.inc.php");
		$sidebarElementObj->print_sidebarelem();
		##################################################
		return;
	}


	function logout() {
		if($_SESSION["username"]) {
			$this->loginMessage["title"] = "Thanks...";
			$this->loginMessage["message"] = "...for using SeePHI, ".$_SESSION["username"].".\n";
		}
		session_unset();
	}


	function protect_page() {
		if(!$this->valid_login()) {
			$this->logout();
			include $this->incPath."protected.inc.php";
			exit;
		}
	}
	#####################################################


	### Click-Statistic #################################
	function clicklog_start() {
		$sqlQuery = "INSERT INTO ".$this->dbObj->dbTables['clicklog']." (click_path, click_ip, click_datetime)
        		     VALUES ('".$_SERVER['REQUEST_URI']."', '".$_SERVER['REMOTE_ADDR']."', '".$this->now."')";
		$this->dbObj->sql_query($sqlQuery);
	}


	function clicklog_end() {
		$sqlQuery = "UPDATE ".$this->dbObj->dbTables['clicklog']." SET click_parsetime = ".(microtime()-$this->parseStart)."
        		     WHERE click_ip = '".$_SERVER['REMOTE_ADDR']."' AND click_datetime = '".$this->now."'";
		$this->dbObj->sql_query($sqlQuery);
	}
	#####################################################
}
?>
