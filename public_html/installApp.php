<?php

/*
 * Before running this script, make sure you add your
 * DB credentials in the config.ini file. This script
 * will create whatever DB you specify in the config file.
 *
 * After the app has been installed, this file should be
 * deleted or moved out of the public_html directory! If
 * the file is left, it could be a security issue.
 */


define('APPLICATION_PATH', __DIR__ . '/..');


// Gather the needed files
require sprintf('%s/environment.inc', APPLICATION_PATH);
require sprintf('%s/Model/Config.php', APPLICATION_PATH);
require sprintf('%s/Model/Db.php', APPLICATION_PATH);

use Model\Config;
use Model\Db;

// Setup the Config credentials
Config::init(sprintf('%s/config.ini', APPLICATION_PATH));


echo '<strong>Results:</strong><br>';


// First we'll create the database
try {
    $conn = new PDO(
        sprintf(
            'mysql:host=%s;dbname=%s',
            Config::$instance->db->host,
            'mysql'
        ),
        Config::$instance->db->user,
        Config::$instance->db->pass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE " . Config::$instance->db->name;
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "Database created successfully<br>";
} catch(\PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// setup the DB connection
Db::setup();



// Now we'll create the DB tables
$sql = [
    'config' => 'CREATE TABLE `config` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `item` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `amount` int(22) DEFAULT NULL,
                  `updated` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    'repos'   => 'CREATE TABLE `repos` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `repo_id` int(22) unsigned NOT NULL,
                  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `stars` int(22) DEFAULT NULL,
                  `pushed` datetime DEFAULT NULL,
                  `created` datetime DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
];

foreach ($sql as $table => $s) {
    try {
        $stmt = Db::$pdo->prepare($s);
        $stmt->execute();
        echo $table . ' - table has been created.<br>';
    } catch(\PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

echo '<br>';


// Now let's install composer
$installComposer = sprintf(
    'cd %1$s; COMPOSER_HOME="%1$s" php composer.phar install 2>&1',
    APPLICATION_PATH
);

exec( $installComposer, $output );


foreach ($output as $line) {
    echo $line . '<br>';
}

// Done!