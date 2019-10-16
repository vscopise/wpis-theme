/* global ajaxObj */

jQuery(document).ready(function($){
    var pageNumber = 2;
    var maxNumPages = parseInt(ajaxObj.MaxNumPages);
    var loadMore = '<div class="load-more"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>';
    var loading = false;
    $(window).scroll(function(){
        if ( ! loading ) {
            if ( $(window).scrollTop() + 200 >= $(document).height() - $(window).height()){
                if ( pageNumber > maxNumPages){
                    return false;
                } else {
                    loading = true;
                    loadPosts(pageNumber);
                }
                pageNumber++;
            }
        }
    });
    function loadPosts(pageNumber) {
        $('#content .row .col-sm-12.col-md-9').append(loadMore);
        $.ajax({
            url: ajaxObj.AjaxUrl,
            type:'POST',
            data: {
                'action': 'wpis_infinite_scroll',
                'pageNumber': pageNumber,
                'nonce': ajaxObj.nonce
            },
            success: function (response) {
                loading = false;
                $('.load-more').remove();
                $('#content .row .col-sm-12.col-md-9').append(response.result);
            }
        });
        return;
    }
});


