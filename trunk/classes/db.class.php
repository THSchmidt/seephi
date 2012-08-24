<?php
class db {
    var $dbLink;
    var $sqlResult;
    var $dbTables;

    function db($dbHost, $dbUser, $dbPass, $dbTables) {
        $this->dbLink = mysql_connect($dbHost, $dbUser, $dbPass) or die ("<div class=\"error\">ERROR: Cannot Connect to Database...</div>\n");
        $this->dbTables = $dbTables;
        return $this->dbLink;
    }


    function select_db($dbName) {
        if (!mysql_select_db($dbName, $this->dbLink))
            echo "<div class=\"error\">ERROR: Cannot select Database...</div>\n";
    }


    function sql_query($sqlCmd) {
        if (!$this->sqlResult = mysql_query($sqlCmd, $this->dbLink))
            echo "<div class=\"error\">ERROR: Cannot send SQL-Query...</div>\n";
        return $this->sqlResult;
    }
}
?>
