 <aside class="right-side">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Forum
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Forum</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-xs-6 col-xl-12">
                            <!-- input box's box -->
                            <div class="modal-body">
                                <button id="opencatModal">New Category</button>
            <div class="well">

                <?php $forum->WIEditCategories();  ?>

                     </div>
                     </div>
                     </div>
                     </div>

                     </section>
                 </aside>

    
   <?php 

    $modal->moduleModal('new-cat', 'Add new category', 'WIForum', 'forum_cat','create category'); 
   ?>