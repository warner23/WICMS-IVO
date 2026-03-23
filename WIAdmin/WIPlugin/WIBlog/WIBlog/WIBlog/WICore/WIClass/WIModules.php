<?php

class WIModules
{

    public function __construct() {
        $this->WIdb = WIdb::getInstance();
    }

   
     public function columns()
     {
        $mod_name = "columns";
        $mod_status = "enabled";
        $sql = "SELECT * FROM `wi_mod` WHERE module_name = :mod_name AND mod_status = :mod_status";
        $query = $this->WIdb->prepare($sql);
        $query->bindParam(':mod_name', $mod_name, PDO::PARAM_STR);
        $query->bindParam(':mod_status', $mod_status, PDO::PARAM_STR);
        $query->execute();

        $res = $query->fetch(PDO::FETCH_ASSOC);

        if ($res > 0)
            return true;
        else
            return false;

     }

     public function CheckModPower($modName)
     {
        
     }

         public function getMod($mod)
    {
        //echo $mod;
        //echo   'WIAdmin/WIModule/' .$mod.'/'.$mod.'.php';
        $dir = dirname(dirname(dirname(__DIR__)));
        require_once  $dir .'/WIAdmin/WIModule/pages/' .$mod.'/'.$mod.'.php';
        
       // echo $mod;
        $mod = new $mod;

        $mod->mod_name();
    }



    public function getModMain($mod, $page, $module)
    {
        $dir = dirname(dirname(dirname(__DIR__)));
        require_once  $dir .'/WIAdmin/WIModule/pages/' .$mod.'/'.$mod.'.php';
        
        $mod = new $mod;
        $mod->mod_name($module, $page);


    }

            public function moduleImg($page_id, $column)
    {
        $sql1 = "SELECT * FROM `wi_modules` WHERE `name`=:name";
        $query1 = $this->WIdb->prepare($sql1);
        $query1->bindParam(':name', $page_id, PDO::PARAM_STR);
        $query1->execute();

        $res = $query1->fetch(PDO::FETCH_ASSOC);
            echo '<img src="WIAdmin/WIMedia/Img/'. $res[$column] . '.PNG" class="img">';


    }

        public function module($page_id, $column)
    {
        $id = "1";
        $name = $page_id;
        //echo $name;
        $sql = "SELECT `multi_lang` FROM `wi_site` WHERE `id` =:id";

        $query = $this->WIdb->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch();
        //echo $result['multi_lang'];
        $mlang = $result['multi_lang'];
        
   // echo $mlang;

        $sql1 = "SELECT * FROM `wi_modules` WHERE `name`=:name";
        $query1 = $this->WIdb->prepare($sql1);
        $query1->bindParam(':name', $name, PDO::PARAM_STR);
        $query1->execute();

        $res = $query1->fetch(PDO::FETCH_ASSOC);
        //print_r($res);

        if ($column === "text") {
            $trans = "trans";
        }elseif ($column === "text1") {
            $trans = "trans1";
        }elseif ($column === "text2") {
            $trans = "trans2";
        }elseif ($column === "text3") {
            $trans = "trans3";
        }elseif ($column === "text4") {
            $trans = "trans4";
        }elseif ($column === "text5") {
            $trans = "trans5";
        }elseif ($column === "text6") {
            $trans = "trans6";
        }
        //echo $trans;
        $lange = $res[$trans];
        $text  = $res[$column];

       // echo $lange;
        if ($mlang === "off"){
            echo $text;
        }else{
            echo WILang::get($lange);
        }

    }


    public function moduleText($page_id, $column)
    {
        $id = "1";
        $name = $page_id;
        //echo $name;
        $sql = "SELECT `multi_lang` FROM `wi_site` WHERE `id` =:id";

        $query = $this->WIdb->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch();
        //echo $result['multi_lang'];
        $mlang = $result['multi_lang'];
        
   // echo $mlang;

        $sql1 = "SELECT * FROM `wi_modules` WHERE `name`=:name";
        $query1 = $this->WIdb->prepare($sql1);
        $query1->bindParam(':name', $name, PDO::PARAM_STR);
        $query1->execute();

        $res = $query1->fetch(PDO::FETCH_ASSOC);
        //print_r($res);

        if ($column === "text") {
            $trans = "trans";
        }elseif ($column === "text1") {
            $trans = "trans1";
        }elseif ($column === "text2") {
            $trans = "trans2";
        }elseif ($column === "text3") {
            $trans = "trans3";
        }elseif ($column === "text4") {
            $trans = "trans4";
        }elseif ($column === "text5") {
            $trans = "trans5";
        }elseif ($column === "text6") {
            $trans = "trans6";
        }
        //echo $trans;
        $lange = $res[$trans];
        $text  = $res[$column];

       // echo $lange;
        if ($mlang === "off"){
            echo $text;
        }else{
            echo WILang::getTranslation($lange);
        }

    }

