$(document).ready(function(){

      
      $('#timepicker').timepicker({
            change: function(time) {
            // the input field
            var element = $(this), text;
            // get access to this Timepicker instance
            var timepicker = element.timepicker();
            text = 'Selected time is: ' + timepicker.format(time);
            element.siblings('span.help-line').text(text);
        },
    timeFormat: 'HH-mm-ss',
    interval: 60,
    minTime: '10',
    maxTime: '6:00pm',
    defaultTime: '11',
    startTime: '10:00',
    dynamic: false,
    dropdown: true,
    scrollbar: true
    }


    );


      $('.date_cell').mouseenter(function(){
        date = $(this).attr('date');
        $(".date_popup_wrap").fadeOut();
        $("#date_popup_"+date).fadeIn();  
      });

      $('.date_cell').mouseleave(function(){
        $(".date_popup_wrap").fadeOut();      
      });   

      $('.month_dropdown').on('change',function(){
        getCalendar('calendar_div',$('.year_dropdown').val(),$('.month_dropdown').val());
      });

      $('.year_dropdown').on('change',function(){
        getCalendar('calendar_div',$('.year_dropdown').val(),$('.month_dropdown').val());
      });

      WIAppointment.getEventTypes();

if(isset(sessionStorage.getItem('class_id'))){
    var id = sessionStorage.getItem('class_id');
    var name = sessionStorage.getItem('name');
    $("select#ma_type option").checked(name);

}
      
});  

var WIAppointment = {};

WIAppointment.getCalendar = function(target_div,year,month){
  $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
      action : "getCalendar",
      year  : year,
      month : month
    },    
    success:function(html){
      $('#'+target_div).html(html);
    }
  });
}

WIAppointment.getWeekCalendar = function(){
    $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
      action : "getWeekCalendar"

    },
    success:function(html){
      $('#calendar').html(html);
      $('#calendar').slideDown('slow');
    }
  });
}

WIAppointment.getDayCalendar = function(){
    $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
      action : "getDayCalendar"

    },
    success:function(html){
      $('#calendar').html(html);
      $('#calendar').slideDown('slow');
    }
  });
}



 WIAppointment.getEvents = function(date){
  $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
        action : "getEvents",
        date   : date
    },
    success:function(response){
      $('#event_list').html(response);
      $('#event_list').slideDown('slow');
    }
  });
}

 WIAppointment.getEventTypes = function(){
  $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
        action : "getEventTypes"
    },
    success:function(response){
      $('#event_list').html(response);
      $('#event_list').slideDown('slow');
    }
  });
}

 WIAppointment.training = function(date, className){

    $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
        action : "training",
        date   : date,
        class  : className
    },
    success:function(response){
      $('#modal-training-details').removeClass('hide').addClass('show');
      $('#training').html(response);
    }
  });
  
}

WIAppointment.endTrain = function(date){
  var type = $("#eventTypeTraining").val();
  console.log(type);
  console.log(date);

  $('#modal-training-details').removeClass('show').addClass('hide');
  WIAppointment.timeSlots(date, type);
}

 WIAppointment.timeSlots = function(date, type){

  console.log(date);
  console.log(type);
    $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
        action : "timeslots",
        date   : date,
        type   : type
    },
    success:function(response){
      $('#modal-booker-details').removeClass('hide').addClass('show');
      $("#bookslots").attr("onclick", "WIAppointment.booker(`"+type+"`,`"+date+"`);");
      $('#bookings').html(response);
    }
  });
  
}

WIAppointment.select = function(time, no){
  console.log(time);
  console.log(no);

  if($("#"+no).hasClass('selected')){
    $("#"+no).removeClass('selected');
  }else{
    $("#"+no).addClass('selected');
    $("#"+no).attr('slot', time);

  }
  
}

