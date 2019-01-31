$(function () {

    const updateInOutInterval = 3000;
    const maxFontSize = 17;
    const minFontSize = 12;

    let currentZoomIn = 14;
    let currentZoomOut = 14;

    /**
     * @param elements jq elements list
     * @param inOrOutZoom boolean
     * @param inOrOutTable boolean
     */

    const zoomText = function (elements, inOrOutZoom, inOrOutTable) {

        let currentZoom = (inOrOutTable) ? currentZoomIn : currentZoomOut;

        if (inOrOutZoom) {
            if (currentZoom !== maxFontSize) {
                currentZoom += 1;
            }
        } else {
            if (currentZoom !== minFontSize) {
                currentZoom -= 1;
            }
        }

        if (inOrOutTable) {
            currentZoomIn = currentZoom;
        } else {
            currentZoomOut = currentZoom;
        }

        $(elements).css('font-size', currentZoom + "px");
    };

    function setGlueTableHeaders() {
        //$('#tableIn').stickyTableHeaders({scrollableArea: $('.overfl')[0], 'fixedOffset': 1});
        //$('#tableOut').stickyTableHeaders({scrollableArea: $('.overfl')[1], 'fixedOffset': 1});
    }

    $('#tableOutContent, #tableInContent').on('click', '.button__zoom-in', function (e) {
        let elements = $(this).parent().parent().find('#tableIn, #tableOut').find('td');
        let id = $(this).parent().parent().attr('id');

        zoomText(elements, true, id === "tableInContent");
        setGlueTableHeaders();
    });

    $('#tableOutContent, #tableInContent').on('click', '.button__zoom-out', function (e) {
        let elements = $(this).parent().parent().find('#tableIn, #tableOut').find('td');
        let id = $(this).parent().parent().attr('id');

        zoomText(elements, false, id === "tableInContent");
        setGlueTableHeaders();
    });

    $('#content, #tableOutContent').on('click', '.button__full-screen, #tableInRollUpButton, #tableOutRollUpButton', function () {
        setGlueTableHeaders();
    });

    setGlueTableHeaders();

    const updateInOutContent = function () {

        let updatedStatus = true;


        /* При наведении на селекты не давать возмождность обновлять список */
        let overTableData = function() {

            $('#tableIn, #tableOut').on('mouseleave mouseenter', 'select', function(e) {
                updatedStatus = e.handleObj.type !== "mouseover";
            })
        };

        overTableData();

        setInterval(function () {

            if(!updatedStatus) {
                return false;
            }

            $.ajax({
                method: "post",
                url: "/main/in-out-content",
                success: function (data) {
                    $('#tableInBody').html(data.content.in);
                    $('#tableOutBody').html(data.content.out);

                    $('#tableInBody td').css('font-size', currentZoomIn + "px");
                    $('#tableOutBody td').css('font-size', currentZoomOut + "px");
                }
            });

        }, updateInOutInterval)
    };

    updateInOutContent();
});