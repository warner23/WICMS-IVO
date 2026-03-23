$(document).ready(function () {

});

var WIWYSIWYG = {};

WIWYSIWYG.wysiwyg = function(){

}

WIWYSIWYG.palettes = function(){

    var colorPalette = ['000000', 'FF9966', '6699FF', '99FF66', 'CC0000', '00CC00', '0000CC', '333333', '0066FF', 'FFFFFF'];
    var forePalette = $('.fore-palette');
    var backPalette = $('.back-palette');

    for (var i = 0; i < colorPalette.length; i++) {
      forePalette.append('<a href="#" data-command="forecolor" data-value="' + '#' + colorPalette[i] + '" style="background-color:' + '#' + colorPalette[i] + ';" class="palette-item"></a>');
      backPalette.append('<a href="#" data-command="backcolor" data-value="' + '#' + colorPalette[i] + '" style="background-color:' + '#' + colorPalette[i] + ';" class="palette-item"></a>');
    }

}

WIWYSIWYG.ForeWrapper = function(){

       var div = $('.fore-wrapper');
        if(div.hasClass('closed')){
            $('.fore-palette').removeClass('hide').addClass('show');
            $('.fore-wrapper').removeClass('closed')
        }else{
            $('.fore-palette').removeClass('show').addClass('hide');
            $('.fore-wrapper').addClass('closed')       
        }
}

WIWYSIWYG.BackWrapper = function(){

         var div = $('.back-wrapper');
        if(div.hasClass('closed')){
            $('.back-palette').removeClass('hide').addClass('show');
            $('.back-wrapper').removeClass('closed')
        }else{
            $('.back-palette').removeClass('show').addClass('hide');
            $('.back-wrapper').addClass('closed')       
        }
}



