<?php 
    namespace App\Model\DailyActivityReportApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DailyActivityReportApprovalQueryHandler extends QueryHandler { 
        
        public function selectSignatoryTransactions($id = false, $dar_id = false, $is_approved = false)
        {
            $fields = array(
                'DS.id',
                'DS.dar_id',
                'DS.signatory_id',
                'DS.queue',
                'DS.seq',
                'DS.is_approved',
                'IF(DS.remarks IS NULL, "", DS.remarks) as remarks',
                'DATE_FORMAT(DS.updated_at, "%M %d, %Y %H:%i:%s %p") as date_approved',
                'DATE_FORMAT(DR.activity_date, "%M %d, %Y") as activity_date',
                'DATE_FORMAT(DR.created_at, "%M %d, %Y") as date_filed',
                // 'IF(DR.project_id IS NULL, D.charging, P.project_code) as charge_account',
                // 'IF(DR.project_id IS NULL, D.name, P.name) as description',
                'DR.project_id',
                'DR.requisition_manpower_id',
                'DR.payroll_cutoff_id',
                'DATE_FORMAT(DR.activity_date, "%m/%d/%Y") as date_activity',
                'DR.created_by',
                'DR.status',
                '(SELECT CONCAT("<b>",project_code,"</b>", " - ", name) FROM projects WHERE id = DR.project_id) as project',
                '(SELECT location FROM projects WHERE id = DR.project_id) as project_location',
                '(SELECT name FROM projects WHERE id = DR.project_id) as name',
                '(SELECT week_no FROM weekly_calendar WHERE id = DR.week_calendar_id) as week_no',
                'DATE_FORMAT(PC.from_payroll_cutoff, "%M %d, %Y") as from_payroll_cutoff',
                'DATE_FORMAT(PC.to_payroll_cutoff, "%M %d, %Y") as to_payroll_cutoff'
            );

            $joins = array(
                'da_reports DR'       =>    'DR.id = DS.dar_id',
                'payroll_cutoffs PC'  =>    'PC.id = DR.payroll_cutoff_id'
                // 'projects P'          =>    'P.id  = DR.project_id',
                // 'departments D'       =>    'D.id  = DR.department_id',
            );

            $initQuery = $this->select($fields)
                              ->from('dar_signatories DS')
                              ->join($joins)
                              ->where(array('DS.is_active' => ':is_active', 'DS.signatory_id' => ':signatory', 'DS.queue' => ':queue'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('DS.id' => ':id')) : $initQuery;
            $initQuery = ($dar_id)      ? $initQuery->andWhere(array('DS.dar_id' => ':dar_id')) : $initQuery;
            $initQuery = ($is_approved) ? $initQuery->andWhere(array('DS.is_approved' => ':is_approved')) : $initQuery;

            return $initQuery;
        }

        public function selectEmployeeDetails($id = false, $user_id = false)
        {
            $fields = array(
                'EI.id',
                'EI.employee_no',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as fullname',
                'P.name as position',
                'D.name as department',
                'U.id as user_id'
            );

            $joins = array(
                'personal_informations PI'  =>  'PI.id = EI.personal_information_id',
                'users U'                   =>  'U.personal_information_id = PI.id',
                'positions P'               =>  'P.id = EI.position_id',
                'departments D'             =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('employment_informations EI')
                              ->join($joins)
                              ->where(array('PI.is_active' => ':is_active'));

            $initQuery = ($id)  ? $initQuery->andWhere(array('EI.id' => ':id')) : $initQuery;
            $initQuery = ($user_id)  ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDarSignatories($dar_id = false)
        {
            $fields = array(
                'DS.id',
                'DS.dar_id',
                'DS.signatory_id',
                'DS.queue',
                'DS.seq',
                'DS.is_approved',
                'DATE_FORMAT(DS.updated_at, "%M %d, %Y %H:%i:%s %p") as date_approved',
                'IF(DS.remarks IS NULL, "", DS.remarks) as remarks',
            );

            $initQuery = $this->select($fields)
                              ->from('dar_signatories DS')
                              ->where(array('DS.is_active' => ':is_active'));

            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DS.dar_id' => ':dar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDarActivities($dar_id = false)
        {
            $fields = array(
                'DA.id',
                'DA.dar_id',
                'DA.position_task_id',
                'DA.expense_type',
                'DA.ps_swi_direct_id',
                'DA.p_wi_indirect_id',
                'DA.account_id',
                'DATE_FORMAT(DA.time_from, "%h:%i %p") as time_from',
                'DATE_FORMAT(DA.time_to, "%h:%i %p") as time_to',
                'DA.subtotal_st',
                'DA.subtotal_ot',
                'DA.activity_date',
                'DA.form_type',
                'DA.leave_type_id',
                'DA.leave_status',
                'DA.destination',
                'DA.purpose',
                'LT.name as leave_type',
                'PT.task'
            );

            $join = array(
                'leave_types LT'           => 'LT.id = DA.leave_type_id',
                'position_tasks PT'        => 'PT.id = DA.position_task_id'
            );

            $initQuery = $this->select($fields)
                              ->from('dar_activities DA')
                              ->leftJoin($join)
                              ->where(array('DA.is_active' => ':is_active'));

            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DA.dar_id' => ':dar_id')) : $initQuery;

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
                              ->where(['A.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['A.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPayrollCutoffs` Query string that will select from table `payroll_cutoffs`
         * @return string
         */
        public function selectPayrollCutoffs($id = false)
        {
            $fields = [
                'PC.id',
                'DATE_FORMAT(PC.from_payroll_cutoff, "%M %d, %Y") as from_payroll_cutoff',
                'DATE_FORMAT(PC.to_payroll_cutoff, "%M %d, %Y") as to_payroll_cutoff',
            ];

            $initQuery = $this->select($fields)
                              ->from('payroll_cutoffs PC')
                              ->where(['PC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(array('PC.id' => ':id')) : $initQuery;

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
                'sw_wis SWI'               => 'SWI.id = PSWID.sw_wi_id',
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
                'WI.unit',
                'WI.wbs'
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

        public function selectSpecificSignatory($dar_id = false, $seq = false)
        {
            $fields = array(
                'DS.id',
                'DS.dar_id',
                'DS.seq'
            );

            $initQuery = $this->select($fields)
                              ->from('dar_signatories DS')
                              ->where(array('DS.is_active' => ':is_active'));
                            //   ->orderBy('DS.seq', 'DESC')
                            //   ->limit(1);

            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DS.dar_id' => ':dar_id')) : $initQuery;
            $initQuery = ($seq) ? $initQuery->andWhere(array('DS.seq' => ':seq')) : $initQuery;

            return $initQuery;
        }

        public function selectRequisitionManpower($id = false)
        {
            $fields = array(
                'RM.id',
                'RM.requisition_field_id',
                'RM.pr_labor_id',
                'CONCAT(RM.lname, ", ", RM.fname, " ", RM.mname) as full_name',
                'PL.id as pl_id',
                'PL.position_id',
                'P.id as p_id',
                'P.name as position',
                'D.id as d_id',
                'D.name as department'
            );

            $joins = array(
                'pr_labors PL'  =>  'PL.id = RM.pr_labor_id',
                'positions P'   =>  'P.id = PL.position_id',
                'departments D' =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_manpower RM')
                              ->join($joins)
                              ->where(array('RM.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('RM.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function updateDaReport($id, $data = array())
        {
            $initQuery = $this->update('da_reports', $id, $data);

            return $initQuery;
        }

        public function updateDarSignatory($id, $data = array())
        {
            $initQuery = $this->update('dar_signatories', $id, $data);

            return $initQuery;
        }
    }