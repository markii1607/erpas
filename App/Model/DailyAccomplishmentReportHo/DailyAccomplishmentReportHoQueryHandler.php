<?php 
    namespace App\Model\DailyAccomplishmentReportHo;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DailyAccomplishmentReportHoQueryHandler extends QueryHandler { 
        
        /**
         * `selectDaReports` Query string that will select from table `ho_da_reports`.
         * @return string
         */
        public function selectDaReports($id = false, $employment_information_id = false, $payroll_cutoff_id = false, $charge_to = false, $date = false)
        {
            $fields = array(
                'DR.id',
                'DR.project_id',
                'DR.employment_information_id',
                'DR.payroll_cutoff_id',
                'DR.week_calendar_id',
                'DATE_FORMAT(DR.activity_date, "%M %d, %Y") as date_of_activity',
                'DATE_FORMAT(DR.created_at, "%M %d, %Y") as date_filed',
                'DR.activity_date',
                'DR.total_st',
                'DR.total_ot',
                'DR.status',
                'DR.created_by',
                // 'DR.id',
                // 'DR.employment_information_id',
                // 'DR.payroll_cutoff_id',
                // 'DR.charge_to',
                // 'DATE_FORMAT(DR.date, "%M %d, %Y") as date',
                // 'DR.total_st',
                // 'DR.total_ot',
                // 'DR.status',
                // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as employee_name',
                // 'P.name as position',
                // 'D.name as department_name'
            );

            $joins = array(
                'employment_informations EI'    =>      'EI.id = DR.employment_information_id',
                'personal_informations PI'      =>      'PI.id = EI.personal_information_id',
                'positions P'                   =>      'P.id  = EI.position_id',
                'departments D'                 =>      'D.id  = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('ho_da_reports DR')
                              ->leftJoin($joins)
                              ->where(array('DR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('DR.id' => ':id')) : $initQuery;
            $initQuery = ($employment_information_id) ? $initQuery->andWhere(array('DR.employment_information_id' => ':employment_information_id')) : $initQuery;
            $initQuery = ($payroll_cutoff_id)         ? $initQuery->andWhere(array('DR.payroll_cutoff_id' => ':payroll_cutoff_id')) : $initQuery;
            $initQuery = ($charge_to) ? $initQuery->andWhere(array('DR.charge_to' => ':charge_to')) : $initQuery;
            $initQuery = ($date)      ? $initQuery->andWhere(array('DR.date' => ':date')) : $initQuery;

            return $initQuery;
        }

        public function selectDarActivities($dar_id = false)
        {
            $fields = array(
                'DA.id',
                'DA.dar_id',
                'DA.department_id',
                'DA.department_activity_id',
                'DA.expense_type',
                'DA.account_id',
                'DA.time_from',
                'DA.time_to',
                'DA.total_st_time',
                'DA.total_ot_time',
                'DA.date',
            );

            $joins = array(
                'departments D'         =>  'D.id = DA.department_id',
                'department_activities DT'  =>  'DT.id = DA.department_activity_id',
                'accounts A'                =>  'A.id = DA.account_id',
            );

            $initQuery = $this->select($fields)
                              ->from('dar_activities DA')
                              ->where(array('DA.is_active' => ':is_active'));

            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DA.dar_id' => ':dar_id')) : $initQuery;

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
                'D.name',
                '"D" as ca_type'
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
                'P.location',
                '"P" as ca_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

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

        public function selectUserInformation($id = false)
        {
            $fields = array(
                'EI.id as employment_information_id',
                'EI.employee_no',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as employee_name',
                'P.name as position',
                'D.id as department_id',
                'D.name as department_name',
                'D.charging'
            );

            $joins = array(
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->leftJoin($joins)
                              ->where(array('U.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectLeaveTypes()
        {
            $fields = array(
                'LT.id',
                'LT.name'
            );

            $initQuery = $this->select($fields)
                              ->from('leave_types LT')
                              ->where(array('LT.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectPayrollCutoffs()
        {
            $fields = array(
                'PC.id',
                'DATE_FORMAT(PC.from_payroll_cutoff, "%M %d, %Y") as from_payroll_cutoff',
                'DATE_FORMAT(PC.to_payroll_cutoff, "%M %d, %Y") as to_payroll_cutoff',
            );

            $initQuery = $this->select($fields)
                              ->from('payroll_cutoffs PC')
                              ->where(array('PC.is_active' => ':is_active'));

            return $initQuery;
        }

        /**
         * `selectPwds` Query string that will select from table `p_wds`
         * @param  boolean $id
         * @param  boolean $projectId
         * @return string
         */
        public function selectPwds($id = false, $projectId = false)
        {
            $fields = [
                'PWD.id',
                'WD.id as wd_id',
                'WD.name as wd_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('p_wds PWD')
                              ->join(['work_disciplines WD' => 'WD.id = PWD.work_discipline_id'])
                              ->where(['PWD.is_active' => ':is_active', 'WD.is_active' => ':is_active']);

            $initQuery = ($id)        ? $initQuery->andWhere(['PWD.id' => ':id'])                 : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PWD.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwSps` Query string that will select from table `pw_sps`.
         * @param  boolean $id
         * @param  boolean  $pwdId
         * @return string
         */
        public function selectPwSps($id = false, $pwdId = false)
        {
            $fields = [
                'PWSP.id',
                'PWSP.name',
                'SP.id as sp_id',
                'SP.name as sp_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('pw_sps PWSP')
                              ->join(['sub_projects SP' => 'SP.id = PWSP.sub_project_id'])
                              ->where(['PWSP.is_active' => ':is_active', 'SP.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['PWSP.id' => ':id'])           : $initQuery;
            $initQuery = ($pwdId) ? $initQuery->andWhere(['PWSP.p_wd_id' => ':p_wd_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPsSwiDirects` Query string that will select from table `ps_swi_directs`.
         * @param  boolean $id
         * @param  boolean  $pwSpId
         * @return string
         */
        public function selectPsSwiDirects($id = false, $pwSpId = false)
        {
            $fields = [
                'PSWID.id',
                'PSWID.quantities',
                'SWI.id as swi_id',
                'SWI.alternative_name as swi_name',
                'SWI.unit as swi_unit',
                'SWI.wbs',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'WI.id as wi_id',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit'
            ];

            $joins = [
                'sw_wis SWI'                => 'SWI.id = PSWID.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('ps_swi_directs PSWID')
                              ->join($joins)
                              ->where(['PSWID.is_active' => ':is_active', 'SWI.is_active' => ':is_active', 'SWIC.is_active' => ':is_active', 'WIC.is_active' => ':is_active', 'WI.is_active' => ':is_active']);
            
            $initQuery = ($id)    ? $initQuery->andWhere(['PSWID.id' => ':id'])             : $initQuery;
            $initQuery = ($pwSpId) ? $initQuery->andWhere(['PSWID.pw_sp_id' => ':pw_sp_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwiIndirects` Query string that will select from table `p_wi_indirects`
         * @param  boolean $id
         * @param  boolean $projectId
         * @return string
         */
        public function selectPwiIndirects($id = false, $projectId = false)
        {
            $fields = [
                'PWII.id',
                'PWII.quantities',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'WI.id as wi_id',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit'
            ];

            $joins = [
                'work_items WI'            => 'PWII.work_item_id = WI.id',
                'work_item_categories WIC' => 'WI.work_item_category_id = WIC.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('p_wi_indirects PWII')
                              ->join($joins)
                              ->where(['PWII.is_active' => ':is_active', 'WI.is_active' => ':is_active', 'WIC.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['PWII.id' => ':id'])                 : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PWII.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAccounts` Query string that will select from table `accounts`.
         * @param  boolean $id
         * @return string
         */
        public function selectAccounts($id = false)
        {
            $fields = [
                'A.id', 
                'A.name',
                'A.account_id',
                'AT.id as account_type_id',
                'AT.name as account_type_name'
            ];

            $joins = [
                'account_types AT' => 'AT.id = A.account_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('accounts A')
                              ->join($joins)
                              ->where(['A.is_active' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['A.id' => ':id']) : $initQuery;

            return $initQuery;
        }

         /**
         * `selectAccounts` Query string that will select from table `department_activities`.
         * @param  boolean $id
         * @return string
         */
        public function selectDepartmentActivity($id = false)
        {
            $fields = [
                'DA.id',
                'DA.activity',
                'DA.department_id',
                'DA.is_project',
                'D.name as department_name',
            ];

            $joins = [
                'departments D' => 'D.id = DA.department_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('department_activities DA')
                              ->join($joins)
                              ->where(['DA.is_active' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['DA.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectChargeAccounts` Query string that will select from table `charge_accounts`.
         * @param  boolean $id
         * @param  boolean $departmentId
         * @param  boolean $projectId
         * @return string
         */
        public function selectChargeAccounts($id = false, $departmentId = false, $projectId = false)
        {
            $fields = [
                'CA.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('charge_accounts CA')
                              ->where(['CA.is_active' => ':is_active']);

            $initQuery = ($id)           ? $initQuery->andWhere(['CA.id' => ':id'])                       : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(['CA.department_id' => ':department_id']) : $initQuery;
            $initQuery = ($projectId)    ? $initQuery->andWhere(['CA.project_id' => ':project_id'])       : $initQuery;

            return $initQuery;
        }

        public function selectSignatories()
        {
            $fields = array(
                'PI.id',
                'P.name as position',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as fullname',
            );

            $joins = array(
                'employment_informations EI'   => 'EI.position_id = P.id',
                'personal_informations PI'     => 'PI.id = EI.personal_information_id'
            );

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->join($joins)
                              ->where(array('P.is_active' => ':is_active', 'P.is_signatory' => ':is_signatory'));

            // $initQuery = ($personal_information_id) ? $initQuery->andWhere(array('PI.id' => ':personal_info_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertDaReports` Query string that will insert to table `ho_da_reports`
         * @return string
         */
        public function insertDaReports($data = [])
        {
            $initQuery = $this->insert('ho_da_reports', $data);

            return $initQuery;
        }

        /**
         * `insertDarActivities` Query string that will insert to table `dar_activities`
         * @return string
         */
        public function insertDarActivities($data = [])
        {
            $initQuery = $this->insert('dar_activities', $data);

            return $initQuery;
        }

        /**
         * `updateDaReport` Query string that will update specific DR information from table `ho_da_reports`
         * @return string
         */
        public function updateDaReport($id = '', $data = [])
        {
            $initQuery = $this->update('ho_da_reports', $id, $data);

            return $initQuery;
        }

        /**
         * `updateDarActivity` Query string that will update specific DR information from table `dar_activities`
         * @return string
         */
        public function updateDarActivity($id = '', $data = [])
        {
            $initQuery = $this->update('dar_activities', $id, $data);

            return $initQuery;
        }
    }