<?php
#[\AllowDynamicProperties]

class WIWYSIWYG
{
	function __contruct(){
	    $this->WIdb = WIdb::getInstance();
	}

  public function Editor()
  {
    echo '<style class="cp-pen-styles">
    .fore-wrapper{
          width: 4%;
    float: left;
    }

    .back-wrapper{
                width: 4%;
    float: left;
    }
        .editor-control {
          padding: 0.5em;
          background: #DBDBDB;
        }
        .editor-control a {
          color: #858788;
          text-decoration: none;
          padding: 2px 5px;
          border: 1px solid ​​#C0392B;
          border-radius: 2px;
          margin: 0;
          display: inline;
          font-size: 12px;
          font-weight: 400;
          text-transform: uppercase;
          -webkit-transition: all 0.5s ease;
          transition: all 0.5s ease;
              float: left;
    width: 3%;
    height: 22px;

        }

     .fore-wrapper, .back-wrapper {
    border: 1px solid #AAA;
    background: #FFF;
    font-family: "Candal";
    border-radius: 1px;
    color: black;
    padding: 5px;
    width: 2.5em;
    margin: -2px;
    margin-top: 0px;
    display: inline-block;
    text-decoration: none;
    box-shadow: 0px 1px 0px #ccc;
}

        .editor-control a:hover {
          color: #F0A2A2;
          -webkit-transition: all 0.5s ease;
          transition: all 0.5s ease;
        }
        /*  Modal
        --------------------*/

        .custom-modal-overlay {
          position: fixed;
          top: 0;
          right: 0;
          bottom: 0;
          left: 0;
          z-index: 9998;
          background-color: #FFF;
          background-color: rgba(255, 255, 255, 0.72);
        }
        .custom-modal {
          position: absolute;
          top: 20%;
          left: 50%;
          z-index: 9999;
          padding: 1.2em;
          width: 300px;
          margin-left: -150px;
          background-color: #FFFFFF;
          border: 1px solid #CACACA;
        }
        .custom-modal-header {
          margin: -1.2em -1.2em 0;
          padding: 0.5em 0.7em;
          background-color: #F0F0F0;
          color: #B9B9B9;
          font-weight: normal;
        }
        .custom-modal-content {
          margin: 1.2em 0
        }
        .custom-modal input, .custom-modal button {
          background-color: #F5F5F5;
          color: #A3A3A3;
          border: 1px solid #D3D3D3;
          padding: 5px;
        }
        .custom-modal input {
          display: block;
          width: 96%;
        }
        .custom-modal button {
          padding-right: 10px;
          padding-left: 10px;
          border-color: #DADADA;
          color: #9B9B9B;
          cursor: pointer;
          margin: 0 4px 0 0;
          -webkit-transition: all 0.5s ease;
          transition: all 0.5s ease;
        }
        .custom-modal button:focus, .custom-modal button:hover {
          background-color: #FFFFFF;
          -webkit-transition: all 0.5s ease;
          transition: all 0.5s ease;
        }
        .editor{
    position: relative;
    display: block;
    width: 100%;
    margin: 1% auto;
    background: #F0F0F0;
    height: 324px;
    overflow: hidden;
    border: 1px solid #DFDFDF;
        }
        .editor .editor-area {
          display: block;
              width: 100%;
          margin: 0px;
          padding: 0px;
          height: 245px;
          background: #FFFFFF;
          color: #6B6B6B;
          border: none;
          overflow:auto;
          resize: none;
        }
        .editor .editor-area:focus {
          outline-color: #C9C5C5;
        }

        .result {
          position: absolute;
          top: 34px;
          left: 0;
          display: block;
          width: 580px;
          margin: 5px;
          padding: 5px;
          height: 245px;
          border: none;
          overflow:auto;
          visibility: hidden;
          opacity:0;
          background: #FFFFFF;
          color: #6B6B6B;
          -webkit-transition: all 1s ease;
          transition:all 1s ease;
        }
        .result img{
          display:block;
          width:100%;
        }
        .show{
          visibility: visible !important;
          opacity:1;
          -webkit-transition: all 1s ease;
          transition:all 1s ease;
        }
        .active{
          color:#f55 !important;
        }

        .fore-wrapper{
          cursor:pointer;
        }


        .back-wrapper{
          cursor:pointer;
        }

        .back-palette{
          width: 382%;
    height: 9px;
    overflow: inherit;
    background-color: #f5f4f4;
        }

        .fore-palette{
          width: 382%;
    height: 9px;
    overflow: inherit;
    background-color: #f5f4f4;
        }

        .toolbox{
              width: 100%;
    height: 53px;
        }
        </style>
        <div class="editor">
            <div class="editor-control" id="editor-control">
            <div class="toolbar" id="toolbar">
              <a href="javascript:void(0);" title="undo" data-command="undo"><i class="fa fa-undo"></i></a>
            <a href="javascript:void(0);" title="redo" data-command="redo"><i class="fa fa-repeat"></i></a>
            <a href="javascript:void(0);" title="bold" data-command="bold"><i class="fa fa-bold"></i></a>
            <a href="javascript:void(0);" title="italic" data-command="italic"><i class="fa fa-italic"></i></a>
            <a href="javascript:void(0);" title="underline" data-command="underline"><i class="fa fa-underline"></i></a>
            <a href="javascript:void(0);" title="strikeThrough" data-command="strikeThrough"><i class="fa fa-strikethrough"></i></a>
            <a href="javascript:void(0);" title="justifyLeft" data-command="justifyLeft"><i class="fa fa-align-left"></i></a>
            <a href="javascript:void(0);" title="justifyCenter" data-command="justifyCenter"><i class="fa fa-align-center"></i></a>
            <a href="javascript:void(0);" title="justifyRight" data-command="justifyRight"><i class="fa fa-align-right"></i></a>
            <a href="javascript:void(0);" title="justifyFull" data-command="justifyFull"><i class="fa fa-align-justifyFull"></i></a>
            <a href="javascript:void(0);" title="indent" data-command="indent"><i class="fa fa-indent"></i></a>
            <a href="javascript:void(0);" title="outdent" data-command="outdent"><i class="fa fa-outdent"></i></a>
            <a href="javascript:void(0);" title="insertUnorderedList" data-command="insertUnorderedList"><i class="fa fa-list-ul"></i></a>
            <a href="javascript:void(0);" title="insertOrderedList" data-command="insertOrderedList"><i class="fa fa-list-ol"></i></a>
            <a href="javascript:void(0);" title="h1" data-command="h1">H1</a>
            <a href="javascript:void(0);" title="h2" data-command="h2">H2</a>
            <a href="javascript:void(0);" title="createlink" data-command="createlink"><i class="fa fa-link"></i></a>
            <a href="javascript:void(0);" title="unlink(filename)" data-command="unlink"><i class="fa fa-unlink"></i></a>
            <a href="javascript:void(0);" title="insertimage" data-command="insertimage"><i class="fa fa-image"></i></a>
            <a href="javascript:void(0);" title="P" data-command="p">P</a>
            <a href="javascript:void(0);" title="subscript" data-command="subscript"><i class="fa fa-subscript"></i></a>
            <a href="javascript:void(0);" title="superscript" data-command="superscript"><i class="fa fa-superscript"></i></a>
            <a href="javascript:void(0);" title="code" data-command="code"><i class="fa fa-code"></i></a>
            <a href="javascript:void(0);" title="Quote"><i class="fa fa-quote"></i></a>
            <a href="javascript:void(0);" title="hr"><i class="fa fa-hr"></i></a>
            <a href="javascript:void(0);" title="Text Filler"><i class="fa fa-lorem"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-undo"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-rotate-right"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-select"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-intro"></i></a>

             <div class="fore-wrapper closed" onclick="WIWYSIWYG.ForeWrapper()" id="fore-wrapper"><i class="fa fa-font" style="color:#C96;"></i>
              <div class="fore-palette hide" id="fore-palette">
              </div>
            </div>
            <div class="back-wrapper closed" onclick="WIWYSIWYG.BackWrapper()" id="back-wrapper"><i class="fa fa-font" style="background:#C96;"></i>
              <div class="back-palette hide" id="back-palette">
              </div>
            </div>


            <a href="javascript:void(0);" id="eye" title="Preview">
            <i class="fa fa-eye"></i></a>
            </div>
            <textarea class="editor-area" id="editor-area" placeholder="Start here..">

        <h2>Html editor </h2>

        <p>Click in eye to see the result.</p>

        <p>Graece donan, Latine voluptatem vocant. <i>Hoc sic expositum dissimile est superiori.</i> Tria genera bonorum; Nam Pyrrho, Aristo, Erillus iam diu abiecti. <b>Ratio quidem vestra sic cogit.</b> </p>


            </textarea>
          
          <div class="result"></div>
          <script type="text/javascript">';

             echo " $('.toolbar a').click(function(e) {
              console.log('clicked');

              var textarea = document.getElementById('editor-area');  
var selection = (textarea.value).substring(textarea.selectionStart,textarea.selectionEnd);
 console.log(selection);

  var command = $(this).data('command');
  console.log(command);

  if (command == 'h1' || command == 'h2' || command == 'p') {
    document.execCommand('formatBlock', false, command);
  }
  if (command == 'forecolor' || command == 'backcolor') {
    document.execCommand($(this).data('command'), false, $(this).data('value'));
  }
    if (command == 'createlink' || command == 'insertimage') {
  url = prompt('Enter the link here: ','http:\/\/'); document.execCommand($(this).data('command'), false, url);
  }

  else window.document.execCommand($(this).data('command'), false, selection);

  const newElement = document.createElement($(this).data('command'));
  newElement.append(selection);
  console.log(newElement);
        var text = $('#editor-area').html();
        var str = selection;
        str.replace(selection, newElement)
        $('#editor-area').html(text.replace(selection, newElement));

});
    


// the eye
var e = document.querySelector(`#eye`),
    i =  document.querySelector(`#editor-area`),
    o = document.querySelector(`.result`);

e.onclick = function(){ 
   o.innerHTML = i.value;
   o.classList.toggle(`show`);
   this.classList.toggle(`active`);
}

</script></div></div>";


  }


	
}


?>