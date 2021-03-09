<?php 
    namespace App\Model\UserAccessAuthorization;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class UserAccessAuthorizationQueryHandler extends QueryHandler { 

        /**
         * `selectUsers` Query string that will fetch `users`.
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'U.username',
                'EI.employee_no',
                'CONCAT(PI.lname,", ", PI.fname," ", PI.mname) as full_name',
                'P.name as position_name',
            ];

            $joins = [
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => ':is_active', 'P.department_id' => ':department_id'])
                              ->andWhereNotEqual(['U.id' => $_SESSION['user_id']]);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            $initQuery = $initQuery->orderBy('U.id', 'asc');

            return $initQuery;
        }

        /**
         * `selectUserAccesses` Query string that will fetch `user_accesses`.
         * @return string
         */
        public function selectUserAccesses($id = false, $userId = false)
        {
            $fields = [
                'UA.id',
                'UA.level',
                'M.parent',
                'M.name',
                'M.id as menu_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('user_accesses UA')
                              ->join(['menus M' => 'M.id = UA.menu_id'])
                              ->where(['UA.is_active' => ':is_active', 'M.system' => 2]);

            $initQuery = ($id)     ? $initQuery->andWhere(['UA.id' => ':id'])           : $initQuery;
            $initQuery = ($userId) ? $initQuery->andWhere(['UA.user_id' => ':user_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('UA.id', 'asc');

            return $initQuery;
        }

        /**
         * `selectMenus` Query string that will fetch menu.
         * @return string
         */
        public function selectMenus($id = false, $name = false, $parent = false)
        {
            $fields = [
                'M.id',
                'M.name',
                'M.level',
                'M.icon',
                'M.box_color',
                'M.link',
                'M.office',
                'M.parent',
                '(SELECT PM.name FROM menus PM WHERE PM.id = M.parent) as parent_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('menus M')
                              ->where(['M.is_active' => ':is_active', 'M.system' => 2]);

            $initQuery = ($id)     ? $initQuery->andWhere(['M.id' => ':id'])         : $initQuery;
            $initQuery = ($name)   ? $initQuery->andWhereLike(['M.name' => ':name']) : $initQuery;
            $initQuery = ($parent) ? $initQuery->andWhere(['M.parent' => ':parent']) : $initQuery;

            return $initQuery;
        }
        
        /**
         * `selectHeadMenus` Query string that will fetch menus assigned to department head.
         * @return string
         */
        public function selectHeadMenus($id = false)
        {
            $fields = [
                'M.id',
                'M.name',
                'M.level',
                'M.icon',
                'M.box_color',
                'M.link',
                'M.office',
                'M.parent',
                '(SELECT PM.name FROM menus PM WHERE PM.id = M.parent) as parent_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('user_accesses UA')
                              ->join(['menus M' => 'M.id = UA.menu_id'])
                              ->where(['UA.is_active' => ':is_active', 'M.system' => 2])
                              ->andWhere(['UA.user_id' => $_SESSION['user_id']])
                              ->andWhereNotEqual(['M.link' => '"user_access_authorization"']);

            $initQuery = ($id) ? $initQuery->andWhere(['UA.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertUserAccess` Query string that will insert to table `user_accesses`
         * @return string
         */
        public function insertUserAccess($data = [])
        {
            $initQuery = $this->insert('user_accesses', $data);

            return $initQuery;
        }

        /**
         * `updateUserAccess` Query string that will update specific user access from table `user_accesses`
         * @return string
         */
        public function updateUserAccess($id = '', $data = [])
        {
            $initQuery = $this->update('user_accesses', $id, $data);

            return $initQuery;
        }
    }