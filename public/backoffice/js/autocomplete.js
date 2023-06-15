$(document).ready(function() {
    /*
    $(".get_user").autocomplete({
        source: function( request, response ) {
            $.ajax({
                url : DASHBOARD_URL + '/getidnameemailuser',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        minLength: 1,
        autoFocus: true,
        select: function (event, ui) {
        }
    });
     */

    /*=====Airports Autocomplete=====*/
    var check_ui_autocomplete = $(".fromflight").length;
    var value_from = '';
    var go_from = 0;
    if (check_ui_autocomplete > 0) {
        $(".fromflight").blur(function () {
            go_from = 1;
        }).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url : DASHBOARD_URL + '/getsuggestions',
                    type: 'POST',
                    dataType: "json",
                     data: {
                         keyword: request.term
                     },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            minLength: 2,//search after two characters
            autoFocus: true, // first item will automatically be focused,
            select: function (event, ui) {
                value_from = ui.item.value;
            },
            focus: function (event, ui) {
                value_from = ui.item.value;
                $('.ui-state-focus').addClass('ui-state-active');

            },
            close: function () {
                if (go_from == 1) {
                    $('#from').val(value_from);
                    go_from = 0;
                    $(".fromflight").trigger('blur');
                    $('.from-insert').removeClass('hidden');
                }
            }
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            var class_defaut = '';
            var class_parent = '';
            switch (item.format) {
                case "parent":
                    class_defaut = 'suggestion-box__menu icon ic-location is-all is--all';
                    class_parent = 'one';
                    break;
                case "sub":
                    class_defaut = 'suggestion-box__menu icon ic-arw-bend childSuggest';
                    class_parent = 'two';
                    break;
                default:
                    class_defaut = 'suggestion-box__menu icon ic-flight';
                    class_parent = 'zero';
                    break;
            }
            return $("<li>")
                .append("<div class='" + class_parent + "'>" + "<span>" + item.value + "</span>"
                    + "<i class='" + class_defaut + "'>" + "</i>"
                    + "</div>")
                .appendTo(ul);
        };
        var value_to = '';
        var go_to = 0;
        $(".departflight").blur(function () {
            go_to = 1;
        }).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url : DASHBOARD_URL + '/getsuggestions',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        keyword: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            minLength: 2,//search after two characters
            autoFocus: true, // first item will automatically be focused
            select: function (event, ui) {
                value_to = ui.item.value;
            },
            focus: function (event, ui) {
                value_to = ui.item.value;
                $('.ui-state-focus').addClass('ui-state-active');
            },
            close: function () {
                if (go_to == 1) {
                    $('#to').val(value_to);
                    go_to = 0;
                    $(".departflight").trigger('blur');
                    $('.to-insert').removeClass('hidden');
                }
            }
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            var class_defaut = '';
            var class_parent = '';
            switch (item.format) {
                case "parent":
                    class_defaut = 'suggestion-box__menu icon ic-location is-all is--all';
                    class_parent = 'one';
                    break;
                case "sub":
                    class_defaut = 'suggestion-box__menu icon ic-arw-bend childSuggest';
                    class_parent = 'two';
                    break;
                default:
                    class_defaut = 'suggestion-box__menu icon ic-flight';
                    class_parent = 'zero';
                    break;
            }
            return $("<li>")
                .append("<div class='" + class_parent + "'>" + "<span>" + item.value + "</span>"
                    + "<i class='" + class_defaut + "'>" + "</i>"
                    + "</div>")
                .appendTo(ul);
        };
    }
    $('.fromflight').keyup(function () {
        var value = $(this).val();
        if ( value.length > 0) {
            $('.from-insert').removeClass('hidden');
        }else
        {
            $('.from-insert').addClass('hidden');
        }
    });

    $('.departflight').keyup(function () {
        var value = $(this).val();
        if ( value.length > 0) {
            $('.to-insert').removeClass('hidden');
        }else
        {
            $('.to-insert').addClass('hidden');
        }
    });

    $('.from-insert').click(function (e) {
        $('.fromflight').val('');
        $(this).addClass('hidden');
        $("#from-error").hide();
        e.preventDefault();
    });

    $('.to-insert').click(function (e) {
        $('.departflight').val('');
        $(this).addClass('hidden');
        $("#from-error").hide();
        e.preventDefault();
    });
    $("#from").focusin(function () {
        var _default = "default";
        $.ajax({
            url : DASHBOARD_URL + '/getsuggestions',
            type: 'POST',
            dataType: "json",
            data: {
                keyword: _default
            },
            success: function( data ) {
            }
        });
    });
    $("#to").focusin(function () {
        var _default = "default";
        $.ajax({
            url : DASHBOARD_URL + '/getsuggestions',
            type: 'POST',
            dataType: "json",
            data: {
                keyword: _default
            },
            success: function( data ) {
            }
        });
    });
    $("#button_search").click(function () {
        $("#flight").submit();
    });
    $(function() {
        var giatri_from = $('#from').val();
        var giatri_to   = $('#to').val();
        if(giatri_from!='')
        {
            $('.from-insert').removeClass('hidden');
        }
        if(giatri_to!='')
        {
            $('.to-insert').removeClass('hidden');
        }
    });

});