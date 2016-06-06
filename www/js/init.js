// создаем окно моделирующее модальность всплывающего окна
$.fn.overlay=function() {
    
    var el=$(this);
    $('body').prepend('<div id="overlay"></div>');
    $('#overlay').click(function(){
        el.hide();
        /*$('tr').removeClass('gr_tr');*/
        $('#overlay').remove();
    });
    $('#overlay').show('slow');
    return this;
};

$.fn.overlay_white=function() {
    var el=$(this);
    $('body').prepend('<div id="overlay_white"></div>');
    $('#overlay_white').show('slow');
    return this;
};

// размещение всплывающего окна по центру окна
$.fn.centering = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.outerHeight() ) / 4  + "px");
    this.css("left", ( $(window).width() - this.outerWidth() ) / 2 + "px");
    return this;
};

// вероятно для создания плавающего фрейма
if ($.fn.bgIframe == undefined) {
    
    $.fn.bgIframe = function() {return this; };
}

// получаем абсолютные координаты элемента (Х, У)
function _getAbsolutePosition(el) {

    var r = { x: el.offsetLeft, y: el.offsetTop };
    if (el.offsetParent) {
        var tmp = _getAbsolutePosition(el.offsetParent);
        r.x += tmp.x;
        r.y += tmp.y;
    }
    return r;
}

// получаем абсолютные координаты элемента (Х, У)
function getAbsolutePosition(el) {

    var coord = _getAbsolutePosition(el),
        container = $(el).parents('#container');

    if (container.length){

        coord.x -= container.offset().left;
        coord.offset = container.offset().left;
    }
    return coord;
}

// получаем абсолютные координаты элемента (Х, У)
function getAbsolutePositionRelative(el) {

    return _getAbsolutePosition(el);
}

// Функция возвращает значение параметра из query string
$.urlParam = function(name){
    
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (!results) { return 0; }
    return results[1] || 0;
};

/**
 * Склонение существительных после числительных
 * 
 * @example 
 * declOfNum(12, ['комментарий','комментария','комментариев']); //"12 комментариев"
 *
 **/
function declOfNum($number, $titles){  
    
    var $cases = [2, 0, 1, 1, 1, 2];  
    return $number+" "+$titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[Math.min($number%10, 5)] ];  
}


//определена ли переменная
function isDefined (mixed) {
    
    return typeof(mixed) != 'undefined';
}

//является ли функцией переменная
function isFunction (mixed) {
    
    return isDefined(mixed) && mixed.constructor == Function;
}

//конвертируем объект в массив
function objToArray (obj) {
                
    var array = $.map(obj, function(k, v) {
        return [k];
    });
    
    return array;
}

// повторяет символ определенное количество раз
String.prototype.repeat = function( num ){
    
    return new Array( num + 1 ).join( this );
};

Array.prototype.max = function() {
    var max = this[0];
    var len = this.length;
    for (var i = 1; i < len; i++) {
        if (!max || this[i] > max) {
            max = this[i];
        }
    }
    return max;
};
Array.prototype.min = function() {
    var min = this[0];
    var len = this.length;
    for (var i = 1; i < len; i++) {
        if (!min || this[i] < min) {
            min = this[i];
        }
    }
    return min;
};

/**
 * Трассировка данных
 * @param data
 */
function trace(data) {

    if(typeof console != "undefined"){
        try { console.log(data) } catch (e) {
            //т.к. фунция alert у нас переопределена, она принимает только строки
            alert(data || data.message || '');
        }
    } else {
        alert(data);
    }
}

/**
 * переопределяем метод val, для того чтобы он адекватно обрабатывал ситуации с placeholder'ом
 */
$.fn.standartVal = $.fn.val;
$.fn.val = function(value) {

    if (typeof(value) != 'undefined') {
        return this.standartVal(value);
    }

    if (this[0]) {
        var e = $(this[0]),
            placeholder = e.attr('placeholder'),
            val = e.standartVal();

        if (placeholder && placeholder == val) {
            return '';
        } else {
            return val;
        }
    }

    return undefined;
};

/**
 * Создаем jquery-radio для элемента e 
 * В качестве элемента e - присылаем .voting
 */
function applyRadio(e) {
    
    $('input:radio:not([jquery-radio=1])', e).attr('jquery-radio', '1').checkbox({cls:'jquery-radio'}).click(function() {
        
        $(this).parent().parent().siblings().removeClass('active').end().addClass('active');
    }).parents('li').click(function() {
        
        $('input',this).click();
    });
}

/**
 * применяем присутствие плейсхолдера, для браузеров не относящихся к webkit
 * @param e
 */
