<?php
include 'config.php';
$arr = array();
$request = file_get_contents('php://input');
$input = json_decode($request);
if(isset($input->results)){
  $resultArray = array();
  for ($i = 0; $i < count($input->results->data); $i++) {
  	$resultArray[] = $input->results->data[$i]->pid;
  }

	if (!($getpolicyid = $conn->prepare("SELECT PolicyID FROM policydeatils WHERE Name IS NULL OR
		Name = '' OR Contact_No IS NULL OR Contact_No= '' "))) {
		DisplayErrMsg(sprintf("internal error11 %d:%s\n", $pdo->errorCode(), $pdo->errorInfo()));
		return 0;
	}
	$getpolicyid->execute();
	$pidArray = array();
	while($getpolicyrow = $getpolicyid->fetch()) {
		if(!($insertQuery = $conn->prepare("UPDATE policydeatils SET name = ?, Contact_No = ? WHERE PolicyID = ?"))) {
			DisplayErrMsg(sprintf("internal error11 %d:%s\n", $pdo->errorCode(), $pdo->errorInfo()));
			return 0;
		}
		$insertQuery->execute([$input->results->data[$getpolicyrow['PolicyID']-1]->name, $input->results->data[$getpolicyrow['PolicyID']-1]->contactno, $input->results->data[$getpolicyrow['PolicyID']-1]->pid]);
     }
     $in = str_repeat('?,', count($resultArray)-1).'?';
     if(!($getPidQuery = $conn->query("SELECT DISTINCT(PolicyID) FROM policydeatils"))) {
     	DisplayErrMsg(sprintf("internal error11 %d:%s\n", $pdo->errorCode(), $pdo->errorInfo()));
		return 0;
     }
     $oldPidArray = array();
     while ($getPidQueryRow = $getPidQuery->fetch()) {
     	$oldPidArray[] = $getPidQueryRow["PolicyID"];
     }
     $newArray = array_diff($resultArray, $oldPidArray);
     foreach ($newArray as $key => $value) {
     	if(!($insertQuery = $conn->prepare("INSERT INTO policydeatils values(?,?,?,?,?,?)"))) {
     		DisplayErrMsg(sprintf("internal error11 %d:%s\n", $pdo->errorCode(), $pdo->errorInfo()));
			return 0;		
     	}
     	$insertQuery->execute([$input->results->data[$value-1]->pid, $input->results->data[$value-1]->name, $input->results->data[$value-1]->dob, $input->results->data[$value-1]->contactno, $input->results->data[$value-1]->amount, $input->results->data[$value-1]->code]);
     }
     $arr["result"] = "Successfully Updated"; 
}
else{
	$arr["result"] = "No enough information";
}
header('content-type:application/json');
echo json_encode($arr);
?>
