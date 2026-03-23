<?php
#[\AllowDynamicProperties]

class WYSIWYG
{
	function __contruct(){
	    $this->WIdb = WIdb::getInstance();
	}

	public function Paragraph()
	{
		echo '<div class="toolbar">
  <a href="#" data-command="undo"><i class="fa fa-undo"></i></a>
  <a href="#" data-command="redo"><i class="fa fa-repeat"></i></a>
  <div class="fore-wrapper"><i class="fa fa-font" style="color:#C96;"></i>
    <div class="fore-palette">
    </div>
  </div>
  <div class="back-wrapper"><i class="fa fa-font" style="background:#C96;"></i>
    <div class="back-palette">
    </div>
  </div>
  <a href="#" data-command="bold"><i class="fa fa-bold"></i></a>
  <a href="#" data-command="italic"><i class="fa fa-italic"></i></a>
  <a href="#" data-command="underline"><i class="fa fa-underline"></i></a>
  <a href="#" data-command="strikeThrough"><i class="fa fa-strikethrough"></i></a>
  <a href="#" data-command="justifyLeft"><i class="fa fa-align-left"></i></a>
  <a href="#" data-command="justifyCenter"><i class="fa fa-align-center"></i></a>
  <a href="#" data-command="justifyRight"><i class="fa fa-align-right"></i></a>
  <a href="#" data-command="justifyFull"><i class="fa fa-align-justify"></i></a>
  <a href="#" data-command="indent"><i class="fa fa-indent"></i></a>
  <a href="#" data-command="outdent"><i class="fa fa-outdent"></i></a>
  <a href="#" data-command="insertUnorderedList"><i class="fa fa-list-ul"></i></a>
  <a href="#" data-command="insertOrderedList"><i class="fa fa-list-ol"></i></a>
  <a href="#" data-command="h1">H1</a>
  <a href="#" data-command="h2">H2</a>
  <a href="#" data-command="createlink"><i class="fa fa-link"></i></a>
  <a href="#" data-command="unlink"><i class="fa fa-unlink"></i></a>
  <a href="#" data-command="insertimage"><i class="fa fa-image"></i></a>
  <a href="#" data-command="p">P</a>
  <a href="#" data-command="subscript"><i class="fa fa-subscript"></i></a>
  <a href="#" data-command="superscript"><i class="fa fa-superscript"></i></a>
</div>
<div id="editor" contenteditable>
  <h1>A WYSIWYG Editor.</h1>
  <p>Try making some changes here. Add your own text or maybe an image.</p>
  <p>
    Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
    ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
    amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
    odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
    </p>
</div>';
	}
}

public function test()
{
  echo '<style class="cp-pen-styles">
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
</style></head><body>
<div class="editor">
    <div class="editor-control" id="editor-control">
      <a href="#bold">
        <i class="fa fa-bold"></i>
      </a>
      <a href="#italic">
        <i class="fa fa-italic"></i>
      </a>
      <a href="#code">
        <i class="fa fa-code"></i>
      </a>
      <a href="#quote">Q</a>
      <a href="#li">
        <abbr title="List">LI</abbr>
      </a>
      <a href="#ul-list">
        <i class="fa fa-list-ul"></i>
      </a>
      <a href="#ol-list">
        <i class="fa fa-list-ol"></i>
      </a>
      <a href="#link">
        <i class="fa fa-chain"></i>
      </a>
      <a href="#image">
        <i class="fa fa-image"></i>
      </a>
      <a href="#h1">H1</a>
      <a href="#h2">H2</a>
      <a href="#h3">H3</a>
      <a href="#p">
        <i class="fa fa-paragraph"></i>
      </a>
      <a href="#hr">-</a>
      <a href="#lorem">Lorem</a>
      <a href="#undo">
        <i class="fa fa-undo"></i>
      </a>
      <a href="#redo">
        <i class="fa fa-rotate-right"></i>
      </a>
      <a href="#select">Sel</a>
      <a href="#return">intro</a>
      <a href="#"  id="eye" title="Preview">
        <i class="fa fa-eye"></i>
      </a>
    </div>

    <textarea class="editor-area" id="editor-area" placeholder="Start here..">

<h2>Html editor </h2>

<p>Click in eye to see the result.</p>

<p>Graece donan, Latine voluptatem vocant. <i>Hoc sic expositum dissimile est superiori.</i> Tria genera bonorum; Nam Pyrrho, Aristo, Erillus iam diu abiecti. <b>Ratio quidem vestra sic cogit.</b> </p>


    </textarea>
  
  <div class="result"></div>
</div>';
}


?>