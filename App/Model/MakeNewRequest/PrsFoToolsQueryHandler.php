<?php
    namespace App\Model\MakeNewRequest;

    require_once('MakeNewRequestQueryHandler.php');

    use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;

    class PrsFoToolsQueryHandler extends MakeNewRequestQueryHandler {
        
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

        public function selectPwds($id = false, $projectId = false)
        {
            $fields = [
            'PWD.id',
            'WD.id as wd_id',
            'WD.name as wd_name',
            'WD.wbs'
            ];

            $initQuery = $this->select($fields)
            ->from('p_wds PWD')
            ->join(['work_disciplines WD' => 'WD.id = PWD.work_discipline_id'])
            ->where(['PWD.is_active' => ':is_active', 'WD.is_active' => ':is_active']);

            $initQuery = ($id)        ? $initQuery->andWhere(['PWD.id' => ':id'])                 : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PWD.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        public function selectPwSps($id = false, $pwdId = false)
        {
            $fields = [
            'PWSP.id',
            'PWSP.name',
            'SP.id as sp_id',
            'SP.name as sp_name',
            'SP.wbs'
            ];

            $initQuery = $this->select($fields)
            ->from('pw_sps PWSP')
            ->join(['sub_projects SP' => 'SP.id = PWSP.sub_project_id'])
            ->where(['PWSP.is_active' => ':is_active', 'SP.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['PWSP.id' => ':id'])           : $initQuery;
            $initQuery = ($pwdId) ? $initQuery->andWhere(['PWSP.p_wd_id' => ':p_wd_id']) : $initQuery;

            return $initQuery;
        }

        public function selectPsSwiDirects($id = false, $pwSpId = false)
        {
            $fields = [
                'PSWID.id',
                'PSWID.quantities as work_volume',
                'SWI.id as swi_id',
                'SWI.alternative_name as swi_name',
                'SWI.unit as swi_unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part',
                'WI.id as wi_id',
                'WI.name as item_name',
                'WI.item_no',
                'WI.unit',
                'WI.direct',
                'SWIC.id as swic_id',
                'SWIC.wbs as wic_wbs',
                'SWI.wbs',
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

        public function selectPwiIndirects($id = false, $projectId = false)
        {
            $fields = [
                'PWII.id',
                'PWII.quantities as work_volume',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WI.id as wi_id',
                'WI.name as item_name',
                'WI.item_no',
                'WI.unit',
                'WI.direct',
                'WIC.part',
                'WIC.code as wic_wbs',
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
            ->where(array('A.is_active' => ':is_active', 'A.account_type_id' => ':account_type_id'));

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
                'PI.id as pi_id',
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
