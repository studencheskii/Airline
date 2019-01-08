<?php
	require_once 'includes/sessions.php';
	$string = <<<HTML
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
	</head>
	<body>
	<a href="index.php">Главная | </a>
	<a href="flights.php">Рейсы | </a>
	<a href="login.php"><b>Войти</b></a>
	<hr />
		<form action="" method="post">
 			E-mail: <input type="text" name="login" />
 			Password: <input type="password" name="password" />
 			<input type="submit" value="Войти" name="log_in" />
		</form>
	</body>
</html>
HTML;
	if (isset($_POST['login'])) 
	{
		$login = htmlspecialchars( trim($_POST['login']) ); 
		$password = htmlspecialchars( trim($_POST['password']) );
	
		if (!empty($login) && !empty($password))
 		{
 			$sql = 'SELECT acc_id, acc_password FROM accounts WHERE acc_email = :login';
 			$params = [':login' => $login];
 			$stmt = $db->prepare($sql);
 			$stmt->execute($params);

 			$user = $stmt->fetch(PDO::FETCH_OBJ);

 			if ($user) 
 			{
 				if ($user->acc_password == md5($password))
 				{
 					mySession_write($user->acc_id);
 					header('Location: index.php');
 				}
 				else
				{
					echo $string;
 					echo "Неверный логин или пароль!"; 
				}
 			}
 			else
			{
				echo $string;
 				echo "Пользователь не найден!";
			}
 		}
 		else
		{
			echo $string;
 			echo "Неверно задан логин или пароль!"; 
		}
	}
	else
	{
		echo $string;
	}
?>


