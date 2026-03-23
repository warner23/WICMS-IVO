 $(document).ready(function(){


  
});  

var WIQuizBar = {};
var interval;
WIQuizBar.start = function(){
    

    	$.ajax({
    	url: "WICore/WIClass/WIAjax.php",
    	type: "GET",
    	data: {
    		action : "quizStart"
    	},
    	success: function(result)
    	{
    		$(".quiz-container").html(result);
    	}
    });
}

WIQuizBar.Easystart = function(){
    

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "quizBarEasyStart"
        },
        success: function(result)
        {
            $(".quiz-container").html(result);
        }
    });
}

WIQuizBar.Modstart = function(){
    

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "quizBarModStart"
        },
        success: function(result)
        {
            $(".quiz-container").html(result);
        }
    });
}

WIQuizBar.Advstart = function(){
    

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "quizBarAdvStart"
        },
        success: function(result)
        {
            $(".quiz-container").html(result);
            var no = $(".show").attr('id');
            var id = $(".show").attr('marker');
            console.log(no);
            console.log(id);
            WIQuizBar.countdown(id, no);
        }
    });
}


WIQuizBar.countdown = function(id, no){
    var timer2 = "1:01";
    clearInterval(interval);
    interval = setInterval(function() {

      
      var timer = timer2.split(':');
      //by parsing integer, I avoid all extra string processing
      var minutes = parseInt(timer[0], 10);
      var seconds = parseInt(timer[1], 10);
      --seconds;
      minutes = (seconds < 0) ? --minutes : minutes;

      if (minutes < 0) {
        clearInterval(interval);
        WIQuizBar.AdvBarNextQuest(id, no);
     }

      seconds = (seconds < 0) ? 59 : seconds;
      seconds = (seconds < 10) ? '0' + seconds : seconds;
      //minutes = (minutes < 10) ?  minutes : minutes;
      $('.countdown').html(minutes + ':' + seconds);
      timer2 = minutes + ':' + seconds;
    }, 1000);
    console.log(interval);
}

WIQuizBar.nextQuest = function(id, no){

	var selected = $("label.active>input").val();
	console.log(selected);

	$.ajax({
    	url: "WICore/WIClass/WIAjax.php",
    	type: "POST",
    	data: {
    		action : "quizAnswers",
    		id : id,
    		selected : selected,
    		no   : no
    	},
    	success: function(result)
    	{
    		var res = JSON.parse(result);
    		var next_id = res.no;
    		console.log(next_id);
    		$("#"+next_id).addClass('show').removeClass('hide');
    		$("#"+no).addClass('hide').removeClass('show');
    		$("label.active").removeClass('active');
    	}
    });

}

WIQuizBar.easyBarNextQuest = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "easyBarQuizAnswers",
            id : id,
            selected : selected,
            no   : no
        },
        success: function(result)
        {
            var res = JSON.parse(result);
            var next_id = res.no;
            console.log(next_id);
            $(".class").removeClass('hide');
           $("#loader").addClass('hide').removeClass('show');
            $("#"+next_id).addClass('show').removeClass('hide');
            $("#"+no).addClass('hide').removeClass('show');
            $("label.active").removeClass('active');
        }
    });

}

WIQuizBar.modBarNextQuest = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);

    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "modBarQuizAnswers",
            id : id,
            selected : selected,
            no   : no
        },
        success: function(result)
        {
            var res = JSON.parse(result);
            var next_id = res.no;
            console.log(next_id);
            $(".class").removeClass('hide');
           $("#loader").addClass('hide').removeClass('show');
            $("#"+next_id).addClass('show').removeClass('hide');
            $("#"+no).addClass('hide').removeClass('show');
            $("label.active").removeClass('active');
        }
    });

}

WIQuizBar.AdvBarNextQuest = function(id, no){

    var selected = $("label.active>input").val();

    if(selected === "" || selected == undefined){
        var selected = 0;
    }
    console.log(no);
    if(no == 0){
        no = 1;
    }
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "advBarQuizAnswers",
            id : id,
            selected : selected,
            no   : no
        },
        success: function(result)
        {
            var res = JSON.parse(result);
            var next_id = res.no;
            console.log(next_id);
            $(".class").removeClass('hide');
           $("#loader").addClass('hide').removeClass('show');
            $("#"+next_id).addClass('show').removeClass('hide');
            $("#"+no).addClass('hide').removeClass('show');
            $("label.active").removeClass('active');
            console.log(interval);
            clearInterval(interval);
            WIQuizBar.countdown(id, res.no);
        }
    });

}

WIQuizBar.submit = function(id, no){

	var selected = $("label.active>input").val();
	console.log(selected);

    

	$.ajax({
    	url: "WICore/WIClass/WIAjax.php",
    	type: "POST",
    	data: {
    		action : "quizSubmit",
    		id : id,
    		selected : selected,
    		no   : no
    	},
    	success: function(result)
    	{
    	next_id =  parseInt(no) + 1;
    	console.log(next_id);

		$("#"+next_id).addClass('show').removeClass('hide');
		$("#"+no).addClass('hide').removeClass('show');
		$("label.active").removeClass('active');
		$("#quizResults").html(result);
		WIQuizBar.getResults();
    	}
    });

}

