/***********
** WIProduct NAMESPACE
**************/
$(document).ready(function(event)
{


});

var WITeam = {}




WITeam.sendData = function(data , btn, action_name){

    event.preventDefault();

    // put button into the loading state
    //WICore.loadingButton(btn, "Creating New Course");

    $(".ajax-loading").removeClass("hide").addClass("show");
     $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : action_name,
            data   : data
        },
        success: function(result)
        {
            
            console.log(result);
            // return the button to normasl state
           // WICore.removeLoadingButton(btn);
            
            //window.alert(result);
            //parse the data to json
            //var res = JSON.stringify(result);
            var res = JSON.parse(result);
            //var res = $.parseJSON(result);
            console.log(res);
            if(res.status === "error")
            {
                $(".ajax-loading").removeClass("show").addClass("hide");
                /// display all errors
                WICore.displayadminerrorsMessage($("#cstatus"), res.msg);
                
            }
            else if(res.status === "successful")
            {
              $(".ajax-loading").removeClass("show").addClass("hide");
                // dispaly success message
                WICore.displaySuccessfulMessage($("#cstatus"), res.msg);
               //WICore.Refresh();

            }
        }
    });
}

WITeam.type = function(){
    $.ajax({
            url      : "WICore/WIClass/WIAjax.php",
            method   : "POST",
            data     : {
                action : "getCourseType"
            },
            success  : function(data){
                $("#get_brand").html(data);
            }
        });
}

WITeam.Newteam = function(){
     var 
            btn          = $("#Newtrain"),
            name         = $("#name").val(),
            summary      = $("#description").val(),
            job_title    = $("#job_title").val(),
            fb_link      = $("#fb_link").val(),
            tw_link      = $("#tw_link").val(),
            linked_link  = $("#linked_link").val(),
            image        = $("#team_photo").attr('value'),
            action_name  = "new_team";

             //create data that will be sent over server

              team = {
                teamData:{
                    name           : name,
                    summary         : summary,
                    job_title         : job_title,
                    fb_link         : fb_link,
                    tw_link         : tw_link,
                    linked_link         : linked_link,
                    image           : image

                },
                FieldId:{
                    name            : "name",
                    summary          : "summary",
                    job_title          : "job_title",
                    fb_link          : "fb_link",
                    tw_link          : "tw_link",
                    linked_link          : "linked_link",
                    image            : "image"

                }
             };
             // send data to server
             WITeam.sendData(team, btn, action_name);
}

WITeam.addteammedia = function(){
    $("#modal-team-edit-details").removeClass('show').addClass('hide');
    $("#modal-team-media-details").removeClass('hide').addClass('show');
}

WITeam.addteamupload = function(){
    $("#modal-team-edit-details").removeClass('show').addClass('hide');
    $("#modal-team-upload-details").removeClass('hide').addClass('show');
}
