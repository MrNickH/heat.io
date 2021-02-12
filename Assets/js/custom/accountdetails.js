function updateProfilePic(response, imageselector) {
    var oldImage = $(imageselector).find('img').prop('src');
    $(".pp").html("<img src='" + oldImage + "' />");
}

function initProfilePicPicker() {
    $(".avatar-images li").click(function () {
        callServer('/account/setavatar/' + $(this).data('id'), {}, updateProfilePic, $(this));
    });
}

