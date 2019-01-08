<!DOCTYPE HTML>
<html>
	<head>
		<title>Рейсы</title>
	</head>
	<body>
		<a href="index.php">Главная | </a>
		<a href="flights.php"><b>Рейсы</b> | </a>
		<?php
		require_once("includes/sessions.php");
		require_once("includes/db.php");
		if(login())
		{
			echo "<a href='private.php'>Личный кабинет | </a>";
			echo "<a href='logout.php'>Выход</a><hr />";
		}
		else 
		{
			echo "<a href='login.php'>Войти</a><hr />";
		}
		
		echo <<<HTML
		<table border><tr>
		<th>ID рейса</th>
		<th>№ рейса</th>
		<th>Время вылета</th>
		<th>Время прилета</th>
		<th>Аэропорт отправления</th>
		<th>Аэропорт прибытия</th>
		<th>Статус рейса</th>
		<th>Модель самолета</th>
HTML;
		if(login())
		{
			echo "<th>Действие</th>";
		}
		
		for($i = 1; $i < 26; $i++)
		{
			$sql = 'SELECT city, ap_name FROM airports where :i = ap_code';
			$params = [':i' => $i];
			$stmt = $db->prepare($sql);
			$stmt->execute($params);
			$r = $stmt->fetch(PDO::FETCH_OBJ);
			$city[] = $r->city;
			$ap_name[] = $r->ap_name;
		}
		
		for($i = 1; $i <= 11; $i++)
		{
			$sql = 'SELECT a_model,a_capacity FROM aircrafts where :i = a_code';
			$params = [':i' => $i];
			$stmt = $db->prepare($sql);
			$stmt->execute($params);
			$r = $stmt->fetch(PDO::FETCH_OBJ);
			$aircraft[] = $r->a_model;
			$capacity[] = $r->a_capacity;
		}
	
		$stmt = $db->query('SELECT * FROM flights');
		$auth = login();
		$id = 1;
		while ($row = $stmt->fetch())
		{	
			echo "<tr>";
			for($i = 0; $i < count($row); $i++)
			{
				if($row[$i])
				{
					echo "<td>";
					if($i == 4 || $i == 5)
					{
						echo $ap_name[$row[$i]-1] . "(" . $city[$row[$i]-1] . ")";
					}
					elseif($i == 7)
					{
						echo $aircraft[$row[$i] - 1];
					}
					else
					{
						echo $row[$i];
					}
					echo "</td>";
				}
			}
			if($auth)
			{
				if(isAdmin())
				{
					echo <<<HTML
					<form action='delete_flight.php' method='post'>
					<input type='hidden' name='id' value='$row[0]'>
					<td><input name='delete' type='submit' value='Удалить'>
					</form>
HTML;
				}
				else
				{
					if(trim($row[6]) == 'Scheduled')
					{
						echo <<<HTML
						<form action='order.php' method='post'>
						<input type='hidden' name='id' value='$row[0]'>
						<td><input name='order' type='submit' value='Заказать билет'>
						</form>
HTML;
					}
					else
					{
						echo "<td></td>";
					}

				}
				
			}
			echo "</tr>";
			$id++;
		}
		echo "</table>";
	
		?>
	</body>
</html>
