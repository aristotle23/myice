<?php

class Admin extends DbHandler
{
    public $message;
    function __construct()
    {
        parent::__construct();
    }
    function setAdmin($name, $email,$right){
        $existingId = $this->getOne("select id from admin where email = ?",array($email));
        if($existingId){
            return false;
        }
        $aId = $this->executeGetId("insert into admin (name, email,`right`) VALUES (?,?,?)",array($name,$email,$right));
        if($aId){
            return true;
        }
        return false;
    }
    function getRight($right = null){
        if($right == null){
            $access = $this->getAll("select * from admin_right");
            return $access;
        }
        $access = $this->getOne("select * from admin_right where `right` = ?",array($right));
        return $access;
    }
    function login($email, $password){
        $admin = $this->getOne("select id,name,`right`,initial,password from admin where email = ?",array($email));
        if($admin == null){
            $this->message = "Email does not exist";
            return false;
        }
        if($admin['initial'] == 1){
            return 101;
        }
        try {
            if (!password_verify($password, $admin['password'])) {
                $this->message = "Incorrect password";
                return false;
            }
        }catch (Exception $e){
            if(sha1($password) != $admin['password']){
                $this->message = "Incorrect password";
                return false;
            }
        }
        return $admin;
    }
    function newPwd($email, $pwd,$repwd){
        if($pwd != $repwd){
            $this->message = "Mis-match Password";
            return false;
        }
        try {
            $hashpwd = password_hash($pwd, PASSWORD_DEFAULT);
        }catch (Exception $e){
            $hashpwd = sha1($pwd);
        }
        $this->execute("update admin set initial = 0, password = ? where email = ?",array($hashpwd,$email));
        return true;
    }
    function getAdmin($adminId = null){
        if($adminId == null){
            $Admin = $this->getAll("select a.id, name, email,ar.label from admin a inner join admin_right ar on ar.`right` = a.`right`");
            return $Admin;
        }
        $Admin = $this->getOne("select name, email,ar.label from admin a inner join admin_right ar on ar.`right` = a.`right` where a.id = ?", array($adminId));
        return $Admin;
    }
    function delete($adminId){
        $this->execute("delete from admin where id = ?",array($adminId));
        return true;
    }
}