<?php 
    namespace App\Model\EquipmentDailyActivityReportApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class EquipmentDailyActivityReportApprovalQueryHandler extends QueryHandler { 
        
        public function selectSignatoryTransactions($id = false, $ear_id = false, $is_approved = false)
        {
            $fields = array(
                'ES.id',
                'ES.ear_id',
                'ES.signatory_id',
                'ES.queue',
                'ES.seq',
                'ES.is_approved',
                'IF(ES.remarks IS NULL, "", ES.remarks) as remarks',
                'DATE_FORMAT(ES.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'DATE_FORMAT(ER.activity_date, "%M %d, %Y") as activity_date',
                'DATE_FORMAT(ER.created_at, "%M %d, %Y") as date_filed',
                // 'IF(ER.project_id IS NULL, D.charging, P.project_code) as charge_account',
                // 'IF(ER.project_id IS NULL, D.name, P.name) as description',
                'ER.project_id',
                // 'ER.requisition_manpower_id',
                'ER.heavy_equipment_id',
                // 'ER.payroll_cutoff_id',
                'DATE_FORMAT(ER.activity_date, "%m/%d/%Y") as date_activity',
                'ER.created_by',
                'ER.status',
                '(SELECT CONCAT(project_code, " - ", name) FROM projects WHERE id = ER.project_id) as project',
                '(SELECT location FROM projects WHERE id = ER.project_id) as project_location',
                '(SELECT week_no FROM weekly_calendar WHERE id = ER.week_calendar_id) as week_no',
                'ER.sk_before',
                'ER.sk_after',
                // 'DATE_FORMAT(PC.from_payroll_cutoff, "%M %d, %Y") as from_payroll_cutoff',
                // 'DATE_FORMAT(PC.to_payroll_cutoff, "%M %d, %Y") as to_payroll_cutoff'
            );

            $joins = array(
                'ea_reports ER'       =>    'ER.id = ES.ear_id',
                // 'payroll_cutoffs PC'  =>    'PC.id = ER.payroll_cutoff_id'
                'projects P'          =>    'P.id  = ER.project_id',
                // 'departments D'       =>    'D.id  = ER.department_id',
            );

            $initQuery = $this->select($fields)
                              ->from('ear_signatories ES')
                              ->join($joins)
                              ->where(array('ES.is_active' => ':is_active', 'ES.signatory_id' => ':signatory', 'ES.queue' => ':queue'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('ES.id' => ':id')) : $initQuery;
            $initQuery = ($ear_id)      ? $initQuery->andWhere(array('ES.ear_id' => ':ear_id')) : $initQuery;
            $initQuery = ($is_approved) ? $initQuery->andWhere(array('ES.is_approved' => ':is_approved')) : $initQuery;

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

        public function selectEarSignatories($ear_id = false)
        {
            $fields = array(
                'ES.id',
                'ES.ear_id',
                'ES.signatory_id',
                'ES.queue',
                'ES.seq',
                'ES.is_approved',
                'DATE_FORMAT(ES.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'IF(ES.remarks IS NULL, "", ES.remarks) as remarks',
            );

            $initQuery = $this->select($fields)
                              ->from('ear_signatories ES')
                              ->where(array('ES.is_active' => ':is_active'));

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('ES.ear_id' => ':ear_id')) : $initQuery;

            return $initQuery;
        }

        public function selectEarActivities($ear_id = false)
        {
            $fields = array(
                'EA.id',
                'EA.ear_id',
                // 'EA.position_task_id',
                'EA.expense_type',
                'EA.ps_swi_direct_id',
                'EA.p_wi_indirect_id',
                'EA.account_id',
                'DATE_FORMAT(EA.time_from, "%h:%i %p") as time_from', 
                'DATE_FORMAT(EA.time_to, "%h:%i %p") as time_to',
                'EA.activity',
                'EA.remarks',
                'EA.subtotal_st',
                'EA.subtotal_ot',
                'EA.activity_date',
                // 'EA.form_type',
                // 'EA.leave_type_id',
                // 'EA.leave_status',
                // 'EA.destination',
                // 'EA.purpose',
                // 'LT.name as leave_type',
                // 'PT.task'
            );

            // $join = array(
            //     // 'leave_types LT'           => 'LT.id = EA.leave_type_id',
            //     // 'position_tasks PT'        => 'PT.id = EA.position_task_id'
            // );

            $initQuery = $this->select($fields)
                              ->from('ear_activities EA')
                            //   ->leftJoin($join)
                              ->where(array('EA.is_active' => ':is_active'));

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('EA.ear_id' => ':ear_id')) : $initQuery;

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

        public function selectSpecificSignatory($ear_id = false, $seq = false)
        {
            $fields = array(
                'ES.id',
                'ES.ear_id',
                'ES.seq'
            );

            $initQuery = $this->select($fields)
                              ->from('ear_signatories ES')
                              ->where(array('ES.is_active' => ':is_active'));
                            //   ->orderBy('ES.seq', 'DESC')
                            //   ->limit(1);

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('ES.ear_id' => ':ear_id')) : $initQuery;
            $initQuery = ($seq) ? $initQuery->andWhere(array('ES.seq' => ':seq')) : $initQuery;

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

        /**
         * ``
         *
         * @param boolean $id
         * @return void
         */
        public function selectHeavyEquipments($id = false)
        {
            $fields = [
                'HE.id',
                'HE.body_no',
                'HE.equipment_type_id',
                'HE.eqpt_code',
                'HE.brand',
                'HE.model',
                'HE.capacity',
                'HE.c_unit',
                'CONCAT(HE.capacity, " ", HE.c_unit) as capacities',
                'CONCAT(HE.cost_code, " - ", ET.name, "(", HE.brand, " - ", HE.model, ")") as heavy',
                'ET.name',
                'ET.cost_code',
                'ET.classification'
            ];

            $joins = [
                'equipment_types ET'    => 'ET.id = HE.equipment_type_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('heavy_equipments HE')
                              ->join($joins)
                              ->where(['HE.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['HE.id' => ':id']) : $initQuery;

            return $initQuery;

        }

        public function selectEarAttachments($ear_id = false)
        {
            $fields = array(
                'HAT.id',
                'HAT.ear_id',
                'HAT.file_name',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('ear_attachments HAT')
                              ->where(array('HAT.is_active' => ':is_active'));

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('HAT.ear_id' => ':ear_id')) : $initQuery;

            return $initQuery;
        }

        public function updateEaReport($id, $data = array())
        {
            $initQuery = $this->update('ea_reports', $id, $data);

            return $initQuery;
        }

        public function updateEarSignatory($id, $data = array())
        {
            $initQuery = $this->update('ear_signatories', $id, $data);

            return $initQuery;
        }
    }