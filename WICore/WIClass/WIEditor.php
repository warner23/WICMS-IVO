<?php
/**
 * cafe class.
 */
#[\AllowDynamicProperties]
class WIEditor 
{

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->site = new WISite();
    }


    public function WIEdit()
    {
     echo '<div class="editor">
            <div class="editor-control" id="editor-control">
            <div class="toolbar" id="toolbar">
              <a href="javascript:void(0);" data-command="undo" class="btn tile"><i class="fa fa-undo"></i></a>
            <a href="javascript:void(0);" data-command="redo" class="btn tile"><i class="fa fa-repeat"></i></a>
            <div class="fore-wrapper closed" onclick="WIWYSIWYG.ForeWrapper()" id="fore-wrapper"><i class="fa fa-font" style="color:#C96;"></i>
              <div class="fore-palette hide" id="fore-palette">
              </div>
            </div>
            <div class="back-wrapper closed" onclick="WIWYSIWYG.BackWrapper()" id="back-wrapper"><i class="fa fa-font" style="background:#C96;"></i>
              <div class="back-palette hide" id="back-palette">
              </div>
            </div>
            <a href="javascript:void(0);" data-command="bold" class="btn tile" title="Bold">
            <i class="fa fa-bold"></i>
            </a>
            <a href="javascript:void(0);" data-command="italic" class="btn tile" title="Italic">
            <i class="fa fa-italic"></i>
            </a>
            <a href="javascript:void(0);" data-command="underline" class="btn tile" title="underline">
            <i class="fa fa-underline"></i>
            </a>
            <a href="javascript:void(0);" data-command="strikeThrough" class="btn tile">
            <i class="fa fa-strikethrough"></i>
            </a>
            <a href="javascript:void(0);" data-command="justifyLeft" class="btn tile"><i class="fa fa-align-left"></i></a>
            <a href="javascript:void(0);" data-command="justifyCenter" class="btn tile"><i class="fa fa-align-center"></i></a>
            <a href="javascript:void(0);" data-command="justifyRight" class="btn tile"><i class="fa fa-align-right"></i></a>
            <a href="javascript:void(0);" data-command="justifyFull" class="btn tile"><i class="fa fa-align-justifyFull"></i></a>
            <a href="javascript:void(0);" data-command="indent" class="btn tile"><i class="fa fa-indent"></i></a>
            <a href="javascript:void(0);" data-command="outdent" class="btn tile"><i class="fa fa-outdent"></i></a>
            <a href="javascript:void(0);" data-command="insertUnorderedList" class="btn tile"><i class="fa fa-list-ul"></i></a>
            <a href="javascript:void(0);" data-command="insertOrderedList" class="btn tile"><i class="fa fa-list-ol"></i></a>
            <a href="javascript:void(0);" data-command="h1" class="btn tile">H1</a>
            <a href="javascript:void(0);" data-command="h2" class="btn tile">H2</a>
            <a href="javascript:void(0);" data-command="createlink" class="btn tile"><i class="fa fa-link"></i></a>
            <a href="javascript:void(0);" data-command="unlink" class="btn tile"><i class="fa fa-unlink"></i></a>
            <a href="javascript:void(0);" data-command="insertimage" class="btn tile"><i class="fa fa-image"></i></a>
            <a href="javascript:void(0);" data-command="p" class="btn tile">P</a>
            <a href="javascript:void(0);" data-command="subscript" class="btn tile"><i class="fa fa-subscript"></i></a>
            <a href="javascript:void(0);" data-command="superscript" class="btn tile"><i class="fa fa-superscript"></i></a>
            <a href="javascript:void(0);" data-command="code" class="btn tile"><i class="fa fa-code"></i></a>
            <a href="javascript:void(0);">
            <i class="fa fa-quote" class="btn tile"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fa fa-hr"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fa fa-lorem"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fa fa-undo"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fa fa-rotate-right"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fa fa-select"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fa fa-intro"></i>
            </a>
            <a href="javascript:void(0);" class="btn tile">
            <i class="fas fa-brackets-curly"></i>
            </a>
            <a href="javascript:void(0);" data-command="actions" class="btn tile"><i class="fa fa-code"></i></a>

            <a href="javascript:void(0);" id="eye" title="Preview" class="btn tile" title="Eye">
            <i class="fa fa-eye"></i></a>
            </div>
            <textarea class="editor-area" id="editor-area" placeholder="Start here.." rows="8" cols="65">

        <h2>Html editor </h2>

        <p>Click in eye to see the result.</p>

        <p>Graece donan, Latine voluptatem vocant. <i>Hoc sic expositum dissimile est superiori.</i> Tria genera bonorum; Nam Pyrrho, Aristo, Erillus iam diu abiecti. <b>Ratio quidem vestra sic cogit.</b> </p></textarea>
          
          <div class="result"></div>
          <script type="text/javascript">';

             echo " $('.toolbar a').click(function(e) {
              console.log('clicked');


              var textarea = document.getElementById('editor-area');  
            var selection = (textarea.value).substring(textarea.selectionStart,textarea.selectionEnd);
             console.log(selection);

              var command = $(this).data('command');
              console.log(command);

              if(command == 'Code'){
                const newElement = ". $this->site->Website_Info('site_name') . "
              
                var text = $('#editor-area').html();
                
                $('#editor-area').html(text.replace(selection, newElement.outerHTML));

                }else if(command == 'actions'){
                    
                }
                else{
                  const newElement = document.createElement($(this).data('command'));
              newElement.append(selection);
              console.log(newElement);
                var text = $('#editor-area').html();
                //var markUp = text.replace(selection, newElement.outerHTML);
                //console.log(markUp);
                $('#editor-area').html(text.replace(selection, newElement.outerHTML));
                }

              

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