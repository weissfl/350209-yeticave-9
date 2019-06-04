<form class="form container form--add-lot <?php if (isset($errors)): echo 'form--invalid'; endif; ?>" action="add.php"
      method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?php if (isset($errors['lot-name'])): echo 'form__item--invalid'; endif; ?>">
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name"
                   value="<?php if (isset($lot['lot-name'])): echo strip_tags($lot['lot-name']); endif; ?>"
                   placeholder="Введите наименование лота">
            <span class="form__error"><?php if (isset($errors['lot-name'])): echo strip_tags($errors['lot-name']); endif; ?></span>
        </div>
        <div class="form__item <?php if (isset($errors['category'])): echo 'form__item--invalid'; endif; ?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">

                <?php
                foreach ($categories as $category) {
                    if (isset($category["id"])) {
                        echo '<option value="' . strip_tags($category["id"]) . '" ' . ((isset($lot["category"]) && $category["id"] == $lot["category"]) ? 'selected' : '') . '>';
                        if (isset($category["name"])) {
                            echo strip_tags($category["name"]);
                        }
                        echo '</option>';
                    }
                }
                ?>
            </select>
            <span class="form__error"><?php if (isset($errors['category'])): echo strip_tags($errors['category']); endif; ?></span>
        </div>
    </div>
    <div class="form__item form__item--wide <?php if (isset($errors['message'])): echo 'form__item--invalid'; endif; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message"
                  placeholder="Напишите описание лота"><?php if (isset($lot['message'])): echo strip_tags($lot['message']); endif; ?></textarea>
        <span class="form__error"><?php if (isset($errors['message'])): echo strip_tags($errors['message']); endif; ?></span>
    </div>
    <div class="form__item form__item--file <?php if (isset($errors['lot-img'])): echo 'form__item--invalid'; endif; ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
            <label for="lot-img">
                Добавить
            </label>
        </div>
        <span class="form__error"><?php if (isset($errors['lot-img'])): echo strip_tags($errors['lot-img']); endif; ?></span>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?php if (isset($errors['lot-rate'])): echo 'form__item--invalid'; endif; ?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0"
                   value="<?php if (isset($lot['lot-rate'])): echo strip_tags($lot['lot-rate']); endif; ?>">
            <span class="form__error"><?php if (isset($errors['lot-rate'])): echo strip_tags($errors['lot-rate']); endif; ?></span>
        </div>
        <div class="form__item form__item--small <?php if (isset($errors['lot-step'])): echo 'form__item--invalid'; endif; ?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0"
                   value="<?php if (isset($lot['lot-step'])): echo strip_tags($lot['lot-step']); endif; ?>">
            <span class="form__error"><?php if (isset($errors['lot-step'])): echo strip_tags($errors['lot-step']); endif; ?></span>
        </div>
        <div class="form__item <?php if (isset($errors['lot-date'])): echo 'form__item--invalid'; endif; ?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date"
                   placeholder="Введите дату в формате ГГГГ-ММ-ДД"
                   value="<?php if (isset($lot['lot-date'])): echo strip_tags($lot['lot-date']); endif; ?>">
            <span class="form__error"><?php if (isset($errors['lot-date'])): echo strip_tags($errors['lot-date']); endif; ?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>