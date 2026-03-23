 $(document).ready(function(){

  
});  

var WIQuiz = {};
var interval;
WIQuiz.start = function(){
    

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

WIQuiz.Easystart = function(){
    

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "quizEasyStart"
        },
        success: function(result)
        {
            $(".quiz-container").html(result);
        }
    });
}

WIQuiz.Modstart = function(){
    

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "quizModStart"
        },
        success: function(result)
        {
            $(".quiz-container").html(result);
        }
    });
}

WIQuiz.Advstart = function(){
    

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "quizAdvStart"
        },
        success: function(result)
        {
            $(".quiz-container").html(result);
            var no = $(".show").attr('id');
            var id = $(".show").attr('marker');
            console.log(no);
            console.log(id);
            WIQuiz.countdown(id, no);
        }
    });
}

WIQuiz.countdown = function(id, no){
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
        WIQuiz.advNextQuest(id, no);
     }

      seconds = (seconds < 0) ? 59 : seconds;
      seconds = (seconds < 10) ? '0' + seconds : seconds;
      //minutes = (minutes < 10) ?  minutes : minutes;
      $('.countdown').html(minutes + ':' + seconds);
      timer2 = minutes + ':' + seconds;
    }, 1000);
}

WIQuiz.nextQuest = function(id, no){

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

WIQuiz.easyNextQuest = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');
    var type = "easy";
    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action   : "easyQuizAnswers",
            id       : id,
            selected : selected,
            no       : no,
            type     : type
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

WIQuiz.easymcNextQuest = function(id, no){

    var selected =[];
    $( "label.active>input" ).each(function( index ) {
    selected.push($(this).val() );
});
    
    selected = JSON.stringify(selected);
    selected = selected.replace("[", "").replace("\"", "").replace("]", "").replace("\"", "").replace("]", "").replace("\"", "").replace("[", "").replace("\"", "").replace("]", "").replace("\"", "").replace("]", "").replace("\"", "");
    console.log(selected);
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    var type = "easy";

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action   : "easyQuizAnswers",
            id       : id,
            selected : selected,
            no       : no,
            type     : type
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


WIQuiz.modNextQuest = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "modQuizAnswers",
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

WIQuiz.advNextQuest = function(id, no){

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
            action : "advQuizAnswers",
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
            WIQuiz.countdown(id, res.no);
        }
    });

}

WIQuiz.submit = function(id, no){

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
		WIQuiz.getResults();
    	}
    });

}

WIQuiz.easySubmit = function(id, no){

    var selected = $("label.active>input").val();
    
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    var type = "easy";


    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizEasySubmit",
            id : id,
            selected : selected,
            no   : no,
            type : type
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
        WIQuiz.getEasyResults();
        }
    });

}

WIQuiz.easymcSubmit = function(id, no){

    var selected =[];
    $( "label.active>input" ).each(function( index ) {
    selected.push($(this).val() );
});
    
    selected = JSON.stringify(selected);
    selected = selected.replace("[", "").replace("\"", "").replace("]", "").replace("\"", "").replace("]", "").replace("\"", "").replace("[", "").replace("\"", "").replace("]", "").replace("\"", "").replace("]", "").replace("\"", "");
    console.log(selected);
    
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');
    var type = "easy";
    
    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizEasySubmit",
            id : id,
            selected : selected,
            no   : no,
            type : type
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
        WIQuiz.getEasyResults();
        }
    });

}



WIQuiz.modSubmit = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);
    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizModSubmit",
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
        WIQuiz.getModResults();
        }
    });

}


WIQuiz.advSubmit = function(id, no){

    var selected = $("label.active>input").val();
    console.log(selected);

    $(".class").addClass('hide');
    $("#loader").addClass('show').removeClass('hide');


    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "quizModSubmit",
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
        WIQuiz.getAdvResults();
        }
    });

}

WIQuiz.getResults = function(){
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

WIQuiz.getEasyResults = function(){
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "getEasyResults"
        },
        success: function(result)
        {
           $("#quizResults").html(result);
           $("#fireworks").fireworks();
        }
    });
}

WIQuiz.getModResults = function(){
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "getModResults"
        },
        success: function(result)
        {
           $("#quizResults").html(result);
            $("#fireworks").fireworks();
        }
    });
}

WIQuiz.getAdvResults = function(){
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "getAdvResults"
        },
        success: function(result)
        {
           $("#quizResults").html(result);
            $("#fireworks").fireworks();
        }
    });
}

WIQuiz.finish = function(no){

  console.log(no);
   $("#"+no).addClass('hide').removeClass('show');
   WIQuiz.start();


}

WIQuiz.easyFinish = function(no){
  
      
    var name = $("#scoreName").val();
    score = $("#percentage").val();
    console.log(no);
    console.log(name);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "EasySaveScore",
            score  : score,
            name   : name
        },
        success: function(result)
        {
        $("#"+no).addClass('hide').removeClass('show');
        WIQuiz.Easystart();
        }
    });
}

WIQuiz.modFinish = function(no){
  
      
    var name = $("#scoreName").val();
    score = $("#percentage").val();
    console.log(no);
    console.log(name);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "ModSaveScore",
            score  : score,
            name   : name
        },
        success: function(result)
        {
        $("#"+no).addClass('hide').removeClass('show');
        WIQuiz.Modstart();
        }
    });

}

WIQuiz.advFinish = function(no){
  
      
    var name = $("#scoreName").val();
    score = $("#percentage").val();
    console.log(no);
    console.log(name);
      $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "AdvSaveScore",
            score  : score,
            name   : name
        },
        success: function(result)
        {
        $("#"+no).addClass('hide').removeClass('show');
        WIQuiz.Modstart();
        }
    });


}

WIQuiz.RevealEasyAnswers = function(){
          $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "RevealEasyAnswers"
        },
        success: function(result)
        {
        $("#easyBarAnswers").html(result);
        }
    });
}

WIQuiz.RevealModAnswers = function(){
          $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "RevealModAnswers"
        },
        success: function(result)
        {
        $("#modBarAnswers").html(result);
        }
   });
}

WIQuiz.RevealAdvAnswers = function(){
          $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "GET",
        data: {
            action : "RevealAdvAnswers"
        },
        success: function(result)
        {
        $("#advBarAnswers").html(result);
        }
    });
}