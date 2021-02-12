$(function () {
    if (!$("messaging").length) {
        return;
    }

    window['handleAjaxError'] = function () {
        open(location, '_self').close();
    }

    var emptyDefaults = {
        'conversation': {
            'icon': 'fa-comments',
            'text': 'Please select a thread, or search for a friend to start chatting..'
        },
        'threads': {
            'icon': 'fa-list-ul',
            'text': 'Please wait while we load your chats...'
        },
        'search': {
            'icon': 'fa-search',
            'text': 'Search for friends or messages...'
        },
    };
    var loadingTimer = 0;

    start();
    /*

    888888888888         db         88888888ba    ad88888ba
         88             d88b        88      "8b  d8"     "8b
         88            d8'`8b       88      ,8P  Y8,
         88           d8'  `8b      88aaaaaa8P'  `Y8aaaaa,
         88          d8YaaaaY8b     88""""""8b,    `"""""8b,
         88         d8""""""""8b    88      `8b          `8b
         88        d8'        `8b   88      a8P  Y8a     a8P
         88       d8'          `8b  88888888P"    "Y88888P"
    */


    function showSection(section, display, delay) {
        if (isUndefined(section)) {
            return;
        }

        if (!$(section).length) {
            return;
        }

        if (isUndefined(delay)) {
            delay = loadingTimer;
        }

        if (isUndefined(display)) {
            display = 'block';
        }


        if(!$(section).is(":visible")){
            $(section).css('display', display).show(loadingTimer);
        }
    }

    function hideSection(section, text) {
        if (isUndefined(section)) {
            return;
        }

        if (!$(section).length) {
            return;
        }

        $(section).fadeOut(loadingTimer);
    }

    function showEmptySection(section, text, icon, timeout) {
        if (isUndefined(section)) {
            return;
        }

        if (!$(section).length) {
            return;
        }

        if (isUndefined(text)) {
            text = getEmptyDefault(section, 'text');
        }

        if (isUndefined(icon)) {
            icon = getEmptyDefault(section, 'icon');
        }

        if (isUndefined(timeout)) {
            timeout = loadingTimer;
        }

        if (!$(section + ' empty .' + icon).length) {
            $(section + ' empty i').remove();
            $(section + ' empty').prepend($('<i class="fa ' + icon + '"></i>'));
        }

        $(section + ' empty span').text(text);
        if(!$(section + ' empty').is(":visible")){
            $(section + ' empty').show(loadingTimer);
        }
    }

    function hideEmptySection(section, text, icon) {
        if (isUndefined(section)) {
            return;
        }

        if (!$(section).length) {
            return;
        }

        $(section + ' empty').fadeOut(loadingTimer);
    }

    function slideLeft() {
        if ($(window).width() < 1024){
            $("left").hide('slide', {'direction': 'left', 'easing':'swing'}, 500);
            $("conversation").stop().show('slide', {'direction': 'right', 'easing':'swing'}, 500,function(){
                $(this).css('display', 'inline-block');
            });
        }
    }

    function slideRight() {
        if ($(window).width() < 1024){
            currentThread = -1;
            $("conversation").hide('slide', {'direction': 'right', 'easing':'swing'}, 500);
            $("left").stop().show('slide', {'direction': 'left', 'easing':'swing'}, 500);
        }
    }

    function getEmptyDefault(section, type) {
        var defaultVal = emptyDefaults[section][type];
        var def = isUndefined(defaultVal);
        return def ? '' : defaultVal;
    }

    function isUndefined(val) {
        return (typeof val == "undefined")
    }

    /*===================START===============*/

function start() {
        hideEmptySection('threads');
        loadThreads(0);
    }
    /*
         888888888888  88        88  88888888ba   88888888888         db         88888888ba,     ad88888ba
              88       88        88  88      "8b  88                 d88b        88      `"8b   d8"     "8b
              88       88        88  88      ,8P  88                d8'`8b       88        `8b  Y8,
              88       88aaaaaaaa88  88aaaaaa8P'  88aaaaa          d8'  `8b      88         88  `Y8aaaaa,
              88       88""""""""88  88""""88'    88"""""         d8YaaaaY8b     88         88    `"""""8b,
              88       88        88  88    `8b    88             d8""""""""8b    88         8P          `8b
              88       88        88  88     `8b   88            d8'        `8b   88      .a8P   Y8a     a8P
              88       88        88  88      `8b  88888888888  d8'          `8b  88888888Y"'     "Y88888P"
     */

    var threads = {};
    var currentThread;


    function loadThreads(time) {
        setTimeout(function () {
            callServer('/account/messages/threads', '', populateThreads);
        }, time);
    }

    function populateThreads(threadData) {
        if (isUndefined(threadData)) {
            showEmptySection(
                'threads',
                'An error occured When getting your conversations. Trying again in 10 seconds....',
                'fa-exclamation-triangle');

            loadThreads(10000);
            return;
        }

        if (!threadData.length) {
            showEmptySection(
                'threads',
                'You have not chatted with anyone yet, why not start a new conversation?'
            );
            loadThreads(10000);
            return;
        }

        processThreads(threadData);
        showSection('threadlist', 'flex');

        loadThreads(10000);
    }

    /* ======== PROCESS THREADS ========= */

    function processThreads(threads) {
        threads.forEach(function (thread) {
            if (!threadExists(thread.td)) {
                addThread(thread);
            } else {
                updateThread(thread);
            }
            setThreadOrder(thread);
        });
    }

    function buildThreadObject(threadData) {
        return {
            id: threadData.td,
            avatar: threadData.mru.avatar,
            name: threadData.mru.username,
            message: threadData.mrm,
            time: threadData.mrt,
            pid: threadData.pid,
            friendlytime: threadData.mrtf,
            seen: threadData.mrs,
            messageparticipant: threadData.mrmi
        }
    }

    function buildThreadTag(thread) {
        var e = {
            avatarTag: createTag('img'),
            nameTag: createTag('mr-user'),
            messageTag: createTag('mr-mess'),
            timeTag: createTag('mr-time').append((thread.meta.messageparticipant == thread.meta.pid) ? createTag('i').addClass('fa').addClass('fa-check-double') : ''),
            rightTag: createTag('right'),
            threadTag: createTag('thread').attr('id', 'thread-id-' + thread.meta.id),
        };

        return e.threadTag.append(e.avatarTag).append(e.rightTag.append(e.nameTag).append(e.messageTag).append(e.timeTag))
    }

    /* ======== THREAD CRUD ========= */

    function addThread(thread) {

        var thread = {'meta': buildThreadObject(thread), 'messages': []};
        var threadTag = buildThreadTag(thread);

        $("threadlist").append(threadTag);

        updateThreadLineContent(thread.meta, threadTag)

        threads[thread.meta.id] = thread;

        $(threadTag).click(function () {
            $('#dummythread').slideUp(300);
            clickThread(thread);
        });
    }

    function updateThread(thread) {
        var threadMeta = buildThreadObject(thread);
        var threadLine = $('thread#thread-id-' + threadMeta.id);

        updateThreadLineContent(threadMeta, threadLine);

        threads[threadMeta.id]['meta'] = threadMeta;
    }

    function updateThreadLineContent(threadMeta, threadLine){
        if( threadLine.find("img").attr('src') != threadMeta.avatar) {
            threadLine.find("img").attr('src', threadMeta.avatar);
        }

        threadLine.find("mr-user").text(threadMeta.name);
        threadLine.find("mr-mess").text(
            ((threadMeta.messageparticipant == threadMeta.pid) ? 'You: ' : '') + threadMeta.message
        );

        threadLine.find("mr-time").text(
            threadMeta.friendlytime
        ).append((threadMeta.messageparticipant == threadMeta.pid) ? createTag('i').addClass('fa').addClass('fa-check-double') : '');

        updateSeenAndUnread(threadMeta, threadLine);
    }
    function updateSeenAndUnread(threadMeta, threadLine) {

        if (threadMeta.seen) {
            threadLine.removeClass('unread');
            threadLine.addClass('seen');
        } else {
            threadLine.removeClass('seen');
            if (threadMeta.messageparticipant != threadMeta.pid){
                threadLine.addClass('unread');
            } else {
                threadLine.removeClass('unread');
            }
        }
    }

    function updateThreadWithMessage(message, threadMeta) {
        var threadLine = $('thread#thread-id-' + message.thread_id);

        var text = message.message_text.substr(0, 25) + (message.message_text.length > 25 ? '...' : '');


        threadLine.find("mr-mess").text(
            ((threadMeta.messageparticipant == threadMeta.pid) ? 'You: ' : '') + text
        );

        threadLine.find("mr-time").text(
            message.friendlytime
        ).append((threadMeta.messageparticipant == threadMeta.pid) ? createTag('i').addClass('fa').addClass('fa-check-double') : '');

        threadLine.removeClass('unread');
    }

    function createThread() {
    }

    function deleteThread(threadId) {
        $('#thread-id-' + threadId).remove();
        threads[threadId] = undefined;
    }

    function setThreadOrder(thread) {
        $('#thread-id-' + thread.td).css('order', thread.mrt);
    }

    /* ======== EXISTING THREADS ========= */

    function clickThread(thread) {

        slideLeft();

        if (currentThread === thread.meta.id) {
            return;
        }

        atBottom = true;

        $("replybar input").val('');
        setUpMessageTopBar(thread);
        loadConversation(thread, 0);
    }

    /* ======== THREAD SANITY CHECK ========= */

    function threadExists(tid) {
        var tOE = threadObjectExists(tid);
        var tEE = threadElementExists(tid);
        return sanityCheckThread(tOE, tEE, tid);
    }

    function sanityCheckThread(tOE, tEE, tid) {
        if (!tOE && !tEE) {
            return false;
        }

        if (tOE ^ tEE) {
            deleteThread(tid);
            return false;
        }

        return true;
    }

    function threadObjectExists(tid) {
        return !isUndefined(threads[tid]);
    }

    function threadElementExists(tid) {
        return $('#thread-id-' + tid).length;
    }


    /*
     88b           d88  88888888888   ad88888ba    ad88888ba          db           ,ad8888ba,   88888888888   ad88888ba
     888b         d888  88           d8"     "8b  d8"     "8b        d88b         d8"'    `"8b  88           d8"     "8b
     88`8b       d8'88  88           Y8,          Y8,               d8'`8b       d8'            88           Y8,
     88 `8b     d8' 88  88aaaaa      `Y8aaaaa,    `Y8aaaaa,        d8'  `8b      88             88aaaaa      `Y8aaaaa,
     88  `8b   d8'  88  88"""""        `"""""8b,    `"""""8b,     d8YaaaaY8b     88      88888  88"""""        `"""""8b,
     88   `8b d8'   88  88                   `8b          `8b    d8""""""""8b    Y8,        88  88                   `8b
     88    `888'    88  88           Y8a     a8P  Y8a     a8P   d8'        `8b    Y8a.    .a88  88           Y8a     a8P
     88     `8'     88  88888888888   "Y88888P"    "Y88888P"   d8'          `8b    `"Y88888P"   88888888888   "Y88888P"
    */

    var sendingMessage = false;
    var atBottom = true;

    function setUpMessageTopBar(thread) {
        $("convotopbar img").attr('src', thread.meta.avatar);
        $("convotopbar currentpagetext").text(thread.meta.name);
    }

    $("convotopbar i.fa-arrow-left").click(function(){
        $('#dummythread').hide();
        slideRight();
    });

    function loadConversation(thread, time) {
        if (threads[thread.meta.id].messages.length) {
            if(thread.meta.id != currentThread){
                atBottom = true;
                clearMessages();
                clearErrors();
            }
            processMessages(threads[thread.meta.id].messages, threads[thread.meta.id]);
            showSection('conversation messages', 'flex', 0);
        }

        currentThread = thread.meta.id;

        setTimeout(function () {

            if(thread.meta.id != currentThread){
                return;
            }

            if (!threads[thread.meta.id].messages.length){
                hideSection('conversation messages');
                hideEmptySection('conversation');
                clearMessages();
                setUpMessageTopBar(thread);
                atBottom = true;
            }

            callServer('/account/messages/thread/'+thread.meta.id, '', populateConversation);
        }, time);
    }

    function populateConversation(conversationData) {

        if (isUndefined(conversationData)) {
            showEmptySection(
                'conversation',
                'An error occured When getting your conversation. Trying again in 10 seconds....',
                'fa-exclamation-triangle'
            );

            loadConversation(threads[conversationData.tid], 10000);
            return;
        }

        var messages = conversationData.messages;

        processMessages(messages, threads[conversationData.tid]);

        showSection('conversation messages', 'flex');

        loadConversation(threads[conversationData.tid], 5000);
    }

    function processMessages(messages, thread) {
        messages.forEach(function(message){
            message = messageObject(message);
            if(!messageElementExists(message)){
                $('messages').append(createMessageElement(message));
            } else {
                updateMessageElement(message);
            }

            setMessageOrder(message);
        });


        var keys = Object.keys(messages);
        var latestMessage = messages[keys[keys.length-1]];

        if(latestMessage) {
            updateThreadWithMessage(latestMessage, thread.meta);
        }

        if (atBottom) {
            updateScroll();
        }

    }

    function updateScroll(){
        var element = document.querySelector('messages');
        element.scrollTop = element.scrollHeight;
    }

    function checkScrollPosition(element){
        if ($(element).scrollTop() + $(element).innerHeight()>= $(element)[0].scrollHeight) {
            atBottom = true;
        } else {
            atBottom = false;
        }
    }

    $('messages').bind('scroll', function(){
        checkScrollPosition(this);
    });


    // ******* MESSAGE CRUD ********* //

    function messageObject(message) {
        return threads[message.thread_id]['messages'][message.message_id] = message;
    }

    function createMessageElement(message) {
        var tick = createTag('i').addClass('fa').addClass('fa-check-double');

        var mim = messageIsMine(message, threads[message.thread_id].meta.pid);

        return createTag('message')
            .attr('id', 'message-id-'+message.message_id)
            .append(createTag('messagetext').text(message.message_text))
            .append(
                createTag('messagetime')
                    .append(createTag('time').text(message.friendlytime))
                    .append(mim ? createTag('messageticks').append(tick) : '')
            )
            .addClass(mim ? 'you' : '')
            .addClass((mim && message.seen) ? 'seen' : '');
    }

    function updateMessageElement(message) {
        var messageElement = $('#message-id-' + message.message_id)
        var mim = messageIsMine(message, threads[message.thread_id].meta.pid);

        var text = messageElement.find('messagetext');

        if (text.text() !== message.message_text) {
            text.text(message.message_text);
        }

        var time = messageElement.find('messagetime time');

        if (time.text() !== message.friendlytime) {
            time.text(message.friendlytime);
        }

        if(mim && message.seen){
            if(!messageElement.hasClass('seen')){
                messageElement.addClass('seen');
            }
        } else {
            messageElement.removeClass('seen');
        }
    }

    $("replybar .fa-paper-plane").click(function () {
        sendMessage();
    });

    $(document).on('keypress',function(e) {
        if(e.which == 13) {
            sendMessage();
        }
    });


    function sendMessage() {
        if(sendingMessage || $('replybar input').val().length <= 0){
            return;
        }

        var params = {'message': $('replybar input').val()};
        var func = sentMessage;

        if(currentThread === -2){
            params.username = $("convotopbar currentpagetext").text();
            func = sentNewThreadMessage;
        }

        atBottom = true;
        sendingMessage = true;
        callServer('/account/messages/message/' + currentThread, params, func);
    }

    function sentNewThreadMessage(convoData) {

        sendingMessage = false;

        if (convoData.Error) {
            $('replybar error').text(convoData.Error).slideDown(300).delay(10000).slideUp(300);
            return;
        }

        console.log(convoData);

        callServer('/account/messages/threads', '', function(newThreadData){
            processThreads(newThreadData);
            $('#thread-id-' + convoData.tid).click();
        });

    }

    function sentMessage(convoData) {
        sendingMessage = false;

        if (convoData.Error) {
           $('replybar error').text(convoData.Error).slideDown(300).delay(10000).slideUp(300);
           return;
        }

        $('replybar input').val('');
        populateConversation(convoData);
    }

    function disableSending() {
        $('replybar input').attr('disabled', 'disabled');
        $('replybar .fa-paper-plane').addClass('disabled');
        $('replybar .spinner').fadeIn(100);
    }

    function enableSending() {
        $('replybar input').removeAttribute('disabled');
        $('replybar .fa-paper-plane').removeClass('disabled');
        $('replybar .spinner').hide(0);
    }

    function setMessageOrder(message) {
        $('#message-id-' + message.message_id).css('order', message.message_id);
    }

    function clearMessages() {
        $("messages").empty();
    }

    function clearErrors() {
        $('replybar error').text('').slideUp(150);
    }

    // ****** MESSAGE SANITY CHECK ***** //
    function messageElementExists(message) {
        return $('#message-id-' + message.message_id).length;
    }

    function messageIsMine(message, pid) {
        return pid == message.participant_id;
    }

    /*

       ad88888ba   88888888888         db         88888888ba     ,ad8888ba,   88        88
      d8"     "8b  88                 d88b        88      "8b   d8"'    `"8b  88        88
      Y8,          88                d8'`8b       88      ,8P  d8'            88        88
      `Y8aaaaa,    88aaaaa          d8'  `8b      88aaaaaa8P'  88             88aaaaaaaa88
        `"""""8b,  88"""""         d8YaaaaY8b     88""""88'    88             88""""""""88
              `8b  88             d8""""""""8b    88    `8b    Y8,            88        88
      Y8a     a8P  88            d8'        `8b   88     `8b    Y8a.    .a8P  88        88
       "Y88888P"   88888888888  d8'          `8b  88      `8b    `"Y8888Y"'   88        88

   */

    function getSearchBar(){
        return $("messaging searchbar");
    }

    $('currentpageaction .fa-edit').click(function(){
        getSearchBar().find('input').focus();
    });

    getSearchBar().find('input').focus(function () {
        hideSection('threads');
        showSection('search');
        showEmptySection('search', 'Search for friends');
        resetSearch();
    });

    getSearchBar().find('i.fa-times').click(function () {
        getSearchBar().find('input').val('');
        hideSection('search');
        showSection('threads');
    });

    $("messaging searchbar input").keypress(
        debounce(function () {
            var searchVal = getSearchBar().find('input').val();
            if(searchVal.length > 3 ){
                doSearch(searchVal);
            } else {
                resetSearch();
            }
        }, 500)
    );

    function doSearch(term) {
        hideEmptySection('search')
        callServer('/account/messages/search/' + term,'', searchResults);
        //Search locally first then do remote
    }

    function searchResults(results) {

        $('search results').empty();

        if (results.users.length === 0) {
            showEmptySection('search', 'Sorry nothing here.');
            return;
        }

        results.users.forEach(function(result){
            var pc = $('<profilecard></profilecard>').addClass('mini');
            pc.append($('<img></img>').attr('src', result.avatar));
            pc.append($('<name></name>').text(result.username));

            if (result.blocked) {
                var newConvoButton = '';
                var blockButton = $('<i class="fa fa-check-circle"></i>').click(function(){
                    $(this).addClass('loading');
                    unblockUser(result.username);
                });
            } else {
                var newConvoButton = $('<i class="fa fa-comment-alt"></i>').click(function(){
                    $(this).addClass('loading');
                    startConversation(result);
                });
                var blockButton = $('<i class="fa fa-ban"></i>').click(function(){
                    $(this).addClass('loading');
                    blockUser(result.username);
                });
            }

            let actions = $('<actions></actions>');
            actions.append(newConvoButton);
            actions.append(blockButton);

            pc.append(actions);

            $('search results').append(pc);
        });


        showSection('search results');
    }

    function resetSearch() {
        showEmptySection('search');
        //Search locally first then do remote
    }

    /*

        88        88   ad88888ba   88888888888  88888888ba             db         ,ad8888ba,  888888888888  88    ,ad8888ba,    888b      88   ad88888ba
        88        88  d8"     "8b  88           88      "8b           d88b       d8"'    `"8b      88       88   d8"'    `"8b   8888b     88  d8"     "8b
        88        88  Y8,          88           88      ,8P          d8'`8b     d8'                88       88  d8'        `8b  88 `8b    88  Y8,
        88        88  `Y8aaaaa,    88aaaaa      88aaaaaa8P'         d8'  `8b    88                 88       88  88          88  88  `8b   88  `Y8aaaaa,
        88        88    `"""""8b,  88"""""      88""""88'          d8YaaaaY8b   88                 88       88  88          88  88   `8b  88    `"""""8b,
        88        88          `8b  88           88    `8b         d8""""""""8b  Y8,                88       88  Y8,        ,8P  88    `8b 88          `8b
        Y8a.    .a8P  Y8a     a8P  88           88     `8b       d8'        `8b  Y8a.    .a8P      88       88   Y8a.    .a8P   88     `8888  Y8a     a8P
         `"Y8888Y"'    "Y88888P"   88888888888  88      `8b     d8'          `8b  `"Y8888Y"'       88       88    `"Y8888Y"'    88      `888   "Y88888P"
     */

    function startConversation(newrecipient) {

        $('#dummythread mr-user').text(newrecipient.username);
        $('#dummythread img').attr('src', newrecipient.avatar);

        newrecipient.name = newrecipient.username;

        $('#dummythread').off('click');

        $('#dummythread').on('click',function(){
            currentThread = -2;
            hideSection('search');
            showSection('threads');
            setUpMessageTopBar({meta: newrecipient});
            hideSection('conversation empty');
            $('conversation messages').empty();
            showSection('conversation messages', 'flex');
            slideLeft();
        });

        $('#dummythread').show(300);

        currentThread = -2;
        hideSection('search');
        showSection('threads');
        setUpMessageTopBar({meta: newrecipient});
        hideSection('conversation empty');
        $('conversation messages').empty();
        showSection('conversation messages', 'flex');
        slideLeft();
    }

    function blockUser(username) {
        callServer('/account/messages/block/'+username, {},  handleBlock);
    }

    function handleBlock(response) {
        var searchVal = getSearchBar().find('input').val();
        if(searchVal.length > 3 ) {
            doSearch(searchVal);
        }
    }

    function unblockUser(username) {
        callServer('/account/messages/unblock/'+username, {}, handleUnblock);
    }

    function handleUnblock(username) {
        var searchVal = getSearchBar().find('input').val();
        if(searchVal.length > 3 ) {
            doSearch(searchVal);
        }
    }





});

