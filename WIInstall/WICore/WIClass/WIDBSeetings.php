<?php



class WIDBSeetings
{
	
	public function TestDB($HOST, $DB, $USER, $PASS)
	{
		$MYSQL = "mysql";
		WISession::set("type", $MYSQL);
		WISession::set("HOST", $HOST);
		WISession::set("DB", $DB);
		WISession::set("USER", $USER);
		WISession::set("PASS", $PASS);
        if (!preg_match('/^[A-Za-z0-9_]+$/', $DB)) {
            die(json_encode(array('outcome' => false, 'message' => 'Database name may only contain letters, numbers and underscores.')));
        }
        try{
            $dbh = WIdb::createInstallServerConnection($HOST, $USER, $PASS);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbname = '`' . str_replace('`', '``', $DB) . '`';
            $dbh->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8 COLLATE utf8_unicode_ci");
            $dbh->exec("USE $dbname");
            die(json_encode(array('outcome' => true)));
        }
        catch(PDOException $ex){
            die(json_encode(array('outcome' => false, 'message' => 'Unable to connect or create database: ' . $ex->getMessage())));
        }
	}
			
}