    public function ModName($page_id)
    {
        echo $page_id;
        $sql = "SELECT * FROM `wi_page` WHERE `name`=:page";

        $query = $this->WIdb->prepare($sql);
        $query->bindParam(':page', $page_id, PDO::PARAM_STR);
        $query->execute();

        $res = $query->fetch(PDO::FETCH_ASSOC);

        $mod_name = $res['contents'];

        return $mod_name;
    }

    public function save_mod( $mod_name, $contents, $content)
    {
        $modules = $this->WIdb->select("SELECT * FROM `wi_mods`
                     WHERE `module_name` = :n",
                     array(
                       "n" => $mod_name
                     ));

        if(count($modules) >0){
            $id = $modules[0]['module_id'];
                $this->WIdb->update(
                    "wi_mods", 
                    array("module" => $content, 
                        "edit_module" => $contents 
                    ), 
                    "`module_id` = :id",
                    array( "id" => $id )
               );
        }else{
            $this->WIdb->insert('wi_mods', array(
            "module_name"     => $mod_name,
            "module"     => $content,
            "edit_module"     => $contents
        ));

        }

        $pages = $this->WIdb->select("SELECT * FROM `wi_pages`
                     WHERE `page_name` = :n",
                     array(
                       "n" => $mod_name
                     ));

        if(count($pages) >0){

            $id = $pages[0]['page_id'];
                $this->WIdb->update(
                    "wi_pages", 
                    array("pagemod" => $content, 
                        "edit_page_mod" => $contents,
                        "page_status" => "enabled"
                    ), 
                    "`page_id` = :id",
                    array( "id" => $id )
               );
        }else if(count($pages) >0){

            $id = $pages[0]['page_id'];
                $this->WIdb->update(
                    "wi_pages", 
                    array("pagemod" => $content, 
                        "edit_page_mod" => $contents,
                        "page_status" => "enabled"
                    ), 
                    "`page_id` = :id",
                    array( "id" => $id )
               );
        }else{
            $this->WIdb->insert('wi_pages', array(
            "page_name"     => $mod_name,
            "pagemod"     => $content,
            "edit_page_mod" => $contents
        ));

        }

        $file_saved = self::saving_mod($mod_name, $contents, $content);
        if($file_saved == "1")
        {
            $msg = "Successfully created Module and saved as file";

    $result = array(
                "status" => "success",
                "msg"    => $msg
            );
            
            echo json_encode($result);
        }else{


        $msg = "Successfully created Module";

    $result = array(
                "status" => "success",
                "msg"    => $msg
            );
            
            echo json_encode($result);
        }
    }


    public function saving_mod($mod_name)
    {
      
        $dir = dirname(dirname(dirname(dirname(__FILE__)))) .'/WIAdmin/WIModule/pages/' .$mod_name;
        echo $dir;
        if (!file_exists($dir)) {
                  mkdir($dir, 0777, true);
        }

      $NewPage = fopen($dir. '/' .$mod_name . '.php', "w") or die("Unable to open file!");

      $File = 
'<?php

/**
* 
*/
class ' . $mod_name . '
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
    }

    public function editMod()
    {
        
    $this->Web->EditModTemp();
     $result = $this->WIdb->select("SELECT `edit_page_mod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page,));
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["edit_page_mod"];
        }
 
    }

    public function editPageContent($page)
    {
      $result = $this->WIdb->select("SELECT `edit_page_mod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page,));
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["edit_page_mod"];
        }

    }

    public function mod_name($module, $page)
    {
        if(isset($page)){
        $left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
        $leftSideBar = $this->Web->PageMod($page, "left_sidebar");
        if ($left_sidePower === "0") {
            
        }else{

            $this->mod->getMod($leftSideBar);
        }
        }

        $result = $this->WIdb->select("SELECT `pagemod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page) );
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["pagemod"];
        }
        

      if(isset($page)){         
        $right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
        $rightSideBar = $this->Web->PageMod($page, "right_sidebar");
        //echo $Panel;
        if ($right_sidePower === "0") {
            
        }else{

            $this->mod->getMod($rightSideBar);
        }

        }           
                    

    echo "</div>
            </div>";
    }  
}';


     
      fwrite($NewPage, $File);
      fclose($NewPage);
    

      return "1";
    }






}