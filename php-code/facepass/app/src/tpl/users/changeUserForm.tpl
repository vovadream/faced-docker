<h2>Изменение данных пользователя - <?php echo $users[0]->surname." "; echo $users[0]->first_name." "; echo $users[0]->patronymic?></h2>
<form name='updateUserForm'>
    <table>
        <tbody>
        <tr>
            <td>Фамилия</td>
            <td><input class='boryes margins' type='text' name='surname' placeholder='Фамилия' value='<?php echo $users[0]->surname?>'></td>
            </tr>

        <tr>
            <td>Имя</td>
            <td><input class='boryes margins' type='text' name='first_name' placeholder='Имя' value='<?php echo $users[0]->first_name?>'></td>
        </tr>

        <tr>
            <td>Отчество</td>
            <td><input class='boryes margins'  type='text' name='last_name' placeholder='Отчество' value='<?php echo $users[0]->patronymic?>'></td>
        </tr>

        <tr>
            <td>Телефон</td>
            <td><input class='boryes margins robotocr' type='text' name='phone' placeholder='Номер телефона' value='<?php echo $users[0]->phone?>'></td>
        </tr>

        <tr>
            <td>Почта</td>
            <td><input class='boryes margins robotocr' type='email' name='email' placeholder='Электронная почта' value='<?php echo $users[0]->email?>'></td>
        </tr>

        <tr>
            <td>Дата рождения</td>
            <td><input class='boryes margins robotocr' type='date' name='birthday' value='<?php echo $users[0]->birthday?>'></td>
        </tr>
		 <tr>
		 <td>Тип пользователя</td>
		<td>
		<select name='user_type_id'>";
		<?php if(isset($user_types['status'])) echo "<option value='0'>Нет данных</option>";
		else echo "<option value='0'>Не выбран тип пользователя</option>" ;
		?>
        <?php for($i=0;$i<count($user_types);$i++)
        {
            echo "<option value='{$user_types[$i]->id}'";
			if ($user_types[$i]->id==$users[0]->user_type_id)  echo " selected";
			echo ">{$user_types[$i]->name}</option>";
        }
		?>
        </select>
		</td>
		</tr>
		<?php if(isset($departments))
		{
		echo "<tr> <td>Отдел</td><td><select name='department_id'>";
		if(isset($departments['status'])) echo "<option value='0'>Нет данных</option>";
		else echo "<option value='0'>Не выбран отдел</option>";
        for($i=0;$i<count($departments);$i++)
        {
            echo "<option value='{$departments[$i]->id}'";
			if ($departments[$i]->id==$users[0]->dep_id)  echo " selected";
			echo ">{$departments[$i]->name}</option>";
        }
		echo "</select>	</td></tr>";
		}?>
        </tbody>
    </table>
</form>
<div class='button' onclick="sendAjax('/users/<?php echo $users[0]->id ?>/', 'POST', 'updateUserForm')">Изменить данные</div>
