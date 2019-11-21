<?php
class User
{
    private $userId ;
    private $db;
    public $err_message = null;
    public function __construct($userId = null)
    {
        $this->userId = $userId;
        $this->db = new DbHandler();
    }
    public function registerUser($name, $email,$password,$phone,$term,$address,$sex,$appId){

        try {
            $this->db->startTransaction();
            $chckAppId = $this->db->getOne("select id from user where appid = ?",array($appId));
            if($chckAppId != null){
                $this->err_message = "Application ID already exist";
                return false;
            }
            $chckInfo = $this->db->getOne("select id from userinfo where  email = ? or phone = ?",array($email,$phone));
            if($chckInfo != null){
                $this->err_message = "Either email or phone number already exist";
                return false;
            }
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $userInfoId = $this->db->executeGetId("insert into userinfo (name, email, password, phone, term, address, sex) VALUES (?,?,?,?,?,?,?)",
            array($name, $email, $hashPassword, $phone, $term, $address, $sex));

            $userId = $this->db->executeGetId("insert into user ( userinfo_id, appid) values (?,?)", array($userInfoId, $appId));

            $this->db->commitTransaction();

            return $userId;
        }catch (Exception $e){
            $this->db->rollBack();
            $this->err_message = "Unable to register new user";
            return false;
        }

    }
    public function login($email, $password, $appid){

        $user = $this->db->getOne("select u.id, ui.password, appid from userinfo ui inner join user u on ui.id = u.userinfo_id where email = ?",array($email));
        if($user == null){
            $this->err_message = "Email does not exist";
            return false;
        }

        if(!password_verify($password,$user['password'])){
            $this->err_message = "Incorrect password";
            return false;
        }

        $this->db->execute("update user set appid = ? where id = ?",array($appid,$user["id"]));
        return $user["id"];
    }
    public function getInfo($label = null){
        $user = $this->db->getOne("select email, phone, address, name, sex from userinfo ui inner join user u on u.userinfo_id = ui.id where  u.id = ?",array($this->userId));
        if($label != null){
            $user[$label];
        }
        return $user;
    }
    private function is_valid($userId){
        $chck = $this->db->getOne("select id from user where  id = ?",array($userId));
        if($chck == null){
            return false;
        }
        return true;
    }
}