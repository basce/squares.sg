var _order = "popular";
var makingvote = false;
var _tempholdID = 0;
var makevote =(id)=>{
    if(!_userObj){
        _tempholdID = id;
        $("#lb-fblogin-trigger").click();
        return;
    }
    if(_userObj["status"] !== "registered"){
        _tempholdID = id;
        $(".form_name").attr({value:_userObj["name"]});
        $(".form_email").attr({value:_userObj["email"]});
        $("#lb-form-trigger").click();
        return;
    }
    if(makingvote) return;
    makingvote = true;
    $.ajax({
        type: "POST",
        data: {
            method: "makevote",
            signed_request:_signed_request,
            id:id
        },
        dataType: 'json',
        timeout:5000,
        success:(d)=>{
            makingvote = false;
            //update dialog
            $(".designer_first_name").text(d.submission.first_name);

            if(d.status == "voted"){
                //update number
                $("#lb-novote-trigger").click();
            }else if(d.status == "nologin"){
                _tempholdID = id;
                $("#lb-fblogin-trigger").click();
            }else if(d.status == "success"){
                $(".grid-item[data-id='"+id+"'] .number-votes, .lb-number-votes ").text( +d.submission.number_of_vote == 1 ? "1 vote" : d.submission.number_of_vote+ " votes");
                $(".vote-btn[data-id='"+id+"']").addClass("voted");
                $("#lb-voted-trigger").click();
            }else if(d.status == "closed"){
                $("#lb-closed-trigger").click();
            }
        },
        error:(request, status, err)=>{
            console.log(status);
        }
    })
}

var _errorHandler = (msg)=>{
    console.log(msg);
}

var _user = 0;
var _userObj = null;
var _signed_request = "";
var _voted = [];
var fblogging = false;
var fblogin = ()=>{
    if(fblogging) return;
    fblogging = true;

    FB.login(function(response){
        fblogging = false;
        _user = response.authResponse.userID;
        _signed_request = response.authResponse.signedRequest;
        if( response.authResponse ){
            $.ajax({
                type: "POST",
                data: {
                    method: "fblogin",
                    signed_request:_signed_request
                },
                dataType:'json',
                timeout:5000,
                success:(data)=>{
                    if(data.error){
                        _errorHandler(data.msg);
                    }else{
                        _userObj = data.userObj;
                        if(_userObj["status"] !== "registered"){
                            $(".form_name").attr({value:_userObj["name"]});
                            $(".form_email").attr({value:_userObj["email"]});
                            $("#lb-form-trigger").click();
                        }else{
                            //show registeration form
                            makevote(_tempholdID);
                            _tempholdID = 0;
                        }
                    }
                },
                error:(request, status, err)=>{
                    _errorHandler(status);
                }
            })
        }else{
            console.log("user cancel");
        }
    }, {scope: "email"});
}

var regging = false;
var reg = ()=>{
    if(regging) return;
    regging = true;

    $.ajax({
        type: "POST",
        data: {
            method: "reg",
            signed_request:_signed_request,
            name:$(".form_name:visible").val(),
            email:$(".form_email:visible").val(),
            pdp:$(".form_pdp:visible:checked").length
        }, 
        dataType:'json',
        timeout:5000,
        success:(data)=>{
            regging = false;
            if(data.error){
                _errorHandler(data.msg);
            }else{
                _userObj = data.userObj;
                makevote(_tempholdID);
                _tempholdID = 0;
            }
        },
        error:(request, status, err)=>{
            _errorHandler(status);
        }
    });
}

var appendItem = (data)=>{
    var div = `
        <div class="grid-item work3d print percent-33" id="submission-${data.id}" data-id="${data.id}">
            <div class="single-portfolio">
                <a class="venobox" data-gall="gall-img${data.id}" href="${data.items[0].image_url}">
                    <img src="${data.items[0].image_url}" alt="" />
                </a>
                <div class="zoom-icon">
                    ${
                        (items=>{
                            return items.map((item,index)=>{
                                if(index == 0){
                                    return "";
                                }else{
                                    return `<a style="display:none" class="venobox" data-gall="gall-img${data.id}" href="${item.image_url}"></a>`;
                                }
                            }).join("");
                        })(data.items)
                    }
                </div>
                <div class="project-title light-bg ptb-40">
                    <a href="/${data.unique_code}" target="_self">
                        <h4 class="no-margin">${data.artwork_name}</h4>
                    </a>
                    <p>${data.designer_name}, ${data.faculty}</p>
                    <hr class="line" />
                    <p class="vote">
                        <a href="#" class="vote-btn" data-id="${data.id}"><span class="oi vote-icon" data-glyph="heart"></span> vote this!</a> | <span class="number-votes">${
                            (number_of_vote=>{
                                if(number_of_vote == 1){
                                    return "1 vote";
                                }else{
                                    return number_of_vote +" votes";
                                }
                            })(data.number_of_vote)
                        }</span></p>
                    </p>
                </div>
            </div>
        </div>
    `;
    var a = $(div);
    a.find(".vote-btn").click(function(e){
        e.preventDefault();
        makevote($(this).attr("data-id"));
    });

    //check if voted
    if($.inArray(data.id+"", _voted) !== -1){
        //voted
        a.find(".vote-btn").addClass("voted");
    }

    $(".portfolio-grid").append(a);
};

