(function( $ ) {

    $(document).on('ready', function() {

        var $body = $('body');

        $body.on( 'click', '.ajax-submit:not(:disabled)', function(e) {

            e.preventDefault();

            var $this = $(this);
            var form = $this.parents('form.ajax-target');

            var form_valid = true;

            form.find('input.ajax-validation, textarea.ajax-validation').each(function () {

                if ( !$(this)[0].checkValidity() ) {
                    form_valid = false;
                    $(this)[0].setCustomValidity("not a valid input")
                }

                // add basic validation checks here as needed
                if ( $(this).attr('data-validation') ) {

                    var validation_type = $(this).attr('data-validation');

                    if ( validation_type === 'tel') {
                        // Make sure it is numbers only.
                        var regex=/^(?=.*\d)[\d ]+$/;
                        if ( !$(this).val().match(regex) ) {
                            $(this)[0].setCustomValidity("not a valid input");
                            form_valid = false;
                        }


                    }
                }

            });

            if ( !form_valid )
                return;


            $this.attr('disabled', true);

            var data = {
                'action': 'submit_contact_form',
                'form': form.serialize(),
                'method': 'ajax'
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.ajax({
                method: "POST",
                url: ajax_attributes.adminurl,
                data: data
            })
                .done(function (msg) {

                    var JSON = jQuery.parseJSON(msg);

                    if (JSON.saved) {
                        $this.addClass('submitted').val('Thanks for your query!');
                        form.find('textarea, input').attr('disabled', true);
                    } else {
                        form.find('textarea, input').attr('disabled', true);
                        $this
                            .addClass('failed-submission')
                            .val('Try again?')
                            .attr('disabled', false);
                    }


                })
                .fail(function (msg) {
                    console.log('failed')
                })


        });

    })

})( jQuery );