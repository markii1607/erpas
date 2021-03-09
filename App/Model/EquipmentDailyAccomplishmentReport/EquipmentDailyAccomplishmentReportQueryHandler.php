<?php
    namespace App\Model\EquipmentDailyAccomplishmentReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class EquipmentDailyAccomplishmentReportQueryHandler extends QueryHandler {


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

        /**
         * `selectEaReports` Query string that will select from table `ea_reports`.
         * @return string
         */
        public function selectEaReports($id = false)
        {
            $fields = [
                'EAR.id',
                'EAR.heavy_equipment_id',
                'EAR.project_id',
                // 'EAR.payroll_cutoff_id',
                'EAR.week_calendar_id',
                'DATE_FORMAT(EAR.activity_date, "%M %d, %Y") as date_of_activity',
                'DATE_FORMAT(EAR.created_at, "%M %d, %Y") as date_filed',
                'EAR.activity_date',
                'EAR.total_st',
                'EAR.total_ot',
                'EAR.sk_before',
                'EAR.sk_after',
                'EAR.status',
                'EAR.created_by',
                // 'EI.position_id',
                // 'EI.employee_no',
                // 'P.name as position_name',
                // 'P.department_id',
                // 'D.name as department_name',
                // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            ];

            /* $joins = [
                'employment_informations EI' => 'EI.id = EAR.employment_information_id',
                'personal_informations PI'   => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id',
            ]; */

            $initQuery = $this->select($fields)
                              ->from('ea_reports EAR')
                            //   ->join($joins)
                              ->where(['EAR.is_active' => ':is_active', 'EAR.created_by' => ':created_by']);

            $initQuery = ($id) ? $initQuery->andWhere(['EAR.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectEarActivities($ear_id = false)
        {
            $fields = array(
                'EA.id',
                'EA.ear_id',
                'EA.expense_type as type',
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
                '"saved" as data_status'
            );

            // $join = array(
            //     'leave_types LT'    => 'LT.id = EA.leave_type_id'
            // );

            $initQuery = $this->select($fields)
                              ->from('ear_activities EA')
                            //   ->leftJoin($join)
                              ->where(array('EA.is_active' => ':is_active'));

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('EA.ear_id' => ':ear_id')) : $initQuery;

            return $initQuery;
        }

        public function selectEarSignatories($ear_id = false)
        {
            $fields = array(
                'ES.id',
                'ES.ear_id',
                'ES.signatory_id',
                'ES.seq',
                'ES.is_approved',
                'IF(ES.remarks IS NULL, "", ES.remarks) as remarks',
                'DATE_FORMAT(ES.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as fullname',
                'EI.employee_no',
                'P.name as position',
                'D.name as department_name'
            );

            $joins = array(
                'users U'                       =>      'U.id = ES.signatory_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('ear_signatories ES')
                              ->leftJoin($joins)
                              ->where(array('ES.is_active' => ':is_active'));

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('ES.ear_id' => ':ear_id')) : $initQuery;

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

            $initQuery = ($id) ? $initQuery->andWhere(array('D.id' => ':id')) : $initQuery;

            return $initQuery;
        }

         /**
         * `selectProjects` Query string that will select from table `projects`
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code as charging',
                'P.name',
                'P.location',
                '"P" as ca_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

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
                              ->where(['PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;

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

        /**
         * `selectAccounts` Query string that will select from table `accounts`.
         * @param  boolean $id
         * @return string
         */
        // public function selectAccounts($id = false)
        // {
        //     $fields = [
        //         'A.id',
        //         'A.name',
        //         'A.account_id',
        //         'AT.id as account_type_id',
        //         'AT.name as account_type_name'
        //     ];

        //     $joins = [
        //         'account_types AT' => 'AT.id = A.account_type_id'
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('accounts A')
        //                       ->join($joins)
        //                       ->where(['A.is_active' => ':is_active']);

        //     $initQuery = ($id) ? $initQuery->andWhere(['A.id' => ':id']) : $initQuery;

        //     return $initQuery;
        // }

        public function selectAccounts($id = false, $hasAcc_type = false)
        {
            $fields = array(
            'A.id',
            'A.account_id',
            'A.name',
            'AT.id as type_id',
            'AT.name as type_name',
            );

            $joins = array(
            'account_types AT' => 'AT.id = A.account_type_id',
            );

            $initQuery = $this->select($fields)
            ->from('accounts A')
            ->join($joins)
            ->where(array('A.is_active' => ':is_active'))
            ->andWhereLike(array('A.name' => ':like_name'));

            $initQuery = ($id) ? $initQuery->andWhere(array('A.id' => ':id')) : $initQuery;
            $initQuery = ($hasAcc_type) ? $initQuery->andWhere(array('A.account_type_id' => ':account_type_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectFieldActivity` Query string that will select from table `wi_activities`.
         * @param  boolean $id
         * @return string
         */
        public function selectFieldActivity($id = false)
        {
            $fields = [
                'FA.id',
                'FA.work_item_id',
                'FA.name',
            ];


            $initQuery = $this->select($fields)
                              ->from('wi_activities FA')
                              ->where(['FA.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['FA.id' => ':id']) : $initQuery;

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
                'CA.id',
                'IF(CA.project_id IS NULL, D.charging, P.project_code) as charge_account',
                'IF(CA.project_id IS NULL, D.name, P.name) as description',
            ];

            $leftJoins = [
                'projects P'    => 'P.id = CA.project_id',
                'departments D' => 'CA.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('charge_accounts CA')
                              ->leftJoin($leftJoins)
                              ->where(['CA.is_active' => ':is_active']);

            $initQuery = ($id)           ? $initQuery->andWhere(['CA.id' => ':id'])                       : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(['CA.department_id' => ':department_id']) : $initQuery;
            $initQuery = ($projectId)    ? $initQuery->andWhere(['CA.project_id' => ':project_id'])       : $initQuery;

            return $initQuery;
        }

        /**
         * selectSignatories
         *
         * @param boolean $id
         * @return void
         */
        public function selectSignatories($user_id = false)
        {
            $fields = array(
                'P.name as position',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as fullname',
                'D.charging',
                'D.name as department_name',
                'U.id'
            );

            $joins = array(
                'departments D'                 =>      'D.id = P.department_id',
                'employment_informations EI'    =>      'EI.position_id = P.id',
                'personal_informations PI'      =>      'PI.id = EI.personal_information_id',
                'users U'                       =>      'U.personal_information_id = PI.id'
            );

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->join($joins)
                              ->where(array('P.is_active' => ':is_active', 'P.is_signatory' => ':is_signatory'));

            $initQuery = ($user_id) ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectEmployees
         *
         * @return void
         */
        public function selectEmployees($id = false, $department_id = false, $user_id = false, $ei_id = false)
        {
            $fields = array(
                'PI.id',
                'EI.position_id',
                'EI.id as ei_id',
                'P.department_id',
                'P.name as position_name',
                'D.charging',
                'D.name as department_name',
                'CONCAT(PI.lname,", ",PI.fname," ",PI.mname) as full_name',
                'EI.employee_no'
            );

            $join = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id',
                'users U'                    => 'U.personal_information_id = PI.id'
            );

            $initQuery = $this->select($fields)
                ->from('personal_informations PI')
                ->leftJoin($join)
                ->where(array('PI.is_active' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;
            $initQuery = ($department_id) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($user_id) ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;
            $initQuery = ($ei_id) ? $initQuery->andWhere(array('EI.id' => ':ei_id')) : $initQuery;

            return $initQuery;
        }

        public function selectTotalTime($ear_id = false)
        {
            $fields = array(
                'EARA.id',
                'EARA.ear_id',
                'SUM(EARA.subtotal_st) as total_st',
                'SUM(EARA.subtotal_ot) as total_ot',
            );

            $initQuery = $this->select($fields)
                              ->from('ear_activities EARA')
                              ->where(array('EARA.is_active' => ':is_active'));

            $initQuery = ($ear_id) ? $initQuery->andWhere(array('EARA.ear_id' => ':ear_id')) : $initQuery;

            return $initQuery;
        }

        public function selectWeeklyCalendar($date = false, $id = false)
        {
            $fields = array(
                'WC.id',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%m/%d/%Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%m/%d/%Y") as to_date',
            );

            $initQuery = $this->select($fields)
                              ->from('weekly_calendar WC')
                              ->where(array('WC.is_active' => ':is_active'));

            $initQuery = ($date)  ? $initQuery->logicEx('AND :date_from >= DATE_FORMAT(WC.from_date, "%m/%d/%Y")')  : $initQuery;
            $initQuery = ($date)  ? $initQuery->logicEx('AND :date_from <= DATE_FORMAT(WC.to_date, "%m/%d/%Y")')    : $initQuery;
            $initQuery = ($id)    ? $initQuery->andWhere(array('WC.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectRequisitionFields($project_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'RF.id as rf_id',
                'RF.purchase_requisition_id'
            );

            $join = array(
                'requisition_fields RF' =>  'RF.purchase_requisition_id = PR.id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($join)
                              ->where(array('PR.is_active' => ':is_active', 'PR.status' => ':status', 'PR.request_type_id' => ':request_type_id'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function selectRequisitionManpower($requisition_field_id = false, $id = false)
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

            $initQuery = ($requisition_field_id) ? $initQuery->andWhere(array('RM.requisition_field_id' => ':requisition_field_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('RM.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectPositionTasks($position_id = false, $id = false)
        {
            $fields = array(
                'PT.id',
                'PT.position_id',
                'PT.task',
            );

            $initQuery = $this->select($fields)
                              ->from('position_tasks PT')
                              ->where(array('PT.is_active' => ':is_active'));

            $initQuery = ($position_id) ? $initQuery->andWhere(array('PT.position_id' => ':position_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('PT.id' => ':id')) : $initQuery;

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

        /**
         * `insertEaReports` Query string that will insert to table `ea_reports`
         * @return string
         */
        public function insertEaReports($data = [])
        {
            $initQuery = $this->insert('ea_reports', $data);

            return $initQuery;
        }

        /**
         * `insertEarActivities` Query string that will insert to table `ear_activities`
         * @return string
         */
        public function insertEarActivities($data = [])
        {
            $initQuery = $this->insert('ear_activities', $data);

            return $initQuery;
        }

        public function insertEarSignatories($data = [])
        {
            $initQuery = $this->insert('ear_signatories', $data);

            return $initQuery;
        }

        /**
         * `updateEaReport` Query string that will update specific DAR information from table `ea_reports`
         * @return string
         */
        public function updateEaReport($id = '', $data = [])
        {
            $initQuery = $this->update('ea_reports', $id, $data);

            return $initQuery;
        }

        /**
         * `updateDarActivity` Query string that will update specific DAR information from table `ear_activities`
         * @return string
         */
        public function updateEarActivity($id = '', $data = [])
        {
            $initQuery = $this->update('ear_activities', $id, $data);

            return $initQuery;
        }

        public function insertEarAttachment($data = [])
        {
            $initQuery = $this->insert('ear_attachments', $data);

            return $initQuery;
        }

        public function updateEarAttachment($id = '', $data = [])
        {
            $initQuery = $this->update('ear_attachments', $id, $data);

            return $initQuery;
        }
    }