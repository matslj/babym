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

$payload = $pc->POSTisSetOrSetDefault('payload', '');

$data = json_decode($payload);
$method = $data -> action;

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

$log->debug("@@@@@@@@@@@@@@@@@@@ Current command: '{$method}' @@@@@@@@@@@@@@@@@@@@@");

// -------------------------------------------------------------------------------------------
//
// Get content of file archive from database
//
$db = new CDatabaseController();
$mysqli = $db->Connect();

$userId = 2;

if (strcmp("selectDate", $method) == 0) {
    if (validateDate($data -> date)) {
        $babyDataDAO = CBabyDataDAO::newInitializedInstance($db, $userId, $data -> date);
        $jsonResult = $babyDataDAO->getBabyDataForSelectedDateAsJSON();
        // $log->debug(print_r($jsonResult, TRUE));
    }
} else if (strcmp("showall", $method) == 0) {
    $babyDataDAO = CBabyDataDAO::newEmptyInstance($userId);
    $jsonResult = $babyDataDAO->getAllBabyDataAsJSON($db);
} else if (strcmp("post", $method) == 0) {
    $type = $data -> type;
    $value = $data -> value;
    $datetime = $data -> datetime . ":00";
    $note = $data -> note;
    
    // Validate/sanitize input
    $error = false;
    $error = $error || !CBabyData::testType($type);
    $error = $error || !validateDate($datetime, 'Y-m-d H:i:s');
    $log->debug($value);
    if (strcmp("Pee", $type) != 0 && strcmp("Poo", $type) != 0) {
        $tempVal = str_replace(",", ".", $value);
        $error = $error || !is_numeric($tempVal);
    } else {
        $error = $error || !(strcasecmp("ja", $value) == 0 || strcasecmp("nej", $value) == 0);
        // $log -> debug("Error: " . $error);
    }
    $note = $mysqli->real_escape_string($note);
    
    if (!$error) {
        // Get db-function name
        $spCreateBabyData = DBSP_CreateBabyData;

        $query = "SET @aBabyDataId = 0;";
        $query .= "CALL {$spCreateBabyData}({$userId}, '{$type}', '{$value}', '{$note}', '{$datetime}', @aBabyDataId);";
        $query .= "SELECT @aBabyDataId AS id;";
        $res = $db->MultiQuery($query);

        // Use results
        $results = Array();
        $db->RetrieveAndStoreResultsFromMultiQuery($results);
        //$log -> debug("fetching result");
        // Retrieve and update the id of the BabyData-object
        $row = $results[2]->fetch_object();
        //$log -> debug("getting row id");
        $bDataId = $row->id;

        //$log -> debug("closing");
        // Close the result set
        $results[2]->close();

        if (!empty($bDataId)) {
            $bData = new CBabyData($bDataId, $type, $value, $datetime, $note);
            $tempArray = array();
            $tempArray[] = ($bData -> toJson());
            $jsonResult = json_encode($tempArray);
        }
    }
    // If insert did not go through, no id value will be returned. Test for existance of id in client.
} else if (strcmp("delete", $method) == 0) {
    CPageController::IsNumericOrDie($data -> id);
    $spDeleteBabyData = DBSP_DeleteBabyData;
    $queryDelete = "CALL {$spDeleteBabyData}({$data -> id});";
    $res = $db->MultiQuery($queryDelete);
    $nrOfStatements = $db->RetrieveAndIgnoreResultsFromMultiQuery();
    
    $status = "OK";

    if($nrOfStatements != 1) {
        // Delete not OK
        self::$LOG -> debug("ERROR: Kunde inte radera post med id: " . $data -> id . " - number of statements: " . $nrOfStatements);
        $status = "ERROR";
    }
    
    $jsonResult .= <<< EOD
    {
        "status": "{$status}"
    }
EOD;
}

$mysqli->close();

$log->debug($jsonResult);

// Print the header and page
$charset	= WS_CHARSET;
header("Content-Type: text/html; charset={$charset}");
echo $jsonResult;
exit;

?>