WIAppointment.booker = function(type,date){
console.log(type);
  console.log(date);
  var duration = "0";
  var startingTime = [];


  $("#timeslots>li.selected").each(function() {
        startingTime.push(this.slot);
    });

  duration = $(".selected").length;

  console.log(startingTime);
  console.log(duration);

      $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
        action : "details",
        date   : date,
        type   : type
    },
    success:function(response){
      $('#modal-booker-details').removeClass('show').addClass('hide');
      $('#modal-person-details').removeClass('hide').addClass('show');

      var startTime = startingTime[0]['slot'];


      startingTime = JSON.stringify(startingTime);
      //console.log(time);
      $("#payperson").attr("onclick", "WIAppointment.details('"+type+"','"+date+"', '"+duration+"', '"+startingTime+"');");
      $('#details').html(response);
    }
  });

}

WIAppointment.details = function(type, date, duration, startTime){
    var place = $('#place option:selected').val();
    var placement = $('#placement').val();
    var contact_no = $('#contact_no').val();
    var notes = $('textarea#booking_notes').val();
    var name = $('#fullname').val();

    console.log(placement);
    console.log(place);
    console.log(contact_no);
    console.log(notes);
    console.log(name);

    $.ajax({
    type:'POST',
    url:'WICore/WIClass/WIAjax.php',
    data: {
        action : "paypalpayment",
        date   : date,
        type   : type,
        duration : duration,
        startTime : startTime,
        placement   : placement,
        contact_no : contact_no,
        name       : name,
        notes    : notes

    },
    success:function(data)
    {
      $('#payment').html(data);
    $('#modal-person-details').removeClass('show').addClass('hide');
    $('#modal-paypal-details').removeClass('hide').addClass('show');
    }

    
  });
}

WIAppointment.addEvent = function(date){

    $('#eventDate').val(date);
    $('#eventDateView').html(date);
    $('#calender_section_top').css("margin-left", "0px");
    $('#calender_section_bot').css("margin-left", "24px");
    $('#event_list').slideUp('slow');
    $('#event_add').removeClass('none').slideDown('slow');
}

WIAppointment.addEventBtn = function(){
var dating = $('#eventDate').val();
var title = $('#eventTitle').val();
var name  = $('#fullname').val();
var timing = $('#timepicker').val();
var contact_no = $('#mobile').val();
var duration = $('#duration').val();

   var appointment = {
                UserData:{
                       title        : title,
                    dating           : dating,
                    timing           : timing,
                    duration          : duration,
                    name             : name,
                    contact_no             : contact_no

                },
                FieldId:{
                    title           : "title",
                    dating         : "dating",
                    timing         : "timing",
                    duration       :"duration",
                    name            : "name",
                    contact_no      : "contact_no"

                }
             };
        $.ajax({
            url: "WICore/WIClass/WIAjax.php",
            type: "POST",
            data: {
            action : "addEventBtn",
            appointment   : appointment
            },
            success:function(msg){
                if(msg == 'ok'){
                    var dateSplit = date.split("-");
                    $('#eventTitle').val('');
                    alert('Event Created Successfully.');
                    getCalendar('calendar_div',dateSplit[0],dateSplit[1]);
                }else{
                    alert('Some problem occurred, please try again.');
                }
            }
        });
}


WIAppointment.pushpayment = function(res, ord){

    if (res.ack) {
        if(WIAppointment.getUrlParams('commit') === 'true') {
          console.log('execute');
             WIAppointment.showPaymentExecute(res.data, ord);
        } else {
          console.log('payGet');
             WIAppointment.showPaymentGet(res.data, ord);
        }
    } else {
        alert('Something went wrong');
    } 

}


WIAppointment.showPaymentExecute = function(response, ord){
console.log('exec8te');
console.log(response);
console.log(ord);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "showPaymentExecute",
            response     : response,
            ord         : ord
        },
        success: function (response) {
         var res = JSON.parse(response);
         console.log(res)
         if(res.status == "COMPLETED"){
        $("#orderView").css("display", "block");
        $("#loadingAlert").css("display", "none");
        $("#event_add").css('width', '100%');
        $("#orderView").css('margin', '0 254px 20px');
        $("#paypalpay").html(res.receipt);
         }else{

         }

        }
    });
}

