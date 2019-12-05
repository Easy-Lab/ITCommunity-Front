$('.auto-background-url').each(function() {
    $(this).css('background-image', 'url(' + $(this).attr('data-src') + ')')
})

function dataURItoBlob(dataURI) {
    var byteString = atob(dataURI.split(',')[1]);
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
    return new Blob([ab]);
}

function disableOtherInputImage(_input_image)
{
    input_image = (typeof _input_image === 'undefined' || _input_image == null) ? $("div.picture-holder button.cropit-image-input-button") : _input_image;
    if (input_image.length > 0)
    {
        var count = 0;
        input_image.each(function(idx) {

            if (count > 0)
            {
                $(this).attr("disabled", true);
            }
            else{
                $(this).attr("disabled", false);
            }

            if ($(this).is(":visible") && count == 0)
            {
                $(this).attr("disabled", false);
                ++count;
            }
        })
    }
}

if($('.image-editor').length) {
    $('.image-editor').each(function() {
        var $imageEditor = $(this)

        var props = {
            imageState: {
                src: $imageEditor.attr('data-src')
            },
            smallImage: 'stretch',
            onImageLoaded: function() {
                resizeHandler();
            },
        }

        switch($imageEditor.attr('data-type')) {
            case 'profile_picture':
                break;
            case 'dealer_profile_picture':
                break;
            default:
                break;
        }

        $imageEditor.cropit(props);

        $imageEditor.find('.cropit-image-uploading').hide()
        $imageEditor.find('.cropit-image-removing').hide()
        $imageEditor.find('.cropit-image-upload').hide()
        $imageEditor.find('.cropit-image-update').hide()
        $imageEditor.find('.cropit-edit-bar').hide();

        if($imageEditor.attr('data-src').length) {
            $imageEditor.find('.cropit-image-input-button').hide()
        }
        else {
            $imageEditor.find('.cropit-editor').hide()
            $imageEditor.find('.cropit-image-remove').hide()
            $imageEditor.find('.cropit-image-update').hide()
        }

        $imageEditor.find('.cropit-image-input').change(function() {
            $imageEditor.find('.cropit-editor').show()
            if(!$imageEditor.attr('data-src').length) {
                $imageEditor.find('.cropit-image-upload').show()
                $imageEditor.find('.cropit-edit-bar').show();
            }
        })

        $imageEditor.find('.rotate-cw').click(function(e) {
            e.preventDefault()
            $imageEditor.cropit('rotateCW');
            return false;
        });

        $imageEditor.find('.rotate-ccw').click(function(e) {
            e.preventDefault()
            $imageEditor.cropit('rotateCCW');
            return false;
        });

        $imageEditor.find('.cropit-image-input-button').click(function(e) {
            e.preventDefault()
            $imageEditor.find('.cropit-image-input').click()
            return false;
        })

        $imageEditor.find('.cropit-image-update, .cropit-image-upload').click(function(e) {
            e.preventDefault()

            var endpoint = $imageEditor.attr('data-endpoint');
            var id = $imageEditor.attr('data-id');
            var type = $imageEditor.attr('data-type');
            var user = $imageEditor.attr('data-user');
            var name = $imageEditor.attr('data-name');
            var imageData = $imageEditor.cropit('export');
            // console.log(endpoint)
            // console.log(type)
            // console.log(name)
            // console.log(user)



            if(typeof imageData === 'undefined') return false;
            // console.log('OK');
            $imageEditor.find('.cropit-image-upload').hide()
            $imageEditor.find('.cropit-image-update').hide()
            $imageEditor.find('.cropit-image-remove').hide()
            $imageEditor.find('.cropit-image-uploading').show()
            $imageEditor.find('.cropit-control').hide()
            $imageEditor.find('.cropit-edit-bar').hide()
            var img = $imageEditor.find('img')

            var fileReader = new FileReader();

            fileReader.onload = function (f) {
                $.ajax({
                    method: 'post',
                    type: 'POST',
                    url: endpoint,
                    data: {
                        'picture': f.target.result,
                        'id': id,
                        'type': type,
                        'user':user,
                        'height': img.height(),
                        'width':img.width()

                    },
                    success: function (data) {
                        $imageEditor.attr('data-id', data.id)
                        $imageEditor.find('.cropit-image-uploading').hide()
                        $imageEditor.find('.cropit-image-upload').hide()
                        //$imageEditor.find('.cropit-image-update').show()
                        $imageEditor.find('.cropit-edit-bar').hide();
                        $imageEditor.find('.cropit-image-remove').show()
                        $imageEditor.find('.cropit-control').show()
                        $imageEditor.find('.cropit-image-input').val(null)
                        $imageEditor.find('.cropit-image-input-button').hide()
                        // console.log('euuuh');
                        // console.log(name);
                        // console.log(data);
                        if ($('#share_fb')){
                            $('meta[property=og\\:image]').attr('content', data['path']);
                        }

                        disableOtherInputImage();
                    }
                });
            }

            fileReader.readAsDataURL(dataURItoBlob(imageData))
            return false;
        });

        $imageEditor.find('.cropit-image-remove').click(function(e) {
            e.preventDefault()

            $imageEditor.find('.cropit-image-removing').show()
            $imageEditor.find('.cropit-image-upload').hide()
            $imageEditor.find('.cropit-image-remove').hide()
            $imageEditor.find('.cropit-image-update').hide()
            $imageEditor.find('.cropit-control').hide()

            var endpoint = $imageEditor.attr('data-endpoint');
            var id = $imageEditor.attr('data-id');
            var type = $imageEditor.attr('data-type');
            var user = $imageEditor.attr('data-user');

            $.ajax({
                type: 'POST',
                url: endpoint,
                data: {
                    'id': id,
                    'type': type,
                    'user':user,
                    'action': 'delete'
                },
                success: function (data) {
                    $imageEditor.find('.cropit-preview-image').attr('src', '')
                    $imageEditor.attr('data-id', '')
                    $imageEditor.attr('data-src', '')
                    $imageEditor.find('.cropit-image-removing').hide()
                    $imageEditor.find('.cropit-image-input-button').show()
                    $imageEditor.find('.cropit-control').show()
                    $imageEditor.find('.cropit-editor').hide()
                    $imageEditor.find('.cropit-image-input').hide()

                    disableOtherInputImage();

                    if($imageEditor.closest('.images-orderable').length) {
                        $imageEditor.appendTo($imageEditor.closest('.images-orderable'))
                    }
                }
            })
            return false;
        })
    })

    resizeHandler();
    window.addEventListener('resize', function() {
        waitForFinalEvent(resizeHandler, timeToWaitForLast, 'mainresize');
    });
}

