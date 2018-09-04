(function( $ ) {

    $(document).on('ready', function() {

        // handles multiple forms' validation on a single page
        $('.ocf-form').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        });


    })

})( jQuery );