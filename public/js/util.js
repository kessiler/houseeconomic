/**
 * Created with JetBrains PhpStorm.
 * User: KESSILER
 * Date: 07/04/13
 * Time: 03:38
 * To change this template use File | Settings | File Templates.
 */
var Framework = {
    verifyOnOff: function() {
        Framework.request('loadIpArduino', '', 'GET', false, function(response) {
            $.ajax({
                type: 'GET',
                url: response,
                dataType: 'jsonp',
                data: 'RequestStatus',
                crossDomain: true,
                cache: false,
                jsonp : false,
                timeout: 10000,
                jsonpCallback: 'callBack',
                success: function(data) {
                    data = $.parseJSON(data);
                    $.each(data, function(index, requestStatus){
                        $('.'+requestStatus.name+' .number').empty().append(requestStatus.status);
                        if(requestStatus.status == 'ON') {
                            $('.'+requestStatus.name).fadeTo(300, 1.0);
                        } else {
                            if(requestStatus.status == 'OFF') {
                                $('.'+requestStatus.name).fadeTo(300, 0.4);
                            }
                        }
                    });
                }
            });
        });
        return true;
    },
    requestOnOff: function(state, disp) {
        var stateMod = '';
        if($.trim($('#'+state+' .number').text()) == 'ON') {
            stateMod = '0';
        } else {
            stateMod = '1';
        }
        if(Framework.requestArduino(disp+'='+stateMod, false)) {
            if(stateMod == '0')
            {
                $('#'+state+' .number').empty().append('OFF');
                $('#'+state).fadeTo(1000, 0.4);
            } else {
                $('#'+state+' .number').empty().append('ON');
                $('#'+state).fadeTo(1000, 1.0);
            }
        }
    },
    requestTemperature: function(iddispositive, charts) {
        if(Framework.requestArduino(iddispositive, function (data) {
            data = $.parseJSON(data);
            $.each(data, function(index, dataReturn){
                if (charts) {
                    var chart = $('#'+dataReturn.name).highcharts();
                    chart.series[0].points[0].update(parseFloat(dataReturn.status));
                } else {
                    return dataReturn.status;
                }
            });
        }));
    },
    temperatureDiv: function(idsDisp, writeDiv) {
        var value = Framework.requestTemperature(idsDisp, false);
        $('#' + writeDiv).empty().append(value);
    },
    requestArduino: function(data, responseHandler) {
        Framework.request('loadIpArduino', '', 'GET', false, function(response) {
            $.ajax({
                type: 'GET',
                url: response,
                dataType: 'jsonp',
                data: data,
                crossDomain: true,
                cache: false,
                jsonp : false,
                timeout: 10000,
                jsonpCallback: 'callBack',
                success: function(data) {
                    if(responseHandler) {
                        responseHandler(data);
                    }
                },
                error: function(error) {
                    console.error(error.message);
                }
            });
        });
        return true;
    },
    request: function (action, data, type, loading, responseHandler) {
        var params = {
            action: action,
            dataset: JSON.stringify(data)
        };
        if(loading) {
            if(Framework.isMobile()) {
                window.Mobile.showLoading();
            } else {
                $('body').append('<div class="modal-backdrop fade in" id="modalback">' +
                    '<div id="ajaxdiv">' +
                    '<img src="public/img/ajax-loader.gif" />' +
                    '</div>' +
                    '</div>'
                );
                $("#ajaxdiv").center(false);
            }
        }
        $.ajax({
            type: type,
            url: 'index.php',
            data: params,
            timeout: 10000,
            cache: false,
            success: function (response) {
                responseHandler(response);
            },
            error: function (error) {
                console.error(error.message);
            },
            complete: function () {
                if(loading) {
                    if(Framework.isMobile()) {
                        window.Mobile.removeLoading();
                    } else {
                        $('#modalback').remove();
                    }
                }
            }
        })
    },
    loadWindow: function (windowName) {
        Framework.request('loadWindow', windowName, 'GET', true, function (response) {
            $('.page-content').empty().append(response);
            $('#' + windowName).each(function () {
                $('#' + windowName).addClass('active');
                $('#' + windowName).children('a > span.arrow').addClass('open');
            });
            $('#' + windowName).addClass('active');
            if ($(window).width() < 900) {
                if($(".nav-collapse").hasClass("in")) {
                    $(".nav-collapse").collapse('hide');
                }
            }
            Framework._handleStyler();
        })
    },
    init: function () {
        if($(window).width() >= 1024 && $(window).width() <= 1280) {
            $('.page-content').css('min-height', '550px');
        } else {
            if($(window).width() > 1280) {
                $('.page-content').css('min-height', '830px');
            } else {
                if($(window).width() < 500) {
                    $('.navbar .brand').css('font-size', '16px');
                }
            }
        }
        this._handleSidebar();
        this._handleGoTop();
        this._handleTabletElements();
        this._handleDesktopElements();
        this.handleSidenarAndContentHeight();
        Charts.reinitialize();
    },
    _handleSidebar: function () {
        var sideBarOpen = true;
        if ($(window).width() < 900) {
            $(".page-container").removeClass("sidebar-closed");
            $("#hidden-phonetoggle").css({display: "none"});

        } else {
            $("#hidden-phonetoggle").css({display: "block"});
        }
        $(".page-container .sidebar-toggler").click(function () {
            if (sideBarOpen) {
                $("body").addClass("page-sidebar-closed");
                sideBarOpen = false;
            } else {
                $("body").removeClass("page-sidebar-closed");
                sideBarOpen = true;
            }
            setTimeout(function () {
                Charts.reinitialize();
            }, 100);
        })
    },
    handleSidenarAndContentHeight: function () {
        var content = $('.page-content');
        var sidebar = $('.page-sidebar');
        var height = sidebar.height() + 20;
        if (height >= content.height()) {
            content.attr('style', 'min-height:' + height + 'px !important');
        }
    },
    _handleGoTop: function () {
        jQuery('.footer .go-top').click(function (e) {
                jQuery('html,body').animate({scrollTop: $("html").offset().top}, 'slow');
                e.preventDefault();
            });
    },
    _handleTabletElements: function () {
        if ($(window).width() <= 1280) {
            $(".responsive").each(function () {
                var forTablet = $(this).attr('data-tablet');
                var forDesktop = $(this).attr('data-desktop');
                if (forTablet) {
                    $(this).removeClass(forDesktop);
                    $(this).addClass(forTablet);
                }
            });
        }
    },
    _handleDesktopElements: function () {
        if ($(window).width() > 1280) {
            $(".responsive").each(function () {
                var forTablet = $(this).attr('data-tablet');
                var forDesktop = $(this).attr('data-desktop');
                if (forTablet) {
                    $(this).removeClass(forTablet);
                    $(this).addClass(forDesktop);
                }
            });
        }
    },
    _handleStyler: function () {
        var panel = $('.color-panel');
        if (panel.is(':visible')) {
            $('.icon-color', panel).click(function () {
                $('.color-mode').show();
                $('.icon-color-close').show();
            });

            $('.icon-color-close', panel).click(function () {
                $('.color-mode').hide();
                $('.icon-color-close').hide();
            });

            $('li', panel).click(function () {
                var color = $(this).attr("data-style");
                setColor(color);
                $('.inline li', panel).removeClass("current");
                $(this).addClass("current");
            });

            var setColor = function (color) {
                if (color == 'style') {
                    $('#style_theme').remove();
                } else {
                    $('#style_theme').remove();
                    $('head').append('<link href="public\\css\\themes\\' + color + '.css" rel="stylesheet" id="style_theme" />');
                }
            }
        }
    },
    _handlePortlets: function() {
        jQuery('.portlet .tools .collapse, .portlet .tools .expand').click('click', function () {
            var el = jQuery(this).parents(".portlet").children(".portlet-body");
            if (jQuery(this).hasClass("collapse")) {
                jQuery(this).removeClass("collapse").addClass("expand");
                el.slideUp(200);
            } else {
                jQuery(this).removeClass("expand").addClass("collapse");
                el.slideDown(200);
            }
        });
    },
    logout: function() {
        Framework.request('logout', '', 'POST', false, function(response) {
            window.location.href = "";
        })
    },
    stateServer: function() {
        Framework.request('loadIpArduino', '', 'GET', false, function(response) {
            $.ajax({
                type: 'GET',
                url:response,
                dataType: 'jsonp',
                crossDomain: true,
                cache: false,
                jsonp : false,
                timeout: 2000,
                jsonpCallback: 'callBack',
                complete: function(data) {
                    if(data.readyState == 4 && data.status == 200) {
                        $('#stateServer').empty().html('<font class="onlineStatus">Online</font>');
                    } else {
                        $('#stateServer').empty().html('<font class="offlineStatus">Offline</font>');
                    }
                }
            });
        });
        return true;
    },
    _loginValidate: function() {
        var form = $('.login-form');
        form.validationEngine();
        if(form.validationEngine('validate')) {
            form = form.serializeObject();
            Framework.request('logar', form, 'POST', true, function (response) {
                response = $.parseJSON(response);
                if(response.error) {
                    if(Framework.isMobile()) {
                        window.Mobile.showMessage(response.msg);
                    } else {
                        $('#error #errormsg').empty().append(response.msg);
                        $('#error').slideDown(400, function() {
                            $(this).removeClass('hide');
                        });
                    }
                } else {
                    document.getElementById('ajaxLoad').innerHTML = response.msg;
                    Framework.Menus();
                    Framework.loadWindow('pageSobre');
                    Framework.init();
                }
            });
        }
    },
    formUserValidate: function() {
        var form = $('#userInfo');
        form.validationEngine();
        if(form.validationEngine('validate')) {
            form = form.serializeObject();
            Framework.request('saveUserInfo', form, 'POST', true, function (response) {
                if(response == '1') {
                    if(!$('#sucessInfoUser').hasClass('hide')) {
                        $('#sucessInfoUser').css({display: 'none'});
                    }
                    $('#errorInfoUser').css({display: 'none'});
                    $('#sucessInfoUser').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                } else {

                    if(!$('#errorInfoUser').hasClass('hide')) {
                        $('#errorInfoUser').css({display: 'none'});
                    }
                    $('#sucessInfoUser').css({display: 'none'});
                    $('#errorInfoUser').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                    console.error('saveUserInfoError: '+response);
                }
            });
        }
    },
    formPassValidate: function() {
        var form = $('#passform');
        form.validationEngine();
        if(form.validationEngine('validate')) {
            form = form.serializeObject();
            Framework.request('savePassword', form, 'POST', true, function (response) {
                if(response == '1') {
                    if(!$('#sucessPass').hasClass('hide')) {
                        $('#sucessPass').css({display: 'none'});
                    }
                    $('#errorPass').css({display: 'none'});
                    $('#sucessPass').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                } else {
                    if(!$('#errorPass').hasClass('hide')) {
                        $('#errorPass').css({display: 'none'});
                    }
                    $('#sucessPass').css({display: 'none'});
                    $('#errorPass').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                    console.error('savePasswordError: '+response);
                }
            });
        }
    },
    formArdValidate: function() {
        var form = $('#arduinoform');
        form.validationEngine();
        if(form.validationEngine('validate')) {
            form = form.serializeObject();
            Framework.request('ipArduinoSave', form, 'POST', true, function (response) {
                if(response == '1') {
                    if(!$('#sucessArdID').hasClass('hide')) {
                        $('#sucessArdID').css({display: 'none'});
                    }
                    $('#errorArdID').css({display: 'none'});
                    $('#sucessArdID').fadeIn("slow", function() {
                                $(this).removeClass('hide');
                            }
                        );
                } else {
                    if(!$('#errorArdID').hasClass('hide')) {
                        $('#errorArdID').css({display: 'none'});
                    }
                    $('#sucessArdID').css({display: 'none'});
                    $('#errorArdID').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                    console.error('ipArduinoSaveError: '+response);
                }
            });
        }
    },
    Menus: function() {
        $('#menu-left').on('click', 'li > a', function (e) {
            var menuContainer = jQuery('.page-sidebar ul');

            if($(this).next().hasClass('sub-menu')) {
                return;
            }
            menuContainer.children('li.active').removeClass('active');
            menuContainer.children('arrow.open').removeClass('open');

            $(this).parents('li').each(function () {
                $(this).addClass('active');
                $(this).children('a > span.arrow').addClass('open');
            });
            $(this).parents('li').addClass('active');
        })
        $('.page-sidebar').on('click', 'li > a', function (e) {
            if ($(this).next().hasClass('sub-menu') == false) {
                if ($('.btn-navbar').hasClass('collapsed') == false) {
                    $('.btn-navbar').click();
                }
                return;
            }

            var parent = $(this).parent().parent();

            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('.sub-menu').slideUp(200);
            parent.children('li.open').removeClass('open');

            var sub = jQuery(this).next();
            if (sub.is(":visible")) {
                jQuery('.arrow', jQuery(this)).removeClass("open");
                jQuery(this).parent().removeClass("open");
            } else {
                jQuery('.arrow', jQuery(this)).addClass("open");
                jQuery(this).parent().addClass("open");
                sub.slideToggle('open');
            }
            e.preventDefault();
        });
    },
    hideMsgAjaxLogin: function() {
       $('#closeMsgErrorAjax, .closeMsg').click(function() {
          $(this).parent().addClass('hide').slideUp(400);
           event.stopPropagation();
           event.preventDefault();
       });
    },
    isMobile: function() {
        return (window.Mobile);
    },
    openCamera: function() {
        if(Framework.isMobile()) {
            Mobile.openCamera();
        }
    },
    handleForm: function() {
        if (!jQuery().timepicker || !jQuery().bootstrapSwitch()) {
            return;
        }
        $('.action-toggle-button').bootstrapSwitch();
        $('.consumo-toggle-button').bootstrapSwitch();
        $('.presenca-toggle-button').bootstrapSwitch();
        if(Framework.isMobile()) {
            document.getElementById('timeExec').type = 'time';
            $('.add-on').remove();
        } else {
            $('.timepicker-24').timepicker({
                minuteStep: 1,
                showMeridian: false
            });
        }
        $('.consumo-toggle-button').on('switch-change', function (e, data) {
            if(data.value) {
                $('#consumoMedioValue').addClass('validate[required] text-input');
                $('#divConsumoMedio').fadeIn(800, function() {
                    $(this).removeClass('hide');
                });
            } else {
                $('#consumoMedioValue').removeClass('validate[required] text-input');
                $('#divConsumoMedio').fadeOut(800, function() {
                    $(this).addClass('hide');
                });
            }
        });
    },
    formWizard: function() {
        var form = $('#formWizard');
        form.validationEngine();
        if(form.validationEngine('validate')) {
            form = form.serializeObject();
            Framework.request('saveEvent', form, 'POST', false, function (response) {
                if(response == '1') {
                    if(!$('#sucessWizard').hasClass('hide')) {
                        $('#sucessWizard').css({display: 'none'});
                    }
                    $('#errorWizard').css({display: 'none'});
                    $('#successWizard').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                } else {
                    if(!$('#errorWizard').hasClass('hide')) {
                        $('#errorWizard').css({display: 'none'});
                    }
                    $('#successWizard').css({display: 'none'});
                    $('#errorWizard').fadeIn("slow", function() {
                            $(this).removeClass('hide');
                        }
                    );
                    console.error('saveEvent: '+response);
                }
            });
        }
    },
    removeEvent: function(idEvent) {
        Framework.request('removeEvent', idEvent, 'POST', false, function (response) {
            if(response == '1') {
                $('#idTimeLime'+idEvent).slideUp("slow", function() {
                        $(this).remove();
                    }
                );
            } else {
                console.error('removeEvent: '+response);
            }
        });
    }
};



