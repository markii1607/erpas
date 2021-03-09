<?php
    namespace App\Model\MakeNewRequest;

    require_once('MakeNewRequestQueryHandler.php');

    use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;

    class PrsSuMedicalQueryHandler extends MakeNewRequestQueryHandler {
        
        public function selectMedicalMaterials($filter = false, $limit = '')
        {
            $fields = array(
                'MS.id',
                'M.id as material_id',
                'MC.id as category_id',
                'MSB.id as msb_id',
                'MS.specs as specification',
                'MS.code',
                'M.name as material_name',
                'MC.name as category',
                // 'MSBS.code'
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = MSB.id LIMIT 1) as material_code',
                '(SELECT MU.unit FROM msb_suppliers MSBS JOIN material_units MU ON MU.id = MSBS.material_unit_id WHERE MSBS.material_specification_brand_id = MSB.id LIMIT 1) as unit'
            );

            $leftJoins = array(
                'material_specification_brands MSB'     =>      'MSB.material_specification_id = MS.id',
                // 'msb_suppliers MSBS'                    =>      'MSBS.material_specification_brand_id = MSB.id',
                'materials M'                           =>      'M.id = MS.material_id',
                'material_categories MC'                =>      'MC.id = M.material_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('material_specifications MS')
                              ->leftJoin($leftJoins)
                              ->where(array('MS.is_active' => ':is_active', 'M.is_active' => ':is_active', 'MC.is_active' => ':is_active'))
                              ->andWhereLike(array('MC.name' => ':category'));

            $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('M.name' => ':filter', 'MS.specs' => ':filter', 'MS.code' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 25') : $initQuery;

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
        public function selectEmployees($id = false, $department_id = false, $user_id = false)
        {
            $fields = array(
                'PI.id',
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

            $initQuery = ($id)              ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;
            $initQuery = ($department_id)   ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($user_id)         ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;
 
            return $initQuery;
        }

        public function insertPrMedicalMaterials($data = array())
        {
            $initQuery = $this->insert('pr_medical_materials', $data);
            
            return $initQuery;
        }

        public function insertPrMedicalAttachments($data = array())
        {
            $initQuery = $this->insert('pr_medical_attachments', $data);
            
            return $initQuery;
        }

        public function updatePurchaseRequisition($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }
    }
