<section class="lot-item container">
    <?php if (isset($lot["name"])) {
        echo '<h2>'.strip_tags($lot["name"]).'</h2>';
    } ?>
    <div class="lot-item__content">
        <div class="lot-item__left">

            <?php if (isset($lot["img_url"])) {
                echo '<div class="lot-item__image">
                    <img src="../'.strip_tags($lot["img_url"]).'" width="730" height="548" alt="'.((isset($lot["name"])) ? strip_tags($lot["name"]) : '').'">
                </div>';
            } ?>

            <?php if (isset($lot["cat"])) {
                echo '<p class="lot-item__category">Категория: <span>' . strip_tags($lot["cat"]) . '</span></p>';
            } ?>

            <?php if (isset($lot["description"])) {
                echo '<p class="lot-item__description">'.strip_tags($lot["description"]).'</p>';
            } ?>

        </div>
        <div class="lot-item__right">

            <?php if(isset($_SESSION['user'])) { ?>
            <div class="lot-item__state">
                <?php if (isset($lot["date_finish"])) {
                   echo '<div class="lot-item__timer timer '.((warningOneHourLeft($lot["date_finish"])) ? 'timer--finishing' : '').'">
                   '.lifetime_lot($lot["date_finish"]).'</div>';
                } ?>
                <div class="lot-item__cost-state">

                    <?php if(isset($lot['price']) || isset($lot['last_price'])) { ?>
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost">
                            <?php
                            $current_price = currentPrice($lot['price'], $lot['last_price']);
                            echo format_price($current_price);
                            ?>
                        </span>
                    </div>
                    <?php } ?>

                    <?php if(isset($lot['step'])) { ?>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span>
                            <?php
                                $step = $lot['step'];
                                $min_bet = minBet($current_price, $step);

                                echo format_price($min_bet);
                            ?>
                        </span>
                    </div>
                    <?php } ?>

                </div>

                <form class="lot-item__form" action="" method="post" autocomplete="off">
                    <p class="lot-item__form-item form__item <?php if(isset($errors['cost'])): echo 'form__item--invalid'; endif; ?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" placeholder="12 000">
                        <span class="form__error"><?php if(isset($errors['cost'])): echo strip_tags($errors['cost']); endif; ?></span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>

            </div>
            <?php } ?>

            <!--<div class="history">
                <h3>История ставок (<span>10</span>)</h3>
                <table class="history__list">
                    <tr class="history__item">
                        <td class="history__name">Иван</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">5 минут назад</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Константин</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">20 минут назад</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Евгений</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">Час назад</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Игорь</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 08:21</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Енакентий</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 13:20</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Семён</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 12:20</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Илья</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 10:20</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Енакентий</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 13:20</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Семён</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 12:20</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Илья</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">19.03.17 в 10:20</td>
                    </tr>
                </table>
            </div>-->
        </div>
    </div>
</section>