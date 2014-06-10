<?php
// ===========================================================================================
//
// File: PScoreboard.php
//
// Description: This provides the content for a score board dialog in html format.
//
// Author: Mats Ljungquist
//

$log = logging_CLogger::getInstance(__FILE__);

$pc = CPageController::getInstance(FALSE);

// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();
$intFilter->FrontControllerIsVisitedOrDie();

$method = $pc->POSTisSetOrSetDefault('method', '');
$payload = $pc->POSTisSetOrSetDefault('payload', '');

/**
 * Validates that a string is a valid date.
 * 
 * @param type $date the date string to be validated.
 * @param type $format the format to validate against. Y-m-d is default.
 * @return type true if valid false otherwise
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

$jsonResult = "";

$log->debug("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ starting filtering page @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");

// -------------------------------------------------------------------------------------------
//
// Get content of file archive from database
//
$db = new CDatabaseController();
$mysqli = $db->Connect();

$userId = 2;


if (strcmp("selectDate", $method) == 0) {
    $log->debug("hääär");
    $data = json_decode($payload);
    $log->debug("data1: " . $data);
    if (validateDate($data)) {
        $babyData = CBabyDataDAO::newInitializedInstance($db, $userId, $data);
        $log->debug("data2: " . $data);
        $jsonResult = $babyData->getBabyDataForSelectedDateAsJSON();
        $log->debug(print_r($jsonResult, TRUE));
    }
    // $jsonResult = $babyData->getBabyDataByDate($)
} else if (strcmp("showall", $method) == 0) {
    $babyData = CBabyDataDAO::newEmptyInstance($userId);
    $jsonResult = $babyData->getAllBabyDataAsJSON($db);
} else if (strcmp("post", $method) == 0) {
    $babyData = CBabyDataDAO::newEmptyInstance($userId);
    $data = json_decode($payload);
    $type = $data -> type;
    $value = $data -> value;
    $datetime = $data -> datetime . ":00";
    $note = $data -> note;
    
    // $log -> debug($type . " " . $value);
    
    // Get db-function name
    $spCreateBabyData = DBSP_CreateBabyData;
                
    $query = "SET @aBabyDataId = 0;";
    $query .= "CALL {$spCreateBabyData}({$userId}, '{$type}', '{$value}', '{$note}', '{$datetime}', @aBabyDataId);";
    $query .= "SELECT @aBabyDataId AS id;";
    $res = $db->MultiQuery($query);

    // Use results
    $results = Array();
    $db->RetrieveAndStoreResultsFromMultiQuery($results);
    $log -> debug("fetching result");
    // Retrieve and update the id of the Match-object
    $row = $results[2]->fetch_object();
    $log -> debug("getting row id");
    $bDataId = $row->id;

    $log -> debug("closing");
    // Close the result set
    $results[2]->close();
    
    if (!empty($bDataId)) {
        $bData = new CBabyData($bDataId, $type, $value, $datetime, $note);
        $tempArray = array();
        $tempArray[] = ($bData -> toJson());
        $jsonResult = json_encode($tempArray);
    }
    // $jsonResult = $babyData->getAllBabyDataAsJSON($db);
}

$mysqli->close();

$log->debug($jsonResult);

// Print the header and page
$charset	= WS_CHARSET;
header("Content-Type: text/html; charset={$charset}");
echo $jsonResult;
exit;

?>