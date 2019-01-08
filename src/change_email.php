<!DOCTYPE HTML>
<html>
	<head>
		<title>Смена e-mail</title>
	</head>
	<body>
	<a href="index.php">Главная | </a>
	<a href="flights.php">Рейсы | </a>
	<a href="private.php" ><b>Личный кабинет</b> |</a>
	<a href="logout.php">Выход</a>
	<hr />
	<?php
		require_once 'includes/sessions.php';
		if(login())
		{
			echo "<form action='' method='post'>";
			echo "New E-Mail: <input type='text' name='mail'>";
			echo "Your Password: <input type='password' name='pass'>";
			echo "<input type='submit' name='change' value='Изменить'>";
			echo "</form>";
		}
		if($_POST['change'])
		{
			$newMail = htmlspecialchars( trim($_POST['mail']) ); 
			$password = htmlspecialchars( trim($_POST['pass']) );
			$reg_test = "/^[a-z0-9](?:[a-z0-9\-\._]*[a-z0-9])@[a-z0-9](?:[a-z0-9\-]*[a-z0-9])\.[a-z0-9]+$/i";
			if(!empty($newMail) && !empty($password))
			{
				if(preg_match($reg_test, $newMail))
				{
					$row = aboutUser();
					if($row->acc_password == md5($password))
					{
						$sql = 'UPDATE accounts SET acc_email = :mail where acc_password = :password';
						$params = [':mail' => $mail, ':password' => $password];
						$stmt = $db->prepare($sql);
						$stmt->execute($params);
						echo "Успешно";	
					}
					else
					{
						echo "Пароль не подходит";
					}
				}
				else
				{
					echo "E-mail некорректен";
				}
			}
			else
			{
				echo "Заполните все поля";
			}
		}
	?>
	</body>
</html>