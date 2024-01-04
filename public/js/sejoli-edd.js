(function($){

    'use strict';

    $(document).ready(function(){
        $('body').on('click', '.edd-view-purchase', function(){

            let payment_key = $(this).data('payment-key');

            $.ajax({
                url:    sejoli_edd.detail_url,
                data: {
                    key:    payment_key
                },
                dataType: 'json',
                beforeSend:     function() {
                    sejoli.block('#sejoli-edd-table-holder');
                    $('#edd-purchase-detail .content').html('');
                },
                complete: function(jqXHR, textStatus) {

                    if( textStatus === "success" ) {

                        return true;

                    } else {

                        sejoli.unblock('#sejoli-edd-table-holder');

                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {

                    var alert = {};

                    if( textStatus === "parsererror" || textStatus === "timeout" || textStatus === "error" || textStatus === "abort" ) {

                        var errMessage = ['Uncaught Error: \n' + jqXHR.responseText];

                        alert.type = 'error';

                        alert.messages = errMessage;

                        var template   = $.templates("#alert-template");
                        var htmlOutput = template.render(alert);
                        $(".edd-alert-holder").html(htmlOutput);

                    } else {

                        return true;

                    }

                },
                success: function(response, textStatus, errorThrown) {
                    sejoli.unblock('#sejoli-edd-table-holder');
                    $('#edd-purchase-detail .content').html(response.content);
                    $('#edd-purchase-detail').modal('show');
                }
            })

            return false;
        });
    });

})(jQuery);
