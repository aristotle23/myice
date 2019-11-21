<?php
define("PERVIEW",2);

class Screen
{
    private $type = null;
    private $db = null;
    public function __construct($type)
    {
        $this->type = $type;
        $this->db = new DbHandler();
    }
    public function pagination(){
        $page = array();
        $ttl = $this->db->getOne("select count(id) as count from emergency where type_id = ?",array($this->type));
        $ttl = $ttl['count'];
        $division = intval($ttl)/PERVIEW;
        IF($division < PERVIEW){
            array_push($page,1);
            return $page;
        }
        return $page;
    }
}