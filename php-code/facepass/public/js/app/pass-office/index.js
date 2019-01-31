$(function() {
    $("div#addModalInv #date").change(function () {
        var $this = $(this);
        var divH = $("div#addModalInv .hearing");
        var selectH = $("div#addModalInv .hearing select");
        $.post('/invitees/hearings', 'date=' + $this.val(), function (data) {
            if (data.status == 'success') {
                selectH.html('');
                $.each(data.data, function (i, el) {
                    selectH.append( new Option(el.name, el.id) );
                });
                divH.removeClass('hidden');
            }
        });
    });
});