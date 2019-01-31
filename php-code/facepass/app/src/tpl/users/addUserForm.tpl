<h2>Добавление пользователя</h2>
<form name='addUsersForm'>
    <table>
        <tbody>
            <tr>
                <td>Фамилия</td>
                <td><input class='boryes margins robotocr' type='text' name='surname'></td>
            </tr>

            <tr>
                <td>Имя</td>
                <td><input class='boryes margins robotocr' type='text' name='first_name'></td>
            </tr>

            <tr>
                <td>Отчество</td>
                <td><input class='boryes margins robotocr' type='text' name='last_name'></td>
            </tr>

            <tr>
                <td>Телефон</td>
                <td><input class='boryes margins robotocr' type='text' name='phone'></td>
            </tr>

            <tr>
                <td>Почта</td>
                <td><input class='boryes margins robotocr'	 type='email' name='email'></td>
            </tr>

            <tr>
                <td>Дата рождения</td>
                <td><input class='robotocr boryes margins' type='date' name='birthday'></td>
            </tr>

        </tbody>
    </table>
</form>
<div class='button margins' onclick="sendAjax('/users/', 'POST', 'addUsersForm')">Создать пользователя</div>
