<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
        горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $value): ?>
            <li class="promo__item promo__item--<?= strip_tags($value["char_code"]); ?>">
                <a class="promo__link" href="pages/all-lots.html">
                    <?php if (isset($value["name"])) {
                        echo strip_tags($value["name"]);
                    } ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($ads as $key => $value): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="
                    <?php if (isset($value['img_url'])) {
                        echo strip_tags($value['img_url']);
                    } ?>
                    " width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                        <span class="lot__category">
                            <?php if (isset($value['cat'])) {
                                echo strip_tags($value['cat']);
                            } ?>
                        </span>
                    <h3 class="lot__title">
                        <a class="text-link" href="lot.php?id=<?php if (isset($value['id'])): echo strip_tags($value['id']); endif; ?>">
                            <?php if (isset($value['name'])) {
                                echo strip_tags($value['name']);
                            } ?>
                        </a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost">
                                <?php if (isset($value['start_price'])) {
                                    echo format_price($value['start_price']);
                                } ?>
                            </span>
                        </div>
                        <div class="lot__timer timer  <?php if (warningOneHourLeft($value["date_finish"])): echo 'timer--finishing'; endif; ?>">
                            <?= lifetime_lot($value["date_finish"]) ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
