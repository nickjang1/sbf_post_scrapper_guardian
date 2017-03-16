jQuery(document).ready(function($) {
    $('#sbf_post_scrapper_do_scrapping').click(function(e) {
        $button = $(this);
        $button.closest('.button-container').find('span').addClass('is-active');
        var data = {
            'action': 'do_scrapping',
        };
        $.post(ajaxurl, data, function() {

            })
            .done(function() {

            })
            .fail(function() {

            })
            .always(function() {
                $button.closest('.button-container').find('span').removeClass('is-active');
            });
    });
});
