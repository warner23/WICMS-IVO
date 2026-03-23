
      

 <script>
  $( function() {
    $( "#tabs4" ).tabs();
  } );
  </script>
 <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        WIForum Options
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">WIForum Options</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-xs-6 col-xl-12">
                            <!-- input box's box -->
                            <div class="modal-body">

            <div class="well">


                     <div id="tabs4">
  <ul>
    <li><a href="#tabs-1">options</a></li>
    <li><a href="#tabs-2">permissions</a></li>
  </ul>
  <div id="tabs-1">
<?php include_once "WIInc/site/WIForum/options.php"; ?>  
  </div>
  <div id="tabs-2">
<?php include_once "WIInc/site/WIForum/permissions.php"; ?> 
  </div>
</div>
                     </div>
                     </div>
                     </div>
                     </div>

                     </section>
<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
      