var updateAllVoted = ()=>{
    /*
    $(".grid-item").each(function(index,value){
        if($.inArray($(value).attr("data-id"), _voted) !== -1){
            //in array
            $(value).find(".vote-btn").addClass("voted");
        }
    });
    */
    $(_voted).each(function(index, value){
        $(".vote-btn[data-id='"+value+"']").addClass("voted");
    });
}

var loadingSubmission = false;
var loadSubmission = ()=>{
    if(!$("#load-more-btn").attr("page-index")){
        $("#load-more-btn").attr("page-index",1);
    }

    if(loadingSubmission) return;
    loadingSubmission = true;

    $.ajax({
        type:"POST",
        data:{
            method:"getSubmission",
            order:_order,
            pageindex:$("#load-more-btn").attr("page-index")
        },
        dataType:'json',
        timeout:5000,
        success:(d)=>{
            loadingSubmission = false;
            if(d.submissions){
                if(d.submissions.cpage >= d.submissions.pages){
                    //hide loadbutton
                    $("#load-more-btn").css({display:"none"});
                }else{
                    $("#load-more-btn").attr({"page-index":1+d.submissions.cpage});
                    $("#load-more-btn").removeAttr("style");
                }

                if(d.submissions.data && d.submissions.data.length){
                    d.submissions.data.forEach((item)=>{
                        appendItem(item);
                    });

                    $(".venobox:not(.vbox-item)").venobox();
                }
            }
        },
        error:(request, status, err)=>{
            _errorHandler(status);
        }
    });
}

$(function(){
    $('#loading-wrap').fadeIn(0);
})

var fbReady = ()=>{
    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        // The user is logged in and has authenticated your
        // app, and response.authResponse supplies
        // the user's ID, a valid access token, a signed
        // request, and the time the access token 
        // and signed request each expire.
        _user = response.authResponse.userID;
        _signed_request = response.authResponse.signedRequest;

        //get voted history
        $.ajax({
            type: "POST",
            data: {
                method: "getVoted",
                signed_request: _signed_request,
            },
            dataType: 'json',
            timeout: 5000,
            success:(d)=>{
                if(!d.error){
                    _voted = d.voted;
                    _userObj = d.userObj;
                    updateAllVoted();
                }
            },
            error:(request, status, err)=>{
                _errorHandler(status);
            }

        });
    
      } else if (response.status === 'not_authorized') {
        // The user hasn't authorized your application.  They
        // must click the Login button, or you must call FB.login
        // in response to a user gesture, to launch a login dialog.
      } else {
        // The user isn't logged in to Facebook. You can launch a
        // login dialog with a user gesture, but the user may have
        // to log in to Facebook before authorizing your application.
      }
     });
    $(function(){

        $(".vote-btn").click(function(e){
            e.preventDefault();
            makevote($(this).attr("data-id"));
        });

        $('#loading-wrap').fadeOut(1000);

        $("#load-more-btn").click((e)=>{
            e.preventDefault();
            loadSubmission();
        });

        $("#load-more-btn").click();

        $("#sort_order").change(function(e){
            $('#loading-wrap').fadeIn(0);
            switch($(this).val()){
                case "POPULAR":
                    _order = "popular";
                break;
                case "RECENT":
                    _order = "recent";
                break;
                case "DESIGNER":
                    _order = "designer";
                break;
                case "TITLE":
                    _order = "title";
                break;
            }

            $(".portfolio-grid").empty();

            $("#load-more-btn").attr({"page-index":1});
            $("#load-more-btn").click();                    
            $('#loading-wrap').fadeOut(1000);
        });
    });
}