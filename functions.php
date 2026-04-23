<?php
function sanitizeString($var)
{
$var = stripslashes($var);
$var = htmlentities($var);
$var = strip_tags($var);
return $var;
}
function sanitizeMySQL($connection, $var)
{ // Using the mysqli extension
$var = $connection->real_escape_string($var);
$var = sanitizeString($var);
return $var;
}
function mysql_entities_fix_string($connection, $string)
{
return htmlentities(mysql_fix_string($connection, $string));
}
function mysql_fix_string($connection, $string)
{
$string = stripslashes($string);
return $connection->real_escape_string($string);
}
?>