$(document).ready(function()
{


});


var WIMembership ={};

WIMembership.upgrade = function(id, plan, price){

    sessionStorage.setItem("membership_id", id);
    sessionStorage.setItem("membership_plan", plan);
    sessionStorage.setItem("membership_price", price);
    window.location = "upgrade.php";
    
}