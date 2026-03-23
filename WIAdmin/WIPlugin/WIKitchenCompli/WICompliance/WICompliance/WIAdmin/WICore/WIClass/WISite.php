<?php
#[\AllowDynamicProperties]
/**
* Site Class
* Created by Warner Infinity
* Author Jules Warner
*/
class WISite 
{
	private $WIdb = null;

	public function __construct()
	{
		$this->WIdb = WIdb::getInstance();

	}	

	public function Website_Info($column) 
	{

		$user_id = 1;

		$result = $this->WIdb->selectColumn('SELECT * FROM `wi_site` WHERE `id` = :user_id', array('user_id' => $user_id), $column);

		return  $result;
	}


		public function Theme_Info($column) 
	{

		$theme_id = 1;

		$result = $this->WIdb->selectColumn('SELECT * FROM `wi_theme` WHERE `id` = :theme_id', array('theme_id' => $theme_id), $column);
		return $result;
	}

		public function UserPostDisplay()
	{

		echo '<div id="dragandrophandler">Drag & Drop Files Here</div>
        <div class="img-preview" id="preview"></div>
        <div class="upload-msg" id="status"></div>
        </figure>
        <input type="hidden" name="supload" id="supload" value="userpost">';
	}


	

}

?>
