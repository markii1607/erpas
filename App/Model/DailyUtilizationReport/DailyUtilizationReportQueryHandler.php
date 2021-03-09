<?php 
    namespace App\Model\DailyUtilizationReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DailyUtilizationReportQueryHandler extends QueryHandler { 


        /**
         * `selectEquipments` Query string that will select from table `equipments`.
         * @param  boolean $id
         * @param  boolean $equipmentType
         * @return string
         */
        public function selectEquipments($id = false, $equipmentType = false, $bodyNo = false)
        {
            $fields = [
                'E.id',
                'E.cost_code',
                'E.body_no',
                'E.brand',
                'E.model',
                'E.equipment_status',
                'E.equipment_type_id',
                'E.capacity',
                'E.capacity_unit',
                'ET.cost_code as et_cost_code',
                'ET.name as equipment_type_name',
            ];

            $joins = [
                'equipment_types ET' => 'E.equipment_type_id = ET.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('equipments E')
                              ->join($joins)
                              ->where(['E.status' => 1]);

            $initQuery = ($id)            ? $initQuery->andWhere(['E.id' => ':id']) : $initQuery;
            $initQuery = ($equipmentType) ? $initQuery->andWhere(['E.equipment_type_id' => ':equipment_type_id']) : $initQuery;
            $initQuery = ($bodyNo) ? $initQuery->andWhere(['E.body_no' => ':body_no']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEquipmentTypes` Query string that will select from table `equipment_types`.
         * @return string
         */
        public function selectEquipmentTypes($id = false)
        {
            $fields = [
                'ET.id',
                'ET.name',
                'ET.cost_code',
                'ET.classification'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipment_types ET');

            $initQuery = ($id) ? $initQuery->where(['ET.id' => ':id']) : $initQuery;

            return $initQuery;
        }
        
        /**
         * `selectDaReports` Query string that will select from table `da_reports`.
         * @return string
         */
        public function selectDaReports($id = false)
        {
            $fields = [
                'DAR.id',
                'DAR.payroll_cutoff_id',
                'DAR.employment_information_id',
                'DATE_FORMAT(DAR.activity_date, "%M %d, %Y") as date_of_activity',
                'DATE_FORMAT(DAR.created_at, "%M %d, %Y") as date_filed',
                'DAR.activity_date',
                'DAR.total_st',
                'DAR.total_ot',
                'DAR.status',
                'DAR.created_by',
                // 'EI.position_id',
                // 'EI.employee_no',
                // 'P.name as position_name',
                // 'P.department_id',
                // 'D.name as department_name',
                // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            ];

            $joins = [
                'employment_informations EI' => 'EI.id = DAR.employment_information_id',
                'personal_informations PI'   => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('da_reports DAR')
                            //   ->join($joins)
                              ->where(['DAR.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['DAR.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectDarActivities($dar_id = false)
        {
            $fields = array(
                'DA.id',
                'DA.dar_id',
                'DA.project_id',
                'DA.department_id',
                'DA.department_activity_id',
                'DA.field_activity_id',
                'DA.expense_type as type',
                'DA.ps_swi_direct_id',
                'DA.p_wi_indirect_id',
                'DA.account_id',
                'DATE_FORMAT(DA.time_from, "%H:%i %p") as time_from',
                'DATE_FORMAT(DA.time_to, "%H:%i %p") as time_to',
                'DA.subtotal_st',
                'DA.subtotal_ot',
                'DA.activity_date',
                'DA.form_type',
                'DA.leave_type_id',
                'DA.leave_status',
                'DA.destination',
                'DA.purpose',
                'LT.name as leave_type'
            );

            $join = array(
                'leave_types LT'    => 'LT.id = DA.leave_type_id'
            );

            $initQuery = $this->select($fields)
                              ->from('dar_activities DA')
                              ->leftJoin($join)
                              ->where(array('DA.is_active' => ':is_active'));

            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DA.dar_id' => ':dar_id')) : $initQuery;

            return $initQuery;
        }
        
        public function selectDarSignatories($dar_id = false)
        {
            $fields = array(
                'DS.id',
                'DS.dar_id',
                'DS.signatory_id',
                'DS.seq',
                'DS.is_approved',
                'DS.remarks',
                'DATE_FORMAT(DS.updated_at, "%M %d, %Y %H:%i:%s %p") as date_approved',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as fullname',
                'EI.employee_no',
                'P.name as position',
                'D.name as department_name'
            );

            $joins = array(
                'users U'                       =>      'U.id = DS.signatory_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('dar_signatories DS')
                              ->leftJoin($joins)
                              ->where(array('DS.is_active' => ':is_active'));
    
            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DS.dar_id' => ':dar_id')) : $initQuery;
    
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

        public function selectLeaveTypes($id = false)
        {
            $fields = array(
                'LT.id',
                'LT.name'
            );

            $initQuery = $this->select($fields)
                              ->from('leave_types LT')
                              ->where(array('LT.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('LT.id' => ':id')) : $initQuery;

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
         * `selectDepartmentActivity` Query string that will select from table `department_activities`.
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
                              ->where(['DA.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['DA.id' => ':id']) : $initQuery;

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

        public function selectTotalTime($dar_id = false)
        {
            $fields = array(
                'DARA.id',
                'DARA.dar_id',
                'SUM(DARA.subtotal_st) as total_st',
                'SUM(DARA.subtotal_ot) as total_ot',
            );

            $initQuery = $this->select($fields)
                              ->from('dar_activities DARA')
                              ->where(array('DARA.is_active' => ':is_active'));

            $initQuery = ($dar_id) ? $initQuery->andWhere(array('DARA.dar_id' => ':dar_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertDaReports` Query string that will insert to table `da_reports`
         * @return string
         */
        public function insertDaReports($data = [])
        {
            $initQuery = $this->insert('da_reports', $data);

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

        public function insertDarSignatories($data = [])
        {
            $initQuery = $this->insert('dar_signatories', $data);

            return $initQuery;
        }

        /**
         * `updateDaReport` Query string that will update specific DAR information from table `da_reports`
         * @return string
         */
        public function updateDaReport($id = '', $data = [])
        {
            $initQuery = $this->update('da_reports', $id, $data);

            return $initQuery;
        }

        /**
         * `updateDarActivity` Query string that will update specific DAR information from table `dar_activities`
         * @return string
         */
        public function updateDarActivity($id = '', $data = [])
        {
            $initQuery = $this->update('dar_activities', $id, $data);

            return $initQuery;
        }
    }