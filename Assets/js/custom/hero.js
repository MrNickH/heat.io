$(function(){
   initHero();
});

var moving = false;

function initHero(){

    $("hero control i.fa-angle-left").click(function(){

        if(moving){
            return;
        }
        clicked = true;
        hideButtons();
        moveLeft();

    });

    $("hero control i.fa-angle-right").click(function(){

        if(moving){
            return;
        }

        clicked = true;
        hideButtons();
        moveRight();
    });

    $("hero").on('swipeleft', function(){
        if(moving){
            return;
        }
        hideButtons();
        clicked = true;

        moveRight();

    });

    $("hero").on('swiperight', function(){
        if(moving){
            return;
        }
        hideButtons();
        clicked = true;

        moveLeft();

    });


    var direction = "right";
    var clicked = false;


    setInterval(function(){

        var clickedLastTime = clicked;
        clicked = false;

        if ($('hero:hover').length != 0 || clickedLastTime) {
            return;
        }

        if(direction === "right"){
            if(hasRight()){
                direction = "left";
            }else{
                moveRight();
            }
        }else if(direction === "left"){
            if(hasLeft()){
                direction = "right";
            }else{
                moveLeft();
            }
        }
    }, 7000);
}

function moveLeft(){
    moving =true;

    hideButtons();

    if(hasLeft()){
        $("hero slide").animate({"left": "+=" + $("hero").width()}, 500, function(){
            moving = false;
            hasRight();
        });
    }


}

function moveRight(){
    moving = true;

    hideButtons();

    if(hasRight()){
        $("hero slide").animate({"left": "-=" + $("hero").width()} , 500, function(){
            moving = false;
            hasLeft();
        });
    }

}

function hasRight(){

    hideButtons();
    var valid = true;


    $("hero slide").each(function(index, item){
        var left = $(item).css('left');
        left = left.substr(0, left.length - 2);
        left = parseFloat(left);
        left = Math.round(left);


        if(left <= 0 && index === parseInt($("hero slide").length - 1)){
            valid = false;
        }

    });

    if(valid){
        $("i.fa-angle-right").fadeIn();
    }


    return valid;
}
function hasLeft(){

    hideButtons();

    var valid = true;

    $("hero slide").each(function(index, item){
        var left = $(item).css('left');
        left = Math.round(parseFloat(left.substr(0, left.length - 2)));

        if(left >= 0 && index == 0){
            valid = false;
        }

    });

    if(valid){
        $("i.fa-angle-left").fadeIn();
    }

    return valid;
}

function hideButtons(){
    $("i.fa-angle-left").fadeOut();
    $("i.fa-angle-right").fadeOut();
}