(function( $ ) {

    $(document).on('ready', function() {

        $('form.ajax-actions').on('submit', function(e) {

            e.preventDefault();

            var submitted_form = $(this);

            submitted_form.find('button').attr('disabled', true);

            var data = {
                'action': 'delete_entry',
                'form': $(this).serialize()
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: data
            })
                .done(function (msg) {

                    var JSON = jQuery.parseJSON(msg);

                    if ( JSON.message === 'row_deleted' ) {

                        $('#entry-table').find('tr[data-row-number=' + JSON.entry_id + ']').fadeOut(300, function() {
                            $(this).remove();
                        });

                    } else {

                        submitted_form.find('button').attr('disabled', false);
                    }


                })
                .fail(function (msg) {
                    console.log('failed')
                })

        })

    })

})( jQuery );