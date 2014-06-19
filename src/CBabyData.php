<?php

// ===========================================================================================
//
// Class: CBabyData
//
// 
// Author: Mats Ljungquist
//
class CBabyData {

    private $id;
    private $type;
    private $value;
    private $date; // type:CDate
    private $note;
    
    private $cType;
    private $typeUnit;
    
    private function convertType() {
        switch ($this->type) {
            case "Weight":
                $this -> cType = "Vikt";
                $this -> typeUnit = "kg";
                break;
            case "Height":
                $this -> cType = "Längd";
                $this -> typeUnit = "cm";
                break;
            case "SkullSize":
                $this -> cType = "Skallmått";
                $this -> typeUnit = "cm";
                break;
            case "BreastMilk":
                $this -> cType = "Bröstmjölk";
                $this -> typeUnit = "ml";
                break;
            case "Formula":
                $this -> cType = "Ersättningsmjölk";
                $this -> typeUnit = "ml";
                break;
            case "Poo":
                $this -> cType = "Bajs";
                break;
            case "Pee":
                $this -> cType = "Kiss";
                break;
        }
    }
    
    public static function testType($type) {
        $retVal = false;
        switch ($type) {
            case "Weight":
                $retVal = true;
                break;
            case "Height":
                $retVal = true;
                break;
            case "SkullSize":
                $retVal = true;
                break;
            case "BreastMilk":
                $retVal = true;
                break;
            case "Formula":
                $retVal = true;
                break;
            case "Poo":
                $retVal = true;
                break;
            case "Pee":
                $retVal = true;
                break;
        }
        return $retVal;
    }
    
    function __construct($id, $type, $value, $date, $note) {
        $this->id = $id;
        $this->type = $type;
        $this->value = $value;
        $this->date = CDate::getInstanceFromMysqlDatetime($date);
        $this->note = $note;
        $this->convertType();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        $this->convertType();
    }
    
    public function getConvertedType() {
        return $this->cType;
    }

    public function getValue() {
        if (strcmp($this->type, "Pee") == 0 || strcmp($this->type, "Poo") == 0) {
            return strcmp($this->value, "true") != 0 ? "Ja" : "Nej";
        } else {
            return $this->value;
        }
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }
    
    public function getTypeUnit() {
        return $this->typeUnit;
    }
    
    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }
        
    public function toJson() {
        
        return array(
            "id" => $this->getId(),
            "type" => $this->getType(),
            "typec" => $this->getConvertedType(),
            "unit" => $this->getTypeUnit(),
            "value" => $this->getValue(),
            "date" => $this->getDate()->getDate(),
            "time" => $this->getDate()->getHour() . ":" . $this->getDate()->getMinute(),
            "timestamp" => $this->getDate()->getTimestamp(),
            "note" => $this->getNote(),
        );
    }

} // End of Of Class

?>