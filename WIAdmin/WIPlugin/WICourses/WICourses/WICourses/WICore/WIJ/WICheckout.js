$(document).ready(function()
{

});





var checkout ={};

checkout.stepOne = function(){
    $("#step_one").removeClass('show');
    $("#step_one").addClass('hide');
    $("#step_two").removeClass('hide');
    $("#step_two").addClass('show');

    $("#stepOne").removeClass('active');
    $("#stepOne").addClass('passActive');
    $("#stepTwo").removeClass('inactive');
    $("#stepTwo").addClass('active');


}


checkout.stepTwo = function(){
    $("#step_two").removeClass('show');
    $("#step_two").addClass('hide');
    $("#step_three").removeClass('hide');
    $("#step_three").addClass('show');
    $("#stepTwo").removeClass('active');
    $("#stepTwo").addClass('passActive');
    $("#stepThree").removeClass('inactive');
    $("#stepThree").addClass('active');


}

checkout.stepThree = function(){
    $("#step_three").removeClass('show');
    $("#step_three").addClass('hide');
    $("#step_four").removeClass('hide');
    $("#step_four").addClass('show');
    $("#stepThree").removeClass('active');
    $("#stepThree").addClass('passActive');
    $("#stepFour").removeClass('inactive');
    $("#stepFour").addClass('active');


}

checkout.stepFour = function(){
    $("#step_four").removeClass('show');
    $("#step_four").addClass('hide');
    $("#step_five").removeClass('hide');
    $("#step_five").addClass('show');
    $("#stepFour").removeClass('active');
    $("#stepFour").addClass('passActive');
    $("#stepFive").removeClass('inactive');
    $("#stepFive").addClass('active');


}