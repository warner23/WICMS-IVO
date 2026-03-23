
  <script>
  $( function() {

    var index = 'key';
    //  Define friendly data store name
    var dataStore = window.sessionStorage;
    //  Start magic!
    try {
        // getter: Fetch previous value
        var oldIndex = dataStore.getItem(index);
    } catch(e) {
        // getter: Always default to first tab in error state
        var oldIndex = 0;
    }
    $( "#tabs" ).tabs({
        // The zero-based index of the panel that is active (open)
        active : oldIndex,
        // Triggered after a tab has been activated
        activate : function( event, ui ){
            //  Get future value
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            //  Set future value
            dataStore.setItem( index, newIndex ) 
        }
    }); 

    
    });
  </script>

 <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Martial Arts
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Martial Arts</li>
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

                  <?php $martial->MMA(); ?>


                     </div>
                     </div>
                     </div>
                     </div>

                     </section>
<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMMA.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMedia.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMediaCenter.js"></script>
   
<?php
// moves modals
$modal->moduleModal('mma-add', 'Add Moves', 'WIMMA', 'createMove','Save','');  
$modal->moduleModal('mma-media', 'Change Media', 'WIMMA', 'mmavideos','Save' , ''); 
$modal->moduleModal('mma-upload', 'Upload Media', 'WIMMA', 'Uploadmmavideos','Save', 'mmaMoves'); 
//belts modals
$modal->moduleModal('mma-add-belts', 'Add Belts', 'WIMMA', 'createBelt','Save','ma_id');  

//martial arts modals
$modal->moduleModal('mma-add-ma', 'Add Martial Arts', 'WIMMA', 'createMa','Save','');  
 


?>