$(document).ready(function()
{
 var
    member_id = sessionStorage.getItem("membership_id");
    member_plan =  sessionStorage.getItem("membership_plan");
    member_price =  sessionStorage.getItem("membership_price");
   // WIUpgrade.upgrade(member_id, member_plan, member_price);
    WIUpgrade.checkUpgrade(member_id);

});


var WIUpgrade ={};

WIUpgrade.upgrade = function(id, plan, price){
     
     if(plan == "VIP"){
     	var desc = "VIP Membership to Evade's Martial Arts Courses and classes Learn and study at your own pace, with guidance and help from expert Coach's. Who really do love there Jobs";
     }else if(plan == "Platium"){
     	var desc = "Platium Membership to Evade's Martial Arts Courses and classes Learn and study at your own pace, with guidance and help from expert Coach's. Who really do love there Jobs";
     }else if(plan == "Test"){
     	var desc = "This is a test";
     }else if(plan == "Student"){
     	var desc = "Student MEmbership to Evade's Martial Arts Courses and classes. Learn and study at yourpwnpace, with gudiance and help from expert coachs. Who really do love what they do";
     }

    $("#upgrade_title").html(plan);
    $("#upgrade_desc").html(desc);

}

WIUpgrade.checkUpgrade = function(id){

	    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "checkUpgrade",
            id      : id
        },
        success: function (result) {
        	console.log(result);

        $("#upgrade_acc").html(result);        

        }
    });
}

WIUpgrade.accepted = function(id, plan, price, role){
	createdAt    = new Date().toISOString().substr(0,19).replace('T',' ');
		    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action    : "accepted",
            id        : id,
            plan      : plan,
            price     : price,
            role      : role,
            createdAt : createdAt
        },
        success: function (result) {
        	console.log(result);

        $("#upgrade_acc").html(result);        

        }
    });
}