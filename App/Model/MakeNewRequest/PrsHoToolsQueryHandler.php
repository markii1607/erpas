<?php
    namespace App\Model\MakeNewRequest;

    require_once('MakeNewRequestQueryHandler.php');

    use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;

    class PrsHoToolsQueryHandler extends MakeNewRequestQueryHandler {
        
        public function selectPowerTools()
        {
            $fields = array(
                'PT.id',
                'PT.code',
                'PT.specification',
                'PT.u_o_m as unit',
                'PTC.name as tool_category',
            );

            $join = array(
                'power_tool_categories PTC' =>  'PTC.id = PT.category'
            );

            $initQuery = $this->select($fields)
                              ->from('power_tools PT')
                              ->join($join)
                              ->where(array('PT.is_active' => ':is_active', 'PTC.is_active' => ':is_active'));


            return $initQuery;
        }

        public function selectHandTools()
        {
            $fields = array(
                'HT.id',
                'HT.code',
                'HT.specification',
                'HT.u_o_m as unit',
                'HTC.name as tool_category',
            );

            $join = array(
                'hand_tool_categories HTC'  =>  'HTC.id = HT.category'
            );

            $initQuery = $this->select($fields)
                              ->from('hand_tools HT')
                              ->join($join)
                              ->where(array('HT.is_active' => ':is_active', 'HTC.is_active' => ':is_active'));

            return $initQuery;
        }

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
            ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('A.id' => ':id')) : $initQuery;
            $initQuery = ($hasAcc_type) ? $initQuery->andWhere(array('A.account_type_id' => ':account_type_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectSignatories
         *
         * @param boolean $id
         * @return void
         */
        public function selectSignatories($id = false)
        {
            $fields = array(
                'SS.id',
                'SS.menu_id',
                'SS.signatories',
                'SS.no_of_signatory',
            );

            $initQuery = $this->select($fields)
                         ->from('signatory_sets SS')
                         ->where(array('SS.status' => ':status'));
            $initQuery = ($id) ? $initQuery->andWhere(array('SS.menu_id' => ':id')) : $initQuery;
 
            return $initQuery;
        }

        /**
         * selectEmployees
         *
         * @return void
         */
        public function selectEmployees($id = false, $department_id = false)
        {
            $fields = array(
                'U.id',
                'EI.position_id',
                'P.department_id',
                'P.name as position_name',
                'D.charging',
                'D.name as department_name',
                'CONCAT(PI.lname,", ",PI.fname," ",PI.mname) as fullname',
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
 
            return $initQuery;
        }

        public function insertPrTools($data = array())
        {
            $initQuery = $this->insert('pr_tools', $data);
            
            return $initQuery;
        }

        public function insertPrWorkItems($data = array())
        {
            $initQuery = $this->insert('pr_work_items', $data);
            
            return $initQuery;
        }

        public function insertPrToolAttachments($data = array())
        {
            $initQuery = $this->insert('pr_tool_attachments', $data);
            
            return $initQuery;
        }

        public function updatePurchaseRequisition($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }
    }
