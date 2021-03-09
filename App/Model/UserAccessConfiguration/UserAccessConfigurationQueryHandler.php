<?php 
    namespace App\Model\UserAccessConfiguration;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class UserAccessConfigurationQueryHandler extends QueryHandler { 

        /**
         * `selectUsers` Query string that will fetch `users`.
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'U.username',
                'U.account_status',
                'EI.employee_no',
                'CONCAT(PI.lname,", ", PI.fname," ", PI.mname) as full_name',
                'P.name as position_name',
                'D.name as department_name',
            ];

            $joins = [
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id',
                'departments D'              => 'D.id = P.department_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => ':is_active']);

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
        public function selectMenus($id = false, $name = false)
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

            $initQuery = ($id)     ? $initQuery->andWhere(['M.id' => ':id']) : $initQuery;
            $initQuery = ($name)   ? $initQuery->andWhereLike(['M.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        public function selectProjectAccesses($userId = false)
        {
            $fields = array(
                'PA.id',
                'PA.user_id',
                'PA.project_id',
                'PA.level',
                'P.project_code',
                'P.name as project_name',
            );

            $join = array(
                'projects P'    =>  'P.id = PA.project_id'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->join($join)
                              ->where(array('PA.is_active' => ':is_active'));

            $initQuery = ($userId) ? $initQuery->andWhere(array('PA.user_id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjects()
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name as project_name',
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(array('P.is_active' => ':is_active'));


            return $initQuery;
        }

        public function selectUserDeputies($userId = false)
        {
            $fields = array(
                'UD.id',
                'UD.priviledges',
                'UD.user_id',
                'UD.deputy_id',
                'IF(UD.status = 1, "ON", "OFF") as status',
                'CONCAT(PI.fname, " ", LEFT(PI.mname, 1), ". ", PI.lname) as full_name',
                'P.name as position_name',
                'D.name as department_name',
                'UD.priviledges as priviledges',
            );

            $joins = array(
                'users U'                       =>  'U.id = UD.deputy_id',
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id',
                'departments D'                 =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('user_deputies UD')
                              ->join($joins)
                              ->where(array('UD.is_active' => ':is_active'));

            $initQuery = ($userId) ? $initQuery->andWhere(array('UD.user_id' => ':user_id')) : $initQuery;

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

        public function insertProjectAccess($data = [])
        {
            $initQuery = $this->insert('project_accesses', $data);

            return $initQuery;
        }

        public function insertUserDeputies($data = [])
        {
            $initQuery = $this->insert('user_deputies', $data);

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

        public function updateProjectAccess($id = '', $data = [])
        {
            $initQuery = $this->update('project_accesses', $id, $data);

            return $initQuery;
        }

        public function updateUsers($id = '', $data = [])
        {
            $initQuery = $this->update('users', $id, $data);

            return $initQuery;
        }

        public function updateUserDeputies($id = '', $data = [])
        {
            $initQuery = $this->update('user_deputies', $id, $data);

            return $initQuery;
        }
    }