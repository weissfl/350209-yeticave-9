<div class="container">
    <section class="lots">

        <?php if (!empty($keyword)) { ?>
            <h2>Результаты поиска по запросу «<span><?= strip_tags($keyword) ?></span>»</h2>

            <?php if (!empty($search_results)) { ?>
                <ul class="lots__list">
                    <?php foreach ($search_results as $key => $value): ?>
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
                                    <a class="text-link"
                                       href="lot.php?id=<?php if (isset($value['id'])): echo strip_tags($value['id']); endif; ?>">
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
                                        <?php if (isset($value["date_finish"])) {
                                            echo lifetime_lot($value["date_finish"]);
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>

                </ul>
            <?php } else {
                echo '<p>«Ничего не найдено по вашему запросу»</p>';
            } ?>

        <?php } else {
            echo 'Слово для поиска не задано';
        } ?>

    </section>

    <?php if (count($pages) > 1): ?>
        <ul class="pagination-list">

            <li class="pagination-item pagination-item-prev">
                <?php if ($page_current > 1): ?>
                    <a href="?search=<?php if (isset($keyword)): echo strip_tags($keyword); endif; ?>&page=<?php if (isset($page_current)): echo $page_current - 1; endif; ?>">Назад</a>
                <?php endif; ?>
            </li>

            <?php foreach ($pages as $page): ?>
                <li class="pagination-item <?php if ($page == $page_current): ?>pagination-item-active<?php endif; ?>">
                    <a href="?search=<?php if (isset($keyword)): echo strip_tags($keyword); endif; ?>&page=<?php if (isset($page)): echo strip_tags($page); endif; ?>"><?php if (isset($page)): echo strip_tags($page); endif; ?></a>
                </li>
            <?php endforeach; ?>

            <li class="pagination-item pagination-item-next">
                <?php if ($page_current != count($pages)): ?>
                    <a href="?search=<?php if (isset($keyword)): echo strip_tags($keyword); endif; ?>&page=<?php if (isset($page_current)): echo $page_current + 1; endif; ?>">Вперёд</a>
                <?php endif; ?>
            </li>
        </ul>
    <?php endif; ?>


</div>