<?php
require './models/PostLike.php';

class PostlikeDAOMysql implements PostlikeDAO
{
    private $pdo;

    public function __construct(PDO $driver)
    {
        $this->pdo = $driver;
    }

    public function getLikeCount($id_post)
    {
        $sql = $this->pdo->prepare("SELECT COUNT(*) as c FROM postlikes WHERE ID_POST = :id_post");
        $sql->bindValue(":id_post", $id_post);
        $sql->execute();
        $data = $sql->fetch();

        return $data['c'];
    }
    public function isLiked($id_post, $id_user)
    {
        $sql = $this->pdo->prepare("SELECT *  FROM postlikes WHERE ID_POST = :id_post AND ID_USER = :id_user");

        $sql->bindValue(":id_post", $id_post);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function likeToggle($id_post, $id_user)
    {
        if ($this->isLiked($id_post, $id_user)) {
            $sql = $this->pdo->prepare("DELETE FROM postlikes WHERE ID_POST = :id_post AND ID_USER = :id_user");
        } else {
            $sql = $this->pdo->prepare("INSERT INTO postlikes (ID_POST, ID_USER, CREATED_AT) 
            VALUES (:id_post, :id_user, now())");
        }

        $sql->bindValue(":id_post", $id_post);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();
    }

    public function deleteFromPost($id_post){
        $sql = $this->pdo->prepare("DELETE FROM postlikes WHERE id_post = :id_post");
        $sql->bindValue(':id_post', $id_post);
        
        $sql->execute();
    }
}
