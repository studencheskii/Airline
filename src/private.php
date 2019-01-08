<!DOCTYPE HTML>
<html>
	<head>
		<title>Личный кабинет</title>
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
			
			if(isAdmin())
			{
				echo "<a href='add_fights.php'>Добавить рейс</a> | ";
				echo "<a href='show_orders.php'>Показать все заказы</a>";
			}
			else 
			{
				echo "<a href='show_orders.php'>Мои заказы</a>";
			}
			
			$row = aboutUser();
			if($row->acc_role == 'a')
				$role = "Admin";
			else
				$role = "User";
			echo <<<HTML
			<br><br><table>
			<tr><td>E-Mail</td><td> $row->acc_email (<a href='change_email.php'>Изменить</a>)</td></tr>
			<tr><td>Registration</td><td> $row->acc_registration </td></tr>
			<tr><td>Role</td><td> $role</td></tr>
			<tr><td>Name</td><td> $row->u_name</td></tr>
			<tr><td>Address</td><td> $row->u_address</td></tr>
			<tr><td>Phone</td><td> $row->u_phone</td></tr>
			<tr><td>Birthday</td><td> $row->u_birthday</td></tr>
			<tr><td>Company</td><td> $row->u_company</td></tr>
			<tr><td>About</td><td> $row->u_about</td></tr>
			<tr><td>Card number</td><td> $row->card_num</td></tr>
			<tr><td>Balance</td><td> $row->balance</td></tr>
			</table>
HTML;
		}
	?>
	</body>
</html>