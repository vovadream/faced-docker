<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class='auth'>
                <div class='authName'>Авторизация</div>
                <div id='authResult'></div>
                <form name='authForm' class='authForm'>
                    <div>
                        <input class="form-control input__login" type='text' name='login' placeholder='Логин'>
                    </div>
                    <br>
                    <div>
                        <input class="form-control input__password" type='password' name='password' placeholder='Пароль'>
                        <input class="form-control" type='hidden' name='auth' value='1'>
                    </div>
                </form>
                <br>
                <div class='btn btn-lg button auth gray' onclick="sendAjax('/auth/', 'POST', 'authForm');">Войти</div>
                <div class='lostPassword'><a href='#'>Забыли пароль?</a></div>
            </div>
        </div>
    </div></div>