<?php 
    namespace App\Model\DailyAccomplishmentReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DailyAccomplishmentReportQueryHandler extends QueryHandler { 
        
        /**
         * `selectDaReports` Query string that will select from table `da_reports`.
         * @return string
         */
        public function selectDaReports($id = false)
        {
            $fields = [
                'DAR.id',
                'EI.position_id',
                'P.name as position_name',
                'P.department_id',
                'D.name as department_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                '"" as date_activity',
                '"" as signatories',
                '"" as status'
            ];

            $joins = [
                'employment_informations EI' => 'EI.id = DAR.employment_information_id',
                'personal_informations PI'   => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('da_reports DAR')
                              ->join($joins)
                              ->where(['DAR.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['DAR.id' => ':id']) : $initQuery;

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
                'P.project_code as charging',
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