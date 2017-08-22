if (typeof(tsar) == 'undefined') {
    var tsar = new Object();
}
tsar.Loading = {
    Object: '<div class="loading"><div class="loading-img"></div><div class="loading-background"></div></div>',
    Start: function (_where, _callback) {
        if (typeof(_where) == 'undefined' || !_where) {
            _where = 'body';
        }
        if ($(_where + ' > .loading').length > 0 || $('body > .loading').length > 0) {
            return false;
        }
        $(tsar.Loading.Object).appendTo(_where).show();
        if (typeof(_callback) != 'undefined' && typeof(_callback) == 'function') {
            _callback();
        }
        if (_where == 'body') {
            $('.loading').css('position', 'fixed');
            document.body.style.cursor = "progress";
        } else {
            $(_where).css('position', 'relative');
        }
    },
    End: function (_where) {
        if (typeof(_where) == 'undefined') {
            $('.loading').fadeOut(function (){
                $(this).remove();
            });
            document.body.style.cursor = "default";
        } else {
            $(_where + ' > .loading').fadeOut(function(){
                $(this).remove();
            });
            document.body.style.cursor = "default";
        }
    }
};
tsar.Popup = {
    Object: '<div class="tsarPopup">' + '<div class="tsarPopupWrap">' + '<div class="tsarPopupClose"><div class="tsarPopupCloseButton">x</div></div>' + '<div class="tsarPopupContent">' + '</div>'  + '</div>' + '<div class="fancyboxTitle fancyboxTitleFloatWrap"><span class="child"></span></div>' + '</div>',
    Show: function (_params) {
        if (typeof(_params) == 'undefined') {
            return false;
        }
        var $content = $(tsar.Popup.Object);
        var $params = {
            title: false,
            html: false,
            text: false,
            alert: false,
            alertButton: false,
            url: false,
            nocache: false,
            element: false,
            elementClone: true,
            oldElement: false,
            image: false,
            imageZoom: true,
            video: false,
            iframe: false,
            width: false,
            height: false,
            overlay: true,
            position: 'fixed',
            autoclose: false,
            callback: false,
            addClass: false,
            newStyle: false,
            showClose: true,
            onClose: false,
            hidePrevious: true
        };
        for (var $i in _params) {
            if (_params.hasOwnProperty($i)) {
                if (typeof(_params[$i]) != 'undefined') {
                    $params[$i] = _params[$i];
                }
            }
        }
        if ($params.newStyle) {
            $content.addClass('newStyle');
        }
        if (!$params.showClose) {
            $content.addClass('tsarPopupNoclose');
            $content.find('.tsarPopupClose').remove();
        }
        var $contentFrame = $content.find('.tsarPopupContent');
        if ($params.addClass) {
            $content.addClass($params.addClass);
        }

        $content.attr('data-overlay',$params.overlay ? '1' : '0');

        if ($params.html) {
            $contentFrame.html($params.html);
            calculation();
        } else if ($params.text) {
            $contentFrame.html('<div class="tsarPopupText">' + $params.text + '</div>');
            calculation();
        } else if ($params.alert) {
            if ($params.alertButton == '') {
                $params.alertButton = 'Продолжить';
            }
            $content.addClass('isAlert');
            $contentFrame.html('<div class="popup_text">' + $params.alert + '</div><div class="btn-a" onclick="tsar.Popup.Close($(this));">' + $params.alertButton + '</div>'), calculation();
        } else if ($params.url) {
            $contentFrame.load($params.url + ($params.nocache ? '?_=' + (new Date()).getTime() : ''), function () {
                calculation();
            });
        } else if (typeof($params.element) == 'object' && $params.element) {
            var $element = $params.element;
            if ($element.length == 0) {
                console.log('Элемент не найден на странице');
                return false;
            }
            var $newDome = $element.clone($params.elementClone);
            $contentFrame.append($newDome);
            calculation();
        } else if (typeof($params.oldElement) == 'object' && $params.oldElement) {
            var $element = $params.oldElement;
            if ($element.length == 0) {
                console.log('Элемент не найден на странице');
                return false;
            }
            var $newDome = $element.clone(true);
            $contentFrame.append($newDome);
            $contentFrame.children().wrap('<div id="WrModalWindow" class="wrapper_modal_window" />').show();
            calculation();
        } else if ($params.image) {
            var $image = '<img src="' + $params.image + ($params.nocache ? '?_=' + (new Date()).getTime() : '') + '" class="directImage">';
            $content.addClass('directImageOnly');
            tsar.Loading.Start();
            $($image).get(0).onload = function () {
                $contentFrame.append($image);
                var $cBack = function(){
                    tsar.Loading.End();
                };
                calculation($cBack);

            }
        } else if (typeof($params.video) == 'object' && $params.video) {
            var $video = $params.video;
            var $videoParams = {url: '', width: 614, height: 346};
            for (var $i in $videoParams) {
                if ($videoParams.hasOwnProperty($i)) {
                    if (typeof($video[$i]) != 'undefined') {
                        $videoParams[$i] = $video[$i];
                    }
                }
            }
            var $regExp = new RegExp('.[^.]+$');
            var $flashPath = $videoParams['url'];
            var $path = $flashPath.replace($regExp, '');
            var $videoHTML = '<video id="html5video" width="' + $videoParams['width'] + '" height="' + $videoParams['height'] + '" preload controls>' + '<source src="' + $path + '.mp4" type="video/mp4" />' + '<source src="' + $path + '.webm" type="video/webm" />' + '<source src="' + $path + '.ogv" type="video/ogg" />' + '<object id="flowplayer" width="' + $videoParams['width'] + '" height="' + $videoParams['height'] + '" type="application/x-shockwave-flash">' + '<param name="movie" value="/sites/all/modules/flowplayer/flowplayer/flowplayer-3.2.16.swf" />' + '<param name="flashvars" value="config={\'clip\':\'' + $flashPath + '\'}" />' + '</object>' + '</video>';
            $contentFrame.html($videoHTML);
            calculation();
        } else if (typeof($params.iframe) == 'object' && $params.iframe) {
            var $iFrame = $params.iframe;
            var $iFrameParams = {url: '', width: 900, height: ($(window).height() - 150), id: 'VPIframe'};
            for (var $i in $iFrameParams) {
                if ($iFrameParams.hasOwnProperty($i)) {
                    if (typeof($iFrame[$i]) != 'undefined') {
                        $iFrameParams[$i] = $iFrame[$i];
                    }
                }
            }
            var $iFrameHTML = '<div class="iFrameWrap" style="width:' + $iFrameParams['width'] + 'px;"><iframe id="' + $iFrameParams['id'] + '" style="display: block;" src="' + $iFrameParams['url'] + '" width="' + $iFrameParams['width'] + '" height="' + $iFrameParams['height'] + '" align="left">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>';
            $contentFrame.html($iFrameHTML);
            calculation();
        } else {
            console.log('Неверные параметры попапа!');
            return false;
        }
        function calculation($cBack) {
            $content.appendTo('body');
            $contentFrame.css({
                'max-width': $(window).width() - 100 + 'px',
                'max-height': $(window).height() - 100 + 'px'
            });
            if ($params.image) {
                if (typeof($params.imageZoom) == 'boolean' && $params.imageZoom) {
                    $content.find('img').css({
                        'width': 'auto',
                        'height': 'auto',
                        'max-width': $contentFrame.width(),
                        'max-height': $contentFrame.height()
                    });
                } else if (typeof($params.imageZoom) == 'boolean' && !$params.imageZoom) {
                    $contentFrame.css({'overflow': 'auto'});
                } else if (typeof($params.imageZoom) == 'number') {
                    $content.find('img').css({'height': $params.imageZoom + 'px'});
                    $contentFrame.css({'overflow': 'auto'});
                }
            }
            if ($params.title) {
                var $titleWidth = $contentFrame.width();
                $contentFrame.prepend('<div class="tsarPopupTitle title">' + $params.title + '</div>');
                $contentFrame.find('.tsarPopupTitle').css({'width': $params.width ? $params.width : $titleWidth});
            }
            if ($params.width && !$params.iframe && !$params.video) {
                $contentFrame.css({'width': $params.width + 'px'});
            }
            if ($params.height && !$params.iframe && !$params.video) {
                $contentFrame.css({'height': $params.height + 'px', 'overflow-y': 'auto'});
            }
            if ($('.tsarPopup').length == 0) {
                var $zIndex = 1100;
            } else {
                var $zIndex = parseInt($('.tsarPopup').last().css('z-index')) + 50;
            }
            var $contendWidth = $contentFrame.width();
            var $contendHeight = $contentFrame.height();
            switch ($params.position) {
                case'topleft':
                    $content.css({
                        'z-index': $zIndex,
                        'margin-left': '0px',
                        'margin-top': '0px',
                        'top': '30px',
                        'left': '20px',
                        'right': 'auto',
                        'bottom': 'auto'
                    });
                    break;
                case'topright':
                    $content.css({
                        'z-index': $zIndex,
                        'margin-left': '0px',
                        'margin-top': '0px',
                        'top': '30px',
                        'left': 'auto',
                        'right': '30px',
                        'bottom': 'auto'
                    });
                    break;
                case'bottomleft':
                    $content.css({
                        'z-index': $zIndex,
                        'margin-left': '0px',
                        'margin-top': '0px',
                        'top': 'auto',
                        'left': '20px',
                        'right': 'auto',
                        'bottom': '20px'
                    });
                    break;
                case'bottomright':
                    $content.css({
                        'z-index': $zIndex,
                        'margin-left': '0px',
                        'margin-top': '0px',
                        'top': 'auto',
                        'left': 'auto',
                        'right': '30px',
                        'bottom': '20px'
                    });
                    break;
                case'absolute':
                    $content.css({
                        'z-index': $zIndex,
                        'margin-left': '-' + (($contendWidth + 10) / 2) + 'px',
                        'margin-top': 0 - (($contendHeight + 10) / 2) + $(window).scrollTop() + 'px',
                        'position': 'absolute',
                        'left': '50%',
                        'top': '50%'
                    });
                    break;
                default:
                    $content.css({
                        'z-index': $zIndex,
                        'margin-left': '-' + (($contendWidth + 20) / 2) + 'px',
                        'margin-top': '-' + (($contendHeight + 20) / 2) + 'px',
                        'left': '50%',
                        'top': '50%'
                    });
                    break;
            }

            var $innerHeight = 0, $children = $contentFrame.children();
            $($children).each(function () {
                $innerHeight = $innerHeight + $(this).height();
            });
            if ($contentFrame.height() + 5 < $innerHeight) {
                $contentFrame.css({
                    'overflow-y': 'auto',
                    'padding-right': '20px',
                    'width': ($contentFrame.width() + 5) + 'px'
                });
                $content.css({
                    'margin-left': (parseInt($content.css('margin-left')) -10) + 'px'
                });
            }
            if ($params.hidePrevious) {
                $('.tsarPopup').prevAll('.tsarPopup').hide();
            }
            $content.animate({opacity: 1}, 500, function () {
                $('.tsarPopupClose').bind('click', function () {
                    if (typeof($params.onClose) == 'function' && $params.onClose) {
                        tsar.Popup.Close($(this), $params.onClose());
                    } else {
                        tsar.Popup.Close($(this));
                    }
                });
                if (typeof($cBack) == 'function' && $cBack) {
                    $cBack();
                }
                if (typeof($params.callback) == 'function' && $params.callback) {
                    $params.callback();
                }
            });
            if ($params.overlay && $('#tsarPopupOverlay').length == 0) {
                if ($(window).width() < 750) {
                    $('<div id="tsarPopupOverlay"></div>').appendTo('body').show();
                } else {
                    $('<div id="tsarPopupOverlay" onclick="' + ($params.showClose ? 'tsar.Popup.Close();' : '') + '"></div>').appendTo('body').show();
                }
            }
        }

        if ($params.autoclose) {
            var $rel = $('.tsarPopup').filter('[rel]');
            if ($rel.length == 0) {
                var $thisID = 1;
            } else {
                var $thisID = parseInt($rel.last().attr('rel')) + 1;
            }
            $content.attr('rel', $thisID);
            tsar.Popup.Timeouts($thisID, $params.autoclose);
        }
        tsar.Popup.EscapeClose();
    },
    Close: function (_item, _callback, _notCloseOverlay) {
        if (typeof(_item) != 'undefined' && _item != false) {
            var $object = $(_item).closest('.tsarPopup');
        } else {
            var $object = $('.tsarPopup').last();
        }
        if ($('.tsarPopup').length > 1) {
            $('.tsarPopup').fadeIn('slow');
        }
        $object.animate({opacity: 0}, 500, function () {
            $object.remove();
        });
        if (typeof(_notCloseOverlay) != 'undefined' && _notCloseOverlay) {
            _callback();
        } else {
            tsar.Popup.OverlayRemove(_callback);
        }
    },
    OverlayRemove: function (_callback) {
        if ($('.tsarPopup[data-overlay="1"]').length <= 1) {
            $('#tsarPopupOverlay').hide().remove();
        }
        if (typeof(_callback) != 'undefined' && typeof(_callback) == 'function') {
            _callback();
        }
    },
    Timeouts: function ($thisID, $time) {
        setTimeout(function () {
            $('.tsarPopup').filter('[rel=' + $thisID + ']').find('.tsarPopupClose').click();
        }, ($time * 1000));
    },
    EscapeClose: function () {
        $(document).keyup(function (evt) {
            if (evt.which == 27) {
                $('.tsarPopupClose').last().click();
            }
        });
    },
    Error : function($title,$message,$button,$closeLoading) {
        if (typeof($button) == 'undefined' || !$button || $button == '') {
            $button = 'Продолжить';
        }
        if (typeof($title) == 'undefined' || !$title || $title == '') {
            $title = 'Обратите внимание';
        }
        if (typeof($closeLoading) == 'undefined') {
            $closeLoading = true;
        }
        tsar.Popup.Show({
            title : $title,
            html : '<div class="errorsList">'+$message+'</div><div class="btn-a" onclick="tsar.Popup.Close($(this))">'+$button+'</div>',
            callback : function(){
                if ($closeLoading) {
                    tsar.Loading.End();
                }
            }
        })
    }
};
tsar.TestEmail = function ($email) {
    var $RegEx = /^[-a-zA-Z0-9!#$%&'*+/=?^_`{|}~]+(?:\.[-a-zA-Z0-9!#$%&'*+/=?^_`{|}~]+)*@(?:[a-zA-Z0-9]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9])?\.)*(?:[a-zA-Z]{2,6})$/;
    var $result = $RegEx.test($email);
    return $result;
};
tsar.ArrayLength = function (_obj) {
    if (typeof(_obj) == 'undefined') {
        return false;
    }
    var count = 0;
    for (var i in _obj) {
        if (_obj.hasOwnProperty(i)) {
            count++;
        }
    }
    return count;
};
tsar.Rand = function (min, max, count) {
    if (typeof(count) == 'undefined') {
        count = 0
    }
    return (Math.random() * (max - min) + min).toFixed(count);
};
tsar.Plural = function (a, b, c, d) {
    a = parseInt(a);
    var $plural = 0
    if (a % 10 == 1 && a % 100 != 11) {
        $plural = 0
    } else {
        if (a % 10 >= 2 && a % 10 <= 4 && a % 100 < 10 | a % 100 >= 20) {
            $plural = 1
        } else {
            $plural = 2
        }
    }
    switch ($plural) {
        case 0:
        default:
            return b;
        case 1:
            return c;
        case 2:
            return d
    }
};

tsar.LeadZero = function ($num) {
    $num = parseInt($num);
    return $num < 10 ? '0' + $num : $num
};
tsar.CheckForm = {
    init: function () {
        $(document).on('focus', '.toCheck input, .toCheck select, .toCheck textarea', function () {
            if ($(this).is('.err')) {
                $(this).removeClass('err');
                $(this).siblings('.errMsg').slideUp();
            }
        }).on('change', '.toCheck input[type="checkbox"], .toCheck input[type="radio"], .toCheck input[type="file"]', function () {
            if ($(this).is('.err')) {
                $(this).removeClass('err');
                $(this).siblings('.errMsg').slideUp();
            }
            $(this).closest('.err').removeClass('err');
        }).on('keyup', '.numsOnly', function (evt) {
            var $allow = /[^\d]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.floatNumsOnly', function (evt) {
            $(this).val($(this).val().replaceAll(',', '.'));
            var $allow = /[^\d\.]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.lettersOnly', function (evt) {
            var $allow = /[^A-Za-z\s]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.lettersNumsOnly', function (evt) {
            var $allow = /[^A-Za-z0-9]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.lettersOnlyRus', function (evt) {
            var $allow = /[^А-Яа-я]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.lettersOnlyAll', function (evt) {
            var $allow = /[^A-Za-zА-Яа-я\s]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.lettersOnlyRusSpace', function (evt) {
            var $allow = /[^А-Яа-я\s]/;
            $(this).val($(this).val().replaceAll($allow, ''));
        }).on('keyup', '.phoneOnly', function (evt) {
            var $allow = /[^\d\(\)\s\-\+']/;
            $(this).val($(this).val().replaceAll($allow, ''));
        });

        if ( $('.errorsAll').length > 0 && $.trim($('.errorsAll').html()) != '' ) {
            tsar.Popup.Error('Обратите внимание', $('.errorsAll').html());
        }
    },
    Result: function ($errors, $focus) {
        $('.errMsg').remove();
        $('.err').removeClass('err');
        $.each($errors, function (i, item) {
            $(item.field).addClass('err');
            if (typeof(item.place) != 'undefined' && item.place) {
                var $where = $(item.field).siblings(item.place);
            } else {
                var $where = $(item.field);
            }
            if (item.mess && item.mess != '') {
                $('<div class="errMsg">' + item.mess + '</div>').insertAfter($where);
            }
        });
        if (typeof($focus) != 'undefined' && $focus) {
            tsar.Scroll($('.err').eq(0))
        }
    },
    ResultsarPopup: function ($errors) {
        tsar.Popup.Show({
            title: 'Вы неверно заполнили следующие поля:',
            html: '<ul class="formErrors"><li>' + $errors.join('</li><li>') + '</li></ul>'
        })
    }
};
tsar.Scroll = function ($element, $offset) {
    if (typeof($offset) == 'undefined') {
        $offset = 10;
    }
    if ($('#header').css('position') == 'fixed') {
        $offset = $offset + $('#header').height();
    }
    if (typeof($element) == 'object' || typeof($element) == 'string') {
        if ($($element).length > 0) {
            $('body,html').animate({scrollTop: $($element).offset().top - $offset}, 500);
        }
    } else if (typeof($element) == 'number') {
        $('body,html').animate({scrollTop: parseInt($element) - $offset}, 500);
    }
};

tsar.ObjectClone = function (o) {
    if (!o || "object" !== typeof o) {
        return o;
    }
    var c = "function" === typeof o.pop ? [] : {};
    var p, v;
    for (p in o) {
        if (o.hasOwnProperty(p)) {
            v = o[p];
            if (v && "object" === typeof v) {
                c[p] = clone(v);
            }
            else c[p] = v;
        }
    }
    return c;
};

tsar.getMaxHeight = function($elem) {
    var $res = 0;
    if (typeof($elem) == 'undefined') {
        return $res;
    }
    $($elem).each(function(){
        if ( $(this).height() > $res ) {
            $res =  $(this).height();
        }
    });
    return $res;
};


tsar.getUniqueArray = function($names){
    var $uniqueNames = [];
    $.each($names, function(i, el){
        if($.inArray(el, $uniqueNames) === -1) $uniqueNames.push(el);
    });
    return $uniqueNames;
};

tsar.EqualHeights = function ($selectors) {
    var $height = 0;
    $($selectors).each(function(){
        if ($(this).height() > $height) {
            $height = $(this).height();
        }
    });
    $($selectors).height($height);
    return $height;
};

$(document).ready(function(){
    tsar.CheckForm.init();
});

String.prototype.replaceAll = function (a, b) {
    return this.split(a).join(b)
};


tsar.Request = function () {
    if (!/\?/.test(window.location.href)) {
        return false;
    }
    var $request = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&"), $request_result = {};
    for (var $i = 0; $i < $request.length; $i++) {
        $temp = $request[$i].split("=");
        if (typeof($temp[1]) == 'undefined') {
            $temp[1] = false;
        }
        $request_result[$temp[0]] = $temp[1];
    }
    return $request_result;
};

tsar.LocalStorage = {
    get: function ($key) {
        var $data = '';
        if (typeof localStorage !== 'undefined') {
            try {
                $data = localStorage.getItem($key);
            } catch(e) {}
        }
        return $data;
    },
    set: function ($key, $value) {
        if (typeof localStorage !== 'undefined') {
            try {
                if (typeof($value) == 'object' || typeof($value) == 'array') {
                    $value = JSON.stringify($value);
                }
                localStorage.setItem($key, $value);
            } catch(e) {}
        }
        return true;
    },
    delete: function ($key) {
        if (typeof localStorage !== 'undefined') {
            try {
                localStorage.removeItem($key);
            } catch(e) {}
        }
        return true;
    },
    clear: function () {
        if (typeof localStorage !== 'undefined') {
            try {
                localStorage.clear();
            } catch(e) {}
        }
        return true;
    }
};


tsar.popitup = function (url) {
	newwindow = window.open(url, 'name', 'height=256,width=512');
	if (window.focus) {
		newwindow.focus()
	}
	return false;
};