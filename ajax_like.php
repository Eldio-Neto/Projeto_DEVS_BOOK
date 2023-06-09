<?php
require_once './config.php';
require_once './models/Auth.php';
require_once './dao/PostLikeDAOMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_GET, 'id');


if (!empty($id)){
    $postLikeDAO  = new PostLikeDAOMysql($pdo);

    $postLikeDAO->likeToggle($id, $userInfo->id);
}