<?php
require_once './config.php';
require_once './models/Auth.php';
require_once './dao/PostCommentDAOMysql.php';
require_once './models/PostComment.php';
$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_POST, 'id');
$txt = filter_input(INPUT_POST, 'body');

$array = [];

if ($id && $txt){
    $postCommentDao = new PostCommentDAOMysql($pdo);

    $newComment = new PostComment();

    $newComment->id_post = $id;
    $newComment->body = $txt;
    $newComment->id_user = $userInfo->id;
    $newComment->created_at = date('Y-m-d H:i:s');
    // print_r($newComment);
    // die();
    $postCommentDao->addComment($newComment);

    $array=[
        'error'=>'',
        'link'=> $base.'/perfil.php?id='.$userInfo->id,
        'avatar' => $base.'/media/avatars/'.$userInfo->avatar,
        'name'=>$userInfo->name,
        'body'=> $txt
    ];
}

header("Content-Type: application/json");
echo json_encode ($array);
exit;

