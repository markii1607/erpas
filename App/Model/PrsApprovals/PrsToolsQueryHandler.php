<?php

    namespace App\Model\PrsApprovals;

    require_once('PrsApprovalsQueryHandler.php');

    use App\Model\PrsApprovals\PrsApprovalsQueryHandler;

    class PrsToolsQueryHandler extends PrsApprovalsQueryHandler
    {
        /**
         * selectPrs
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
                'IF(PR.project_id IS NOT NULL, 0, 1) as charge_to',
                'PR.status',
                'PR.prs_no',
                'PR.request_type_id',
                'PR.for_cancelation',
                'PR.head_id',
                'DATE_FORMAT(PR.date_requested, "%M %d, %Y %r") as date_requested',
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
                'request_types RT'  => 'RT.id = PR.request_type_id',
                'users U'           => 'U.id = PR.user_id',
                'personal_informations PI'   =>  'PI.id = U.personal_information_id',
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($id)           ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
            $initQuery = ($request_type) ? $initQuery->andWhere(array('PR.request_type_id' => ':request_type')) : $initQuery;
            $initQuery = ($status)       ? $initQuery->andWhere(array('PR.status' => ':status')) : $initQuery;

            return $initQuery;
        }

        public function selectPrTools($id = false, $prsId = false)
        {
            $fields = array(
                'PT.id',
                'PT.pr_id',
                'PT.category as int_category',
                'IF(PT.category IS NOT NULL, IF(PT.category != 1, "INDIRECT", "DIRECT"), "") as category',
                'PT.power_tool_id',
                'PT.hand_tool_id',
                'PT.account_id',
                'PT.remarks',
                'PT.requested_units',
                'PT.unit_of_measurement',
                'PT.status',
                'DATE_FORMAT(PT.date_needed, "%b %d, %Y") as date_needed',
                'IF(PT.power_tool_id IS NOT NULL, "Power Tool", "Hand Tool") as tool_type',
                'IF(PT.power_tool_id IS NOT NULL, PTO.code, HTO.code) as tool_code',
                'IF(PT.power_tool_id IS NOT NULL, PTO.specification, HTO.specification) as tool_specs',
            );

            $leftJoins = array(
                'power_tools PTO'   =>  'PTO.id = PT.power_tool_id',
                'hand_tools HTO'    =>  'HTO.id = PT.hand_tool_id',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_tools PT')
                              ->leftJoin($leftJoins)
                              ->where(array('PT.is_active' => ':is_active'));

            $initQuery = ($id)      ?   $initQuery->andWhere(array('PT.id' => ':id')) : $initQuery;
            $initQuery = ($prsId)   ?   $initQuery->andWhere(array('PT.pr_id' => ':prs_id'))        : $initQuery;

            return $initQuery;
        }

        public function selectPrWorkItems($id = false, $pr_tool_id = false)
        {
            $fields = array(
                'PW.id',
                'PW.pr_tool_id',
                'PW.wi_category_id',
                'PW.wi_id',
                'PW.work_volume',
                'PW.wv_unit',
                'PW.wbs',
                'PW.requested_units',
                'PW.unit_of_measurement',
                'DATE_FORMAT(PW.date_needed, "%b %d, %Y") as date_needed',
                'WI.item_no',
                'WI.name as wi_name',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name'
            );

            $leftJoins = array(
                'work_items WI'             =>  'WI.id = PW.wi_id',
                'work_item_categories WIC'  =>  'WIC.id = PW.wi_category_id'
            );
            
            $initQuery = $this->select($fields)
                              ->from('pr_work_items PW')
                              ->leftJoin($leftJoins)
                              ->where(array('PW.is_active' => ':is_active'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('PW.id' => ':id'))                 : $initQuery;
            $initQuery = ($pr_tool_id)  ? $initQuery->andWhere(array('PW.pr_tool_id' => ':pr_tool_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrToolSignatories($id = false, $pr_tool_id = false, $user_id = false)
        {
            $fields = array(
                'PTS.id',
                'PTS.pr_tool_id',
                'PTS.user_id',
                'PTS.status',
                'IF(PTS.comment IS NOT NULL, PTS.comment, "") as comment',
                'DATE_FORMAT(PTS.updated_at, "%b %d, %Y %H:%i:%s") as updated_at',
                'CONCAT(PI.fname, " ", PI.lname, " ", PI.sname) as fullname',
                'P.name as position_name',
                'D.name as dept_name',
            );

            $joins = array(
                'users U'                       =>  'U.id = PTS.user_id',
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id',
                'departments D'                 =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pr_tool_signatories PTS')
                              ->join($joins)
                              ->where(array('PTS.is_active' => ':is_active'));

            $initQuery = ($id)         ? $initQuery->andWhere(array('PTS.id' => ':id'))                 : $initQuery;
            $initQuery = ($pr_tool_id) ? $initQuery->andWhere(array('PTS.pr_tool_id' => ':pr_tool_id')) : $initQuery;
            $initQuery = ($user_id)    ? $initQuery->andWhere(array('PTS.user_id' => ':user_id'))       : $initQuery;

            return $initQuery;
        }

        public function selectPrToolAttachments($pr_tool_id = false)
        {
            $fields = array(
                'PTA.id',
                'PTA.pr_tool_id',
                'PTA.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_tool_attachments PTA')
                              ->where(array('PTA.is_active' => ':is_active'));

            $initQuery = ($pr_tool_id) ? $initQuery->andWhere(array('PTA.pr_tool_id' => ':pr_tool_id')) : $initQuery;

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

            $initQuery = ($id)  ? $initQuery->andWhere(array('U.id' => ':id'))    : $initQuery;
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

        public function insertPrToolSignatories($data = array())
        {
            $initQuery = $this->insert('pr_tool_signatories', $data);

            return $initQuery;
        }

        public function updateRequest($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }

        public function updatePrTools($id = '', $data = array())
        {
            $initQuery = $this->update('pr_tools', $id, $data);

            return $initQuery;
        }

        public function updatePrToolSignatories($id = '', $data = array())
        {
            $initQuery = $this->update('pr_tool_signatories', $id, $data);

            return $initQuery;
        }
    }
