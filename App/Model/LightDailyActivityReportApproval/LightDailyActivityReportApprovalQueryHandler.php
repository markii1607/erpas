<?php 
    namespace App\Model\LightDailyActivityReportApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class LightDailyActivityReportApprovalQueryHandler extends QueryHandler { 
        
        public function selectSignatoryTransactions($id = false, $lar_id = false, $is_approved = false)
        {
            $fields = array(
                'LS.id',
                'LS.lar_id',
                'LS.signatory_id',
                'LS.queue',
                'LS.seq',
                'LS.is_approved',
                'IF(LS.remarks IS NULL, "", LS.remarks) as remarks',
                'DATE_FORMAT(LS.updated_at, "%M %d, %Y %H:%i:%s %p") as date_approved',
                'DATE_FORMAT(LR.activity_date, "%M %d, %Y") as activity_date',
                'DATE_FORMAT(LR.created_at, "%M %d, %Y") as date_filed',
                // 'IF(LR.project_id IS NULL, D.charging, P.project_code) as charge_account',
                // 'IF(LR.project_id IS NULL, D.name, P.name) as description',
                'LR.project_id',
                // 'LR.requisition_manpower_id',
                'LR.small_equipment_id',
                // 'LR.payroll_cutoff_id',
                'DATE_FORMAT(LR.activity_date, "%m/%d/%Y") as date_activity',
                'LR.created_by',
                'LR.status',
                '(SELECT CONCAT("<b>",project_code,"</b>", " - ", name) FROM projects WHERE id = LR.project_id) as project',
                '(SELECT location FROM projects WHERE id = LR.project_id) as project_location',
                '(SELECT week_no FROM weekly_calendar WHERE id = LR.week_calendar_id) as week_no',
                // 'DATE_FORMAT(PC.from_payroll_cutoff, "%M %d, %Y") as from_payroll_cutoff',
                // 'DATE_FORMAT(PC.to_payroll_cutoff, "%M %d, %Y") as to_payroll_cutoff'
            );

            $joins = array(
                'ea_reports LR'       =>    'LR.id = LS.lar_id',
                // 'payroll_cutoffs PC'  =>    'PC.id = LR.payroll_cutoff_id'
                'projects P'          =>    'P.id  = LR.project_id',
                // 'departments D'       =>    'D.id  = LR.department_id',
            );

            $initQuery = $this->select($fields)
                              ->from('lar_signatories LS')
                              ->join($joins)
                              ->where(array('LS.is_active' => ':is_active', 'LS.signatory_id' => ':signatory', 'LS.queue' => ':queue'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('LS.id' => ':id')) : $initQuery;
            $initQuery = ($lar_id)      ? $initQuery->andWhere(array('LS.lar_id' => ':lar_id')) : $initQuery;
            $initQuery = ($is_approved) ? $initQuery->andWhere(array('LS.is_approved' => ':is_approved')) : $initQuery;

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

        public function selectLarSignatories($lar_id = false)
        {
            $fields = array(
                'LS.id',
                'LS.lar_id',
                'LS.signatory_id',
                'LS.queue',
                'LS.seq',
                'LS.is_approved',
                'DATE_FORMAT(LS.updated_at, "%M %d, %Y %H:%i:%s %p") as date_approved',
                'IF(LS.remarks IS NULL, "", LS.remarks) as remarks',
            );

            $initQuery = $this->select($fields)
                              ->from('lar_signatories LS')
                              ->where(array('LS.is_active' => ':is_active'));

            $initQuery = ($lar_id) ? $initQuery->andWhere(array('LS.lar_id' => ':lar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectLarActivities($lar_id = false)
        {
            $fields = array(
                'LA.id',
                'LA.lar_id',
                // 'LA.position_task_id',
                'LA.expense_type',
                'LA.ps_swi_direct_id',
                'LA.p_wi_indirect_id',
                'LA.account_id',
                'DATE_FORMAT(LA.time_from, "%H:%i %p") as time_from',
                'DATE_FORMAT(LA.time_to, "%H:%i %p") as time_to',
                'LA.subtotal_st',
                'LA.subtotal_ot',
                'LA.activity_date',
                'LA.activity',
                'LA.remarks',
                // 'LA.form_type',
                // 'LA.leave_type_id',
                // 'LA.leave_status',
                // 'LA.destination',
                // 'LA.purpose',
                // 'LT.name as leave_type',
                // 'PT.task'
            );

            // $join = array(
            //     'leave_types LT'           => 'LT.id = LA.leave_type_id',
            //     'position_tasks PT'        => 'PT.id = LA.position_task_id'
            // );

            $initQuery = $this->select($fields)
                              ->from('lar_activities LA')
                            //   ->leftJoin($join)
                              ->where(array('LA.is_active' => ':is_active'));

            $initQuery = ($lar_id) ? $initQuery->andWhere(array('LA.lar_id' => ':lar_id')) : $initQuery;

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
        // public function selectPayrollCutoffs($id = false)
        // {
        //     $fields = [
        //         'PC.id',
        //         'DATE_FORMAT(PC.from_payroll_cutoff, "%M %d, %Y") as from_payroll_cutoff',
        //         'DATE_FORMAT(PC.to_payroll_cutoff, "%M %d, %Y") as to_payroll_cutoff',
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('payroll_cutoffs PC')
        //                       ->where(['PC.is_active' => ':is_active']);

        //     $initQuery = ($id) ? $initQuery->andWhere(array('PC.id' => ':id')) : $initQuery;

        //     return $initQuery;
        // }

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

        public function selectSpecificSignatory($lar_id = false, $seq = false)
        {
            $fields = array(
                'LS.id',
                'LS.lar_id',
                'LS.seq'
            );

            $initQuery = $this->select($fields)
                              ->from('lar_signatories LS')
                              ->where(array('LS.is_active' => ':is_active'));
                            //   ->orderBy('LS.seq', 'DESC')
                            //   ->limit(1);

            $initQuery = ($lar_id) ? $initQuery->andWhere(array('LS.lar_id' => ':lar_id')) : $initQuery;
            $initQuery = ($seq) ? $initQuery->andWhere(array('LS.seq' => ':seq')) : $initQuery;

            return $initQuery;
        }

         /**
         * ``
         *
         * @param boolean $id
         * @return void
         */
        public function selectLightEquipments($id = false)
        {
            $fields = [
                'LE.id',
                'LE.equipment_type_id',
                'LE.model',
                'LE.serial_no',
                // 'LE.body_no',
                'LE.code',
                'LE.cost_code',
                // 'LE.brand',
                'LE.capacity',
                'CONCAT(LE.cost_code, " - ", ET.name, "(", LE.model, ")") as small_details',
                'IF(LE.body_no IS NULL, "NO DATA", LE.body_no) as body_no',
                'IF(LE.brand IS NULL, "NO DATA", LE.brand) as brand',
                // 'LE.model',
                // 'LE.capacity',
                // 'LE.c_unit',
                // 'CONCAT(LE.capacity, " ", LE.c_unit) as capacities',
                'ET.name',
                'ET.classification'
            ];

            $joins = [
                'equipment_types ET'    => 'ET.id = LE.equipment_type_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('small_equipments LE')
                              ->join($joins)
                              ->where(['LE.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['LE.id' => ':id']) : $initQuery;

            return $initQuery;

        }

        /**
         * Undocumented function
         *
         * @param boolean $lar_id
         * @return void
         */
        public function selectLarAttachments($lar_id = false)
        {
            $fields = array(
                'HAT.id',
                'HAT.lar_id',
                'HAT.file_name',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('lar_attachments HAT')
                              ->where(array('HAT.is_active' => ':is_active'));

            $initQuery = ($lar_id) ? $initQuery->andWhere(array('HAT.lar_id' => ':lar_id')) : $initQuery;

            return $initQuery;
        }


        // public function selectRequisitionManpower($id = false)
        // {
        //     $fields = array(
        //         'RM.id',
        //         'RM.requisition_field_id',
        //         'RM.pr_labor_id',
        //         'CONCAT(RM.lname, ", ", RM.fname, " ", RM.mname) as full_name',
        //         'PL.id as pl_id',
        //         'PL.position_id',
        //         'P.id as p_id',
        //         'P.name as position',
        //         'D.id as d_id',
        //         'D.name as department'
        //     );

        //     $joins = array(
        //         'pr_labors PL'  =>  'PL.id = RM.pr_labor_id',
        //         'positions P'   =>  'P.id = PL.position_id',
        //         'departments D' =>  'D.id = P.department_id'
        //     );

        //     $initQuery = $this->select($fields)
        //                       ->from('requisition_manpower RM')
        //                       ->join($joins)
        //                       ->where(array('RM.is_active' => ':is_active'));

        //     $initQuery = ($id) ? $initQuery->andWhere(array('RM.id' => ':id')) : $initQuery;

        //     return $initQuery;
        // }

        public function updateLaReport($id, $data = array())
        {
            $initQuery = $this->update('ea_reports', $id, $data);

            return $initQuery;
        }

        public function updateLarSignatory($id, $data = array())
        {
            $initQuery = $this->update('lar_signatories', $id, $data);

            return $initQuery;
        }
    }