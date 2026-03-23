<?php


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
          position:relative;
          display: block;
          width: 600px;
          margin: 10% auto;
          background: #F0F0F0;
          height: 300px;
          overflow: hidden;
          border: 1px solid #DFDFDF;
        }
        .editor .editor-area {
          display: block;
          width: 580px;
          margin: 5px;
          padding: 5px;
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
        </style>
        <div class="editor">
            <div class="editor-control" id="editor-control">
            <div class="toolbar" id="toolbar">
              <a href="javascript:void(0);" data-command="undo"><i class="fa fa-undo"></i></a>
            <a href="javascript:void(0);" data-command="redo"><i class="fa fa-repeat"></i></a>
            <div class="fore-wrapper closed" onclick="WIWYSIWYG.ForeWrapper()" id="fore-wrapper"><i class="fa fa-font" style="color:#C96;"></i>
              <div class="fore-palette hide" id="fore-palette">
              </div>
            </div>
            <div class="back-wrapper closed" onclick="WIWYSIWYG.BackWrapper()" id="back-wrapper"><i class="fa fa-font" style="background:#C96;"></i>
              <div class="back-palette hide" id="back-palette">
              </div>
            </div>
            <a href="javascript:void(0);" data-command="bold"><i class="fa fa-bold"></i></a>
            <a href="javascript:void(0);" data-command="italic"><i class="fa fa-italic"></i></a>
            <a href="javascript:void(0);" data-command="underline"><i class="fa fa-underline"></i></a>
            <a href="javascript:void(0);" data-command="strikeThrough"><i class="fa fa-strikethrough"></i></a>
            <a href="javascript:void(0);" data-command="justifyLeft"><i class="fa fa-align-left"></i></a>
            <a href="javascript:void(0);" data-command="justifyCenter"><i class="fa fa-align-center"></i></a>
            <a href="javascript:void(0);" data-command="justifyRight"><i class="fa fa-align-right"></i></a>
            <a href="javascript:void(0);" data-command="justifyFull"><i class="fa fa-align-justifyFull"></i></a>
            <a href="javascript:void(0);" data-command="indent"><i class="fa fa-indent"></i></a>
            <a href="javascript:void(0);" data-command="outdent"><i class="fa fa-outdent"></i></a>
            <a href="javascript:void(0);" data-command="insertUnorderedList"><i class="fa fa-list-ul"></i></a>
            <a href="javascript:void(0);" data-command="insertOrderedList"><i class="fa fa-list-ol"></i></a>
            <a href="javascript:void(0);" data-command="h1">H1</a>
            <a href="javascript:void(0);" data-command="h2">H2</a>
            <a href="javascript:void(0);" data-command="createlink"><i class="fa fa-link"></i></a>
            <a href="javascript:void(0);" data-command="unlink"><i class="fa fa-unlink"></i></a>
            <a href="javascript:void(0);" data-command="insertimage"><i class="fa fa-image"></i></a>
            <a href="javascript:void(0);" data-command="p">P</a>
            <a href="javascript:void(0);" data-command="subscript"><i class="fa fa-subscript"></i></a>
            <a href="javascript:void(0);" data-command="superscript"><i class="fa fa-superscript"></i></a>
            <a href="javascript:void(0);" data-command="code"><i class="fa fa-code"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-quote"></i></a>
            <a href="javascript:void(0);"<i class="fa fa-hr"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-lorem"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-undo"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-rotate-right"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-select"></i></a>
            <a href="javascript:void(0);"><i class="fa fa-intro"></i></a>
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