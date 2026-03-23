
<style>
.center{
 text-align: -webkit-center;
}

   .ui-tabs-vertical { width: 55em; }
  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}


</style>
<asider class="right-side">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Forum
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="javascript:void(0);"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Forum</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-12 col-xs-12 col-xl-12 col-md-12">
                          <div class="col-lg-12 col-xs-12 col-xl-12 col-md-12">
                               <button id="opencatModal">New Category</button>
                                <button id="opensectionModal">New Section</button>
                              </div>
                            <!-- input box's box -->
                            <div class="modal-body">
                              
            <div class="well">

              <?php  $forum->edit_forum(); ?>

                     </div>
                     </div>
                     </div>
                     </div>

                     </section>
                   </asider>


<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
<script type="text/javascript" src="WICore/WIJ/WIForum.js"></script>

<script type="text/javascript">
  
  $('#category_selector').on('change', function() {
            // alert( this.value );
    $("#category_selector").val(this.value).prop("selected", "selected");                      
    })
</script>

   <?php 

    $modal->moduleModal('new-cat', 'Add new category', 'WIForum', 'ForumCategory','create category'); 

    $modal->moduleModal('new-section', 'Add new section', 'WIForum', 'ForumSection','create section'); 

    $modal->moduleModal('edit-cat', 'Edit category', 'WIForum', 'ForumEditCategory','edit category'); 

    $modal->moduleModal('edit-section', 'Edit Section', 'WIForum', 'ForumEditSection','edit section'); 

    $modal->moduleModal('delete-cat', 'Delete Section', 'WIForum', 'DeleteCategory','delete category'); 
   ?>