$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

jQuery.fn.center = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
    return this;
}

var Charts = {
    chartTracking: function () {
        if($("#chartTracking").length) {
            var mes = [];
            mes.push([0, 0]);
            mes.push([30, 163]);
            mes.push([60, 308]);
            mes.push([90, 464]);
            mes.push([120,582]);
            mes.push([150,747]);
            mes.push([180,870]);
            mes.push([210,1040]);
            mes.push([240,1184]);
            mes.push([270,1337]);

            var plot = $.plot("#chartTracking",[
                { data: mes, label: "Consumo: 0.00 kWh", color: "#0077FF" }]
            , {
                series: {
                    lines: {
                        show: true
                    }
                },
                crosshair: {
                    mode: "x"
                },
                grid: {
                    hoverable: true,
                    autoHighlight: false
                },
                background: {
                    opacity: 0.8
                },
                xaxis: {
                  tickSize: 30
                },
                points: { show: true },
                lines: { show: true }
            });

            var legends = $("#chartTracking .legendLabel");

            legends.each(function () {
                $(this).css('width', $(this).width());
            });

            var updateLegendTimeout = null;
            var latestPosition = null;

            function updateLegend() {

                updateLegendTimeout = null;

                var pos = latestPosition;

                var axes = plot.getAxes();
                if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
                    pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
                    return;
                }

                var i, j, dataset = plot.getData();
                for (i = 0; i < dataset.length; ++i) {

                    var series = dataset[i];

                    for (j = 0; j < series.data.length; ++j) {
                        if (series.data[j][0] > pos.x) {
                            break;
                        }
                    }

                    var y,
                        p1 = series.data[j - 1],
                        p2 = series.data[j];

                    if (p1 == null) {
                        y = p2[1];
                    } else if (p2 == null) {
                        y = p1[1];
                    } else {
                        y = p1[1] + (p2[1] - p1[1]) * (Math.ceil(pos.x) - p1[0]) / (p2[0] - p1[0]);
                    }

                    legends.eq(i).text(series.label.replace(/:.*/, ": " + y.toFixed(2)) + " kWh");
                }
            }

            $("#chartTracking").bind("plothover",  function (event, pos, item) {
                latestPosition = pos;
                if (!updateLegendTimeout) {
                    updateLegendTimeout = setTimeout(updateLegend, 50);
                }
            });
        }
    },
    chartBars : function() {
        if($('#chartBars').length) {
            var formatAxis = function(x) {
                return x.toFixed(2) + '%';
            }
            var data = [ ["Jan", 4.0], ["Fev", 5.00], ["Mar", 5.70], ["Abr", 2.11], ["Mai", 1.00], ["Jun", 4.24], ["Jul", 5.75], ["Ago", 6.14]];
            $.plot("#chartBars", [ data ], {
                series: {
                    bars: {
                        show: true,
                        barWidth: 0.6,
                        align: "center"
                    }
                },
                xaxis: {
                    mode: "categories",
                    tickLength: 0
                },
                yaxis: {
                    tickSize: 0.7,
                    tickDecimals: 2,
                    tickFormatter: formatAxis
                }
            });
        }
    },
    chartTemperature: function() {
        if($('.chartContainerTemperature').length) {
            $('.chartContainerTemperature').each(function(index, name) {
                $('#' + name.id).highcharts({

                    chart: {
                        type: 'gauge',
                        backgroundColor: 'transparent',
                        plotBorderWidth: 0
                    },
                    credits: {
                        enabled: 0
                    },

                    title: {
                        text: ''
                    },
                    pane: {
                        startAngle: -150,
                        endAngle: 150,
                        background: [{
                            backgroundColor: {
                                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                                stops: [
                                    [0, '#FFF'],
                                    [1, '#333']
                                ]
                            },
                            borderWidth: 0,
                            outerRadius: '109%'
                        }, {
                            backgroundColor: {
                                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                                stops: [
                                    [0, '#333'],
                                    [1, '#FFF']
                                ]
                            },
                            borderWidth: 1,
                            outerRadius: '107%'
                        }, {
                            // default background
                        }, {
                            backgroundColor: '#DDD',
                            borderWidth: 0,
                            outerRadius: '105%',
                            innerRadius: '103%'
                        }]
                    },

                    // the value axis
                    yAxis: {
                        min: 0,
                        max: 50,

                        minorTickInterval: 'auto',
                        minorTickWidth: 1,
                        minorTickLength: 10,
                        minorTickPosition: 'inside',
                        minorTickColor: '#666',

                        tickPixelInterval: 30,
                        tickWidth: 2,
                        tickPosition: 'inside',
                        tickLength: 8,
                        tickColor: '#666',
                        labels: {
                            step: 2,
                            rotation: 'auto'
                        },
                        title: {
                            text: 'Temperatura/C°',
                            y: 30
                        },
                        plotBands: [{
                            from: 0,
                            to: 17,
                            color: '#F0F8FF'
                        }, {
                            from: 17,
                            to: 28,
                            color: '#DDDF0D'
                        }, {
                            from: 28,
                            to: 35,
                            color: '#FF7F50'
                        } , {
                            from: 35,
                            to: 50,
                            color: '#FF0000'
                        }]
                    },

                    series: [{
                        name: 'Temperatura',
                        data: [0]
                    }],
                    tooltip: {
                        pointFormat: "Temperatura {point.y:,.1f} °C"
                    }
                });
            });
            Framework.requestTemperature('RTAll', true);
        }
    },
    reinitialize: function() {
        this.chartTracking();
        this.chartBars();
        this.chartTemperature();
    }
};

jQuery(document).ready(function () {
    Framework.init();
    Framework.Menus();
    Framework.hideMsgAjaxLogin();
    Framework.loadWindow('pageSobre');
});

$(window).setBreakpoints({
    breakpoints: [320, 480, 768, 900, 1024, 1280]
});

$(window).bind('exitBreakpoint320', function () {
    Framework.init();
});
$(window).bind('enterBreakpoint320', function () {
    Framework.init();
});

$(window).bind('exitBreakpoint480', function () {
    Framework.init();
});
$(window).bind('enterBreakpoint480', function () {
    Framework.init();
});

$(window).bind('exitBreakpoint768', function () {
    Framework.init();
});
$(window).bind('enterBreakpoint768', function () {
    Framework.init();
});

$(window).bind('exitBreakpoint900', function () {
    Framework.init();
});
$(window).bind('enterBreakpoint900', function () {
    Framework.init();
});

$(window).bind('exitBreakpoint1024', function () {
    Framework.init();
});
$(window).bind('enterBreakpoint1024', function () {
    Framework.init();
});

$(window).bind('exitBreakpoint1280', function () {
    Framework.init();
});
$(window).bind('enterBreakpoint1280', function () {
    Framework.init();
});

