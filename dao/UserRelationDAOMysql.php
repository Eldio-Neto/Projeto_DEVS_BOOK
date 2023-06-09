<?php
require_once './models/UserRelation.php';

class UserRelationDAOMysql implements UserRelationDAO{
    private $pdo;

    public function __construct(PDO $driver)
    {
        $this->pdo = $driver;
    }

    public function insert(UserRelation $u){
        $sql=$this->pdo->prepare("INSERT INTO userrelations
        (user_from, user_to) VALUES (:user_from, :user_to)");
        $sql->bindValue(':user_from', $u->user_from);
        $sql->bindValue(':user_to', $u->user_to);
        $sql->execute();
    }
    public function delete(UserRelation $u){
        $sql=$this->pdo->prepare("DELETE FROM userrelations
        WHERE user_from = :user_from AND user_to = :user_to");
        $sql->bindValue(':user_from', $u->user_from);
        $sql->bindValue(':user_to', $u->user_to);
        $sql->execute();
    }
    public function getFollowing ($id){
        $users = [];

        $sql = $this->pdo->prepare("SELECT user_to FROM userrelations
        WHERE user_from = :id");
        $sql->bindValue(':id', $id);
        $sql->execute();

        if($sql->rowCount()>0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($data as $item){
                $users [] = $item['user_to'];
            }
        }
        return $users;
    }
    public function getFollowers($id){
        $users = [];
       $sql = $this->pdo->prepare("SELECT user_from FROM userrelations
        WHERE user_to = :id ");
        $sql->bindValue(':id', $id);
        $sql->execute();

        if($sql->rowCount()>0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($data as $item){
                $users [] = $item['user_from'];
            }
        }
        return $users;
    }

    public function isFollowing($id1,$id2 ){
        $sql = $this->pdo->prepare("SELECT user_from FROM userrelations
        WHERE user_to = :user_to AND user_from = :user_from");
        $sql->bindValue(':user_to', $id2);
        $sql->bindValue(':user_from', $id1);
        $sql->execute();

        if($sql->rowCount()>0){
            return true;
        }else{
            return false;
        } 
    }
}