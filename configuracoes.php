<?php
require_once './config.php';
require_once './models/Auth.php';
require_once './dao/PostDAO.mysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'config';

$userDAO = new  UserDAOMysql($pdo);

require './partials/header.php';
require './partials/menu.php';
?>
<section class="feed mt-10">
    <h1>Configurações</h1>
    <?php if (!empty($_SESSION['flash'])) : ?>
        <?= "<p style='color: red;'>" . $_SESSION['flash'] . "</p>"; ?>
        <?php $_SESSION['flash'] = ''; ?>
    <?php endif; ?> 
    <form action="configuracoes_action.php" enctype="multipart/form-data" method="post" class="config-form">
        <label for="">
            Novo Avatar: <br>
            <div class="flex-label-config">
                <div>
                    <img class="mini" src="<?= $base ?>/media/avatars/<?= $userInfo->avatar ?>" alt="">
                </div>

                <div>
                    <input type="file" name="avatar">
                </div>
            </div>
        </label>
        <label for="">
            Nova Capa:<br>
            <div class="flex-label-config">
                <div>
                    <img class="mini" src="<?= $base ?>/media/covers/<?= $userInfo->cover ?>" alt="">
                </div>

                <div>
                    <input type="file" name="cover">
                </div>
            </div>
        </label>
        <hr>
        <label for="">
            Nome Completo:<br>
            <input type="text" name="name" value="<?= $userInfo->name ?>">
        </label>
        <label for="">
            E-mail:<br>
            <input type="email" name="email" value="<?= $userInfo->email ?>">
        </label>
        <label for="">
            Data de nascimento:<br>
            <input type="text" name="birthdate" id="birthdate" value="<?= date('d/m/Y', strtotime($userInfo->birthdate)) ?>">
        </label>
        <label for="">
            Cidade:<br>
            <input type="text" name="city" value="<?= $userInfo->city ?>">
        </label>
        <label for="">
            Trabalho:<br>
            <input type="text" name="work" value="<?= $userInfo->work ?>">
        </label>
        <hr>
        <label for="">
            Nova Senha:<br>
            <input type="password" name="password">
        </label>

        <label for="">
            Confirmar Senha:<br>
            <input type="password" name="password_confirmation">
        </label>

        <button class="button">Salvar</button>
    </form>
</section>

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById('birthdate'), {
            mask: '00/00/0000'
        }
    );
</script>
<?php
require './partials/footer.php';
?>