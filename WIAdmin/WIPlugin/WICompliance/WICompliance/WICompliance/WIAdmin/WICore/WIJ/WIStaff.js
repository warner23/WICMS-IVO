 $(document).ready(function(){
    $(".staff-form").submit(function () {
        return false;
    });

    $("#staff_role").on("change", function() {
           // alert( this.value );
           $("#staff_role").val(this.value).prop("selected", "selected");
            
          });

    $("#staff_site").on("change", function() {
           $("#staff_site").val(this.value).prop("selected", "selected");
            
          });
        // button register click below
    $("#AddNewStaff").click(function()
    {
        if(WIStaff.validateRegistration() == true)
        {
            // validation has been passed
            var RegMail            = $("#staff_email").val(),
             RegUser               = $("#staff_name").val(),
             RegPass               = $("#staff_password").val(),
             RegRole               = $("#staff_role option:selected" ).val(),
             RegSite               = $("#staff_site option:selected").val()

             //create data that will be sent over server

             var data =
             {
                UserData:
                {
                    email           : RegMail,
                    username        : RegUser,
                    password        : RegPass,
                    role            : RegRole,
                    site            : RegSite 
                },
                FieldId:
                {
                    email           : "staff_email",
                    username        : "staff_name",
                    password        : "staff_password",
                    role            : "staff_role",
                    site            : "staff_site"
                }
             };
             // send data to server
             WIStaff.NewStaff(data);
        }
    });

  
});  

var WIStaff = {};

WIStaff.OpenNewStaff = function(){

    $('#modal-AddStaff-details').removeClass('hide').addClass('show');
}

WIStaff.NewStaff = function(data){
// get register btn
    var btn = $("#AddNewStaff");

    // put button into the loading state
    WICore.loadingButton(btn, $_lang.creating_Account);
    //hash the passowrds before sending them over the network
    data.UserData.password = CryptoJS.SHA512(data.UserData.password).toString();

    // send the data to the server
    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "registerStaff",
            User   : data
        },
        success: function(result)
        {
            // return the button to normasl state
            WICore.removeLoadingButton(btn);
            console.log(result);
            //window.alert(result);
            //parse the data to json
            //var res = JSON.stringify(result);
            var res = JSON.parse(result);
            //var res = $.parseJSON(result);
            console.log(res);
            if(res.status === "error")
            {
                /// display all errors
                 for(var i=0; i<res.errors.length; i++) 
                 {
                    var error = res.errors[i];
                    WICore.displayErrorMessage($("#"+error.id), error.msg);
                }
            }
            else
            {
                // dispaly success message
                WICore.displaySuccessMessage($(".register-form fieldset"), res.msg);
                $('#modal-AddStaff-details').removeClass('show').addClass('hide');
                WICore.Refresh();
                //WICore.displaySuccessMessage($(".msg"), res.msg);
            }
        }
    });
}

// validate registration form
WIStaff.validateRegistration = function()
{
    var valid = true;

    // remove all previous error messages
    WICore.removeErrorMessages();

    // check if all fields are filled
    $(".staff-form").find("input").each(function()
    {
        var el = $(this);
         if($.trim(el.val()) === "") 
         {
            WICore.displayErrorMessage(el);
            valid = false;
        }
    });

    // get email, pass, and confirm pass for further validation
    var RegMail           = $("#staff_email"),
        RegPass           = $("#staff_password")

    //check if email is valid
    if(!WICore.validateEmail(RegMail.val()) && RegMail.val() != "") {
        valid = false;
        WICore.displayErrorMessage(RegMail,$_lang.email_wrong_format);
    }


    //check password length
    if($.trim(RegPass.val()).length <= 5) {
        valid = false;
        WICore.displayErrorMessage(RegPass, $_lang.password_length);
    }

    return valid;       

}