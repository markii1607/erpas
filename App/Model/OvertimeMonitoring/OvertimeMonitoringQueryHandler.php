<?php 
    namespace App\Model\OvertimeMonitoring;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class OvertimeMonitoringQueryHandler extends QueryHandler { 
        /**
         * `selectOvertimes` Query string that will fetch overtime.
         * @return string
         */
        public function selectOvertimes($id = false)
        {
            $fields = [
                'O.id',
                'O.personal_information_id',
                'O.department_id',
                'O.project_id',
                'DATE_FORMAT(O.date_of_ot, "%m/%d/%Y") as date_of_ot',
                'IF(O.department_id IS NULL, "P", "D") as charging_type',
                'IF(O.department_id IS NULL, O.project_id, O.department_id) as charging_id',
                'O.time_from',
                'O.time_to',
                'O.total_hours',
                'O.reason',
                'O.status',
                'IF(O.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(O.project_id IS NULL, D.name, P.name) as charging_name',
                'IF(O.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(P.project_code,"-", P.name)) as charging',
                'DATE_FORMAT(O.created_at, "%m/%d/%Y") as created_at',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'PS.name as position_name',
                'EI.employee_no',
            ];

            $leftJoins = [
                'projects P'                    => 'P.id = O.project_id',
                // 'users U'                       => 'U.id = O.personal_information_id',
                'personal_informations PI'      => 'PI.id = O.personal_information_id',
                'employment_informations EI'    => 'EI.personal_information_id = PI.id',
                'positions PS'                  => 'PS.id = EI.position_id',
                'departments D'                 => 'O.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('overtimes O')
                            //   ->join(['departments D'=> 'D.id = O.department_id',])
                              ->leftJoin($leftJoins)
                              ->where(['O.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['O.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * Undocumented function
         *
         * @param boolean $overtime_id
         * @return void
         */
        public function selectOsSignatories($overtime_id = false)
        {
            $fields = array(
                'OS.id',
                'OS.overtime_id',
                'OS.signatory_id',
                'OS.seq',
                'OS.is_approved',
                'IF(OS.remarks IS NULL, "", OS.remarks) as remarks',
                'DATE_FORMAT(OS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'DATE_FORMAT(OS.updated_at, "%M %d, %Y ") as date_approved_print',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'EI.employee_no',
                'P.name as position_name',
                'D.name as department_name'
            );

            $joins = array(
                'users U'                       =>      'U.id = OS.signatory_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('overtime_signatories OS')
                              ->leftJoin($joins)
                              ->where(array('OS.is_active' => ':is_active'));

            $initQuery = ($overtime_id) ? $initQuery->andWhere(array('OS.overtime_id' => ':overtime_id')) : $initQuery;

            return $initQuery;
        }


   /**
         * `selectProjects` Query string that will fetch from table `projects`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code as charging',
                'P.name',
                '"P" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query string that will fetch from table `departments`.
         * @param  boolean $id
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.charging',
                'D.name',
                '"D" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['D.id' => ':id']) : $initQuery;

            return $initQuery;
        }

         /**
         * `selectPersonalInformations` Query string that will from table `personal_informations`.
         * @param  string $id
         * @return string
         */
        public function selectPersonalInformations($id = '')
        {
            $fields = [
                'PI.id',
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'PI.sname',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'EI.id as ei_id',
                'EI.employee_no',
                'P.id as position_id',
                'P.name as position_name',
                'D.name as department_name',
                'D.id as department_id'
            ];

            $joins = [
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

        /**
         * selectEmployees
         *
         * @return void
         */
        public function selectEmployees($id = false, $department_id = false)
        {
            $fields = array(
                'PI.id',
                'EI.position_id',
                'P.department_id',
                'P.name as position_name',
                'D.charging',
                'D.name as department_name',
                'CONCAT(PI.lname,", ",PI.fname," ",PI.mname) as fullname',
            );

            $join = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P' => 'EI.position_id = P.id',
                'departments D' => 'P.department_id = D.id'
            );

            $initQuery = $this->select($fields)
                ->from('personal_informations PI')
                ->leftJoin($join)
                ->where(array('PI.is_active' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;
            $initQuery = ($department_id) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectSignatories
         *
         * @param boolean $id
         * @return void
         */
        public function selectSignatories($id = false)
        {
            $fields = array(
                'OS.id',
                'OS.overtime_id',
                'OS.signatory_id',
                'OS.seq',
                'OS.queue',
                'OS.is_approved',
                'OS.remarks',
            );

            $initQuery = $this->select($fields)
                ->from('overtime_signatories OS')
                ->where(array('OS.status' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('OS.menu_id' => ':id')) : $initQuery;

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

        public function insertOtSignatories($data = [])
        {
            $initQuery = $this->insert('overtime_signatories', $data);

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