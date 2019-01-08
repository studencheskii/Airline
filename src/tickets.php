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
			echo "<a href='private.php'>Скрыть список билетов</a>";
			
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
			
			// Запросить список билетов
			$info = aboutUser();
			$acc_id = $info->acc_id;
			$sql = "SELECT tickets.ticket_no, flights.f_num, flights.departure_airport, flights.arrival_airport,
					flights.sheduled_arrival ,flights.sheduled_departure, flights.a_code, tickets.amount
					FROM tickets join flights ON tickets.acc_id = '$acc_id' and tickets.f_id = flights.f_id";
			$stmt = $db->query($sql);
			//$params = [':acc_id' => $acc_id];
			//$stmt = $db->prepare($sql);
			//$stmt->execute($params);
			//$row = $stmt->fetch(PDO::FETCH_UNIQUE);
			//print_r($row);
			//print_r($acc_id);
			if($row = $stmt->fetch())
			{
				echo "<table border>";
				echo "<tr><th>№ билета</th><th>№ рейса</th><th>Откуда</th><th>Куда</th><th>Дата вылета</th><th>Дата посадки</th><th>Модель самолета</th><th>Цена</th>";
				do
				{
					echo "<tr>";
					for($i = 0; $i < count($row); $i++)
					{
						if($row[$i])
						{
							echo "<td>";
							if($i == 2 || $i == 3)
							{
								echo $ap_name[$row[$i]-1] . "(" . $city[$row[$i]-1] . ")";
							}
							elseif($i == 6)
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
					echo "</tr>";
				} while ($row = $stmt->fetch());
				echo "</table>";
			}
				
			
			
			$row = aboutUser();
			if($row->acc_role == 'a')
				$role = "Admin";
			else
				$role = "User";
			echo "<br><br><table>";
			echo "<tr><td>E-Mail</td><td>". $row->acc_email . "(<a href='change_email.php'>Изменить</a>)</td></tr>";
			echo "<tr><td>Registration</td><td>".$row->acc_registration. "</td></tr>";
			echo "<tr><td>Role</td><td>". $role . "</td></tr>"; 
			echo "<tr><td>Name</td><td>" . $row->u_name . "</td></tr>";
			echo "<tr><td>Address</td><td>" . $row->u_address . "</td></tr>";
			echo "<tr><td>Phone</td><td>" . $row->u_phone . "</td></tr>";
			echo "<tr><td>Birthday</td><td>" . $row->u_birthday . "</td></tr>";
			echo "<tr><td>Company</td><td>".$row->u_company."</td></tr>";
			echo "<tr><td>About</td><td>".$row->u_about."</td></tr>";
			echo "<tr><td>Card number</td><td>".$row->card_num."</td></tr>";
			echo "<tr><td>Balance</td><td>" . $row->balance . "</td></tr>";
			echo "</table>";
		}
	?>
	</body>
</html>