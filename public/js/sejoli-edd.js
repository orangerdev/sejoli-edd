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
                success: function(response) {

                    sejoli.unblock('#sejoli-edd-table-holder');
                    $('#edd-purchase-detail .content').html(response.content);
                    $('#edd-purchase-detail').modal('show');
                }
            })

            return false;
        });
    });

})(jQuery);
