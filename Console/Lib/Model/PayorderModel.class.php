<?php
class PayorderModel extends RelationModel
{
	protected $_link = array('User' => array('mapping_type' => BELONGS_TO, 'foreign_key' => 'uid', 'mapping_name' => 'user'));

}