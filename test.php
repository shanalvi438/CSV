<?php 
require 'vendor/autoload.php';
require 'db.php';

use Google\Client;
use Google\Service\Drive;

$client = new Google\Client();
$client_sec = "google_keys.json";
$client->setAuthConfig($client_sec);
$client->setApplicationName('TechVengers Portal');
$client->setScopes(Google\Service\Drive::DRIVE);
$service = new Drive($client);
$date_start = date("Y-m-d 00:00:00",strtotime('-1 month'));
$date_end = date("Y-m-d 00:00:00");
// print_r($date_start);
// echo "<br>";
// print_r($date_end);
// die;
$users = "SELECT * FROM t_portal_users";

$users_result = $config->mysqli->query($users);

if($users_result->num_rows > 0){
	
	while($row = $users_result->fetch_assoc()){
		$sheet = array();
		$arr = array();
		$sheet['indate'] = 'In Date';
	    $sheet['day'] = 'Day';
	    $sheet['intime'] = 'In Time';
	    $sheet['outtime'] = 'Out Time';
	    $sheet['remarks'] = 'Remarks';
	    $arr[] = $sheet;
		$user_id = $row['id'];
		$user_name = $row['first_name'].' '.$row['last_name'];
		$sql = "SELECT * FROM t_portal_attendance WHERE user_id = {$user_id} AND in_time BETWEEN '{$date_start}' AND '{$date_end}'";

		$results = $config->mysqli->query($sql);

		if($results->num_rows > 0){

			while($attendances = $results->fetch_assoc()){
				$date = date_create($attendances['in_time']);
				$indate = date_format($date,"d-m-20y");
				$date_n = date_create($attendances['out_time']);
				$outtime = date_format($date_n,"h:i:s A");
				$intime = date_format($date,"h:i:s A");
				$sheet['indate'] = $indate;
			    $sheet['day'] =date_format($date,"l");
			    $sheet['intime'] = $intime;
			    $sheet['outtime'] = $outtime;
			    $sheet['remarks'] = '';
			    $arr[] = $sheet;
			}
		}
		if(isset($arr[1])){
			$csv_path = "Mycsv.xlsx";
            $f = fopen($csv_path, 'w'); 

            // Write to the csv
            foreach($arr as $ar){
               fputcsv($f, $ar); 
            }
            // Close the file
            fclose($f);

            $folder_name = date("F-Y",strtotime('-1 month'));
                // print_r($folder_name);
                // die;
            $results = $service->files->listFiles(array(
                    
                'q' => "name='{$folder_name}'ANDmimeType='application/vnd.google-apps.folder'",
                'spaces' => 'drive',
                'fields' => 'nextPageToken, files(id, name)'
                
            ));
            $result_name = $results->getFiles();
            
            if(isset($result_name[0])){

                $folder_id = $result_name[0]->id;

            }else{
                $file = new Google_Service_Drive_DriveFile();
                $file->setMimeType('application/vnd.google-apps.folder');
                $file->setName($folder_name);
                $file->setOwners('techvengers856@gmail.com');
                $file->setParents(array('1_9yEuCUcNnWbhpuFOEAvdS5U0CL0FX5X'));
                $createdFile = $service->files->create($file, array('mimeType' => 'application/vnd.google-apps.folder'));
                $folder_id = $createdFile->id;
            }

            try{

                $files = array();
                $pageToken = null;
                $results = $service->files->get('1_9yEuCUcNnWbhpuFOEAvdS5U0CL0FX5X');
                $folder_name = $results->getName();

                if($folder_name || $folder_name !== ""){
                    $mycsv_data = file_get_contents($csv_path);
                    try {
                        $folder_name = $user_name;
                        $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                        $file = new Google_Service_Drive_DriveFile();
                        $file->setMimeType($mime);
                        $file->setName($folder_name);
                        $file->setOwners('techvengers856@gmail.com');
                        $file->setParents(array($folder_id));
                        $createdFile = $service->files->create($file, array('data' => $mycsv_data, 'mimeType' => $mime, 'uploadType' => 'resumable'));
                        $fileId = $createdFile->getId();
                         $ownerPermission = new Google_Service_Drive_Permission();
                         $ownerPermission->setEmailAddress("techvengers856@gmail.com");
                         $ownerPermission->setType('user');
                         $ownerPermission->setRole('writer');
                         try {
                             $service->permissions->create($fileId, $ownerPermission);
                             // return $createdFile;

                         } catch (Exception $e) {
                             // echo "permission2: ".$e;
                         }

                    }catch(Exception $e) {
                       // echo "Error Message: ".$e;
                    }
                }
                
            }catch(Exception $e){
               // return "Error Message 2: ".$e;
            }
		}
	}
				echo "<pre>";print_r( $arr);echo "</pre>";
}