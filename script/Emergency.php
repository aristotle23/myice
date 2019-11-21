<?php

class Emergency extends DbHandler
{
    private $db;
    private $type;
    public function __construct($type = null)
    {
        parent::__construct();
        $this->type = $type;
        //$this->type =

    }
    public  function setEmergency($userId){

        $emergencyId = $this->executeGetId("INSERT INTO emergency (user_id, type_id) VALUES (?,?)", array($userId, $this->type));

        return $emergencyId;
    }
    public function getEmergency($emergencyId = null,$offset = null,$perpage = null){

        $limit = " limit ".$offset.",".$perpage;
        //$limit = ($offset == null || $perpage == null) ? "" : " limit ".$offset.",".$perpage;
        if($emergencyId == null){
            $limit = "";
            return $this->getAll("select e.id,user_id,narration,location,media,type_id,date,incidence from emergency e left join incident_type i on e.incident_type_id = i.id where type_id = ? order by date desc ".$limit,array($this->type));
        }
        $limit = "";
        return $this->getOne("select e.id,user_id,narration,location,media,type_id,date,incidence from emergency e left join incident_type i on e.incident_type_id = i.id where e.id = ? ".$limit, array($emergencyId));
    }
    public function setOptionalField($emergencyId, $field, $value){
        $sql = sprintf("UPDATE emergency set %s = ? where  id = ?",$field);
        $this->execute($sql,array($value,$emergencyId));
    }
    public function getRecords(){
        $records = $this->getAll("SELECT e.id, narration, location,lat,lng,e.date,media,user_id FROM emergency e left join location l on e.id = l.emergency_id where e.type_id = ?", array($this->type));
        return $records;
    }
    public function getRecordInfo($recordId, $label = null){
        $info = $this->getOne("SELECT e.id, narration, location,lat,lng,e.date,media,user_id FROM emergency e left join location l on e.id = l.emergency_id where e.id = ?",array($recordId));
        if($label != null){
            return $info[$label];
        }
        return $info;
    }
    public function getLabel(){
        $label = $this->getOne("select label from type where id = ?",array($this->type));
        return $label['label'];
    }
    public function is_valid(){
        $type = $this->getOne("select id from type where id = ? or arg = ?",array($this->type,$this->type));
        if(!$type){
            return false;
        }
        $this->type = $type['id'];
        return true;
    }
    public function valid_incident($incidentId){
        $chck = $this->getOne("select id from incident_type where id = ?",array($incidentId));
        if($chck == null){
            return false;
        }
        return true;
    }
    public function getAllType(){
        $types = $this->getAll("select id,label as text from type ");
        return $types;
    }
    public function getAllIncType(){
        $types = $this->getAll("select id, incidence as text from incident_type");
        return $types;
    }
    public function getPublicEmergencyNumbers(){
        $numbers = $this->getAll("select * from contacts");
        return $numbers;
    }
    public function removeEmergency($emergencyId){
        $this->execute("delete from emergency where id = ?",array($emergencyId));
    }
    public function setLocation($emergencyId, $lat,$lng,$alt,$spd){
        $existingLatLng = $this->getOne("select lat, lng from location where lat = ? and lng = ? and emergency_id = ? ", array($lat,$lng,$emergencyId));
        if($existingLatLng){
            return false;
        }
        $this->execute("insert into location (emergency_id, lat, lng,altitude,speed) VALUES (?,?,?,?,?)",array($emergencyId,$lat,$lng,$alt,$spd));
    }
    public function getLocation($emergencyId , $motion = false, $date = null){
        global $firebase;
        $origin = $firebase->get(FIREBASEPATH."/".$emergencyId,array("orderBy" => '"date"',"limitToFirst"=>1));
        $arr = json_decode($origin,true);
        /**
         * if emergency not not in firebase get emergency in database
         */
        if(count($arr) > 0) {
            $origin = $arr[key($arr)];
        }else {
            $origin = $this->getOne("SELECT lat,lng, date, altitude, speed FROM location where emergency_id = ? order by date", array($emergencyId));
        }
        if(!$motion){
            return $origin;
        }
        $dest = $firebase->get(FIREBASEPATH."/".$emergencyId,array("orderBy" => '"date"',"startAt"=>json_encode($date)));
        $dest = json_decode($dest,true);
        /**
         * if  emergency not in firebase get emergency in database
         */
        if(count($dest) > 0){
            $dest = array_values($dest);
        }else{
            $dest = $this->getAll("SELECT lat,lng, date, altitude, speed FROM location where emergency_id = ? and date > ? order by date ",array($emergencyId, $date));
        }
        return array($origin,$dest);
    }
    public function getUserEmergency($userId,$emergencyId = null){
        if($emergencyId == null){
            $userEmergencies = $this->getAll("select e.id,t.label as type ,e.date,type_id from emergency e inner join type t on e.type_id = t.id where user_id = ?",array($userId));
            return $userEmergencies;
        }
        $userEmergencies = $this->getAll("select e.id,t.label as type ,e.date,type_id from emergency e inner join type t on e.type_id = t.id where user_id = ? and e.id = ?",array($userId,$emergencyId));
        return $userEmergencies;
    }
}