WIAppointment.showPaymentGet = function(response, ord){
  
       $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "showPaymentGet",
            response     : response
        },
        success: function (response) {
          var res = JSON.parse(response);
          console.log(res);
          console.log(' payget order id: '+res.id);
         if(res.status == "APPROVED"){
           $("#paypalCheckoutContainer").css("display", "none");
           $("#event_case").css("display", "none");
            $("#paypalpay").removeClass('hide').addClass('show');
            $("#paypalpay").html(res.receipt);
            
        console.log('Get Order result' + JSON.stringify(res));
        $('#orderConfirm').css('display', 'block');

        document.querySelector('#confirmButton').addEventListener('click', function () {

            let postPatchOrderData = {
                    "order_id": res.id,
                    "item_amt": res.item_cost,
                    "tax_amt": res.tax_cost,
                    "total_amt": res.cost,
                    "currency": res.cc
                };

            console.log('patch data: '+ JSON.stringify(postPatchOrderData));
            // Execute the payment
            $('#confirmButton').css('display', 'none');
            $('#loadingAlert').css('display', 'block');
            $('.ajax-loading').removeClass('hide').addClass('show');

            console.log('capture order id: '+res.id);
            WIAppointment.callPaymentCapture(ord); 
        });

           }else{
            console.log("something went wrong.");
           }
         }
    });
}


WIAppointment.getUrlParams = function(prop) {
    let params = {},
        search = decodeURIComponent( window.location.href.slice( window.location.href.indexOf( '?' ) + 1 ) ),
        definitions = search.split( '&' );

    definitions.forEach( function(val) {
        let parts = val.split( '=', 2 );
        params[ parts[ 0 ] ] = parts[ 1 ];
    } );

    return ( prop && prop in params ) ? params[ prop ] : params;
}

WIAppointment.callPaymentCapture = function(ord){
    $.ajax({
    type: 'POST',
    url: 'WICore/WIVendor/paypal/V2/api/captureOrder.php',
    success: function (response) {
        $("#orderConfirm").css("display", "none");
        $("#loadingAlert").css("display", "block");
        console.log('Capture Response : '+ JSON.stringify(response));
        if (response.ack){
          console.log('capture responce data' + response.data);
          ord = sessionStorage.getItem('ord');
            WIAppointment.showPaymentExecute(response.data, ord);
        }else{
           alert("Something went wrong");
        }
           
            }
      });
    }

WIAppointment.create = function(item_amt, item_qty, item_title, total_amt, duration, type, place, name, selectedtime, contact_no, notes, eventDate){


  $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "createOrder",
            item_amt     : item_amt,
            item_qty     : item_qty,
            item_title     : item_title,
            total_amt     : total_amt,
            duration     : duration,
            type         : type,
            place          : place,
            name           : name,
            selectedtime   : selectedtime,
            contact_no : contact_no,
            notes    : notes,
            eventDate   : eventDate

        },
        success: function (response) {
          console.log("createOrder: "+response);
          sessionStorage.setItem('ord', response);
        }
      });
}

WIAppointment.email = function(receipt){

  $("#calender_section_top").css("display", "block");
  $("#calender_section_bot").css("display", "block");
  $("#event_add").css('width', '23%');
  $("#orderView").css('margin', '0 0 20px !important');
  $("#orderView").css("display", "none");
  $("#emailReceipt").removeClass('hide').addClass('show');
  console.log(receipt);

  
}

WIAppointment.finish = function(){
  
  $("#emailling").remove();
  $("#orderConfirm").remove();
  var email = $("#email_Receipt").val();
  var receipt = $("#paypalpay");
  console.log(email);
  console.log(receipt);
  var docReceipt = receipt[0]['innerHTML'];

  $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "appointmentFinish",
            email     : email,
            docReceipt     : docReceipt
        },
        success:function(data)
        {
          $('#modal-paypal-details').removeClass('show').addClass('hide');
          $("#emailReceipt").removeClass('show').addClass('hide');
          $("#event_list").css("display", "block");
          $("#orderView").css("display", "none");
          WICore.Refresh();
        }

      });
}

WIAppointment.return = function(){
  $('#modal-paypal-details').removeClass('show').addClass('hide');
  $("#emailReceipt").removeClass('show').addClass('hide');
  $("#event_list").css("display", "block");
  $("#orderView").css("display", "none");
  WICore.Refresh();
}