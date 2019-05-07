<form class="form container <?php if(isset($errors)): echo 'form--invalid'; endif; ?>" action="sign-up.php" method="post" autocomplete="off" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item  <?php if(isset($errors['email'])): echo 'form__item--invalid'; endif; ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" value="<?php if(isset($sign_up['email'])): echo strip_tags($sign_up['email']); endif; ?>" placeholder="Введите e-mail">
        <span class="form__error"><?php if(isset($errors['email'])): echo strip_tags($errors['email']); endif; ?></span>
    </div>
    <div class="form__item <?php if(isset($errors['password'])): echo 'form__item--invalid'; endif; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" value="<?php if(isset($sign_up['password'])): echo strip_tags($sign_up['password']); endif; ?>" placeholder="Введите пароль">
        <span class="form__error"><?php if(isset($errors['password'])): echo strip_tags($errors['password']); endif; ?></span>
    </div>
    <div class="form__item <?php if(isset($errors['name'])): echo 'form__item--invalid'; endif; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" value="<?php if(isset($sign_up['name'])): echo strip_tags($sign_up['name']); endif; ?>" placeholder="Введите имя">
        <span class="form__error"><?php if(isset($errors['name'])): echo strip_tags($errors['name']); endif; ?></span>
    </div>
    <div class="form__item <?php if(isset($errors['message'])): echo 'form__item--invalid'; endif; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?php if(isset($sign_up['message'])): echo strip_tags($sign_up['message']); endif; ?></textarea>
        <span class="form__error"><?php if(isset($errors['message'])): echo strip_tags($errors['message']); endif; ?></span>
    </div>
    <div class="form__item form__item--file <?php if(isset($errors['avatar'])): echo 'form__item--invalid'; endif; ?>">
        <label>Аватар</label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="avatar" id="avatar-img" value="">
            <label for="avatar-img">
                Добавить
            </label>
        </div>
        <span class="form__error"><?php if(isset($errors['avatar'])): echo strip_tags($errors['avatar']); endif; ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/login.php">Уже есть аккаунт</a>
</form>