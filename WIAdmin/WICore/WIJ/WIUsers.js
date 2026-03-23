var WIUsers = {};

WIUsers.deleteUser = function(id)
{

if(!confirm("Delete this user?"))
return;

$.post(

"WICore/WIClass/WIAjax.php",

{
action:"deleteUser",
id:id
},

function(result)
{

var res = JSON.parse(result);

if(res.status==="success")
{

$("#user-row-"+id).remove();

}

}

);

};

$(document).on("change",".user-role-select",function(){

var user_id=$(this).data("user-id");

var role_id=$(this).val();

$.post(

"WICore/WIClass/WIAjax.php",

{
action:"updateUserRole",
user_id:user_id,
role_id:role_id
}

);

});