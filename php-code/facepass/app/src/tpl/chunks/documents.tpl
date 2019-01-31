<div class='blockweight'>
<?php foreach ($userPhotos as $userPhoto) { ?>
    <img onclick="sendAjax('/account/get-photo-document/<?= $userPhoto['id'] ?>/', 'GET')" src='/images/<?= $userPhoto['path_mini'] ?>' class='bigIcon'>
    <?php } ?>
</div>


