$(document).ready(function(event)
{

});


var WIStats = {}

WIStats.openIp = function(ip){
	console.log(ip);
	sessionStorage.setItem("ip", ip);
	window.location = "WICheckup.php";
}
