<?php
    namespace App\Model\Timesheet;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TimesheetQueryHandler extends QueryHandler {
        /**
         * `selectTimesheets` Query string that will fetch overtime.
         * @return string
         */
        public function selectTimesheets($id = false)
        {
            $fields = [
                'TS.id',
                'TS.personal_information_id',
                'TS.timesheet_no',
                'CONCAT(MONTHNAME(TS.month),", ",DATE_FORMAT(TS.year, "%Y")) as months',
                'TS.timesheet_type',
                'DATE_FORMAT(TS.created_at, "%M %d, %Y") as created_at',
                '"" as status',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'EI.employee_no',
                'P.id as position_id',
                'P.name as position_name',
                'D.name as department_name',
                'D.name as department_name',
                'D.id as department_id'

            ];

            $joins = [
                'personal_informations PI' => 'TS.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('timesheets TS')
                              ->join($joins)
                              ->where(['TS.is_active' => ':is_active','TS.created_by' => ':created_by']);

            $initQuery = ($id) ? $initQuery->andWhere(['TS.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWithdrawalSlipNumbers`
         * @return string
         */
        public function selectWithdrawalSlipNumbers()
        {
            $fields = array(
                'T.id',
                'T.timesheet_no',
            );

            $initQuery = $this->select($fields)
                              ->from('timesheets T')
                              ->where(array('T.is_active' => ':is_active'))
                              ->orderBy('T.id', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectAllWSNumbers()
        {
            $fields = array(
                'T.id',
                'T.timesheet_no',
            );

            $initQuery = $this->select($fields)
                              ->from('timesheets T')
                              ->where(array('T.is_active' => ':is_active', 'T.timesheet_no' => ':timesheet_no'));

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
         * `insertTimesheet` Query string that will insert to table `timesheets`
         * @return string
         */
        public function insertTimesheet($data = [])
        {
            $initQuery = $this->insert('timesheets', $data);

            return $initQuery;
        }

        /**
         * `updateTimesheet` Query string that will update specific overtime information from table `overtime`
         * @return string
         */
        public function updateTimesheet($id = '', $data = [])
        {
            $initQuery = $this->update('timesheets', $id, $data);

            return $initQuery;
        }
    }