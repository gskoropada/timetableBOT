<?php

$URLS = [["https://tafetimetables.rmit.edu.au/btsTT/btstimetables.asp?desc=7&group=IT5C&sem=S2&yr=14","IT5C"],
["https://tafetimetables.rmit.edu.au/btsTT/btstimetables.asp?desc=7&group=IT5B&sem=S2&yr=14","IT5B"],
["https://tafetimetables.rmit.edu.au/btsTT/btstimetables.asp?desc=7&group=IT5A&sem=S2&yr=14","IT5A"]];

foreach($URLS as $URL) {

	echo "<h1>".$URL[0]."</h1>";
	
	$table = getTable($URL[0]);
	
	$course = getTableData($table);
	
	dbStore($course, $URL[1]);

}

function getTable($URL) {
	$page = file_get_contents($URL);
	
	$key = "table_wrap'>";
	
	$page = stristr($page, $key);
	
	$end = stripos($page, "</div>");
	
	$page = substr($page, strlen($key), $end);
	
	return $page;
}

function getTableData($table) {
	$rows = explode("<tr",$table);
	
	$i = 0;
	
	foreach($rows as $row) {
		$course_rough[$i] = explode("<td>",$row);
		$i++;
	}
	
	$count = count($course_rough);
	
	$n=0;
	
	$line = Array();
	
	$course = Array();
	
	for($i=2; $i<$count; $i++) {
	
		$course[$i-2]['code'] = explode("</td>",$course_rough[$i][1])[0];
		$course[$i-2]['name'] = explode("</td>",$course_rough[$i][2])[0];
		$course[$i-2]['day'] = explode("</td>",$course_rough[$i][5])[0];
		$course[$i-2]['start'] = explode("</td>",$course_rough[$i][6])[0];
		$course[$i-2]['end'] = explode("</td>",$course_rough[$i][7])[0];
		$course[$i-2]['location'] = explode("</td>",$course_rough[$i][8])[0];
	
	}
	
	return $course;
}

function dbStore($course, $group) {
	
	$query = "INSERT INTO table (group, code, name, day, start, end, location) VALUES";
	
	$i = 0;
	
	foreach($course as $data) {
		$i++;

		$query.=" ('".$group."','".$data['code']."','".$data['name']."','".$data['day']."','"
				.$data['start']."','".$data['end']."','".$data['location']."')";	
		if($i!=count($course)) {
			$query .= " , ";
		}
	}
	
	echo $query;
}

?>