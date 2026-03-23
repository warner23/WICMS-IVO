<?php

include_once "WICore/init.php";

if($admin->isAdmin() ){
include_once "WIInc/WI_start_up.php";
include_once "WIInc/WI_header.php";
include_once "WIInc/sidebar.php";
include_once "WIInc/meet_team.php";
}else{
header("location:../index.php");
}
?>

</body>
</html>

      