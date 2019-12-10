function setRatingStars(item_part, score)
{
    for (var i = 1; i <= 5; ++i)
    {
        var item = $(item_part + i);
        item.removeClass();
        if (i <= score) {
            item.addClass("icon-star");
        }
        else {
            item.addClass("icon-star-empty");
        }
    }
}

// Voucher evaluation rating

$(".voucher-rating-stars").mouseover(function() {
    var score = this.getAttribute('data-value');
    setRatingStars("#voucher-rating-stars-", score);
});

$("#voucher-rating").mouseleave(function() {
    var score = $("#rating").val();
    setRatingStars("#voucher-rating-stars-", score);
})


$(".voucher-rating-stars").click(function() {
    var new_score = this.getAttribute("data-value");
    $("#rating").val(new_score);
});



// Environment work rating

$(".environment-rating-stars").mouseover(function() {
    var score = this.getAttribute('data-value');
    setRatingStars("#environment-rating-stars-", score);
});

$("#environment-work-rating").mouseleave(function() {
    var score = $("#environment_rating").val();
    setRatingStars("#environment-rating-stars-", score);
})


$(".environment-rating-stars").click(function() {
    var new_score = this.getAttribute("data-value");
    $("#environment_rating").val(new_score);
});
