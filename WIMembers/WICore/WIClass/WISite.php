<?php
#[\AllowDynamicProperties]
/**
* Database Class
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

		$query = $this->WIdb->prepare('SELECT * FROM `wi_theme` WHERE `id` = :theme_id');
		$query->bindParam(':theme_id', $theme_id, PDO::PARAM_STR);
		$query->execute();

		$res = $query->fetch(PDO::FETCH_ASSOC);

		return $res[$column];
	}



		public function avatarDisplay()
	{

		echo '<style>
		#dragandrophandler {
    border: 2px dotted #0B85A1;
    width: 400px;
    color: #92AAB0;
    text-align: left;
    vertical-align: middle;
    padding: 10px 10px 10 10px;
    margin-bottom: 10px;
    font-size: 200%;
     }
</style>
<div id="dragandrophandler">Drag & Drop Files Here</div>
        <div class="img-preview" id="preview"></div>
        <div class="upload-msg" id="status"></div>
        </figure>
        <input type="hidden" name="supload" id="supload" value="avatar">';
	}


		public function Countries()
	{
		$query = $this->WIdb->prepare('SELECT * FROM `wi_countries`');
		$query->execute();

		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		echo '<select id="countries">';
		foreach ($result as $res) {
			echo '<option value="' . $res['country_code'] . '" title="' . $res['country_name'].'">' . $res['country_name'].'</option>';
		}
		echo '</select>';

	}

		public function Country($id, $country)
	{
		$query = $this->WIdb->prepare('SELECT * FROM `wi_countries`');
		$query->execute();

		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		echo '<select id="countries' . $id . '" class="countries">';
		foreach ($result as $res) {
			if ($res['country_name'] === $country) {

				echo '<option value="' . $res['country_code'] . '" title="' . $res['country_name'].'" selected="true">' . $res['country_name'].'</option>';
			}else{
				echo '<option value="' . $res['country_code'] . '" title="' . $res['country_name'].'">' . $res['country_name'].'</option>';
			}
			
		}
		echo '</select>';

	}
	

}

?>
