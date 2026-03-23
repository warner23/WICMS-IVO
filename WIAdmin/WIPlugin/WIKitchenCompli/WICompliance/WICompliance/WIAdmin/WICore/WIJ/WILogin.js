 $(document).ready(function () {
 	//catch form submit
 	$(".form-horizontal").submit(function () {
    	return false;
    });
 	

    //button login click
    $("#admin_btn_login").click(function () {
        var  un    = $("#admin_comp_email"),
             pa    = $("#admin_comp_password");

       //validate login form
       if(login.validateLogin(un, pa) === true) { 
           //validation passed, prepare data that will be sent to server
            var data = {
                username: un.val(),
                password: pa.val(),
                id: {
                    username: "admin_comp_email",
                    password: "admin_comp_password"
                }
            };
            
            //send login data to server
            login.loginUser(data);
       }

    });


    //set focus on username field when page is loaded
    $("#admin_email").focus();
});


/** LOGIN NAMESPACE
 ======================================== */
var login = {};

login.loginUser = function (data) {
    var btn = $("#admin_btn_login");
    WICore.loadingButton(btn, $_lang.logging_in);

    //encrypt password before sending it through the network
    data.password = CryptoJS.SHA512(data.password).toString();

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        dataType: "json",
        data: {
            action  : "staffLogin",
            username: data.username,
            password: data.password,
            id      : data.id
        },
        success: function (result) {
            console.log(result);
           WICore.removeLoadingButton(btn);
           if( result.status === 'success' )
           {
            //console.log("admincompdash.php");
        window.location = result.page;
           }else {
               WICore.displayErrorMessage($("#admin_email"));
               WICore.displayErrorMessage($("#admin_password"), result.message);
           }

        }
    });
};

login.validateLogin = function (un, pass) {
    var valid = true;

    //remove previous error messages
    WICore.removeErrorMessages();

    if($.trim(un.val()) == "") {
        WICore.displayErrorMessage(un);
        valid = false;
    }
    if($.trim(pass.val()) == "") {
        WICore.displayErrorMessage(pass);
        valid = false;
    }

    return valid;
};