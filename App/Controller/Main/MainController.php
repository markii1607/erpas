<?php

namespace App\Controller\Main;

require_once("../../Config/BaseController.php");
require_once("../../Model/Main/MainQueryHandler.php");

use App\Config\BaseController as BaseController;
use App\Model\Main\MainQueryHandler as QueryHandler;

class MainController extends BaseController
{
    /**
     * `$menu_id` Set the menu id
     * @var integer
     */
    public $menu_id = NULL;

    /**
     * `$dbCon` Concern in database connection.
     * @var private class
     */
    public $dbCon;

    /**
     * `$queryHandler` Handles query.
     * @var private class
     */
    public $queryHandler;

    /**
     * `__construct` Constructor
     * @param object $dbCon        Database connetor
     * @param string $queryHandler Query String
     */
    public function __construct(
        $dbCon
    ) {
        parent::__construct();

        $this->dbCon          = $dbCon;
        $this->queryHandler   = new QueryHandler();
    }

    /**
     * `getDetails` Fetching of first needed details.
     * @return array
     */
    public function getDetails()
    {
        $output = [
            'check_session' => $this->checkSession(),
            'user_details'  => $_SESSION
        ];

        return $output;
    }
}
