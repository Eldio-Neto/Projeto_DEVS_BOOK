<?php
require_once './config.php';
require_once './models/Auth.php';
require_once './dao/PostDAO.mysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'home';


$page = intval(filter_input(INPUT_GET, 'p'));

if ($page < 1) {
    $page = 1;
}

$postDAO = new PostDAOMysql($pdo);
$info = $postDAO->getHomeFeed($userInfo->id, $page);

$feed = $info['feed'];
$pages = $info['pages'];
$currentpage = $info['currentPage'];

require './partials/header.php';
require './partials/menu.php';
?>
<section class="feed mt-10">
    <div class="row">
        <div class="column pr-5">
            <?php require './partials/feed_editor.php'; ?>
            <?php foreach ($feed as $item) : ?>
                <?php require './partials/feed-item.php'; ?>
            <?php endforeach; ?>

            <div class="feed-pagination">
                <?php for($i=0; $i < $pages; $i++):?>
                    <a class="<?=($i+1 == $currentpage)?'active':''?>" href="<?=$base?>/?p=<?=$i+1?>"><?=$i+1?></a>
                <?php endfor;?>
            </div>
        </div>
        <div class="column side pl-5">
            <div class="box banners">
                <div class="box-header">
                    <div class="box-header-text">Patrocinios</div>
                    <div class="box-header-buttons">

                    </div>
                </div>
                <div class="box-body">
                    <a href=""><img src="https://alunos.b7web.com.br/media/courses/php.jpg" /></a>
                    <a href=""><img src="https://alunos.b7web.com.br/media/courses/laravel.jpg" /></a>
                </div>
            </div>
            <div class="box">
                <div class="box-body m-10">
                    Criado com ❤️ durante o curso da B7Web
                </div>
            </div>
        </div>
        <div>
</section>
<?php
require './partials/footer.php';
?>