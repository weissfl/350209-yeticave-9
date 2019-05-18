<form class="form container <?php if (isset($errors)): echo 'form--invalid'; endif; ?>" action="login.php"
      method="post">
    <h2>Вход</h2>
    <span class="form__error form__error--bottom"><?php if (isset($errors['form'])): echo strip_tags($errors['form']); endif; ?></span>
    <div class="form__item <?php if (isset($errors['email']) || isset($errors['form'])): echo 'form__item--invalid'; endif; ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email"
               value="<?php if (isset($sign_up['email'])): echo strip_tags($sign_up['email']); endif; ?>"
               placeholder="Введите e-mail">
        <span class="form__error"><?php if (isset($errors['email'])): echo strip_tags($errors['email']); endif; ?></span>
    </div>
    <div class="form__item form__item--last <?php if (isset($errors['password']) || isset($errors['form'])): echo 'form__item--invalid'; endif; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error"><?php if (isset($errors['password'])): echo strip_tags($errors['password']); endif; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>