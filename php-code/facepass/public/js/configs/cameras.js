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
});