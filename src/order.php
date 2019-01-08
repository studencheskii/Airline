<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<a href="index.php">Главная | </a>
<a href="flights.php">Рейсы | </a>
<?php
require_once ("includes/sessions.php");
if(login())
{
	echo "<a href='private.php'>Личный кабинет | </a>";
	echo "<a href='logout.php'>Выход</a><hr />";
	
	
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
	
		$stmt = $db->query('SELECT a_model, a_capacity from aircrafts');
		
		while($row = $stmt->fetch())
		{
			$aircrafts[] = $row['a_model'];
			$air_capacity[] = $row['a_capacity'];
		}
	
	
	if(isset($_POST['order']))
	{
		$id = htmlspecialchars(trim($_POST['id']));
		$sql = 'SELECT * from flights where f_id = :id';
		$params = [':id' => $id];
		$stmt = $db->prepare($sql);
		$stmt->execute($params);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		
		echo "Заказ билета на рейс №".$result->f_num ."<br>";
		echo "Из " . $ap_name[$result->departure_airport-1]. "(". $city[$result->departure_airport-1] . ")" . " в " . $ap_name[$result->arrival_airport-1] . "(". $city[$result->arrival_airport-1].")" . "<br>";
		echo "Дата вылета: " . $result->scheduled_departure . "<br>";
		echo "Дата посадки: " . $result->scheduled_arrival . "<br>";
		echo "Модель самолета: " . $aircrafts[$result->a_code - 1] . "<br>";
		echo "Количество мест: " . $air_capacity[$result->a_code - 1]. "<br>";
		echo "<form action='' method='post'>";
		
		$q = 'SELECT seat_no,amount FROM tickets  where f_id = :id and ticket_no not in (select ticket_no from orders)';
		$stmt = $db->prepare($q);
		$stmt->execute($params);
		if($seat = $stmt->fetch())
		{
			print_r($seat);
			echo "Цена билета: " . $seat[1] . "<br>";
			echo "Номер места: ";
			echo "<select name='seat_num'>";
			do
			{
				print_r($seat);
				echo "<br>";
				if($seat)
				{
					//echo $seat->seat_no;
					echo "<option value='" . $seat[0] . "'>" . $seat[0] .  "</option>";
				}
			} while($seat = $stmt->fetch());
			echo "</select><br>";
			
			echo "<input type='hidden' name='id' value='".$result->f_id."'>";
			echo "<input type='submit' value='Заказать' name='ordered'>";
			
		}
		else 
		{
			echo "Заказ не возможен. Закончились билеты на этот рейс";
		}
		
		echo "</form>";
	}
}
/*function generateTicketNum()
{
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $result = '';
    for ($i = 0; $i < 13; $i++)
        $result .= $characters[mt_rand(0, 61)];
	return $result;
}
*/
if(isset($_POST['ordered']))
{
	$seat = htmlspecialchars(trim($_POST['seat_num']));
	if($seat)
	{
		$f_id = htmlspecialchars(trim($_POST['id']));
		$row = aboutUser();
		$acc_id = $row->acc_id;
		$amount = $db->query("SELECT amount FROM tickets WHERE '$f_id' = f_id and '$seat' = seat_no")->fetch()[0];
		$query = 'SELECT balance FROM cash WHERE acc_id = :acc_id';
		$params = [':acc_id' => $acc_id];
		$stmt = $db->prepare($query);
		$stmt->execute($params);
		$balance = $stmt->fetch(PDO::FETCH_OBJ);
		if($balance->balance < $amount)
			echo "У вас недостаточно средств.";
		else
		{
			
			$sql = "INSERT INTO `orders`(`ticket_no`, `acc_id`, `status`) 
					SELECT ticket_no, :acc_id, 1 
					FROM tickets
					WHERE f_id = :f_id and seat_no = :seat;
					UPDATE cash SET balance = balance - :amount WHERE :acc_id = acc_id";
			$params = [':acc_id'=>$acc_id, ':f_id' => $f_id, ':seat' => $seat, ':amount' => $amount, ':acc_id' => $acc_id];
			$stmt = $db->prepare($sql);
			$stmt->execute($params);
			
			echo "Заказ оформлен!";
		}
	}
	else
	{
		echo "Не выбрано место для посадки";
	}
}

?>


</body>
</html>