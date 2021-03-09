<?php 
	require_once "db_config.php";

	/**
     * `connectToDb` Connects to Database Driver.
     * @return Object db connection string
     */
    function connectToDb() {
        try
        {
            $dbCon = new PDO(DB_DRIVER, DB_USERNAME, DB_PASSWORD);
            $dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }

        return $dbCon;
    }