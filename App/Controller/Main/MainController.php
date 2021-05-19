<?php

namespace App\Controller\Main;

require_once("../../Config/BaseController.php");
require_once("../../Model/Main/MainQueryHandler.php");

use App\Config\BaseController as BaseController;
use App\Model\Main\MainQueryHandler as QueryHandler;
use Exception;

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

    public function changePassword($input)
    {
        // print_r($_SESSION['user_id']);
        // die();
        try {
            $this->dbCon->beginTransaction();

            $salt = '+^7*_<>/?absdia7has723n7as123';

            $entryData = [
                'password'    => crypt($input->new_password, $salt),
                'updated_by'  => $_SESSION['user_id'],
                'updated_at'  => date('Y-m-d H:i:s'),
            ];

            $updateUserPassword = $this->dbCon->prepare($this->queryHandler->updateUser($_SESSION['user_id'], $entryData));
            $status = $updateUserPassword->execute($entryData);
            $this->systemLogs($_SESSION['user_id'], 'users', 'Main', 'change password');
        
            $this->dbCon->commit();

            $output = [
                'status' => $status
            ];

            return $output;
        
        } catch (Exception $exc) {
            echo $exc->getMessage();
            $this->dbCon->rollBack();
        }
        
    }
}
