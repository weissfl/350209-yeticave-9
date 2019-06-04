<h1>Поздравляем с победой</h1>

<p>Здравствуйте, <?php if (isset($data["username"])) {
        echo strip_tags($data["username"]);
    } ?></p>

<p>Ваша ставка для лота <a href="http://350209-yeticave-9/lot.php?id=<?php if (isset($data["username"])) {
        echo strip_tags($data["lot_id"]);
    } ?>">
        <?php if (isset($data["username"])) {
            echo strip_tags($data["lot_name"]);
        } ?></a> победила.</p>

<p>Перейдите по ссылке <a href="http://350209-yeticave-9/my-bets.php">мои ставки</a>,
    чтобы связаться с автором объявления</p>

<small>Интернет Аукцион "YetiCave"</small>