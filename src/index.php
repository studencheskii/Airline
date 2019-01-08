<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<a href="index.php"><b>Главная</b> | </a>
<a href="flights.php">Рейсы | </a>
<?php
require_once ("includes/sessions.php");
if(login())
{
	$row = aboutUser();
	echo "<a href='private.php'>Личный кабинет | </a>";
	echo "<a href='logout.php'>Выход</a><hr />";
	echo "<p>Hello, ". $row->u_name ."! </p>";
}
else
{
	echo"<a href='login.php'>Войти</a><hr />";
	echo "<p>Hello, what is your name? </p>";
}
?>


</body>
</html>