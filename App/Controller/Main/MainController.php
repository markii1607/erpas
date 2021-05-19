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
            'user_details'  => $this->getInformation($_SESSION['user_id']),
        ];

        return $output;
    }

    public function getInformation($userId = '')
    {
        $informations = $this->dbCon->prepare($this->queryHandler->selectUsers(true)->end());
        $informations->execute(['id' => $userId]);

        return $informations->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * `signOut` destroying session.
     * @return destroy session
     */
    public function signOut()
    {    

        $ip = $this->getClientIP();
        $hasEntryExistingAlready = $this->checkSessionLogs($ip);

        $entryData = array(
            'ip_address'    => $ip,
            'user_id'       => $_SESSION['user_id'],
            'session_data'  => json_encode($_SESSION),
            'status'        => "logged_off"
        );

        if(count($hasEntryExistingAlready) != 0){
            foreach ($hasEntryExistingAlready as $key => $value) {
                $logSession = $this->dbCon->prepare($this->queryHandler->updateSessionLogs($value['id'], $entryData));
                $logSession->execute($entryData);
            }
        }

        session_destroy();
    }
}
