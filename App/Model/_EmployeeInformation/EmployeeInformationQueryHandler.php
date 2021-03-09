<?php 
    namespace App\Model\EmployeeInformation;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class EmployeeInformationQueryHandler extends QueryHandler { 

        /**
         * `selectEmployees` Query string that will select table `employees`
         * @param  boolean $id
         * @return string
         */
        public function selectEmployees($id = false, $code = false)
        {
            $fields = [
                'E.id',
                'U.id as user_id',
                'E.code',
                'E.position_id',
                'E.department_id',
                'E.head_id',
                'E.fname',
                'E.lname',
                'E.mname',
                'E.status',
                'E.ho',
                'E.fo',
                'CONCAT(E.lname, ", ", E.fname, " ", E.mname) as fullname',
                'E.ho + E.fo as office',
                'P.name as position_name',
                'P.head_id as position_head_id',
                'D.name as department_name'
            ];

            $joins = [
                'positions P'   => 'E.position_id = P.id',
                'departments D' => 'D.id = E.department_id',
                'users U'       => 'E.id = U.employee_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('employees E')
                              ->join($joins)
                              ->where(['E.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['E.id' => ':id']) : $initQuery;
            $initQuery = ($code) ? $initQuery->andWhere(['E.code' => ':code']) : $initQuery;

            return $initQuery->orderBy('E.code', 'asc');
        }

        /**
         * `selectPositions` Query String that will select from table `positions`
         * @return string
         */
        public function selectPositions($id = false)
        {
            $fields = [
                'P.id',
                'P.name',
                'P.department_id',
                'P.head_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(['P.status' => 1]);

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments()
        {
            $fields = [
                'D.id',
                'D.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.status' => 1]);

            return $initQuery;
        }

        /**
         * `insertEmployee` Query string that will insert to table `employees`
         * @return string
         */
        public function insertEmployee($data = [])
        {
            $initQuery = $this->insert('employees', $data);

            return $initQuery;
        }

        /**
         * `insertUser` Query string that will insert to table `users`
         * @return string
         */
        public function insertUser($data = [])
        {
            $initQuery = $this->insert('users', $data);

            return $initQuery;
        }

        /**
         * `updateEmployee` Query string that will update to table `employees`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateEmployee($id = '', $data = [])
        {
            $initQuery = $this->update('employees', $id, $data);

            return $initQuery;
        }

        /**
         * `updateUser` Query string that will update to table `users`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateUser($id = '', $data = [])
        {
            $initQuery = $this->update('users', $id, $data);

            return $initQuery;
        }
    }