$( document ).ready(function() {

    $('.app-flashes').each(function(index) {

        function rec_fade_out(items, idx, len, timeout) {
            if (idx < len) {
                var item = $(items[idx]);

                item.slideDown();

                item.click(function() {
                    item.fadeOut();
                    rec_fade_out(items, idx + 1, len, timeout);
                });

                setTimeout(function() {
                    if (item.is(':visible')) {
                        item.fadeOut();
                        rec_fade_out(items, idx + 1, len, timeout);
                    }
                }, timeout);
            }
        }

        var timeout = (index + 1) * 7000;
        var items = $('.app-flashes');
        rec_fade_out(items, 0, items.length, timeout);
    });

    // $('.app-flashes').slideDown();
    // setTimeout(function(){ $('.app-flashes').fadeOut(); }, 7000);
    // $('.app-flashes').click(function(){ $('.app-flashes').fadeOut(); });

});