$('.slick-slider').each(function() {
    var gallery = $(this);

    gallery.slick({
        slidesToScroll: 1,
        rows: 0,
        prevArrow: '<button class="slick-prev"><i class="icon-left-open-big"></i></button>',
        nextArrow: '<button class="slick-next"><i class="icon-right-open-big"></i></button>',
        dots: true,
        adaptiveHeight: true,
        dotsClass: 'slick-dots'
    });

    var dots = gallery.find('.slick-dots li');

    gallery.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        setTimeout(function() {
            dots.eq(nextSlide).prevAll().addClass('slick-active');
        }, 10);
    });
});

$('.slick-slider-center').slick({
    centerMode: true,
    centerPadding: '300px',
    slidesToShow: 1,
    prevArrow: '<button class="slick-prev"><i class="icon-left-open-big"></i></button>',
    nextArrow: '<button class="slick-next"><i class="icon-right-open-big"></i></button>',
    mobileFirst: true,
    responsive: [{
        breakpoint: 319,
        settings: {
            centerMode: true,
            centerPadding: '50px',
            slidesToShow: 1
        }
    }, {
        breakpoint: 767,
        settings: {
            centerMode: true,
            centerPadding: '150px',
            slidesToShow: 1
        }
    }, {
        breakpoint: 992,
        settings: {
            centerMode: true,
            centerPadding: '300px',
            slidesToShow: 1
        }
    }]
});

disableOtherInputImage();

//

/**
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
 */
var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) { uniqueId = "Don't call this twice without a uniqueId"; }
        if (timers[uniqueId]) { clearTimeout (timers[uniqueId]); }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();


var timeToWaitForLast = 100, // How often to run the resize event during resize (ms)
    $imageCropper; // Set up a global object to hold image cropper container

/**
 * Runs on window resize
 */
function resizeHandler()
{
    /**
     * Adjust the size of the preview area to be 100% of the image cropper container
     */

    $('.image-editor').each(function() {
        var $imageEditor = $(this)
        var img = $imageEditor.find('img');

        if(['profile_picture', 'dealer_profile_picture'].indexOf($imageEditor.attr('data-type')) !== -1) return;

        let finalWidth  = 720, // The desired width for final image output
            finalHeight = 480, // The desired height for final image output
            newWidth = 0,
            newHeight = 0,
            sizeRatio   = finalHeight / finalWidth;
            if (img.height()>= img.width()){
                newHeight   = $imageEditor.height();
                newWidth    = newHeight * sizeRatio;
            }
            else{
                newWidth    = $imageEditor.width();
                newHeight   = newWidth * sizeRatio;
            }
            newZoom     = finalWidth / newWidth;
        $imageEditor.cropit('previewSize', { width: newWidth, height: newHeight });
        $imageEditor.cropit('exportZoom', newZoom);
    });
}
