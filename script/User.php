<?php
class User extends DbHandler
{
    private $userId ;
    public $err_message = null;
    public function __construct($userId = null)
    {
        parent::__construct();
        $this->userId = $userId;
    }
    public function registerUser($name, $email,$password,$phone,$term,$address,$sex,$appId){

        try {
            $this->startTransaction();
            $chckAppId = $this->getOne("select id from user where appid = ?",array($appId));
            if($chckAppId != null){
                $this->err_message = "Application ID already exist";
                return false;
            }
            $chckInfo = $this->getOne("select id from userinfo where  email = ? or phone = ?",array($email,$phone));
            if($chckInfo != null){
                $this->err_message = "Either email or phone number already exist";
                return false;
            }
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $userInfoId = $this->executeGetId("insert into userinfo (name, email, password, phone, term, address, sex) VALUES (?,?,?,?,?,?,?)",
            array($name, $email, $hashPassword, $phone, $term, $address, $sex));

            $userId = $this->executeGetId("insert into user ( userinfo_id, appid) values (?,?)", array($userInfoId, $appId));

            $this->commitTransaction();

            return $userId;
        }catch (Exception $e){
            $this->rollBack();
            $this->err_message = "Unable to register new user";
            return false;
        }

    }
    public function login($email, $password, $appid){

        $user = $this->getOne("select u.id, ui.password, appid from userinfo ui inner join user u on ui.id = u.userinfo_id where email = ?",array($email));
        if($user == null){
            $this->err_message = "Email does not exist";
            return false;
        }

        if(!password_verify($password,$user['password'])){
            $this->err_message = "Incorrect password";
            return false;
        }

        $this->execute("update user set appid = ? where id = ?",array($appid,$user["id"]));
        return $user["id"];
    }
    public function getInfo($label = null){
        $user = $this->getOne("select email, phone, address, name, sex from userinfo ui inner join user u on u.userinfo_id = ui.id where  u.id = ?",array($this->userId));
        if($label != null){
            return $user[$label];
        }
        return $user;
    }
    public function getUsers(){
        $users = $this->getAll("select name,email,phone,address,sex,u.id,u.deactivate from userinfo ui inner join user u on u.userinfo_id = ui.id ");
        return $users;
    }
    private function is_valid($userId){
        $chck = $this->getOne("select id from user where  id = ?",array($userId));
        if($chck == null){
            return false;
        }
        return true;
    }
    public function deactiave($userId,$act){
        $this->execute("update user set deactivate = ? where id = ?",array($act,$userId));
        return true;
    }
}