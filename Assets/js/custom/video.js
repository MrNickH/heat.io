$(function(){
    initComments();
    initHearts();
});

function initComments(){
    if(!$("videos comments commentlist").length){
        return;
    }

    $.get('/videos/comments/'+getUrlComponent(3)).done(function(data){
        $("commentlist").empty();
        var parsedComments = JSON.parse(data);
        if(typeof parsedComments == "object" && parsedComments.length){
            $("commentcounter").text(parsedComments.length);
            parsedComments.forEach(function(comment){
                addComment(comment);
            });
        }

    });
}

function initHearts(){
    if(!$("videos heartcounter").length){
        return;
    }

    $.get('/videos/hearted/'+getUrlComponent(3)).done(function(data){
        data = JSON.parse(data);
        if(data == 'true'){
            $("heartbutton a i").removeClass("fa-heart");
            $("heartbutton a i").addClass("fa-heartbeat");
        }else{
            $("heartbutton a i").removeClass("fa-heartbeat");
            $("heartbutton a i").addClass("fa-heart");
        }
    });

    $.get('/videos/hearts/'+getUrlComponent(3)).done(function(data){

        data = JSON.parse(data);
        if(!isNaN(parseInt(data))){
            $("heartcounter").text(data);
        }
    });
}

function useHeartButton(){
    $.get('/videos/heart/'+getUrlComponent(3)).done(function(data){
        initHearts();
    });
}

function postComment(){
    $.post(
        '/videos/postcomment/'+getUrlComponent(3),
        {
            'commenttext':editor.getEditorValue()
        }
    ).done(
        function(data){
            if(data != "false"){
                editor.jodit.setNativeEditorValue("");
                initComments();
            }
        }
    );




}

function addComment(comment){

    var img = $("<img/>").prop('src', comment.avatar);
    var author = $("<author></author>").text(comment.username);
    var date = $("<date></date>").text(comment.timewindow+ " ago.");
    var text = $("<text></text>").html(comment.comment_text);
    var rightblock = $("<rightblock></rightblock>");
    var newComment = $("<comment></comment>");

    author.append(date);
    rightblock.append(author);
    rightblock.append(text);

    newComment.append(img);
    newComment.append(rightblock);

    $("commentlist").append(newComment);
}