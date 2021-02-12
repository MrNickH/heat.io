$(function(){
    window['createTag'] = function (tagName){
        return $('<'+tagName+'></'+tagName+'>');
    };
});
