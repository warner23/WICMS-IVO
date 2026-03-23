$(document).ready(function(event)
{

});


var WIContact = {};

WIContact.openMail = function(id){
    if($('#mess-'+id).hasClass('show')){
    $('#mess-'+id).removeClass('show').addClass('hide');
    }else{
        $('#mess-'+id).removeClass('hide').addClass('show');
    }
    
}