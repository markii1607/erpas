<?php
    namespace App\Controller\Dashboard;

    require_once("DashboardController.php");

    use App\Controller\Dashboard\DashboardController as ModuleController;

    class SubMenuController extends ModuleController {

        /**
         * `getDetails` Get details needed in material data control
         * @param  string $id
         * @return array
         */
        public function getDetails($data = [])
        {
            $_SESSION['parent_id'] = $data['parent_id'];

            $output = [
                'accessed_sub_menus' => $this->getSubMenus($_SESSION['user_id'], $data['parent_id']),
                'sub_menus'          => $this->getSubMenus('', $data['parent_id'])
            ];

            return $output;
        }

        /**
         * `getSubMenus` Fetching of sub menus.
         * @param  array $parentMenus
         * @return array
         */
        public function getSubMenus($userId = '', $parentId)
        {
            $userIdCondition = ($userId == '') ? false : true;

            $data = [
                'is_active' => 1,
                'parent'    => $parentId,
            ];

            ($userId != '') ? $data['user_id'] = $userId : '';

            $childMenu = $this->dbCon->prepare($this->queryHandler->selectMenus(false, $userIdCondition)->end());
            $childMenu->execute($data);

            return $childMenu->fetchAll(\PDO::FETCH_ASSOC);
        }
    }