<?php 
    namespace App\Model\PerformanceEvaluation;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PerformanceEvaluationQueryHandler extends QueryHandler { 
        /**
         * `selectOvertimes` Query string that will fetch overtime.
         * @return string
         */
        public function selectOvertimes($id = false, $name = false)
        {
            $fields = [
                'O.id',
                'DATE_FORMAT(O.date_filed, "%m/%d/%Y") as date_filed',
                'O.department_id',
                'DATE_FORMAT(O.date_of_ot, "%m/%d/%Y") as date_of_ot',
                'O.task',
                '"" as status',
                'O.time_from',
                'O.time_to',
                'D.charging as department_charging',
                'D.name as department_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('overtimes O')
                              ->join(['departments D'=> 'D.id = O.department_id',])
                              ->where(['O.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['O.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.charging',
                'D.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            return $initQuery;
        }

            /**
         * `selectEmployees` Query String that will select from table `departments`
         * @return string
         */
        public function selectEmployees($id = false)
        {
            $fields = [
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'EI.ho',
                'EI.fo',
                'EI.date_hired',
                'EI.position_id',
                'P.name as position_name',
                'P.department_id',
                'D.name as department_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'P.is_signatory'
            ];

            $joins = [
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => 1, 'PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }
        

        /**
         * `insertOvertime` Query string that will insert to table `overtimes`
         * @return string
         */
        public function insertOvertime($data = [])
        {
            $initQuery = $this->insert('overtimes', $data);

            return $initQuery;
        }

        /**
         * `updateOvertime` Query string that will update specific overtime information from table `overtime`
         * @return string
         */
        public function updateOvertime($id = '', $data = [])
        {
            $initQuery = $this->update('overtimes', $id, $data);

            return $initQuery;
        }
    }