<?php

    namespace App\Model\PrsApprovals;

    require_once('PrsApprovalsQueryHandler.php');

    use App\Model\PrsApprovals\PrsApprovalsQueryHandler;

    class PrsMedicalQueryHandler extends PrsApprovalsQueryHandler
    {
        /**
         * selectPrss
         *
         * @param boolean $id
         * @param boolean $userId
         * @return void
         */
        public function selectPrs($id = false, $request_type = false, $status = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.department_id',
                'PR.prs_no',
                'PR.request_type_id',
                'PR.status',
                'PR.for_cancelation',
                'PR.head_id',
                'DATE_FORMAT(PR.created_at, "%M %d, %Y %r") as date_requested',
                'RT.name as request_type_name',
                'PR.signatories',
                'PR.prev_id',
                'IF(PR.prev_id IS NULL, "", (SELECT prs_no FROM purchase_requisitions WHERE id = PR.prev_id)) as reference_prs_no',
                'IF(PR.prev_id IS NOT NULL, "resubmitted", "new") as resubmission_status',
                'PR.supplementary',
                'PR.remarks',
                'CONCAT(PI.fname," ",PI.mname," ",PI.lname) as requestor',
            );

            $joins = array(
                'request_types RT' => 'RT.id = PR.request_type_id',
                'users U' => 'U.id = PR.user_id',
                'personal_informations PI' => 'PI.id = U.personal_information_id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
            $initQuery = ($request_type) ? $initQuery->andWhere(array('PR.request_type_id' => ':request_type')) : $initQuery;
            $initQuery = ($status) ? $initQuery->andWhere(array('PR.status' => ':status')) : $initQuery;

            return $initQuery;
        }

        public function selectRequestItems($id = false, $prsId = false)
        {
            $fields = array(
                'PM.id',
                'PM.pr_id',
                'PM.p_wi_indirect_id',
                'PM.material_specification_brand_id',
                'PM.account_id',
                'PM.quantity',
                'PM.unit_of_measurement as unit',
                'DATE_FORMAT(PM.date_needed, "%b %d, %Y") as date_needed',
                'PM.remarks',
                'PM.status',
                'PWI.quantities as work_volume',
                'WI.item_no',
                'WI.name as item_name',
                'WI.unit as wv_unit',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
            );

            $joins = array(
                'p_wi_indirects PWI'        =>  'PWI.id = PM.p_wi_indirect_id',
                'work_items WI'             =>  'WI.id = PWI.work_item_id',
                'work_item_categories WIC'  =>  'WIC.id = WI.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pr_medical_materials PM')
                              ->leftJoin($joins)
                              ->where(array('PM.is_active' => ':is_active'));

            $initQuery = ($id)      ? $initQuery->andWhere(array('PM.id' => ':id'))          : $initQuery;
            $initQuery = ($prsId)   ? $initQuery->andWhere(array('PM.pr_id' => ':prs_id'))   : $initQuery;

            return $initQuery;
        }

        public function selectPrMedicalSignatories($id = false, $pr_medical_material_id = false, $user_id = false)
        {
            $fields = array(
                'PS.id',
                'PS.pr_medical_material_id',
                'PS.user_id',
                'PS.status',
                'IF(PS.comment IS NOT NULL, PS.comment, "") as comment',
                'DATE_FORMAT(PS.updated_at, "%b %d, %Y %H:%i:%s") as updated_at',
                'CONCAT(PI.fname, " ", PI.lname, " ", PI.sname) as fullname',
                'P.name as position_name',
                'D.name as dept_name',
            );

            $joins = array(
                'users U'                       =>  'U.id = PS.user_id',
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id',
                'departments D'                 =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pr_medical_signatories PS')
                              ->join($joins)
                              ->where(array('PS.is_active' => ':is_active'));

            $initQuery = ($id)         ? $initQuery->andWhere(array('PS.id' => ':id'))                 : $initQuery;
            $initQuery = ($pr_medical_material_id) ? $initQuery->andWhere(array('PS.pr_medical_material_id' => ':pr_medical_material_id')) : $initQuery;
            $initQuery = ($user_id)    ? $initQuery->andWhere(array('PS.user_id' => ':user_id'))       : $initQuery;

            return $initQuery;
        }

        public function selectMaterialSpecs($msb_id = false, $filter = false, $limit = '')
        {
            $fields = array(
                'MS.id',
                'MS.material_id',
                'MS.specs',
                'MS.code',
                // 'CONCAT(MS.code, MSBS.code) as mat_code',
                'MSB.id as msb_id',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = MSB.id LIMIT 1) as mat_code',
                'M.name as material_name'
            );

            $joins = array(
                'materials M'                       =>  'M.id = MS.material_id',
                'material_specification_brands MSB' =>  'MSB.material_specification_id = MS.id',
                // 'msb_suppliers MSBS'                =>  'MSBS.material_specification_brand_id = MSB.id'
            );

            $initQuery = $this->select($fields)
                              ->from('material_specifications MS')
                              ->join($joins)
                              ->where(array('MS.is_active' => ':is_active'));


            $initQuery = ($msb_id)      ? $initQuery->andWhere(array('MSB.id' => ':msb_id')) : $initQuery;
            $initQuery = ($filter)      ? $initQuery->logicEx('AND')->orWhereLike(array('MS.specs' => ':filter', 'MS.code' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT ' . $limit . ', 50') : $initQuery;

            return $initQuery;
        }

        public function selectDeliverySequence($id = false, $prmId = false)
        {
            $fields = array(
                'PMD.id',
                'PMD.pr_medical_material_id',
                'PMD.seq_no',
                'PMD.quantity',
                'DATE_FORMAT(PMD.delivery_date, "%b %d, %Y") as delivery_date',
            );

            $initQuery = $this->select($fields)
                              ->from('prm_delivery_sequences PMD')
                              ->where(array('PMD.is_active' => ':is_active'));

            $initQuery = ($id)      ? $initQuery->andWhere(array('PMD.id' => ':id')) : $initQuery;
            $initQuery = ($prmId)   ? $initQuery->andWhere(array('PMD.pr_medical_material_id' => ':prm_id')) : $initQuery;

            return $initQuery;
        }

        public function selectAccounts($id = '')
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
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('A.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectUser($id = false, $pid = false)
        {
            $fields = array(
            'U.personal_information_id',
            'CONCAT(PI.lname,", ",PI.fname," ", PI.mname) as employee',
            'EI.position_id',
            'P.name as position',
            'P.department_id',
            'D.name as department',
            );

            $join = array(
            'personal_informations PI' => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P' => 'EI.position_id = P.id',
            'departments D' => 'P.department_id = D.id'
            );

            $initQuery = $this->select($fields)
            ->from('users U')
            ->leftJoin($join)
            ->where(array('U.is_active' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;
            $initQuery = ($pid) ? $initQuery->andWhere(array('PI.id' => ':p_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjects($id = false, $userId = false)
        {
            $fields = array(
            'P.id',
            'P.project_code',
            'P.name',
            'P.location',
            'P.is_on_going',
            );

            $joins = array(
            'p_wds PWDS' => 'PWDS.project_id = P.id',
            );

            $initQuery = $this->select($fields)
            ->from('projects P')
            ->join($joins)
            ->where(array('P.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectDepartments($id = false, $userId = false)
        {
            $fields = array(
            'D.id',
            'D.code',
            'D.charging',
            'D.name',
            );

            $initQuery = $this->select($fields)
            ->from('departments D')
            ->where(array('D.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('D.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrMedicalAttachments($pr_medical_material_id = false)
        {
            $fields = array(
                'PRM.id',
                'PRM.pr_medical_material_id',
                'PRM.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_medical_attachments PRM')
                              ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($pr_medical_material_id) ? $initQuery->andWhere(array('PRM.pr_medical_material_id' => ':pr_medical_material_id')) : $initQuery;

            return $initQuery;
        }

        public function insertPrMedicalSignatories($data = array())
        {
            $initQuery = $this->insert('pr_medical_signatories', $data);

            return $initQuery;
        }

        public function updateRequest($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }

        public function updatePrMedicalMaterial($id = '', $data = array())
        {
            $initQuery = $this->update('pr_medical_materials', $id, $data);

            return $initQuery;
        }
    }
