<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/UserAccessConfiguration/UserAccessConfigurationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\UserAccessConfiguration\UserAccessConfigurationQueryHandler as QueryHandler;

    class UserAccessConfigurationController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        protected $menu_id = 47;

        /**
         * `$dbCon` Concern in database connection.
         * @var private class
         */
        protected $dbCon;

        /**
         * `$queryHandler` Handles query.
         * @var private class
         */
        protected $queryHandler;

        /**
         * `__construct` Constructor
         * @param object $dbCon        Database connetor
         * @param string $queryHandler Query String
         */
        public function __construct(
            $dbCon
        ) {
            parent::__construct();

            $this->dbCon        = $dbCon;
            $this->queryHandler = new QueryHandler();
        }

        /**
         * `getDetails` Get details needed in menu configuration
         * @param  string $id
         * @return array    
         */
        public function getDetails($data = [])
        {
            $output = [
                'users' => $this->getInformation()
            ];

            return $output;
        }

        /**
         * `getInformation` Fetching all parent menu and information.
         * @return array
         */
        public function getInformation($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $users = $this->dbCon->prepare($this->queryHandler->selectUsers($idCondition)->end());

            ($id != '') ? $data['id'] = $id : '';

            $users->execute($data);

            return $users->fetchAll(\PDO::FETCH_ASSOC);
        }
    }