if (typeof domain == 'undefined')
    var domain = "http://uwpw.ru/skudFace/public";
//var requestFile = domain+"ajax/";

function htmlspecialchars_decode(string, quote_style) {
    var optTemp = 0,
        i = 0,
        noquotes = false;
    if (typeof quote_style === 'undefined') {
        quote_style = 2;
    }
    string = string.toString()
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>');
    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE': 1,
        'ENT_HTML_QUOTE_DOUBLE': 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE': 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i = 0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            } else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
        // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
    }
    if (!noquotes) {
        string = string.replace(/&quot;/g, '"');
    }
    // Put this in last place to avoid escape being double-decoded
    string = string.replace(/&amp;/g, '&');

    return string;
}

function  createRequest() { //функция создания ajax запроса
    try {request = new XMLHttpRequest();}
    catch (trymicrosoft) {
        try {request = new ActiveXObject("Msxml2.XMLHTTP");}
        catch (othermicrosoft) {
            try {request = new ActiveXObject("Microsoft.XMLHTTP");}
            catch (failed) {request = null;}
        }
    }
    if (request == null) {alret("Ошибка создания запроса ;(<br>Попробуйте или обратитесь службу поддержки.");}
}


function sendAjax(url, sendType, formName, successFunction) {//Отправка запроса на сервер
    if(sendType==null) sendType="POST";
    createRequest();

    if(successFunction !== undefined) {
        successEval = successFunction;
    } else {
        successEval = false;
    }


    var formData = null;
    if(formName!=null) {
        var form = document.forms[formName];
        formData = new FormData(form);
    }
    request.open(sendType, domain+url, false);
    request.onreadystatechange = sendAjaxResult;
    request.send(formData);
    return false;
}

function sendAjaxResult()
{
    var data = JSON.parse(request.responseText);
    dump(data);
    if(data.status != undefined) {
        if (data['status'] == "success") {
            if(data['reload']!=null && data['reload']) {
                if (data['page'] != null && data['page'])
                    window.location.href = data['page'];
                else
                    window.location.reload();
            }
            if(data['div']!=undefined && data['html']!=undefined) {
                if(data['div']=='popup')
                {
                    showPopup(htmlspecialchars_decode(data['html']));
                } else {
                    var div = document.getElementById(data['div']);
                    div.innerHTML = data['html'];
                    if(typeof(successEval) === "function") {
                        successEval();
                    }
                }
            }
        } else {
            if (data['status'] == "error") {
                if(data['div']!=undefined && data['div']!="") {
                    if(data['div']=='alert') {alert(data['message']);}
                    else {
                        var div = document.getElementById(data['div']);
                        div.innerHTML = data['message'];
                    }
                } else {

                    alert(data['message']);
                }
            } else {
                alert("Неизвестная ошибка");
            }
        }
    } else {
        alert("Сервер вернул NULL");
    }
}

function showPopup(HTML, title) {
    if(title === undefined) {
        title = '';
    }
    var popup = document.getElementById('popup');
    popup.style.display = "block";
    var html = "<div id='popupContent'>";
    html += "<div class='popupClose'><span class='popup-title'>"+title+"</span><span class='button button-close' onclick='closePopup();'>Закрыть</span></div>";
    html += HTML;
    html += "</div>";
    popup.innerHTML = html;
}

function closePopup() {
    var popup = document.getElementById('popup');
    popup.innerHTML = "";
    popup.style.display = "none";
}


function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    console.log(out);
}

/*
 divs - string with ID's of divs, explode by ','. Example: "one,two,three"
 div - string with ID of main div. Example: "two";
 */
function showHideDivs(event, divs, div, controllClassActive) {
    if(event.className!=null && controllClassActive!=null) {
        var className = event.className;
        var controls = document.getElementsByClassName(className);
        for(i=0;i<controls.length;i++) {
            if(controls[i]==event) {
                controls[i].className = controllClassActive;
            } else {
                controls[i].className = className;
            }
        }
    }
    divs = divs.split(",");
    for(i=0;i<divs.length;i++) {
        var block = document.getElementById(divs[i]);
        if(divs[i]==div) {
            block.style.display = "block";
        } else {
            block.style.display = "none";
        }
    }
}

function goTo(href) {
    location.href = href;
}


function divSlide(object, element, sub, all){
    if(all==false)
        $(element+" > "+sub).slideToggle("slow");
    else
        $(element).find(sub).slideToggle("slow");


    if(object!=null)
        switchButtonVal(object);
}

function divMakeActive(object, normalClass, activeClass) {
    toggleClass("."+activeClass, activeClass);
    $(object).toggleClass(activeClass);
}

function toggleClass(element, className) {
    $('body').find(element).toggleClass(className);
}

function activeTopologyItem(item, activeItem, className, all) {
    toggleClass(activeItem+"."+className, className);
    if(all==0)
        $('body').find(item).find(activeItem).first().toggleClass(className);
    else if(all==1)
        $('body').find(item).find(activeItem).toggleClass(className);
}

function setFormValue(object, items, values) {
    for(i=0; i< items.length; i++) {
        document.getElementById(items[i]).value = values[i];
    }
    document.getElementById(object).innerHTML = "";
}

function switchButtonVal(object) {
    if(object.innerHTML=='+') object.innerHTML = '-';
    else object.innerHTML = '+'
}

function clearDiv(id) {
    document.getElementById(id).innerHTML = "";
}

function sendCursorCoordinates(obj, e, id) {
    var x = e.pageX - $(obj).parent().offset().left;
    var y = e.pageY - $(obj).parent().offset().top;

    var pWidth = x/$(obj).width()*100;
    var pHeight = y/$(obj).height()*100;

    sendAjax('/api/v1/setcursor/'+id+'/'+pWidth+'/'+pHeight+'/','POST');
}

function setCursorArea(manObj, subObj) {
    $(subObj).css('height', ($(manObj).height()-50)+"px");
    $(subObj).css('width', ($(manObj).width()-50)+"px");
}

function selectCalendarDay(obj) {
    var color = $('#dayType').find(':selected').data('color');
    $('#formDay'+obj.dataset.dayid).val($('#dayType').val());
    obj.style.backgroundColor = color;
}

function terminalSize(type, element) {
    var buttonMinimize = document.getElementById('terminal-minimize');
    var buttonMaximize = document.getElementById('terminal-maximize');
    var terminalScreen = document.getElementById(element);

    if(type==0) { // minimized
        buttonMaximize.style.display = "block";
        buttonMinimize.style.display = "none";
        terminalScreen.style.position = "relative";
    } else if(type==1) { // maximized
        buttonMaximize.style.display = "none";
        buttonMinimize.style.display = "block";
        terminalScreen.style.position = "fixed";
        terminalScreen.style.top = "0";
        terminalScreen.style.left = "0";
        terminalScreen.style.width = "100%";
        terminalScreen.style.height = "100%";
    }
}
