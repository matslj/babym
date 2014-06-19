<?php

// ===========================================================================================
//
// Class: CBabyDataDAO
//
// 
// Author: Mats Ljungquist
//
class CBabyDataDAO {
    
    public static $LOG = null;
    
    private $babyData = null;
    private $userId = null;
    private $dates = null;
    private $selectedDate = "";
    
    private function __construct($userId) {
        $this->userId = $userId;
        $this -> dates = array();
        $this -> selectedDate = null;
    }
    
    public static function newEmptyInstance($userId) {
        $bData = new self($userId);
        return $bData;
    }
    
    public static function newDatesInitializedInstance($db, $userId) {
        $bData = new self($userId);
        
        $tBabyData = DBT_BabyData;

        // Create the query
        $query = <<< EOD
        SELECT DISTINCT
            date(dateBabyData) AS date
        FROM {$tBabyData}
        WHERE userBabyData_idUser = {$userId};
EOD;

        // Perform the query and manage results
        $result = $db->Query($query);

        while($row = $result->fetch_object()) {
            $bData -> dates[] = $row -> date;
        }

        $result->close();
        return $bData;
    }
    
    public static function newInitializedInstance($db, $userId, $selectedDate = "") {
        $bData = new self($userId);
        
        self::$LOG = logging_CLogger::getInstance(__FILE__);
        
        // self::$LOG -> debug(" **** I konstruktorn för CBabyDataDAO(db, {$userId}) ****");
        
        $bData -> babyData = array();
        
        $tBabyData = DBT_BabyData;
        
        $extendedWhere = "";
        if (!empty($selectedDate)) {
            $extendedWhere .= " AND date(dateBabyData) = date('{$selectedDate}') ";
            $bData -> selectedDate = $selectedDate;
        }
        
        // Create the query
        $query = <<< EOD
        SELECT 
            idBabyData AS id, 
            userBabyData_idUser AS user, 
            typeBabyData AS type, 
            valueBabyData AS value,
            noteBabyData AS note,
            dateBabyData AS date
        FROM {$tBabyData}
        WHERE userBabyData_idUser = {$userId}
            {$extendedWhere}
        ORDER BY date asc, id asc;
EOD;

        // Perform the query and manage results
        $result = $db->Query($query);
        
        $lastDate = null;
        while($row = $result->fetch_object()) {
            // self::$LOG -> debug(" looping ");
            $temp = new CBabyData($row -> id, $row -> type, $row -> value, $row -> date, $row -> note);
            
            // self::$LOG -> debug($temp -> getDate() -> getDate);
            
            if (empty($lastDate) || $lastDate -> compareWithoutTime($temp ->getDate() -> getTimestamp()) != 0) {
                // self::$LOG -> debug(" found new date ");
                $lastDate = $temp -> getDate();
                $bData -> babyData[$lastDate -> getDate()] = array();
                $bData -> dates[] = $lastDate -> getDate();
            }
            // self::$LOG -> debug(" count 1: " . count($bData -> babyData[$lastDate -> getDate()]));
            $bData -> babyData[$lastDate -> getDate()][] = $temp;
            // self::$LOG -> debug(" count 2: " . count($bData -> babyData[$lastDate -> getDate()]));
        }
        
        // self::$LOG -> debug(" done ");
        
        $result -> close();
        return $bData;
    }
    
    public function getAllBabyData($db, $sortOrder = "ASC") {
        
        self::$LOG = logging_CLogger::getInstance(__FILE__);
        
        // self::$LOG -> debug(" **** I konstruktorn för CBabyDataDAO(db, {$userId}) ****");
        
        $bData = array();
        
        $tBabyData = DBT_BabyData;
        
        // Create the query
        $query = <<< EOD
        SELECT 
            idBabyData AS id, 
            userBabyData_idUser AS user, 
            typeBabyData AS type, 
            valueBabyData AS value,
            noteBabyData AS note,
            dateBabyData AS date
        FROM {$tBabyData}
        WHERE userBabyData_idUser = {$this -> userId}
        ORDER BY date {$sortOrder}, id {$sortOrder};
EOD;

        // Perform the query and manage results
        $result = $db->Query($query);
        
        while($row = $result->fetch_object()) {
            // self::$LOG -> debug(" looping ");
            $bData[] = new CBabyData($row -> id, $row -> type, $row -> value, $row -> date, $row -> note);
        }
        
        // self::$LOG -> debug(" done ");
        
        $result -> close();
        return $bData;
    }
    
    function getAllBabyDataAsJSON($db, $sortOrder = "ASC") {
        $temp = $this ->getAllBabyData($db, $sortOrder);
        return $this ->getBabyDataArrayAsJSON($temp);
    }
    
    function getDatesAsJavascriptArray() {
        if ($this->dates != null) {
            $jsMarkedDatesArray = "[";
            $firstRound = true;
            foreach ($this->dates as $value) {
                if (!$firstRound) {
                    $jsMarkedDatesArray .= ",";
                }
                $jsMarkedDatesArray .= "'" . $value . "'";
                $firstRound = false;
            }
            $jsMarkedDatesArray .= "]";
            return $jsMarkedDatesArray;
        }
        return "[]";
    }
    
    private function getBabyDataArrayAsJSON(array $array) {
        if (empty($array)) {
            return "[]"; 
        } else {;
            foreach ($array as &$value) {
                $value = $value->toJson();
            }
            return json_encode($array);
        }
    }
    
    function getBabyDataByDate($date) {
        if (array_key_exists($date, $this -> babyData)) {
            $temp = $this -> babyData[$date];
            return $this ->getBabyDataArrayAsJSON($temp);
        }
        return "[]";
    }
    
    function getBabyDataForSelectedDateAsJSON() {
        if (!empty($this->selectedDate)) {
            return $this->getBabyDataByDate($this->selectedDate);
        }
        return "[]";
    }
    
    function getBabyDataAsHtml() {
        $html = "";
        
        self::$LOG -> debug(" **** I getBabyDataAsHtml() ****");
        
        foreach ($this -> babyData as $key => $data) {
            self::$LOG -> debug(" hello + count: " . count($data));
            $html .= "<p>" . $key . "</p>";
            $html .= "<table>";
            foreach ($data as $value) {
                $html .= "<tr class='" . strtolower($value -> getType()) . "'>";
                $html .= "<td>{$value -> getConvertedType()}</td>";
                $html .= "<td>{$value -> getValue()}</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        }
        
        self::$LOG -> debug($html);
        
        return $html;
    }

} // End of Of Class

?>