WIQuizBar.easyBarSubmit = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);

    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizEasyBarSubmit",
            id : id,
            selected : selected,
            no   : no
        },
        success: function(result)
        {
        next_id =  parseInt(no) + 1;
        console.log(next_id);
        $(".class").removeClass('hide');
        $("#loader").addClass('hide').removeClass('show');
        $("#"+next_id).addClass('show').removeClass('hide');
        $("#"+no).addClass('hide').removeClass('show');
        $("label.active").removeClass('active');
        $("#quizResults").html(result);
        WIQuizBar.getBarEasyResults();
        }
    });

}

WIQuizBar.modBarSubmit = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);

    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizModBarSubmit",
            id : id,
            selected : selected,
            no   : no
        },
        success: function(result)
        {
        next_id =  parseInt(no) + 1;
        console.log(next_id);
        $(".class").removeClass('hide');
        $("#loader").addClass('hide').removeClass('show');
        $("#"+next_id).addClass('show').removeClass('hide');
        $("#"+no).addClass('hide').removeClass('show');
        $("label.active").removeClass('active');
        $("#quizResults").html(result);
        WIQuizBar.getBarModResults();
        }
    });

}


WIQuizBar.advBarSubmit = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);
    
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizAdvBarSubmit",
            id : id,
            selected : selected,
            no   : no
        },
        success: function(result)
        {
        next_id =  parseInt(no) + 1;
        console.log(next_id);
        $(".class").removeClass('hide');
        $("#loader").addClass('hide').removeClass('show');
        $("#"+next_id).addClass('show').removeClass('hide');
        $("#"+no).addClass('hide').removeClass('show');
        $("label.active").removeClass('active');
        $("#quizResults").html(result);
        WIQuizBar.getBarAdvResults();
        clearInterval(interval);
        }
    });

}

WIQuizBar.getResults = function(){
		$.ajax({
    	url: "WICore/WIClass/WIAjax.php",
    	type: "GET",
    	data: {
    		action : "getResults"
    	},
    	success: function(result)
    	{
		   $("#quizResults").html(result);
    	}
    });
}

WIQuizBar.getBarEasyResults = function(){
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "getBarEasyResults"
        },
        success: function(result)
        {
           $("#quizResults").html(result);
           $("#fireworks").fireworks();
        }
    });
}

WIQuizBar.getBarModResults = function(){
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "getBarModResults"
        },
        success: function(result)
        {
           $("#quizResults").html(result);
           $("#fireworks").fireworks();
        }
    });
}

WIQuizBar.getBarAdvResults = function(){
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "getBarAdvResults"
        },
        success: function(result)
        {
           $("#quizResults").html(result);
           $("#fireworks").fireworks();
        }
    });
}

WIQuizBar.finish = function(no){

  console.log(no);
   $("#"+no).addClass('hide').removeClass('show');
   WIQuizBar.start();


}

WIQuizBar.easyBarFinish = function(no){
  
      
    var name = $("#scoreName").val();
    score = $("#percentage").val();
    console.log(no);
    console.log(name);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "EasyBarSaveScore",
            score  : score,
            name   : name
        },
        success: function(result)
        {
        $("#"+no).addClass('hide').removeClass('show');
        WIQuizBar.Easystart();
        }
    });
}

WIQuizBar.modBarFinish = function(no){
  
      
    var name = $("#scoreName").val();
    score = $("#percentage").val();
    console.log(no);
    console.log(name);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "ModBarSaveScore",
            score  : score,
            name   : name
        },
        success: function(result)
        {
        $("#"+no).addClass('hide').removeClass('show');
        WIQuizBar.Modstart();
        }
    });

}

WIQuizBar.advBarFinish = function(no){
  
      
    var name = $("#scoreName").val();
    score = $("#percentage").val();
    console.log(no);
    console.log(name);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "AdvBarSaveScore",
            score  : score,
            name   : name
        },
        success: function(result)
        {
        $("#"+no).addClass('hide').removeClass('show');
        WIQuizBar.Modstart();
        }
    });


}

WIQuizBar.RevealEasyAnswers = function(){
          $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "RevealEasyBarAnswers"
        },
        success: function(result)
        {
        $("#easyBarAnswers").html(result);
        }
    });
}

WIQuizBar.RevealModAnswers = function(){
          $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "RevealModBarAnswers"
        },
        success: function(result)
        {
        $("#modBarAnswers").html(result);
        }
   });
}

WIQuizBar.RevealAdvAnswers = function(){
          $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "RevealAdvBarAnswers"
        },
        success: function(result)
        {
        $("#advBarAnswers").html(result);
        }
    });
}