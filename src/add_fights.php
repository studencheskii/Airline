<!DOCTYPE HTML>
<html>
	<head>
		<title>Add flight</title>
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
			if(isAdmin())
			{
				
				echo "<form action='' method='post'>";
				$sql = 'SELECT ap_code , ap_name, city FROM airports';
				$stmt = $db->query($sql);
				echo "<select name='from'>";
				while($row = $stmt->fetch())
				{
					echo "<option value=".$row[0].">".$row[0].".".$row[1].",".$row[2]. "</option>";
				}
				echo "</select>";
				
				echo "<select name='to'>";
				$stmt = $db->query($sql);
				while($row = $stmt->fetch())
				{
					echo "<option value=".$row[0].">".$row[0].".".$row[1].",".$row[2]. "</option>";
				}
				echo "</select>";
				$currentdate = date('Y-m-d\TH:i');
				$findate =  date("Y-m-d\TH:i", strtotime($currentdate . "+1 month"));
				$datefly = date("Y-m-d\TH:i", strtotime($currentdate . "+3 hour"));
				$findatefly = date("Y-m-d\TH:i", strtotime($datefly . "+1 month"));
				echo "<input type='datetime-local' name='calendar1' min='$currentdate'
				max='$findate'>
				<input type='datetime-local' name='calendar2' min='$datefly'
				max='$findatefly'>";
				?>
				<select name='status'>
					<option value='Scheduled'>Scheduled</option>
					<option value='Departed'>Departed</option>
					<option value='Arrived'>Arrived</option>
					<option value='Cancelled'>Cancelled</option>
				</select>
				<?php
				$sql = 'SELECT a_code , a_model FROM aircrafts';
				$stmt = $db->query($sql);
				echo "<select name='model'>";
				while($row = $stmt->fetch())
				{
						if($row)
						{
							echo "<option value='".$row[0]."'>".$row[1]."</option>";
						}
				}
				echo "</select>";
				echo "<input type='submit'>";
				echo "</form>";
			}
		}
		else
		{
			echo"<a href='login.php'>Войти</a><hr />";
			echo "<p>Hello, what is your name? </p>";
		}
		
		if (isset($_POST['from']) && isset($_POST['to'])
			&& isset($_POST['calendar1']) && isset($_POST['calendar2'])
			&& isset($_POST['status']) && isset($_POST['model'])) 
		{
			$from = htmlspecialchars(trim($_POST['from']));
			$to = htmlspecialchars(trim($_POST['to']));
			$calendar1 = htmlspecialchars(trim($_POST['calendar1']));
			$calendar2 = htmlspecialchars(trim($_POST['calendar2']));
			$status = htmlspecialchars(trim($_POST['status']));
			$model = htmlspecialchars(trim($_POST['model']));
			
			
			if (!empty($calendar1) && !empty($calendar2))
			{
				if($from != $to && $calendar1 < $calendar2)
				{
					$sql = 'SELECT f_num from flights where departure_airport = :from and arrival_airport = :to and :calendar1 = scheduled_departure and :calendar2 = scheduled_arrival';
					$params = [':from' => $from, ':to' => $to,':calendar1'=>$calendar1, ':calendar2' => $calendar2 ];
					$stmt = $db->prepare($sql);
					$stmt->execute($params);
					$result = $stmt->fetch(PDO::FETCH_OBJ);
					print_r($result);
					if($result)
					{
						echo "Рейс уже существует.<br>";
						$f_num = $result->f_num;
						echo "Номер рейса: " . $f_num;
					}
					else
					{
						$stmt = $db->query('SELECT max(f_num)+1 FROM flights');
						$val = $stmt->fetch();
						if($val[0])
							$f_num = "000".$val[0];
						else
							$f_num = "0001";
						$sql = 'INSERT INTO `flights` (`f_id`, `f_num`, `scheduled_departure`, `scheduled_arrival`, `departure_airport`, `arrival_airport`, `status`, `a_code`) VALUES (NULL, :f_num, :calendar1, :calendar2, :from, :to, :status, :model)';
						$params = [':f_num' => $f_num, ':calendar1' => $calendar1, ':calendar2' => $calendar2, ':from' => $from, ':to' => $to, ':status' => $status, ':model' => $model];
						$stmt = $db->prepare($sql);
						$stmt->execute($params);
						
						$sql = 'SELECT f_num from flights where f_num = :f_num';
						$params = [':f_num'=>$f_num];
						$stmt = $db->prepare($sql);
						$stmt->execute($params);
						$result = $stmt->fetch(PDO::FETCH_OBJ);
						if($result)
						{
							echo "Рейс успешно добавлен.<br>";
							echo "Номер рейса: " . $f_num;
						}
					}
				}
			}
		}
		?>
	</body>
</html>