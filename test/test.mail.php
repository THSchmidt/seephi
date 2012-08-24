<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Unbenanntes Dokument</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>

<?php
$confirmPassword = "dummyPassword";

$mailTo = "mail@t-schmidt.name";
$mailSubj = "Registration to SeePHI (Step 2)";
$mailHeader = "From:seephi@coniubix.com";

$mailText = "Thanks for registration to SeePHI.\n\n";
$mailText .= "To complete click on the following Link ";
$mailText .= "and put in your Password \"$confirmPassword\" on the opening website.\n\n";
#$mailText .= $_SERVER['HTTP_REFERER']."?key=$confirmKey";
print "<code>".nl2br($mailText)."</code>\n";
mail($mailTo, $mailSubj, $mailText, $mailHeader);
?>

</body>
</html>
