<?php
require_once 'includes/sessions.php';
if(isset($_POST['stat']))
{
	$status = htmlspecialchars(trim($_POST['stat']));
	$ticket = htmlspecialchars(trim($_POST['ticket']));
	$amount = htmlspecialchars(trim($_POST['amount']));
	$acc_id = htmlspecialchars(trim($_POST['acc_id']));

	if($status == 3)
	{
		$sql = "UPDATE orders SET status = :status where ticket_no = :ticket;
			UPDATE cash SET balance = balance + :amount WHERE :acc_id = acc_id";
		$params = [':status' => $status, ':ticket' => $ticket, ':amount' => $amount , ':acc_id' => $acc_id];
	}
	else 
	{
		$sql = "UPDATE orders SET status = :status where ticket_no = :ticket";
		$params = [':status' => $status, ':ticket' => $ticket];
	}
	
	$stmt = $db->prepare($sql);
	$stmt->execute($params);
	header("Refresh:0");
}
?>

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
		if(login())
		{
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
			if(isAdmin())
			{
				echo "<a href='add_fights.php'>Добавить рейс</a> | ";
				echo "<a href='private.php'>Скрыть заказы</a>";
				$sql = "SELECT tickets.ticket_no, flights.f_num ,users.u_name, 
								users.u_phone , accounts.acc_email, flights.departure_airport,flights.arrival_airport,
								flights.scheduled_departure, flights.scheduled_arrival,
								flights.a_code, tickets.seat_no, tickets.amount, cash.balance, orders.status, accounts.acc_id
								
						FROM tickets 
						JOIN orders ON orders.ticket_no = tickets.ticket_no
						JOIN flights ON tickets.f_id = flights.f_id
						JOIN accounts ON orders.acc_id = accounts.acc_id
						JOIN users ON accounts.u_id = users.u_id
						JOIN cash ON cash.acc_id = accounts.acc_id";
				$stmt = $db->query($sql);
				if($row = $stmt->fetch(PDO::FETCH_NUM))
				{
					echo "<table border>";
					echo "<tr><th>№ билета</th><th>№ рейса</th><th>Имя закачика</th><th>Phone заказчика</th>
							<th>E-Mail заказчика</th><th>Откуда</th><th>Куда</th><th>Дата вылета</th>
							<th>Дата посадки</th><th>Модель самолета</th>
							<th>Номер места</th><th>Цена</th><th>Баланс пользователя</th><th>Статус заказа</th>";
					do 
					{
						echo "<tr>";
						for($i = 0; $i < count($row) - 1; $i++)
						{
							if($row[$i])
							{
								echo "<td>";
								if($i == 5 || $i == 6)
								{
									echo $ap_name[$row[$i]-1] . "(" . $city[$row[$i]-1] . ")";
								}
								elseif($i == 9)
								{
									echo $aircraft[$row[$i] - 1];
								}
								elseif($i == 13)
								{
									if($row[$i] == 1)
									{
										echo "<form action='' method='post' >";
										echo "<input name='stat' type='radio' value='2'>Apply";
										echo "<input name='stat' type='radio' value='3'>Cancel";
										echo "<input name='ticket' type='hidden' value='$row[0]'>";
										echo "<input name='amount' type='hidden' value='$row[11]'>";
										echo "<input name='acc_id' type='hidden' value='$row[14]'>";
										echo "<input type='submit' value='Выбрать'>";
										echo "</form>";
										
									}
									elseif($row[$i] == 2)
									{
										echo "Confirmed";
									}
									else
									{
										echo "Canceled";
									}
								}
								else
								{
									echo $row[$i];
								}
								echo "</td>";
							}
						}
										

						echo "</tr>";
					} while($row = $stmt->fetch(PDO::FETCH_NUM));
					echo "</table>";
				}
				else
				{
					echo "<br>Заказов нет";
				}
			}
			else 
			{
				echo "<a href='private.php'>Скрыть список заказов</a>";
				// Запросить список билетов
				$info = aboutUser();
				$acc_id = $info->acc_id;
				$sql = "SELECT tickets.ticket_no, flights.f_num, flights.departure_airport, flights.arrival_airport,
						flights.scheduled_arrival, flights.scheduled_departure, flights.a_code, tickets.seat_no, tickets.amount, orders.status
						FROM orders JOIN tickets ON orders.acc_id = '$acc_id' and orders.ticket_no = tickets.ticket_no
						JOIN flights ON tickets.f_id = flights.f_id";
				$stmt = $db->query($sql);
				if($row = $stmt->fetch(PDO::FETCH_NUM))
				{
					echo "<table border>";
					echo "<tr><th>№ билета</th><th>№ рейса</th><th>Откуда</th><th>Куда</th>
							<th>Дата вылета</th><th>Дата посадки</th><th>Модель самолета</th><th>Номер места</th><th>Цена</th><th>Статус заказа</th>";
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
								elseif($i == 9)
								{
									if($row[$i] == 1)
									{
										echo "Waiting for confirmation";
									}
									elseif($row[$i] == 2)
									{
										echo "Confirmed";
									}
									else
									{
										echo "Canceled";
									}	
								}
								
								else
								{
									echo $row[$i];
								}
								echo "</td>";
							}
						}
						echo "</tr>";
					} while ($row = $stmt->fetch(PDO::FETCH_NUM));
					echo "</table>";
				}
				else
				{
					echo "<br>Заказов нет";
				}
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