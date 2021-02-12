$(function () {
    if (!$("forum").length) {
        return;
    }
    
    window['forum'] = {

        /*
                                                               a8  8a
            88  888b      88  88  888888888888  ad88888ba     d8'  `8b
            88  8888b     88  88       88      d8"     "8b   d8'    `8b
            88  88 `8b    88  88       88      Y8,          d8'      `8b
            88  88  `8b   88  88       88      `Y8aaaaa,    88        88
            88  88   `8b  88  88       88        `"""""8b,  88        88
            88  88    `8b 88  88       88              `8b  Y8,      ,8P
            88  88     `8888  88       88      Y8a     a8P   Y8,    ,8P
            88  88      `888  88       88       "Y88888P"     Y8,  ,8P
                                                               "8  8"
         */

        initForum: function () {
            forum.initPostActions();
            forum.initTopicActions();
            forum.initLightBoxes();
            forum.initForumTopic();
        },
        initPostActions: function () {

            $("postactions a").off('click');
            $("postactions a").click(function () {
                if ($(this).prop('href')) {
                    return;
                }

                var dataID = $(this).data('id');
                var action = $(this).data('action');

                $(this).attr("disabled", "true");

                if (action == "delete" || action == "report") {
                    var confirmed = confirm("Are you sure you wish to " + action + " this post?");
                    if (!confirmed) {
                        $(this).removeAttr("disabled");
                        return;
                    }
                }

                callServer('/forum/do_to_post/' + action + '-' + dataID, [], forum[action + 'Return'], $(this).parent().parent().parent());

            });
        },
        initTopicActions: function () {
            $("topicactions a").off('click');
            $("topicactions a").click(function () {
                if ($(this).prop('href')) {
                    return;
                }

                var dataID = $(this).data('id');
                var action = $(this).data('action');

                $(this).attr("disabled", "true");

                if (action == "delete") {
                    var confirmed = confirm("Are you sure you wish to " + action + " this topic?");
                    if (!confirmed) {
                        $(this).removeAttr("disabled");
                        return;
                    }
                }

                callServer('/forum/do_to_topic/' + action + '-' + dataID, [], forum[action + 'Return'], $(this));
            });
        },
        initLightBoxes: function () {
            $("forumpost postcontent img").each(function () {
                $(this).fancybox({
                    href: $(this).attr('src')
                });
            });
        },
        initForumTopic: function () {
            forum.initJodit();
            $("#post").click(function () {

                var form = $('<form></form>');
                form.attr("method", "post");


                var field = $('<input></input>');

                field.attr("type", "hidden");
                field.attr("name", 'htmlinput');
                field.attr("value", forum.editor.getEditorValue());

                form.append(field);
                if ($("#title").length || $("#slug").length) {

                    if ($("#title").length) {
                        form.append($("#title"));
                    }

                    if ($("#slug").length) {
                        form.append($("#slug"));
                    }

                } else {
                    form.attr("action", '/forum/post_to_topic/' + getUrlComponent(3));
                }

                $(document.body).append(form);
                form.submit();
            });
        },

        /*                                                                                               a8  8a
        88888888ba   88888888888  888888888888  88        88  88888888ba   888b      88   ad88888ba     d8'  `8b
        88      "8b  88                88       88        88  88      "8b  8888b     88  d8"     "8b   d8'    `8b
        88      ,8P  88                88       88        88  88      ,8P  88 `8b    88  Y8,          d8'      `8b
        88aaaaaa8P'  88aaaaa           88       88        88  88aaaaaa8P'  88  `8b   88  `Y8aaaaa,    88        88
        88""""88'    88"""""           88       88        88  88""""88'    88   `8b  88    `"""""8b,  88        88
        88    `8b    88                88       88        88  88    `8b    88    `8b 88          `8b  Y8,      ,8P
        88     `8b   88                88       Y8a.    .a8P  88     `8b   88     `8888  Y8a     a8P   Y8,    ,8P
        88      `8b  88888888888       88        `"Y8888Y"'   88      `8b  88      `888   "Y88888P"     Y8,  ,8P
                                                                                                         "8  8"
        */

        lockReturn: function (response, button) {
            if (response) {
                location.reload();
            }

            forum.initTopicActions();
        },
        unlockReturn: this.lockReturn,
        stickyReturn: this.lockReturn,
        unstickyReturn: this.lockReturn,
        subscribeReturn: function (response, button) {
            if (response) {
                button.removeAttr('disabled');
                button.html('<i class="fa fa-bell-slash"></i> Unsubscribe');
                button.data('action', 'unsubscribe');
            }

            forum.initTopicActions();
        },
        unsubscribeReturn: function (response, button) {
            if (response) {
                button.removeAttr('disabled');
                button.html('<i class="fa fa-bell"></i> Subscribe');
                button.data('action', 'subscribe');
            }

            forum.initTopicActions();
        },
        deleteReturn: function (response, post) {
            if (response) {
                if (post.find('postcontent').length) {
                    post.remove();
                    return;
                }
                window.location = '/forum';
            }
        },
        redactReturn: function (response, post) {
            if (response) {
                post.find("postcontent").html('[This post has been redacted by the author]');
                var link = post.find("[data-action='redact']");
                link.replaceWith('<a data-id="' + link.data('id') + '" data-action="unredact"><i class="fa fa-eye"></i> <span>Un-Redact</span></a>');
            }


            forum.initPostActions();
        },
        unredactReturn: function (response, post) {
            if (response) {
                post.find("postcontent").html(response.text);
                var link = post.find("[data-action='unredact']");
                link.replaceWith('<a data-id="' + link.data('id') + '" data-action="redact"><i class="fa fa-eye-slash"></i> <span>Redact</span></a>');
            }


            forum.initPostActions();
        },
        likeReturn: function (response, post) {
            if (response) {
                var link = post.find("[data-action='like']");
                link.replaceWith('<a data-id="' + link.data('id') + '" data-action="unlike"><i class="fa fa-heartbeat"></i> <span class="actiontext"> Un-Like </span> <span class="badge badge-light">' + response.count + '</span></a>');
            }

            forum.initPostActions();
        },
        unlikeReturn: function (response, post) {
            if (response) {
                var link = post.find("[data-action='unlike']");
                link.replaceWith('<a data-id="' + link.data('id') + '" data-action="like"><i class="fa fa-heart"></i> <span class="actiontext"> Like </span> <span class="badge badge-light">' + response.count + '</span></a>');
            }

            forum.initPostActions();
        },
        thankReturn: function (response, post) {
            if (response) {
                var link = post.find("[data-action='thank']");
                link.replaceWith('<a data-id="' + link.data('id') + '" data-action="unthank"><i class="fa fa-thumbs-down"></i> <span class="actiontext"> Un-Thank </span> <span class="badge badge-light">' + response.count + '</span></a>');
            }

            forum.initPostActions();
        },
        reportReturn: function (response, post) {
            if (response) {
                var link = post.find("[data-action='report']");
                link.replaceWith('<i class="fa fa-flag"></i> Reported - Thankyou.');
            }

            forum.initPostActions();
        },
        unthankReturn: function (response, post) {
            if (response) {
                var link = post.find("[data-action='unthank']");
                link.replaceWith('<a data-id="' + link.data('id') + '" data-action="thank"><i class="fa fa-thumbs-up"></i> <span class="actiontext"> Thank </span> <span class="badge badge-light">' + response.count + '</span></a>');
            }

            forum.initPostActions();
        },

        /*

                88    ,ad8888ba,    88888888ba,    88  888888888888
                88   d8"'    `"8b   88      `"8b   88       88
                88  d8'        `8b  88        `8b  88       88
                88  88          88  88         88  88       88
                88  88          88  88         88  88       88
                88  Y8,        ,8P  88         8P  88       88
        88,   ,d88   Y8a.    .a8P   88      .a8P   88       88
         "Y8888P"     `"Y8888Y"'    88888888Y"'    88       88

         */

        editor: null,
        initJodit: function () {

            if (!$("#editor").length) {
                return;
            }

            /*
            I)iiii N)n   nn I)iiii T)tttttt    J)jjjjjj  O)oooo  D)dddd   I)iiii T)tttttt
              I)   N)nn  nn   I)      T)           J)   O)    oo D)   dd    I)      T)
              I)   N) nn nn   I)      T)           J)   O)    oo D)    dd   I)      T)
              I)   N)  nnnn   I)      T)       J)  jj   O)    oo D)    dd   I)      T)
              I)   N)   nnn   I)      T)       J)  jj   O)    oo D)    dd   I)      T)
            I)iiii N)    nn I)iiii    T)        J)jj     O)oooo  D)ddddd  I)iiii    T)
             */

            var imageUploadError = null;

            forum.editor = new Jodit("#editor", {
                uploader: {
                    url: '/forum/imagepost',
                    isSuccess: function (e) {
                        return e
                    },
                    filesVariableName: function (e) {
                        return "image"
                    },
                    process: function (resp) {
                        //this was an important step to align the json with what jodit expects.
                        if (resp.image_error) {
                            imageUploadError = resp.image_error;
                        }

                        return {
                            files: [resp.image_path],
                            path: resp.path,
                            baseurl: resp.path,
                            error: resp.image_error,
                            message: resp.message
                        }
                    },
                    defaultHandlerSuccess: function (resp) {
                        if (imageUploadError) {
                            this.jodit.events.fire("errorMessage", imageUploadError);
                            imageUploadError = null;
                            return;
                        } else if (typeof (resp.files[0]) == "null" || typeof (resp.files[0]) == "undefined") {
                            this.jodit.events.fire("errorMessage", "Your image failed to upload. Sorry about that!")
                            return;
                        }

                        this.jodit.selection.insertImage('/' + resp.files[0], null, this.jodit.options.imageDefaultWidth)
                        this.jodit.selection.insertHTML('<br/>')
                    },
                },
                "toolbarButtonSize": "large",
                "toolbarAdaptive": "false",

                "buttons": ["font", "fontsize", "eraser", "|", "ul", "ol", "|", "paragraph", "symbol", "hr", "image", "video", "link", {
                    name: 'emoji',
                    icon: 'source',
                }, "|", "bold", "italic", "underline", "strikethrough", "|", "brush", "|", "align", "|", "source", "selectall", "fullsize"],

                "buttonsXS": [
                    "bold",
                    "paragraph",
                    "link",
                    "image",
                    "fullsize",
                    "dots",
                ],
                "buttonsSM": ["font", "fontsize", "|", "ul", "|", "paragraph", "symbol", "hr", "image", "video", "link", "break", "bold", "italic", "underline", "strikethrough", "|", "brush", "|", "align", "|", "source", "selectall", "fullsize"],


                "buttonsMD": ["font", "fontsize", "eraser", "|", "ul", "ol", "|", "paragraph", "symbol", "hr", "image", "video", "link", {
                    name: 'emoji',
                    icon: 'source',
                }, "break", "bold", "italic", "underline", "strikethrough", "|", "brush", "|", "align", "|", "source", "selectall", "fullsize"]
            });

            /*
            I)iiii N)n   nn I)iiii T)tttttt    E)eeeeee  M)mm mmm   O)oooo  J)jjjjjj I)iiii
              I)   N)nn  nn   I)      T)       E)       M)  mm  mm O)    oo     J)     I)
              I)   N) nn nn   I)      T)       E)eeeee  M)  mm  mm O)    oo     J)     I)
              I)   N)  nnnn   I)      T)       E)       M)  mm  mm O)    oo J)  jj     I)
              I)   N)   nnn   I)      T)       E)       M)      mm O)    oo J)  jj     I)
            I)iiii N)    nn I)iiii    T)       E)eeeeee M)      mm  O)oooo   J)jj    I)iiii

             */

            var jeb = $(".jodit_icon_emoji").emojioneArea({
                pickerPosition: "bottom",
                filtersPosition: "bottom",
                recentEmojis: false,
                standalone: true,
                autocomplete: false,
            });

            jeb[0].emojioneArea.on("emojibtn.click", function (button, event) {
                forum.editor.jodit.selection.insertHTML("<span>" + jeb[0].innerHTML + "</span>");
            });


            /*
               M)mm mmm   O)oooo  B)bbbb   I)iiii L)       E)eeeeee      A)aa   D)dddd     A)aa   P)ppppp  T)tttttt   A)aa   T)tttttt I)iiii  O)oooo  N)n   nn  S)ssss
              M)  mm  mm O)    oo B)   bb    I)   L)       E)           A)  aa  D)   dd   A)  aa  P)    pp    T)     A)  aa     T)      I)   O)    oo N)nn  nn S)    ss
              M)  mm  mm O)    oo B)bbbb     I)   L)       E)eeeee     A)    aa D)    dd A)    aa P)ppppp     T)    A)    aa    T)      I)   O)    oo N) nn nn  S)ss
              M)  mm  mm O)    oo B)   bb    I)   L)       E)          A)aaaaaa D)    dd A)aaaaaa P)          T)    A)aaaaaa    T)      I)   O)    oo N)  nnnn      S)
              M)      mm O)    oo B)    bb   I)   L)       E)          A)    aa D)    dd A)    aa P)          T)    A)    aa    T)      I)   O)    oo N)   nnn S)    ss
              M)      mm  O)oooo  B)bbbbb  I)iiii L)llllll E)eeeeee    A)    aa D)ddddd  A)    aa P)          T)    A)    aa    T)    I)iiii  O)oooo  N)    nn  S)ssss
           */

            $(".jodit_wysiwyg").focus(function () {

                var ua = navigator.userAgent.toLowerCase();
                var isAndroid = ua.indexOf("android");
                var isiPhone = ua.indexOf("iphone");
                var isiPad = ua.indexOf("ipad");
                var isiPod = ua.indexOf("ipod");

                if (isAndroid > -1 || isiPhone > -1 || isiPad > -1 || isiPod > -1) {
                    if (!forum.editor.isFullSize()) {
                        $("header").slideUp();
                    }
                }

                $(".jodit_wysiwyg").on('keyup', function (e) {
                    let keyCode = String(e.keyCode);

                    if(typeof forum.keyPressHandlers[String(e.keyCode)] != 'function'){
                        return true;
                    }

                    if(!forum.keyPressHandlers[String(e.keyCode)]()){
                        e.preventDefault();
                        return false;
                    }

                    return true;
                });
            });

            $(".jodit_wysiwyg").focusout(function () {
                $("header").slideDown();
                $(".jodit_wysiwyg").off('keydown');
            });

            /*
            T)tttttt Y)    yy P)ppppp  I)iiii N)n   nn   G)gggg      C)ccc  H)    hh E)eeeeee   C)ccc  K)   kk   S)ssss
               T)     Y)  yy  P)    pp   I)   N)nn  nn  G)          C)   cc H)    hh E)        C)   cc K)  kk   S)    ss
               T)      Y)yy   P)ppppp    I)   N) nn nn G)  ggg     C)       H)hhhhhh E)eeeee  C)       K)kkk     S)ss
               T)       Y)    P)         I)   N)  nnnn G)    gg    C)       H)    hh E)       C)       K)  kk        S)
               T)       Y)    P)         I)   N)   nnn  G)   gg     C)   cc H)    hh E)        C)   cc K)   kk  S)    ss
               T)       Y)    P)       I)iiii N)    nn   G)ggg       C)ccc  H)    hh E)eeeeee   C)ccc  K)    kk  S)ssss
            */


            $("#editor").change(
                debounce(
                    function () {
                        if (!forum.checkHTML(forum.editor.value)) {
                            forum.editor.jodit.setNativeEditorValue("Trying to post a link?, please use the contact form and send us a message with the link so we can look into it!");
                            return;
                        }

                        $("#postsample").html(forum.editor.value);
                        forum.tagging(forum.editor.value);
                    }, 1
                )
            );


        },
        checkHTML: function (text) {

            var tags = [
                "NOFRAMES",
                "NOSCRIPT",
                "PLAINTEXT",
                "TEXTAREA",
                "FRAMESET",
                "XML ",
                "HTML ",
                "APPLET",
                "SCRIPT ",
                "<SCRIPT",
                "SVG",
                "ILAYER",
                "META",
                "BGSOUND",
                "ISINDEX",
                "NEXTID",
                "dynsrc",
                "lowsrc",
                "datasrc",
                "srcdoc",
                "onclick",
                "javascript",
                "ondblclick",
                "onmousedown",
                "onmousemove",
                "onmouseover",
                "onmouseout",
                "onmouseup",
                "onratechange",
                "onfilterchange",
                "onerror",
                "onanimationstart",
                "onwebkittransitionend",
                "onkeydown",
                "ontoggle",
                "onkeypress",
                "onpageshow",
                "onkeyup",
                "onscroll",
                "onchange",
                "onsubmit",
                "onreset",
                "onselect",
                "onblur",
                "onfocus",
                "onload",
                "onunload",
                "xlink"
            ];

            var text2check = text.toLowerCase();

            for (var i = 0; i < tags.length; i++) {
                var tag2check = tags[i].toLowerCase();
                if (text2check.indexOf(tag2check) != -1) {
                    console.log("found: " + tag2check);
                    return false;
                }
            }
            return true;
        },


        /*
        888888888888    db         ,ad8888ba,     ,ad8888ba,   88  888b      88    ,ad8888ba,
             88        d88b       d8"'    `"8b   d8"'    `"8b  88  8888b     88   d8"'    `"8b
             88       d8'`8b     d8'            d8'            88  88 `8b    88  d8'
             88      d8'  `8b    88             88             88  88  `8b   88  88
             88     d8YaaaaY8b   88      88888  88      88888  88  88   `8b  88  88      88888
             88    d8""""""""8b  Y8,        88  Y8,        88  88  88    `8b 88  Y8,        88
             88   d8'        `8b  Y8a.    .a88   Y8a.    .a88  88  88     `8888   Y8a.    .a88
             88  d8'          `8b  `"Y88888P"     `"Y88888P"   88  88      `888    `"Y88888P"
         */

        /*
        Yb    dP    db    888b. .d88b.
         Yb  dP    dPYb   8  .8 YPwww.
          YbdP    dPwwYb  8wwK'     d8
           YP    dP    Yb 8  Yb `Y88P'
         */

        tagStart: false,
        taggingCheck: false,
        taggingInterface: false,
        taggingQuery: false,

        /*
            8  dP 8888 Yb  dP 888b. 888b. 8888 .d88b. .d88b.    8   8    db    8b  8 888b. 8    8888 888b. .d88b.
            8wdP  8www  YbdP  8  .8 8  .8 8www YPwww. YPwww.    8www8   dPYb   8Ybm8 8   8 8    8www 8  .8 YPwww.
            88Yb  8      YP   8wwP' 8wwK' 8        d8     d8    8   8  dPwwYb  8  "8 8   8 8    8    8wwK'     d8
            8  Yb 8888   88   8     8  Yb 8888 `Y88P' `Y88P'    8   8 dP    Yb 8   8 888P' 8888 8888 8  Yb `Y88P'

         */

        keyPressHandlers: {
            '8': function () { //BPSK
                let node = $(window.getSelection().getRangeAt(0).startContainer.parentNode);
                if (node.get(0).tagName == 'TAG') {
                    node.remove();
                    return false;
                }
            },
            '46': function () { //DEL
                let node = $(window.getSelection().getRangeAt(0).startContainer.parentNode);
                if (node.get(0).tagName == 'TAG') {
                    node.remove();
                    return false;
                }
            },
            '13': function() { //ENTER
                if(forum.taggingCheck && $('tagging results profilecard').length > 0) {
                    $('tagging results profilecard:first-of-type').click();
                    return false;
                }
            }


        },

        /*
        888 8b  8 888 88888       dP    .d88b 8   8 8888 .d88b 8  dP .d88b.
         8  8Ybm8  8    8        dP     8P    8www8 8www 8P    8wdP  YPwww.
         8  8  "8  8    8       dP      8b    8   8 8    8b    88Yb      d8
        888 8   8 888   8      dP       `Y88P 8   8 8888 `Y88P 8  Yb `Y88P'
         */

        tagging: function(origText) {

            var forumText = jQuery('<p>' + origText + '</p>').text();
            var stringLen = forumText.length;

            if (this.taggingCheck) {
                if (this.checkTaggingStop(stringLen)) {
                    this.stopTagging();
                    return;
                }

                this.checkText(forumText, stringLen);
                return;
            }

            this.checkTaggingStart(forumText, stringLen, origText);
        },
        checkTaggingStart: function(forumText, stringLen, origText) {
            //After the start
            for (let i = -10; i < -2; i++) {

                let spliceLoc = i;

                if(forumText.length < -i){
                    spliceLoc = -forumText.length;
                }

                let checkTagger = forumText.slice(spliceLoc);
                let tagS = checkTagger.indexOf(' @');

                let newTagStartOffset = tagS + 2;

                if (
                    tagS != '-1' && //start tag found
                    checkTagger.slice(newTagStartOffset, checkTagger.length).indexOf(' ') == -1 // no additional space found.
                ) {

                    this.tagStart = stringLen + spliceLoc + newTagStartOffset;
                    this.startTagging();
                    this.tagging(origText);
                    return;
                }
            }

            if(forumText == "@"){
                this.tagStart = 1;
                this.startTagging();
                this.tagging(origText);
                return;
            }

            if(forumText == " @"){
                this.tagStart = 2;
                this.startTagging();
                this.tagging(origText);
                return;
            }
        },
        checkTaggingStop: function(newLength){
            if (this.tagStart > newLength) {
                return true;
            }
        },

        startTagging: function(text) {
            //If not tagging
            this.taggingCheck = true;

            //If no interface
            if(!forum.taggingInterface) {
                forum.taggingInterface = forum.makeTaggingBox();
            } else {
                forum.resetTaggingBox();
            }

            //If interface isn't attached.
            if(!$('maininner forum tagging').length) {
                $("maininner forum").append(forum.taggingInterface);

                $(window).scroll(function() {
                    forum.updateTaggingPosition();
                });
            }

            forum.setTaggingPosition();

            $(':not(maininner forum tagging)').click(function(){
                forum.stopTagging();
            });

        },
        stopTagging: function() {
            this.taggingCheck = false;
            this.tagStart = -1;
            $('maininner forum tagging').remove();
        },

        /*
          888b. .d88b. 888b. 8    8 888b.
          8  .8 8P  Y8 8  .8 8    8 8  .8
          8wwP' 8b  d8 8wwP' 8b..d8 8wwP'
          8     `Y88P' 8     `Y88P' 8
       */

        makeTaggingBox: function() {
            let tagger = createTag('tagging');
            tagger.append(createTag('loading'));
            tagger.append(createTag('empty').text('Please type a username...'));
            tagger.append(createTag('results').text('Empty'));
            return tagger;
        },

        resetTaggingBox: function() {
            forum.taggingInterface.find('results').empty();
            forum.taggingInterface.find('results').hide();
            forum.taggingInterface.find('empty').show();
        },
        updateTaggingPosition: function(atPos) {
            var cursorPos = forum.getCurrentCursorPos();
            if(!cursorPos) {
                return;
            }

            forum.taggingInterface.css({
                'top': cursorPos.top + 24,
            })
        },
        setTaggingPosition: function(atPos) {
            var cursorPos = forum.getCurrentCursorPos();

            if(!cursorPos) {
                return;
            }

            forum.taggingInterface.css({
                'top': cursorPos.top + 24,
            })

            if ($(window).width() > 994){
                forum.taggingInterface.css({

                    'left': cursorPos.left - 16,
                })
            }
        },
        getCurrentCursorPos: function() {
            return window.getSelection().getRangeAt(0).getClientRects()[0];
        },

        /*
           db    .d88b 88888 8    8    db    8       88888    db    .d88b  .d88b.
          dPYb   8P      8   8    8   dPYb   8         8     dPYb   8P www YPwww.
         dPwwYb  8b      8   8b..d8  dPwwYb  8         8    dPwwYb  8b  d8     d8
        dP    Yb `Y88P   8   `Y88P' dP    Yb 8888      8   dP    Yb `Y88P' `Y88P'
         */
        checkText: function(text, length) {

            if(this.checkAndSanitizeQuery(text, length)) {
                forum.resolveAndClose(this.taggingQuery);
                return;
            }

            if(!this.taggingQuery){
                return;
            }


            callServer('/forum/usersearch/' + this.taggingQuery, {}, this.populateSearchTag);
        },
        checkAndSanitizeQuery: function(text, length){
            this.taggingQuery = text.slice(this.tagStart);

            if(this.taggingQuery.length < 3) {
                this.taggingQuery = '';
                return false;
            }

            let allowedChars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            let checkQ = this.taggingQuery.split('');

            for (let i = 0; i<checkQ.length; i++) {
                let testChar = checkQ[i];

                if(allowedChars.indexOf(testChar) === -1){
                    console.log('Stop Tagging ' + testChar + ' Detected');
                    forum.taggingQuery = forum.taggingQuery.slice(0,forum.taggingQuery.indexOf(testChar));
                    return true;
                }
            }
        },
        populateSearchTag: function(response, originalSearch) {
            $('tagging results').empty();

            if (response.users.length == 0) {
                $('tagging results').text('No results :(');
            }

            response.users.forEach(function(result) {

                var pc = $('<profilecard></profilecard>').addClass('mini').addClass('noactions');
                pc.append($('<img></img>').attr('src', result.avatar));
                pc.append($('<name></name>').text(result.username));

                pc.click(function(){
                    let currentText = forum.editor.value;
                    let newText = currentText.replace('@'+forum.taggingQuery, '@'+result.username);

                    forum.editor.jodit.setNativeEditorValue(newText);
                    forum.resolveAndClose(result.username);
                });

                $('tagging results').append(pc)
            });

            $('tagging empty').slideUp(300);
            $('tagging results').slideDown(300);
        },

        resolveAndClose: function(tagToResolve) {
            var resolvedString = forum.editor.value.replace('@'+tagToResolve, '&nbsp;<tag contenteditable="false">'+tagToResolve+'</tag>&nbsp;<span class="aftertag">&nbsp;</span>');
            forum.editor.jodit.setNativeEditorValue(resolvedString);

            var range = document.createRange();
            this.setEndOfContenteditable($('.jodit_wysiwyg span.aftertag:last-of-type').get(0));
            this.stopTagging();
        },

        setEndOfContenteditable: function(contentEditableElement) {
            var range,selection;

            if(!contentEditableElement){
                return;
            }

            if(document.createRange) { //Firefox, Chrome, Opera, Safari, IE 9+
                range = document.createRange();//Create a range (a range is a like the selection but invisible)
                range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
                range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
                selection = window.getSelection();//get the selection object (allows you to change selection)
                selection.removeAllRanges();//remove any selections already made
                selection.addRange(range);//make the range you have just created the visible selection
            } else if(document.selection){
                range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
                range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
                range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
                range.select();//Select the range (make it the visible selection
            }

        },
    }
    
    forum.initForum();
});