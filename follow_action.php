<?php
require_once './config.php';
require_once './models/Auth.php';
require_once './dao/UserRelationDAOMysql.php';
require_once './dao/UserDAOMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_GET, 'id');

if ($id) {
    $userRelationDao = new UserRelationDAOMysql($pdo);
    $userDao = new UserDAOMysql($pdo);

    if ($userDao->findById($id)) {
        $relation = new UserRelation($pdo);
        $relation->user_from = $userInfo->id;
        $relation->user_to = $id;
        if ($userRelationDao->isFollowing($userInfo->id, $id)) {
            $userRelationDao->delete($relation);
        } else {
            $userRelationDao->insert($relation);
        }
        header("Location: perfil.php?id=$id"); 
        exit;
    };
    
}

header("Location: $base");
exit;
