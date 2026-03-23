$(document).ready(function(){


});

var WISupport = {};

WISupport.forum = function(){
	sessionStorage.setItem('cat', '4');
    sessionStorage.setItem('section', '7');
	window.location = "../WIForum/index.php";
}

WISupport.ticket = function(){
	window.location = "../WITickets/create.php";
}