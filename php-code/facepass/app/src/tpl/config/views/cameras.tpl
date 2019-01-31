<div id='settings_cameras' class='overfltable'>

    <div class='button' data-toggle="modal" data-target="#addModal">Добавить камеру</div>
    <div class='button' id="restart">Рестарт</div>
    <br><br>

    <table class='akkt' border='1' cellpadding='5'>
        <tr>
            <th>№</th>
            <th>Ссылка на стрим</th>
            <th>Высота рамки</th>
            <th>Ширина рамки</th>
            <th></th>
        </tr>

        <?php foreach($cameras as $item) { ?>
        <tr>
            <td><?= $item->id ?></td>
            <td><?= $item->stream_url ?></td>
            <td><?= $item->face_min_height ?></td>
            <td><?= $item->face_min_width ?></td>
            <td class='button' id="deleteItem" data-id="<?= $item->id ?>">Удалить</td>
        </tr>
        <?php
        }
        ?>
        </tr>
    </table>
</div>

<div class="modal" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление камеры</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <div class="form-group col-sm-12">
                        <label for="stream_url">Стрим камеры</label>
                        <input type="text" name="stream_url" class="form-control" id="stream_url" placeholder="rtsp://">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="face_min_height" class="control-label">Высота рамки</label>
                        <input type="text" class="form-control" id="face_min_height" name="face_min_height" value="470">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="face_min_width" class="control-label">Ширина рамки</label>
                        <input type="text" class="form-control" id="face_min_width" name="face_min_width" value="470">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="select-what" class="control-label">Тип устройства для привязки</label>
                        <select class="form-control" name="what" id="select-what">
                            <option value="1" selected>Терминал</option>
                            <option value="3">Проходная</option>
                        </select>
                    </div>
                    <div id="dynamic-form">
                        <div class="form-group col-sm-12">
                            <label for="eq" class="control-label">Устройство</label>
                            <select class="form-control" name="equipment_id" id="eq">
                                <option value="1" selected>Терминал</option>
                                <option value="3">Проходная</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 radio-in hidden">
                            <label class="radio-inline"><input type="radio" value="1" name="in">Вход</label>
                            <label class="radio-inline"><input type="radio" value="0" name="in">Выход</label>
                        </div>
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