var $elementWithError = null;

$('[data-error]').each(function() {

    var error = $(this).attr('data-error');
    if(error.length < 1) return true;

    if($elementWithError === null) $elementWithError = $(this);

    // only first error
    error = error.split("\n")[0]

    var p = '<p class="alert alert-danger mt-1">' + error + '</p>';

    $(this).parent().append(p);

});

$('[data-checked]').each(function() {
    if($(this).attr('data-checked') == 1) {
        $(this).prop('checked', true)
    }
    else {
        $(this).prop('checked', false)
    }
})

$('[data-selected]').each(function() {
    var value = $(this).attr('data-selected')
    $(this).find('option').each(function() {
        if($(this).val() == value) $(this).prop('selected', true)
    })
})

if($elementWithError !== null) {
    $('html, body').animate({
        scrollTop: $elementWithError.offset().top - 200
    }, 500);
}