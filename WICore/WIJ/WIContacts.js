$(document).ready(function()
{
	// button register click below
	$("#contact").click(function()
	{

			// validation has been passed
			var RegMail            = $("#email").val(),
			 name                  = $("#name").val(),
			 subject               = $("#subject").val(),
			 message               = $("#message").val();
			 

			 //create data that will be sent over server

			 var data =
			 {
			 	contactData:
			 	{
			 	    email           : RegMail,
                    name            : name,
                    subject         : subject,
                    message         : message
                    
			 	},
			 	FieldId:
			 	{
			 		email           : "email",
			 		name            : "name",
                    subject         : "subject",
                    message         : "message"
                   
			 	}
			 };
			 // send data to server
			 contact.sendMessage(data);
	});
});





var contact ={};

// register a new user
contact.sendMessage = function(data)
{
	// get register btn
    var btn = $("#contact");

	// put button into the loading state
	WICore.loadingButton(btn, $_lang.Sending_Message);

    // send the data to the server
    $.ajax({
    	url: "WICore/WIClass/WIAjax.php",
    	type: "POST",
    	data: {
    		action : "send",
    		info   : data
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
    			
               $("#contactError").removeClass("hidden");

                
    		}
    		else
    		{
    			// dispaly success message
    			$("#contactForm").css("display","none");
    			$("#contactSuccess").removeClass("hidden");
                //WICore.displaySuccessMessage($(".msg"), res.msg);
    		}
    	}
    });

};

