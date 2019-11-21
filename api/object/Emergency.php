<?php

require_once "DbHandler.php";

class Emergency
{
    private $db;
    private $type;
    public function __construct($type = null)
    {
        $this->type = $type;
        $this->db = new  DbHandler();
        //$this->type =

    }
    public  function setEmergency($userId){
        $date = date("Y-m-d H:i:s");
        $notify_elapse = date("Y-m-d H:i:s",strtotime("+1 minute"));
        $emergencyId = $this->db->executeGetId("INSERT INTO emergency (user_id, type_id,date,notify_elapse) VALUES (?,?,?,?)", array($userId, $this->type,$date,$notify_elapse));
        return $emergencyId;
    }
    public function getEmergency($emergencyId = null){
        if($emergencyId == null){
            return $this->db->getAll("select e.id,user_id,narration,location,media,type_id,date,incidence from emergency e left join incident_type i on e.incident_type_id = i.id where type_id = ? order by date desc ",array($this->type));
        }
        return $this->db->getOne("select e.id,user_id,narration,location,media,type_id,date,incidence from emergency e left join incident_type i on e.incident_type_id = i.id where e.id = ? ", array($emergencyId));
    }
    public function setOptionalField($emergencyId, $field, $value){
        $sql = sprintf("UPDATE emergency set %s = ? where  id = ?",$field);
        $this->db->execute($sql,array($value,$emergencyId));
    }
    public function getRecords(){
        $records = $this->db->getAll("SELECT e.id, narration, location,lat,lng,e.date,media,user_id FROM emergency e left join location l on e.id = l.emergency_id where e.type_id = ?", array($this->type));
        return $records;
    }
    public function getRecordInfo($recordId, $label = null){
        $info = $this->db->getOne("SELECT e.id, narration, location,lat,lng,e.date,media,user_id FROM emergency e left join location l on e.id = l.emergency_id where e.id = ?",array($recordId));
        if($label != null){
            return $info[$label];
        }
        return $info;
    }
    public function getLabel(){
        $label = $this->db->getOne("select label from type where id = ?",array($this->type));
        return $label['label'];
    }
    public function is_valid(){
        $type = $this->db->getOne("select id from type where id = ? or arg = ?",array($this->type,$this->type));
        if(!$type){
            return false;
        }
        $this->type = $type['id'];
        return true;
    }
    public function valid_incident($incidentId){
        $chck = $this->db->getOne("select id from incident_type where id = ?",array($incidentId));
        if($chck == null){
            return false;
        }
        return true;
    }
    public function getAllType(){
        $types = $this->db->getAll("select id,label as text from type ");
        return $types;
    }
    public function getAllIncType(){
        $types = $this->db->getAll("select id, incidence as text from incident_type");
        return $types;
    }
    public function getPublicEmergencyNumbers(){
        $numbers = $this->db->getAll("select * from contacts");
        return $numbers;
    }
    public function removeEmergency($emergencyId){
        $this->db->execute("delete from emergency where id = ?",array($emergencyId));
    }
    public function setLocation($emergencyId, $lat,$lng,$alt,$spd){
        $existingLatLng = $this->db->getOne("select lat, lng from location where lat = ? and lng = ? and emergency_id = ? ", array($lat,$lng,$emergencyId));
        if($existingLatLng){
            return false;
        }
        $this->db->execute("insert into location (emergency_id, lat, lng,altitude,speed) VALUES (?,?,?,?,?)",array($emergencyId,$lat,$lng,$alt,$spd));
    }
    public function getLocation($emergencyId , $motion = false, $date = null){
        $origin =  $this->db->getOne("SELECT lat,lng, date, altitude, speed FROM location where emergency_id = ? order by date" ,array($emergencyId));
        if(!$motion){
            return $origin;
        }
        $dest = $this->db->getAll("SELECT lat,lng, date, altitude, speed FROM location where emergency_id = ? and date > ? order by date ",array($emergencyId, $date));
        return array($origin,$dest);

    }
}