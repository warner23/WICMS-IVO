  <form class="form-horizontal" id="extra">
     <fieldset>
                      <div id="legend">
                        <legend class="">Add / Edit Extra's</legend>
                      </div>  


                       <div class="col-sm-12 col-lg-12 col-md-12 col-xs-12">
                          <a href="javascript:void(0);" onclick="WIProducts.addProduct();">
                          <div class="btn btn-sm btn-primary">
                            Add
                          </div>
                        </a>
                        <div id="getExtras"> </div>
                       </div>

      </fieldset>

  </form>
<?php
 $modal->moduleModal('product-edit', 'Change Product', 'WIMedia', 'changeProductPic','Save', ''); 
 $modal->moduleModal('product-media', 'Change Media', 'WIMedia', 'ProductPics','Save', ''); 
 $modal->moduleModal('product-upload', 'Upload Media', 'WIMedia', 'UploadProductPics','Save', ''); 
  $modal->moduleModal('product-delete', 'Delete Product', 'WIPos', 'delete','Delete', 'product-delete'); 
?>
                       
                     

                    

                        