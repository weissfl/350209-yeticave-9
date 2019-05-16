<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">

        <?php foreach ($bets as $bet):
            $class = '';

            if ($bet["lot_winner"] === $_SESSION['user']['id']) {
                $class = 'rates__item--win';
            } elseif (warning_finishing($bet["lot_date_finish"])) {
                $class = 'rates__item--end"';
            }
            ?>
            <tr class="rates__item <?= $class ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <?php if (isset($bet['lot_img'])) { ?>
                            <img src="../<?php echo $bet['lot_img']; ?>" width="54" height="40"
                                 alt="<?php echo strip_tags($bet['lot_name']); ?>">
                        <?php } ?>
                    </div>
                    <div>
                        <h3 class="rates__title">
                            <a href="/lot.php?id=<?php if (isset($bet['lot_id'])): echo $bet['lot_id']; endif; ?>">
                                <?php if (isset($bet['lot_name'])) {
                                    echo strip_tags($bet['lot_name']);
                                } ?>
                            </a>
                        </h3>

                        <?php if (isset($bet['user_contacts']) && $bet["lot_winner"] === $_SESSION['user']['id']) {
                            echo '<p>' . strip_tags($bet['user_contacts']) . '</p>';
                        } ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?php if (isset($bet['cat_name'])) { ?>
                        <?php echo strip_tags($bet['cat_name']); ?>
                    <?php } ?>
                </td>
                <td class="rates__timer">
                    <?php if (isset($bet['lot_date_finish'])) {
                        if ($bet["lot_winner"] === $_SESSION['user']['id']) {
                            echo '<div class="timer timer--win">Ставка выиграла</div>';
                        } elseif (warning_finishing($bet["lot_date_finish"])) {
                            echo '<div class="timer timer--end">Торги окончены</div>';
                        } else {
                            echo '<div class="timer">' . lifetime_lot($bet['lot_date_finish']) . '</div>';
                        }
                    } ?>
                </td>
                <td class="rates__price">
                    <?php if (isset($bet['bet_cost'])) { ?>
                        <?php echo format_price($bet['bet_cost']); ?>
                    <?php } ?>
                </td>
                <td class="rates__time">
                    <?php if (isset($bet['bet_date'])) { ?>
                        <?php echo createdTimeAgo($bet['bet_date']); ?>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>