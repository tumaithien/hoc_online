$(document).ready(function() {
   let typeaheadInit = function (ele) {
        let timeout;
        let locations = [];
        ele.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: "locations",
            source: function (query, processSync, asyncProcess) {
                if (timeout) {
                    clearTimeout(timeout);
                }
                timeout = setTimeout(function () {
                    return $.ajax({
                        url: DASHBOARD_URL + '/getidnameemailuser',
                        type: 'POST',
                        data: {term: query},
                        dataType: 'json',
                        success: function (response) {
                            locations = [];
                            jQuery.each(response, function (i, val) {
                                locations.push(val.value);
                            });
                            return asyncProcess(locations);
                        }
                    });
                }, 500);
            },
            limit: 19
        });
    };


    (function () {
        typeaheadInit($('#user'));
    })();


});