function applyPlaceholder(e) {

    //if (!$.browser.webkit) {

        if (!isDefined(e)) {
            e = $('INPUT[placeholder], TEXTAREA[placeholder]');
        }

        e.blur(function(){

            if ($(this).val() == ''){
                $(this).val($(this).attr('placeholder'));
                $(this).addClass('m-placeholder');
            }
        }).focus(function(){
            $(this).removeClass('m-placeholder');

            if ($(this).val() == '') {
                $(this).val('');
            }
        }).each(function(){

            if (($(this).val()=='') || ($(this).val() == $(this).attr('placeholder'))){
                $(this).val($(this).attr('placeholder'));
                $(this).addClass('m-placeholder');
            }

            var form = $(this).closest('FORM');

            if (form.length) {
                form.submit(function(){
                    if ($(this).val()==$(this).attr('placeholder'))
                        $(this).val('');
                });
            }
        });
    //}
}

/**
 * применяем присутствие полей с плейсхолдерами в форме, для браузеров не относящихся к webkit
 * @param e
 */
function applyFormPlaceholderSubmit(e) {

    if (!$.browser.webkit) {

        if (!isDefined(e)) {
            e = $('FORM');
        }

        e.submit(function(){
            $('INPUT[placeholder], TEXTAREA[placeholder]').each(function(){
                var field = $(this);
                field.val(field.val());
            });
        });
    }
}


