$(document).ready(function() {
    var API_SUGGEST = WEB_URL+'suggest';
    $(".searchsuggest").autocomplete({
        source : function( request, response ) {
        $.ajax({
            url : API_SUGGEST ,
            type: "GET",
            cache: false,
            dataType: "json",
            data: {'searchword': request.term},
            success: function( data ) {
                response( data );
            }
        });
    }, minLength: 1
    });
});
function checkform() {
    var txtSearch = $('#txtSearch').val().trim();
    if(txtSearch.length < 1) {
        $('#txtSearch.searchsuggest').focus();
        $("#keywordMsg").text("Please enter your keyword !");
        return false;
    } else {
        $('#frmSearch').submit();
    }
}