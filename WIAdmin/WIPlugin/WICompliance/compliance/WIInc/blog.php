
  <script>
  $( function() {
    $( "#tabs" ).tabs();
  } );
  </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style type="text/css">
  .blog_type{
    float: left;
    width: 12%;
    margin-left: 15px;
        list-style: none;
  }

  .blogSelect{
    width: 9%;
    margin: 0px 0px 0px 450px;
        cursor: pointer;
  }

  .admin_post{
        width: 18%;
    height: 44px;
    margin-left: 401px;
  }

  .block{
    display: block;
  }

  .hidden{
    display: none;
  }

  #wivid{
        width: 49%;
    height: 137px;
  }
</style>
 <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Blog
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Site</li>
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

                <div class="type">
<div class="blogSelect" id="blog_post" title="Choose Post" onclick="WIBlog.type()">
<i class="fa fa-th"></i>
</div>  

<div class="admin_post hidden">
<ul>
  <li class="blog_type" title="Blog post no media"><a href="javascript:void(0)"  onclick="WIBlog.noMedia(`';echo $this->user->getRole(); echo'`)"><i class="fa fa-file-text" aria-hidden="true"></i></a></li>
  <li class="blog_type" title="Blog post slider"><a href="javascript:void(0)" onclick="WIBlog.slider(`'; echo $this->user->getRole(); echo'`)"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>
  <li class="blog_type" title="Blog post video"><a href="javascript:void(0)" onclick="WIBlog.video(`'; echo $this->user->getRole(); echo'`)"><i class="fa fa-list-alt" aria-hidden="true"></i></a></li>
  <li class="blog_type" title="Blog post audio"><a href="javascript:void(0)"  onclick="WIBlog.audio(`'; echo $this->user->getRole(); echo '`)"><i class="fa fa-file-audio-o" aria-hidden="true"></i></a></li>
  <li class="blog_type" title="Blog post image"><a href="javascript:void(0)" onclick="WIBlog.image(`'; echo $this->user->getRole(); echo '`)"><i class="fa fa-picture-o" aria-hidden="true"></i></a></li>
  <li class="blog_type" title="Blog post youtube"><a href="javascript:void(0)" onclick="WIBlog.youtube(`';echo $this->user->getRole(); echo '`)"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
</ul>
</div>
  </div>


  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">  
  
  <div class="blog_style" id="blog_style">
    
  </div>  

  <div class="blog_style" id="posts">             
            
  </div>      
  
        
            </div>  
            </div>


                     </div>
                     </div>
                     </div>
                     </div>

                     </section>

     <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>

     <script type="text/javascript" src="WICore/WIJ/WIBlog.js"></script>

<script type="text/javascript" src="WICore/WIJ/WIMediaCenter.js"></script>
<script type="text/javascript" src="WICore/WIJ/WIMedia.js"></script>
<script type="text/javascript" src="WICore/WIJ/WISlideMedia.js"></script>
 