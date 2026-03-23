var Builder = {

elements: [],

init:function(){

Builder.drag();
Builder.drop();
Builder.save();

},

drag:function(){

$(".draggable-element").draggable({

helper:"clone",
revert:"invalid"

});

},

drop:function(){

$("#builder_canvas").droppable({

accept:".draggable-element",

drop:function(event,ui){

var element = ui.draggable.data("element");

Builder.addElement(element);

}

});

},

addElement:function(name){

var html =

'<div class="builder-element" data-element="'+name+'">'+
'<div class="builder-header">'+name+
'<button class="remove">x</button>'+
'</div>'+
'</div>';

$("#builder_canvas").append(html);

Builder.elements.push(name);

},

save:function(){

$("#save_module").click(function(){

var moduleName = $("#module_name").val();

if(moduleName === ""){

alert("Enter module name");
return;

}

$.post("WICore/WIClass/WIAjax.php",{

action:"createMod",
mod_name:moduleName,
contents:JSON.stringify(Builder.elements)

},function(res){

console.log(res);

location.reload();

},"json");

});

}

};

$(document).ready(function(){

Builder.init();

});