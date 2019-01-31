<div class="invitees">
    <h2>Оформление приглашений</h2>

    <div class="button" data-toggle="modal" data-target="#addModalInv">Добавить</div>

    <br><br>

    <table class='akkt table table-bordered' border='1' cellpadding='5'>
        <thead>
        <tr>
            <th>№</th>
            <th>Дата</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Статус</th>
            <th>Услуга</th>
        </tr>
        </thead>

        <?php foreach($invitees as $item): ?>
            <tr>
                <td><?= $item->id ?></td>
                <td><?= $item->hdate ?></td>
                <td><?= $item->first_name ?> <?= $item->surname ?> <?= $item->patronymic ?></td>
                <td><?= $item->phone ?></td>
                <td><?php echo $item->user_id ? 'Посетитель' : 'Незарегистрированый гость' ;?></td>
                <td><?= $item->name ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="modal" tabindex="-1" role="dialog" id="addModalInv">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление приглашения</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <div class="form-group col-sm-12">
                        <label for="first_name">Имя</label>
                        <input type="text" name="first_name" class="form-control" id="first_name">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="surname">Фамилия</label>
                        <input type="text" name="surname" class="form-control" id="surname">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="patronymic">Отчество</label>
                        <input type="text" name="patronymic" class="form-control" id="patronymic">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="phone">Телефон</label>
                        <input type="text" name="phone" class="form-control" id="phone">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="date">Дата</label>
                        <input type="date" name="date" class="form-control" id="date">
                    </div>
                    <div class="form-group col-sm-12 hearing hidden">
                        <label for="hearing" class="control-label">Услуга</label>
                        <select class="form-control" name="hearing_id" id="hearing">

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </form>
        </div>
    </div>
</div>