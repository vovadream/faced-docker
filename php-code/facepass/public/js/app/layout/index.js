$(() => {
    $("#showPeopleInBuilding").tooltip({
        html: true,
        trigger: "manual",
        placement: 'bottom'
    }).on({
        click: function () {
            let $el = $(this);
            $.ajax({
                url: "/peopleinbulding/",
                success:
                    function (response) {
                        $el.attr("data-original-title", response.html);
                        $el.tooltip("show");
                    },
                dataType: "json"
            });
        },
        mouseleave: function () {
            $(this).tooltip("hide");
        }
    });
});