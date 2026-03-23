<?php
#[\AllowDynamicProperties]
/**
* image Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIImage
{
       private $WIdb = null;

        function __construct() 
        {
       $this->WIdb = WIdb::getInstance();
        }


            public function AllPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading link">
        <a herf="#" onclick="WIMedia.Folder(`' . $value . '`)">' . $value . '</a></div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/' . $value .'" class="img-responsive img-media-library" style="width:50px; height:50px;"></div>
        <div class="panel-footer"></div>
        </div></div>';
        }
    }

     public function Folder($folder)
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/' . $folder. '/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading link">
        <a herf="#" onclick="WIMedia.Folder(' . $value . ')">' . $value . '</a></div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);

        echo '<div class="col-lg-9 col-md-9 col-sm-12 menu">
              <div id="nav">
               <ul id="mainMenu" class="mainMenu default">
               <li><a href="#" class="btn" onclick="WIMedia.goback()">go back</a></li>

               </ul>
              </div>
             </div>';
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/' . $folder .'/'.$value . '" class="img-responsive" style="width:50px; height:50px;"></div>
        <div class="panel-footer"></div>
        </div></div>';
        }
    }



        public function AllFolders()
        {

        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/*';

        $folder = glob(dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/*', GLOB_ONLYDIR);

       // $folders = basename($folder);

        foreach($folder as $fold => $value) {
      //  $dirname = basename($dir);
            //print_r($fold);
            $f = basename($value);
             echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">
        <a herf="#" onclick="WIMedia.Folder()">' . $f . '</a></div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }

        }

        public function HeaderPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/header/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'PNG');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/header/' . $value .'" class="img-responsive img-media-library" id="' . $value .'" style="width:50px; height:50px;"></div>
        <div class="panel-footer"></div>
        </div></div>';
        }

    }


   


    public function PagePics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/contents/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'PNG');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/header/' . $value .'" class="img-responsive img-media-library" id="' . $value .'" style="width:50px; height:50px;"></div>
        <div class="panel-footer"></div>
        </div></div>';
        }

    }

     public function ModPics($mod_name)
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/' . $mod_name .'/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'PNG');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/' . $mod_name .'/' . $value .'" class="img-responsive img-media-library" id="' . $value .'" style="width:50px; height:50px;"></div>
        <div class="panel-footer"></div>
        </div></div>';
        }

    }

public function faviconPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/favicon/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }

        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/favicon/' . $value .'" class="img-responsive img-media-library favicon" id="' . $value .'" style="width:50px; height:50px;"></div>
        </div></div>';
        }


        }


        public function floorPlanPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/pos/floor_plan';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }

        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/pos/floor_plan/' . $value .'" class="img-responsive img-media-library floor" id="' . $value .'" style="width:50px; height:50px;"></div>
        </div></div>';
        }


        }

        public function foodPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/pos/products';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }

        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/pos/products/' . $value .'" class="img-responsive img-media-library floor" id="' . $value .'" style="width:50px; height:50px;"></div>
        </div></div>';
        }


        }


        public function LangPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/lang/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/lang/' . $value .'" class="img-responsive img-media-library langFlag" id="' . $value .'" style="width:50px; height:50px;"></div>
        </div></div>';
        }


        }


        public function ProductPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/pos/products/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'PNG');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/pos/products/' . $value .'" class="img-responsive img-media-library product" id="' . $value .'" style="width:50px; height:50px;"></div>
        <div class="panel-footer"></div>
        </div></div>';
        }

    }


   

    public function TeamPic()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/team/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'PNG');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">
        <figure class="post-video"><video width="300" height="160" controls><source "WIMedia/Img/team/' . $value .'" id="teamPics" value="' . $value .'" type="video/mp4"></video></figure>
        </div>
        <div class="panel-footer"></div>
        </div></div>';
        }

    }

         public function addLangPics()
        {
        $dir = dirname(dirname(dirname(__FILE__))) . '/WIMedia/Img/lang/';
        $images = scandir($dir);

        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        // init result
        $result = array();

        // directory to scan
        $directory = new DirectoryIterator($dir);

        foreach ($images as $Img => $value) {
        if ($value === '.' or $value === '..') continue;
        if (is_dir($dir.$value)) {
        //code to use if directory
                echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body">Folder </div>
        <div class="panel-footer"></div>
        </div></div>';
        }
        
        }

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $result[] = $fileinfo->getFilename();
                }
            }
        }
        // print result
        //print_r($result);
        foreach ($result as $key => $value) {
                            echo '<div class="col-md-4">
        <div class="panel panel-info">
        <div class="panel-heading">' . $value . '</div>
        <div class="panel-body"><img src="WIMedia/Img/lang/' . $value .'" class="img-responsive img-media-library" id="' . $value .'" style="width:50px; height:50px;"></div>
        </div></div>';
        }


        }


}
