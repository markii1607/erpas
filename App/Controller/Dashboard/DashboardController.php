<?php
    namespace App\Controller\Dashboard;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/Dashboard/DashboardQueryHandler.php");
    // include_once $_SERVER['DOCUMENT_ROOT']."\/scdc\/vendor\/thiagoalessio\/tesseract_ocr\/src\/TesseractOCR.php";

    use App\Config\BaseController as BaseController;
    use App\Model\Dashboard\DashboardQueryHandler as QueryHandler;

    class DashboardController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        protected $menu_id = 0;

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
         * `getDetails` Get details needed in material data control
         * @param  string $id
         * @return array
         */
        public function getDetails($data = [])
        {
            $output = [
                'menus'        => $this->getMenus($_SESSION['user_id']),
                'is_signatory' => $_SESSION['is_signatory'],
                'user_id'      => $_SESSION['user_id']
            ];

            return $output;
        }

        /**
         * `getMenus` Fetching of menus needed in dashboard.
         * @param  string $userId
         * @return array
         */
        public function getMenus($userId = '')
        {
            $parentMenus = $this->dbCon->prepare($this->queryHandler->selectMenus(false, true, true)->end());

            $parentMenus->execute([
                'is_active' => 1,
                'user_id'   => $userId
            ]);

            $parents = $parentMenus->fetchAll(\PDO::FETCH_ASSOC);

            return $parents;
        }
    }