jQuery.support.placeholder = (function(){
    var i = document.createElement('input');
    return 'placeholder' in i;
})();

$(document).ready(function() {

    // Does it support placeholders?
    if ( $.support.placeholder ) {
        // Let the users know what to enter.
        $('.input-block > label').hide();
    }

    // Global Variables
    feedback = $('#feedback');

    // Hide the JS message
    $('.no-js').hide();

    $('#singleFile').submit(function() {
        window.location.replace("#getFile");

        var url = $('input[name=url]').val(),
            load = $('.loader');

        feedback.fadeOut();
        load.fadeIn();

        if ( (!validateURL(url))) {
            feedback.removeClass().addClass('alert alert-error').text('Please enter your valid link!').slideToggle();
            load.fadeOut();
            return false;
        }

        var buildIn = buildInHost(url);

        if (buildIn === false)
        {
            // Post the data and validate it.
            $.ajax({
                type: 'post',
                url: SITE_URL + 'generator/get',
                dataType: 'json',
                data: {
                    url: url

                }, success: function(data) {
                    load.fadeOut();
                    feedback.removeClass().addClass( (data.error == 0) ? 'alert alert-success' : 'alert alert-error' );
                    if (data.error == 0) {
                        feedback.html('<a href="'+data.url+'" target="_blank">'+data.url+'</a> | <a href="'+SITE_URL+'download/?key='+data.key+'&filename='+data.filename+'" title="'+data.filename+' | '+formatFileSize(data.filesize)+'"><strong>DOWNLOAD</strong></a> | '+data.filename+' | '+formatFileSize(data.filesize)).fadeIn();
                    } else {
                        feedback.html(data.message).fadeIn();
                    }
                }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                    feedback.removeClass().addClass('alert alert-error').text('Something went wrong. Please check the console for more information.').fadeIn();
                    load.fadeOut();
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                    // console.log(XMLHttpRequest.responseText);
                }
            });
        }

        return false;
    });
});

function buildInHost(url) {

    var site = new Array('tailieu.vn'),
        link = parseUri(url)["host"];

    if ($.inArray(link, site) == 0) {
        $.blockUI({message: "Your link is processing", css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });

        /* Grab tailieu.vn captcha */
        var request = $.ajax({
            type: 'GET',
            url: SITE_URL + 'generator/captchaTL',
            dataType: 'json'
        });
        request.done(function(msg) {
            $.blockUI({message: '<img src="'+msg.imgUrl+'"/><input type="hidden" name="captchaId" value="'+msg.sessId+'"/><input type="text" name="captchaIn" placeholder="Nhập captcha để tải"/><input type="submit" name="captchaTL" value="Gửi" />'});
            $('input[name=captchaTL]').click(function() {
                if ($('input[name=captchaId]').val() && $('input[name=captchaIn]').val()) {
                    $.unblockUI({
                        onUnblock: function(){
                            // Post the data and validate it.
                            $.ajax({
                                type: 'post',
                                url: SITE_URL + 'generator/get',
                                dataType: 'json',
                                data: {
                                    url: url,
                                    captchaId: msg.sessId,
                                    captchaIn: $('input[name=captchaIn]').val()

                                }, success: function(data) {
                                    $('.loader').fadeOut();
                                    feedback.removeClass().addClass( (data.error == 0) ? 'alert alert-success' : 'alert alert-error' );
                                    if (data.error == 0) {
                                        feedback.html('<a href="'+data.url+'" target="_blank">'+data.url+'</a> | <a href="'+SITE_URL+'download/?key='+data.key+'&filename='+data.filename+'" title="'+data.filename+' | '+formatFileSize(data.filesize)+'"><strong>DOWNLOAD</strong></a> | '+data.filename+' | '+formatFileSize(data.filesize)).fadeIn();
                                    } else {
                                        feedback.html(data.message).fadeIn();
                                    }
                                }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    feedback.removeClass().addClass('alert alert-error').text('Something went wrong. Please check the console for more information.').fadeIn();
                                    $('.loader').fadeOut();
                                    console.log(XMLHttpRequest);
                                    console.log(textStatus);
                                    console.log(errorThrown);
                                    // console.log(XMLHttpRequest.responseText);
                                }
                            });
                        }
                    });
                }
                return false;
            });
        });
        return true;
    } else {
        return false;
    }
}

function getRandomInt (min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function validateURL(textval) {
    var urlregex = new RegExp(
        "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
    return urlregex.test(textval);
}

function formatFileSize(s) {

    if(s >= 1073741824)
    {
        s = (s / 1073741824).toFixed(2) + " GB";
    }
    else if(s >= 1048576)
    {
        s  = (s / 1048576).toFixed(2) + " MB";
    }
    else if(s >= 1024)
    {
        s = (s / 1024).toFixed(2) + " KB";
    }
    else if(s >= 1)
    {
        s = s + " bytes";
    }
    else
    {
        s = "-";
    }

    return s;
}