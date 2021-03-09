<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("ViewAccessController.php");

    use App\Controller\UserAccessConfiguration\ViewAccessController as SubModuleController;

    class AccessController extends SubModuleController {

        /**
         * `getDetails` Fetching of first needed details.
         * @return multi-dimesional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'menus' => $this->getUnassignedMenus($data['user_id'])
            ];

            return $output;
        }

        /**
         * `getUnassignedMenus` Fetching of unassigned menus.
         * @param  string $userId
         * @return multi-dimensional array
         */
        public function getUnassignedMenus($userId = '')
        {
            $userAccess   = [];
            $userAccesses = $this->getUserAccesses('', $userId);

            foreach ($userAccesses as $result) {
                array_push($userAccess, $result['menu_id']);
            }

            $menus = $this->queryHandler->selectMenus();
            $menus = (count($userAccess) > 0) ? $menus->andWhereNotIn('M.id', $userAccess) : $menus;

            $menusNotIn = $this->dbCon->prepare($menus->end());
            $menusNotIn->execute(['is_active' => 1]);

            return $menusNotIn->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getUserAccesses` Fetching of user access of specific user.
         * @param  string $userId
         * @return array
         */
        public function getUserAccesses($id = '', $userId = '')
        {
            $idCondition     = ($id == '')     ? false : true;
            $userIdCondition = ($userId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $userAccesses = $this->dbCon->prepare($this->queryHandler->selectUserAccesses($idCondition, $userIdCondition)->end());

            ($id != '')     ? $data['id'] = $id          : '';
            ($userId != '') ? $data['user_id'] = $userId : '';

            $userAccesses->execute($data);

            return $userAccesses->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `saveAccess` Save new user access to table `user_accesses`
         * @param object $input
         * @return  void
         */
        public function saveAccess($input)
        {
            $accessData = [
                'user_id'    => $input->user_id,
                'menu_id'    => $input->menu->id,
                'level'      => empty($input->level) ? '' : $input->level,
                'created_by' => $_SESSION['user_id'],
                'updated_by' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $saveAccess = $this->dbCon->prepare($this->queryHandler->insertUserAccess($accessData));
            $saveAccess->execute($accessData);

            $accessId = $this->dbCon->lastInsertId();

            $this->systemLogs($accessId, 'accesses', 'user_access_configuration', 'add');

            $return = [
                'id'     => $accessId,
                'status' => true
            ];

            return $return;
        }
    }