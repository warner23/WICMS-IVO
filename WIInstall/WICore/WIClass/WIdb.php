<?php

class WIdb extends PDO
{
    protected $debug = false;
    private static $_instance;


    /**
     * Class constructor
     * Parameters defined as constants in WIConfig.php file
     * @param $type string Database type
     * @param $host string Database host
     * @param $databaseName string Database username
     * @param $username string User's username
     * @param $password string Users's password
     */
    public function __construct($type, $host, $databaseName, $username, $password)
    {
        $dsn = $type . ':host=' . $host;
        if ($databaseName !== null && $databaseName !== '') {
            $dsn .= ';dbname=' . $databaseName;
        }
        $dsn .= ';charset=utf8';
        parent::__construct($dsn, $username, $password);
        $this->exec('SET CHARACTER SET utf8');

        if ($this->debug) {
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
    }

    /**
     * Enable/disable debug for database queries.
     * @param $debug boolean TRUE to enable debug, FALSE otherwise.
     */
    public function debug($debug)
    {
        $this->debug = $debug;
    }


        // this function creates an instance of  WIdb


    /**
     * Create a temporary installer connection using the WIdb layer.
     *
     * The installer runs before the final WIConfig constants are guaranteed to
     * exist, so it cannot safely call getInstance().  This still keeps all
     * database work inside WIdb instead of opening a raw connection in the
     * installer class.
     */
    public static function createInstallConnection(string $host, string $databaseName, string $username, string $password): self
    {
        return new self('mysql', $host, $databaseName, $username, $password);
    }

    public static function createInstallServerConnection(string $host, string $username, string $password): self
    {
        return new self('mysql', $host, '', $username, $password);
    }

    public static function getInstance() {
        // create instance if doesn't exist
        if ( self::$_instance === null )
            self::$_instance = new self(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

        return self::$_instance;
    }
}