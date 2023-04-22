<?php
require_once './models/Post.php';
require_once './dao/UserRelationDAOMysql.php';
require_once './dao/UserDAOMysql.php';
require_once './dao/PostLikeDAOMysql.php';
require_once './dao/PostCommentDAOMysql.php';

class PostDAOMysql implements PostDAO
{
    private $pdo;

    public function __construct(PDO $driver)
    {
        $this->pdo = $driver;
    }
    public function insert(Post $p)
    {
        $sql = $this->pdo->prepare("INSERT INTO posts (id_user,
                                                       type, 
                                                       created_at,
                                                        body) 
                                                VALUES (:id_user,
                                                    :type, 
                                                    :created_at, 
                                                    :body)");
        $sql->bindValue(":id_user", $p->id_user);
        $sql->bindValue(":type", $p->type);
        $sql->bindValue(':created_at', $p->created_at);
        $sql->bindValue(':body', $p->body);
        $sql->execute();
        return true;
    }

    public function getUserFeed($id_user, $page = 1)
    {
        $array = ['feed'=>[]];
        $urDAO = new UserRelationDAOMysql($this->pdo);
        $userlist = $urDAO->getFollowing($id_user);
        $userlist[] = $id_user;

        $perPage = 4;


        $offSet = ($page - 1) * $perPage;

        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id_user = :id_user
        ORDER BY created_at DESC LIMIT $offSet, $perPage");

        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            $array['feed'] = $this->_postListToObject($data, $id_user);
        }

        $sql = $this->pdo->prepare("SELECT COUNT(*) as c FROM posts WHERE id_user = :id_user");

        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        $totalData = $sql->fetch();

        $total = $totalData['c'];        
        $array['pages'] = ceil($total / $perPage);
        $array['currentPage'] = $page;


        return $array;
    }

    public function getHomeFeed($id_user, $page = 1)
    {
        $array = [];

        $perPage = 4;

        $offSet = ($page - 1) * $perPage;

        $urDAO = new UserRelationDAOMysql($this->pdo);
        $userlist = $urDAO->getFollowing($id_user);
        $userlist[] = $id_user;

        $sql = $this->pdo->query("SELECT * FROM posts WHERE id_user 
        in (" . implode(',', $userlist) . ") 
        ORDER BY created_at DESC LIMIT $offSet, $perPage");

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            $array['feed'] = $this->_postListToObject($data, $id_user);
        }

        $sql = $this->pdo->query("SELECT COUNT(*) as c FROM posts WHERE id_user 
        in (" . implode(',', $userlist) . ")");

        $totalData = $sql->fetch();

        $total = $totalData['c'];

        $array['pages'] = ceil($total / $perPage);
        $array['currentPage'] = $page;

        return $array;
    }

    public function getPhotosFrom($id_user)
    {
        $array = [];

        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id_user =  :id_user AND TYPE = 'photo' ORDER BY created_at");
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            $array = $this->_postListToObject($data, $id_user);
        }

        return $array;
    }

    private function _postListToObject($postlist, $id_user)
    {

        $posts = [];
        $userDao = new UserDAOMysql($this->pdo);
        $postLikeDAO = new PostLikeDAOMysql($this->pdo);
        $postCommentDao = new PostCommentDAOMysql($this->pdo);

        foreach ($postlist as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if ($postItem['id_user'] == $id_user) {
                $newPost->mine = true;
            }
            //informações sobre usuario
            $newPost->user = $userDao->findById($postItem['id_user']);

            //informações sobre Like
            $newPost->likeCount = $postLikeDAO->getLikeCount($newPost->id);
            $newPost->liked = $postLikeDAO->isLiked($newPost->id, $id_user);

            //infomações sobre Comments
            $newPost->comments = $postCommentDao->getComments($newPost->id);
            $posts[] = $newPost;
        }

        return $posts;
    }

    public function delete($id, $id_user)
    {
        $postLikeDAO = new PostLikeDAOMysql($this->pdo);
        $postCommentDao = new PostCommentDAOMysql($this->pdo);

        $sql = $this->pdo->prepare("SELECT * FROM posts 
        WHERE id = :id AND ID_USER = :id_user");
        $sql->bindValue(':id', $id);
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $post = $sql->fetch(PDO::FETCH_ASSOC);

            $postLikeDAO->deleteFromPost($id);
            $postCommentDao->deleteFromPost($id);

            if ($post['type'] === 'photo') {
                $img = 'media/uploads/' . $post['body'];
                if (file_exists($img)) {
                    unlink($img);
                }
            }

            $sql = $this->pdo->prepare("DELETE FROM posts WHERE id = :id AND ID_USER = :id_user");
            $sql->bindValue(':id', $id);
            $sql->bindValue(':id_user', $id_user);

            $sql->execute();
        }
    }
}