new function($) {
    $.fn.setCursorPosition = function(pos) {
        if ($(this).get(0).setSelectionRange) {
            $(this).get(0).setSelectionRange(pos, pos);
        } else if ($(this).get(0).createTextRange) {
            var range = $(this).get(0).createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    }
}(jQuery);

/**
 * Функция преобразования формы в массив
 */
(function($){
    $.fn.toJSON = function(options){

        options = $.extend({}, options);

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = self.build({}, self.push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build({}, k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
})(jQuery);


/**
 * наследуем class1 от class2
 * нужно чтобы все методы/свойства классов были описаны в prototype, в противном случае работать не будет или будет не так как надо
 * также прописывается свойство parent для доступа к методам предка, это актуально когда isRewrite = false, и методы потомка могут отличаться от методов предка
 * @param childClass
 * @param parentClass
 * @param isRewrite по умолчанию true, если поставить false то уже существующие свойста не будут перезаписываться
 */
function extendsClass(childClass, parentClass, isRewrite) {

    if (!isDefined(isRewrite)) {
        isRewrite = true;
    }

    for(var property in parentClass.prototype) {

        if (isRewrite || (!isRewrite && !isDefined(childClass.prototype[property]))) {
            
            childClass.prototype[property] = parentClass.prototype[property];
        }
    }

    childClass.prototype.parent =  parentClass.prototype;
}

/**
 * возвращаем только уникальные элементы массива
 * @param array
 */
arrayUnique = function(array) {

    var i = array.length,
        tempObj = {},
        uniqArray = [];

    while(i--) {
        tempObj[array[i]] = 1;
    }

    for(i in tempObj) {
        uniqArray.push(i);
    }

    return uniqArray;
}

/**
 * форматирование даты
 * @param {string} params
 * @param {string} datetime
 */
function dateRus(params, datetime) {
    
    var rusMonths = {
        1:  ['январь',   'января'],
        2:  ['февраль',  'февраля'],
        3:  ['март',     'марта'],
        4:  ['апрель',   'апреля'],
        5:  ['май',      'мая'],
        6:  ['июнь',     'июня'],
        7:  ['июль',     'июля'],
        8:  ['август',   'августа'],
        9:  ['сентябрь', 'сентября'],
        10: ['октябрь',  'октября'],
        11: ['ноябрь',   'ноября'],
        12: ['декабрь',  'декабря']
    };
    
    //сделано так потому что new Date('Y-m-d H:i:s') не хочет принимать
    var datetimeBlocks = datetime.split('.')[0].split(' '),
        dateBlocks = datetimeBlocks[0].split('-'),
        timeBlocks = datetimeBlocks[1].split(':'),
        date = new Date(dateBlocks[0], dateBlocks[1] - 1 /* нумерация месяцев начинается с 0*/, dateBlocks[2], timeBlocks[0], timeBlocks[1], timeBlocks[2]),

        autofillToTwoSignNumber = function(number){

            //изза конвертации number станет строкой
            number = '' + number;

            if (number.length == 1) {
                number = '0' + number;
            }

            return number;
        },

        //вовзращаем более понятную пользователю дату
        //функцию определил внутри, т.к. она бессмысленна в контексте самого объекта
        getUserFriendlyDay = function(date){
            var dateMs = date.getTime(), //в миллисекундах, 0.001 секунды
            curMs = new Date(),
            todayMs = new Date(curMs.getFullYear(), curMs.getMonth(), curMs.getDate()).getTime();
            
            if (dateMs >= todayMs && dateMs <= (todayMs + 86400000)) {
                return 'Сегодня';
            } else if (dateMs >= (todayMs - 86400000) && dateMs < todayMs) {
                return 'Вчера';
            } else {
                return autofillToTwoSignNumber(date.getDate()) + ' ' +
                       rusMonths[date.getMonth() + 1][1] + ' ' +
                       autofillToTwoSignNumber(date.getFullYear()) + ' года';
            }
        };
    
    return params
        .replace('%d', autofillToTwoSignNumber(date.getDate()))
        .replace('%m', autofillToTwoSignNumber(date.getMonth() + 1))
        .replace('%Y', autofillToTwoSignNumber(date.getFullYear()))
        .replace('%H', autofillToTwoSignNumber(date.getHours()))
        .replace('%i', autofillToTwoSignNumber(date.getMinutes()))
        .replace('%s', autofillToTwoSignNumber(date.getSeconds()))
        .replace('%rus_month_nominative', rusMonths[date.getMonth() + 1][0])
        .replace('%rus_month_genitive', rusMonths[date.getMonth() + 1][1])
        .replace('%rus_day_user_friendly%', getUserFriendlyDay(date));
}

function wordwrap( str, int_width, str_break, cut ) {	// Wraps a string to a given number of characters
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Nick Callen

    var i, j, s, r = str.split("\n");
    if(int_width > 0) for(i in r){
        for(s = r[i], r[i] = ""; s.length > int_width;
            j = cut ? int_width : (j = s.substr(0, int_width).match(/\S*$/)).input.length - j[0].length || int_width,
                r[i] += s.substr(0, j) + ((s = s.substr(j)).length ? str_break : "")
            );
        r[i] += s;
    }
    return r.join("\n");
}

/**
 * Частичный поиск по элементам объекта
 *
 * @param findStr
 * @param objData
 * @param onlyFirst
 * @param linkData
 * @return {Array}
 */
function search(findStr, objData, onlyFirst, linkData){

    var resArr,
        transform = function(str){
            return str.toLowerCase().replace('ё', 'e').replace('й', 'и');
        },
        compare = function(a){

            if (onlyFirst){

                return transform(a).substr(0, findStr.length) == transform(findStr);
            } else {

                return transform(a).indexOf(transform(findStr)) > -1;
            }
        };

    // проверяем ее размер
    if (!findStr.length){

        return objData;
    }

    if (objData instanceof Array) {

        resArr = [];
        $.each(objData, function(index, value){

            if (isDefined(linkData) && isDefined(linkData[value[0]])){

                resArr[index] = value;
            } else {

                if (compare(value[1])) {

                    resArr[index] = value;
                }
            }
        });
    } else {

        resArr = {};
        $.each(objData, function(index, value){

            $.each(value, function(ind, val){

                if (compare(val[1])){

                    if (!isDefined(resArr[index])){

                        resArr[index] = [];
                    }
                    resArr[index][ind] = val;
                }
            });
        });
    }

    // ура, выдаем только то что соответсвует
    return resArr;
};

function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

//checks if flash is installed/enabled on the browser
function isFlashEnabled() {
    var hasFlash = false;
    try {
        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
        if (fo) {

            hasFlash = true;
        }
    }
    catch(e) {

        if (navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) {

            hasFlash = true;
        }
    }
    return hasFlash;
}

function copyUrlToClipboard(el) {

    if (isFlashEnabled()) {

        el.zclip({
            path: "/js/ZeroClipboard.swf",
            copy: function(){
                return window.location.href;
            },
            afterCopy:function(){
                return false;
            }
        });
    } else {

        el.hide();
    }
}

/**
 * Посылаем стандартный AJAX-запрос. По выполнению вызываются completeHandler, если произошла ошибка - errorHandler.
 * @param params Объект с описанием запроса. обязательно должно быть указано поле action, остальные опциональные.
 *
 * params = {
 *          action: "someAction",
 *          method: "POST",
 *          complete: function(){},
 *          error: function(){},
 *          data: "some data in any possible way"
 * };
 *
 */
function ajaxSendRequest (params) {
    if(!isDefined(params.action)){
        return;
    }

    if (baseUrl == '/') {

        baseUrl = '';
    }

    var request = {
        action: params.action,
        data: params.data
    };

    var completeHandler = isFunction(params.complete) ? params.complete : function(response){alert(response.data)},
        errorHandler = isFunction(params.error) ? params.error : function(response){alert(response.data)},
        method = isDefined(params.method) ? params.method : "POST",
        async = isDefined(params.async) ? params.async: true,
        progress = isDefined(params.progress) ? params.progress : false;

    var xhr = $.ajax({
        type: method,
        url: baseUrl + "/ajax",
        dataType: 'json',
        data: request,
        async: async,
        success: function (response) {
            if (response || false) {
                if(response.debug.length){
                    trace(response.debug);
                }
                if (response.status == "error") {
                    errorHandler(response);
                } else {
                    completeHandler(response);
                }
            }
        },
        complete: function(){
            if(progress){
                $(progress).hide();
            }
        }
    });

    if(progress){
        $(progress).show();
    }

    return xhr;
}