<?php
// // Set the default timezone to your desired timezone
// date_default_timezone_set('Your_Timezone');

// // Get the current date
// $currentDate = date('Y-m-d',strtotime('-1 day'));

// // Get the day of the week
// $dayOfWeek = date('l', strtotime($currentDate));

// // Display the result
// echo "Today is $dayOfWeek, $currentDate";

// $date_start = date("Y-m-l 00:00:00",strtotime('-1 month'));
// $date_end = date("Y-m-d-l 00:00:00");

// print_r($date_start);
// echo "<br>";
// print_r($date_end);
// die;


// Display the month name for a specific date
$specificDate = date("F-Y",strtotime('-1 month'));;
// echo date('F', strtotime($specificDate)); // Output: December
print_r($specificDate);

// Using DateTime class
// $specificDateObj = new DateTime($specificDate);
// echo $specificDateObj->format('F'); // Output: December


?>