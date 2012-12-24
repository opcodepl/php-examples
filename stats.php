<?php

class Stats {
	private static function change_date($date) {
		$char = $date[0];
		
		$date = substr($date, 1);
		
		$data_values = explode('.', $date);
		
		$day = sprintf("%02d", $data_values[0]);
		$month = $data_values[1];
		
		switch($month) {
			case 'I':		$month = '01'; break;
			case 'II':		$month = '02'; break;
			case 'III':		$month = '03'; break;
			case 'IV':		$month = '04'; break;
			case 'V':		$month = '05'; break;
			case 'VI':		$month = '06'; break;
			case 'VII':		$month = '07'; break;
			case 'VIII':	$month = '08'; break;
			case 'IX':		$month = '09'; break;
			case 'X':		$month = '10'; break;
			case 'XI':		$month = '11'; break;
			case 'XII':		$month = '12'; break;
		}
		
		$year = $data_values[2];
		
		return array($char, $year.'-'.$month.'-'.$day);
	}
	
	private static function db($age, $date, $db_host = 'localhost', $db_name = 'default_dbname', $username = 'default_username', $pass = 'pass') {
		try {
			$pdo = new PDO("mysql:dbname=$db_name;host=$db_host", $username, $pass);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo 'Error: '.$e->getMessage(); exit;
		}
		
		//TODO: remove db queries from loop
		$query = $pdo->query('select id, name, flight_date from planes where flight_date '.$date[0].' \''.$date[1].'\'');
		
		echo '<table border="1" cellspacing="0"><tr><th>Plane Name</th><th>Flight Date</th><th>Female Avg. Age</th><th>Male Avg. Age</th></tr>';
		
		foreach($query as $row) {
			
			echo '<tr><td>'.$row['name'].'</td><td>'.$row['flight_date'].'</td>';
			
			$id = $row['id'];
			
			$q = $pdo->query('select avg(age) as avg from passengers where sex = \'f\' and plane_id = '.$id.' and age '.$age) or die('query error');
			$r = $q->fetch();
			
			$avg = $r['avg']; 
			
			if($avg == 0) {
				$avg = '-1'; 
			} else {
				$avg = number_format(round($avg ,2), 2);
			}
			
			echo '<td>'.$avg.'</td>';
			
			$q = $pdo->query('select avg(age) as avg from passengers where sex = \'m\' and plane_id = '.$id.' and age '.$age) or die('query error');
			$r = $q->fetch();
			
			$avg = $r['avg']; 
			
			if($avg == 0) {
				$avg = '-1'; 
			} else {
				$avg = number_format(round($avg ,2), 2);
			}
			
			echo '<td>'.$avg.'</td></tr>';
		}
		
		
		$query->closeCursor();
		
		echo '</table>';
	}
	
	public static function show_statistics($var) {
		$var = strrev($var);

		$values = explode(';', $var);
		
		$age = str_replace('age', '', $values[0]);
		$date = Stats::change_date(str_replace('date', '', $values[1]));
		
		Stats::db($age, $date);
	}
	
}

Stats::show_statistics('1102.IIX.92<etad;13<ega');

?>