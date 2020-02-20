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

$(".environmentGpu-rating-stars").mouseover(function() {
    var score = this.getAttribute('data-value');
    setRatingStars("#environmentGpu-rating-stars-", score);
});

$(".environmentCpu-rating-stars").mouseover(function() {
    var score = this.getAttribute('data-value');
    setRatingStars("#environmentCpu-rating-stars-", score);
});

$("#environment-work-rating").mouseleave(function() {
    var score = $("#environment_rating").val();
    setRatingStars("#environment-rating-stars-", score);
})

$("#gpu-work-rating").mouseleave(function() {
    var score = $("#gpu_rating").val();
    setRatingStars("#environmentCgu-rating-stars-", score);
})

$("#cpu-work-rating").mouseleave(function() {
    var score = $("#cpu_rating").val();
    setRatingStars("#environmentCpu-rating-stars-", score);
})


$(".environment-rating-stars").click(function() {
    var new_score = this.getAttribute("data-value");
    $("#environment_rating").val(new_score);
});

$(".environmentGpu-rating-stars").click(function() {
    var new_score = this.getAttribute("data-value");
    $("#gpu_rating").val(new_score);
});


$(".environmentCpu-rating-stars").click(function() {
    var new_score = this.getAttribute("data-value");
    $("#cpu_rating").val(new_score);
});
