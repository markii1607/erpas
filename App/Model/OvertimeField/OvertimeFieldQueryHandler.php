<?php 
    namespace App\Model\OvertimeField;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class OvertimeFieldQueryHandler extends QueryHandler { 
        /**
         * `selectOvertimes` Query string that will fetch overtime.
         * @return string
         */
        public function selectOvertimes($id = false, $name = false)
        {
            $fields = [
                'O.id',
                'DATE_FORMAT(O.date_filed, "%m/%d/%Y") as date_filed',
                // 'O.department_id',
                'O.project_id',
                'DATE_FORMAT(O.date_of_ot, "%m/%d/%Y") as date_of_ot',
                'O.task',
                '"" as status',
                'O.time_from',
                'O.time_to',
                // 'D.charging as department_charging',
                // 'CONCAT(D.charging, "(", D.name,")") as department_charging',
                // 'D.name as department_name',
                // 'P.id as project_id',
                'P.name as project_name',
                'P.project_code'
            ];

            $initQuery = $this->select($fields)
                              ->from('overtime_fields O')
                            //   ->join(['departments D'=> 'D.id = O.department_id',])
                              ->join(['projects P'=> 'P.id = O.project_id',])
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
         * `selectProjects` Query string that will select from table `projects`
         * @return string
         */
        public function selectProjects()
        {
            $fields = [
                'P.id',
                'P.project_code',
                'P.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

        /**
         * `insertOvertimeField` Query string that will insert to table `overtimes`
         * @return string
         */
        public function insertOvertimeField($data = [])
        {
            $initQuery = $this->insert('overtime_fields', $data);

            return $initQuery;
        }

        /**
         * `updateOvertime` Query string that will update specific overtime information from table `overtime`
         * @return string
         */
        public function updateOvertimeField($id = '', $data = [])
        {
            $initQuery = $this->update('overtime_fields', $id, $data);

            return $initQuery;
        }
    }