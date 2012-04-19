<?php

class AdCategories
{
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public static function get_one_by_id($id, $allow_cache = true)
	{
		
		$_sql = "SELECT * FROM {{ad_categories}} WHERE ad_categories_id=:ad_categories_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_categories_id', $id);
		$_cmd->execute();
		$_r = $_cmd->queryRow();
		return $_r;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public static function Pages($params = array())
	{
		$_sql = "SELECT * FROM {{ad_categories}} ORDER BY `ad_categories_rank` ASC,`ad_categories_id` DESC";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_r = $_cmd->queryAll();
		return $_r;
	}
}