$(function() {
    $('div#addModal select#select-what').change(function () {
        var select_eq = $( "select#eq" );
        var selected = $( this ).find('option:selected');
        if (selected.val() == '3') {
            $(".radio-in").removeClass('hidden');
        } else {
            $(".radio-in").addClass('hidden');
        }
        
        $.getJSON('/equipment/type/' + selected.val() + '/', function (data) {
            select_eq.html('');
            $.each(data.data, function (i, el) {
                select_eq.append( new Option(el.name, el.id) );
            });
        });
    }).change();

    $('div#settings_cameras div#restart').click(function () {
        $.get('/configs/cameras/restart', function (data) {
            if (data.status == 'success') {
                alert('Готово!');
            } else {
                alert('Что-то пошло не так!');
            }
        });

        return false;
    });

    $('div#settings_cameras #deleteItem').click(function () {
        var $this = $(this);
        var id = $this.data('id');
        $.get('/configs/cameras/del/' + id, function (data) {
            if (data.status == 'success') {
                $this.parent().remove();
            } else {
                alert('Что-то пошло не так!');
            }
        });
    });
});