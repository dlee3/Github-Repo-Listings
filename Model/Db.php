<?php



namespace Model;



//use Model\Config;
use \PDO;



class Db
{
	static $pdo = NULL;


	/**
	 * use:
	 * R::dispense('book');
	 */
//	static function setup ()
//	{
//		define( 'REDBEAN_MODEL_PREFIX', '\\Model\\' );
//
//		require_once APPLICATION_PATH . '/vendor/redbeanphp/rb.php';
//		R::setup(
//			sprintf(
//				'mysql:host=%s;dbname=%s',
//				Config::$instance->db->host,
//				Config::$instance->db->name
//			),
//			Config::$instance->db->user,
//			Config::$instance->db->pass
//		);
//	}



	/**
	 * use:
	 * Db::pdo()
	 * @return pdo object
	 */
	 public static function setup()
	 {
	 	if ( NULL === self::$pdo) {
	 		self::$pdo = new PDO(
	 			sprintf(
	 				'mysql:host=%s;dbname=%s',
	 				Config::$instance->db->host,
	 				Config::$instance->db->name
	 			),
	 			Config::$instance->db->user,
	 			Config::$instance->db->pass
	 		);

	 		self::$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	 		self::$pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

	 	}


	 	return self::$pdo;
	 }




} // Db



