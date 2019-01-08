<!DOCTYPE HTML>
<html>
	<head>
		<title>Deleted</title>
	</head>
	<body>
		<a href="index.php"><b>Главная</b> | </a>
<a href="flights.php">Рейсы | </a>
<?php
	require_once ("includes/sessions.php");
	if(login())
	{
		echo "<a href='private.php'>Личный кабинет | </a>";
		echo "<a href='logout.php'>Выход</a><hr />";
	}
	else
	{
		echo"<a href='login.php'>Войти</a><hr />";
	}
	if(isset($_POST['delete']))
	{
		$id = htmlspecialchars(trim($_POST['id']));
		$sql = 'DELETE FROM flights where :id = f_id';
		$params = [ ':id' => $id];
		$stmt = $db->prepare($sql);
		$stmt->execute($params);
		echo "Рейс успешно удален!";
	}
	?>
	</body>
</html>