<?php
    namespace App\Model\RequisitionFieldRate;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class RequisitionFieldRateQueryHandler extends QueryHandler {

        /**
         * `selectRequisitionField` Query string that will fetch RequisitionField.
         * @return string
         */
        public function selectRequisitionField($id = false, $prsId = false)
        {
            $fields = [
                'RF.id',
                'PR.id as prs_id',
                'PR.prs_no',
                'DATE_FORMAT(PR.date_requested, "%b %d, %Y") as date_requested',
                'PR.request_type_id',
                'PR.status',
                'PR.for_cancelation',
                'PS.id as project_id',
                'PS.project_code',
                'PS.name as project_name',
                'PS.location as project_site',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname, " - ", P.name) as requested_by',
                // 'P.name as position_name',
                // 'D.name as department_name',
                'IF(PS.project_code IS NULL, D.name, "CONSTRUCTION") as department_name',
                'RF.erf_no',
                'RF.status as rf_status',
                // 'RF.hr_status',
            ];

            $leftJoins = [
                'departments D'         => 'D.id = PR.department_id',
                'projects PS'           => 'PS.id = PR.project_id',
                'requisition_fields RF' => 'PR.id = RF.purchase_requisition_id'
            ];

            $joins = [
                'users U'                       => 'U.id = PR.created_by',
                'personal_informations PI'      => 'U.personal_information_id = PI.id',
                'employment_informations EI'    => 'PI.id = EI.personal_information_id',
                'positions P'                   => 'EI.position_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PR.status' => ':status', 'PR.request_type_id' => ':request_type_id', 'RF.status' => ':rf_status'])
                              ->andWhereNull(array('PR.for_cancelation'));

            $initQuery = ($id)    ? $initQuery->andWhere(['RF.id' => ':id'])                      : $initQuery;
            $initQuery = ($prsId) ? $initQuery->andWhere(['PR.id' => ':purchase_requisition_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrLabors` Query string that will fetch from table `pr_labors`.
         * @param  string $id
         * @param  string $prsId
         * @return string
         */
        public function selectPrLabors($id = false, $prsId = false)
        {
            $fields = [
                'PL.id',
                'PL.no_of_labor',
                'DATE_FORMAT(PL.start_date, "%b %d, %Y") as start_date',
                'PL.mandays',
                'PL.remarks',
                'P.id as position_id',
                'P.name as position_name',
                'A.name as account_name',
            ];

            $joins = [
                'positions P' => 'P.id = PL.position_id',
                'accounts A'  => 'PL.account_id = A.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_labors PL')
                              ->join($joins)
                              ->where(['PL.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['PL.id' => ':id'])                         : $initQuery;
            $initQuery = ($prsId) ? $initQuery->andWhere(['PL.pr_id' => ':purchase_requisition_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrWorkItems` Query string that will fetch from table `pr_work_items`.
         * @param  string $id
         * @param  string $prlId
         * @return string
         */
        public function selectPrWorkItems($id = false, $prlId = false)
        {
            $fields = [
                'PWI.id',
                'PWI.requested_labor',
                'IF(PWI.ps_swi_directs_id IS NULL, WII.wbs, SWI.wbs) as wbs',
                'IF(PWI.ps_swi_directs_id IS NULL, WII.name, WID.name) as wi_name',
                'IF(PWI.ps_swi_directs_id IS NULL, WICI.name, WICD.name) as wic_name',
            ];

            $leftJoins = [
                'ps_swi_directs PSD'        => 'PSD.id = PWI.ps_swi_directs_id',
                'sw_wis SWI'                => 'SWI.id = PSD.sw_wi_id',
                'work_items WID'            => 'WID.id = SWI.work_item_id',
                'work_item_categories WICD' => 'WICD.id = WID.work_item_category_id',
                
                'p_wi_indirects PWIN'       => 'PWI.p_wi_indirects_id = PWIN.id',
                'work_items WII'            => 'PWIN.work_item_id = WII.id',
                'work_item_categories WICI' => 'WII.work_item_category_id = WICI.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('prl_work_items PWI')
                              ->leftJoin($leftJoins)
                              ->where(['PWI.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['PWI.id' => ':id'])         : $initQuery;
            $initQuery = ($prlId) ? $initQuery->andWhere(['PWI.prl_id' => ':prl_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRequisitionManpowers` Query string that will fetch from table `requisition_manpowers`.
         * @param  string $id
         * @param  string $prsId
         * @return string
         */
        public function selectRequisitionManpowers($id = false, $requisitionId = false, $prsId = false)
        {
            $fields = [
                'RM.id',
                'RM.requisition_field_id',
                'RM.pr_labor_id',
                'RM.fname as first_name',
                'RM.mname as middle_name',
                'RM.lname as last_name',
                // 'RM.sname',
                'DATE_FORMAT(RM.birthdate, "%m/%d/%Y") as date_of_birth',
                'DATE_FORMAT(RM.date_hired, "%m/%d/%Y") as date_hired',
                'RM.sss_no',
                'RM.phil_no',
                'RM.address',
                'RM.signature',
                'RM.daily_rate',
                '"saved" as data_status'
            ];

            $joins = [
                'requisition_fields RF'  => 'RF.id = RM.requisition_field_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('requisition_manpower RM')
                              ->join($joins)
                              ->where(['RM.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['RM.id' => ':id'])                                           : $initQuery;
            $initQuery = ($requisitionId) ? $initQuery->andWhere(['RM.requisition_field_id' => ':requisition_id'])             : $initQuery;
            $initQuery = ($prsId)         ? $initQuery->andWhere(['RF.purchase_requisition_id' => ':purchase_requisition_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRequisitionField` Query string that will fetch RequisitionField.
         * @return string
         */
        public function selectRequisitionFieldLabor($id = false)
        {
            $fields = [
                'PR.id',
                'PR.prs_no',
                'DATE_FORMAT(PR.date_requested, "%m/%d/%Y") as date_requested',
                'PR.request_type_id',
                'PR.status',
                'PR.for_cancelation',
                'P.name',
            ];

            $joins = [
                'pr_labors PRL'             => 'PRL.pr_id = PR.id',
                'position P'                => 'P.position_id = PRL.id',
                'pr_work_items PRWI'        => 'PRWI.prl_id = PR.id',

            ];

            $leftJoins = [
                'ps_swi_directs PSD'        => 'PRWI.ps_swi_directs_id = PSD.id',
                'departments D'             => 'D.department_id = PR.id',
                'projects PS'               => 'PS.project_id = PR.id',
                'requisition_fields RF'     => 'RF.purchase_requisition_id = PR.id',
                'sw_wis SWW'                => 'PSD.sw_wi_id = SWW.id',
                'work_items WI'             => 'SWW.work_item_id = WI.id',



            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PR.status' => ':status', 'PR.request_type_id' => ':request_type_id'])
                              ->andWhereNull(array('PR.for_cancelation'));


            $initQuery = ($id)   ? $initQuery->andWhere(['PR.id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['R.name' => ':name']) : $initQuery;

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
                'D.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectEmployee($id = false, $name = false, $erId = false)
        {
            $fields = [
                'E.id',
                'DATE_FORMAT(E.date_needed, "%m/%d/%Y") as date_needed',
                'E.no_of_employee',
                'E.position_id',
                'E.salary_range',
                'E.names',
                'E.remarks',
                'P.name as position_name',
                // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                // '"" as status'

            ];

            $initQuery = $this->select($fields)
                              ->from('er_personnels E')
                              ->join(['positions P'=> 'P.id = E.position_id'])
                              ->where(['E.is_active' => ':is_active']);


            $initQuery = ($id)   ? $initQuery->andWhere(['E.employee_requisition_id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['E.name' => ':name']) : $initQuery;
            $initQuery = ($erId) ? $initQuery->andWhere(['E.employee_requisition_id' => ':employee_requisition_id']) : $initQuery;

            return $initQuery;
        }

        public function selectPositions($id = false)
        {
            $fields = [
                'P.id',
                'P.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(['P.is_active' => ':is_active']);

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

        public function selectRequisitionFieldInfo()
        {
            $fields = array(
                'RF.id',
                'RF.erf_no',
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_fields RF')
                              ->where(array('RF.is_active' => ':is_active'));
                            //   ->orderBy('RF.erf_no', 'DESC')
                            //   ->limit(1);

            return $initQuery;
        }

        public function selectRequisitionManpowerAttachments($requisition_manpower_id = false)
        {
            $fields = array(
                'RMA.id',
                'RMA.requisition_manpower_id',
                'RMA.file_name',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_manpower_attachments RMA')
                              ->where(array('RMA.is_active' => ':is_active'));

            $initQuery = ($requisition_manpower_id) ? $initQuery->andWhere(array('RMA.requisition_manpower_id' => ':requisition_manpower_id')) : $initQuery;

            return $initQuery;
        }

         /**
         * `insertManpower` Query string that will insert to table `requisition_manpower`
         * @return string
         */
        public function insertManpower($data = [])
        {
            $initQuery = $this->insert('requisition_manpower', $data);

            return $initQuery;
        }

        /**
         * `insertRequisition` Query string that will insert to table `requisition`
         * @return string
         */
        public function insertRequisition($data = [])
        {
            $initQuery = $this->insert('requisitions', $data);

            return $initQuery;
        }

        /**
         * `insertErPersonnel` Query string that will insert to table `er_personnels`
         * @return string
         */
        public function insertErPersonnel($data = [])
        {
            $initQuery = $this->insert('er_personnels', $data);

            return $initQuery;
        }

        public function insertRequisitionField($data = [])
        {
            $initQuery = $this->insert('requisition_fields', $data);

            return $initQuery;
        }

        public function insertReequisitionManpowerAttachment($data = [])
        {
            $initQuery = $this->insert('requisition_manpower_attachments', $data);

            return $initQuery;
        }

        /**
         * `updateRequisition` Query string that will update specific department information from table `requisition`
         * @return string
         */
        public function updateRequisition($id = '', $data = [])
        {
            $initQuery = $this->update('requisitions', $id, $data);

            return $initQuery;
        }

        public function updateRequisitionManpower($id = '', $data = [])
        {
            $initQuery = $this->update('requisition_manpower', $id, $data);

            return $initQuery;
        }

        public function updateRequisitionField($id = '', $data = [])
        {
            $initQuery = $this->update('requisition_fields', $id, $data);

            return $initQuery;
        }

        public function updateRequisitionManpowerAttachment($id = '', $data = [])
        {
            $initQuery = $this->update('requisition_manpower_attachments', $id, $data);

            return $initQuery;
        }

        /**
         * `updateErPersonnel` Query string that will update specific department information from table `er_personnels`
         * @return string
         */
        public function updateErPersonnel($id = '', $data = [])
        {
            $initQuery = $this->update('er_personnels', $id, $data);

            return $initQuery;
        }


        /**
         * `deleteErPersonnel` Query string that will delete specific er personnel.
         * @param  boolean $id
         * @return string
         */
        public function deleteErPersonnel($id = false)
        {
            $initQuery = $this->delete('er_personnels')
                              ->where(['employee_requisition_id' => ':employee_requisition_id']);

            return $initQuery;
        }
    }