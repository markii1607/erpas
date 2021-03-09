<?php
    namespace App\Model\MakeNewRequest;

    require_once('MakeNewRequestQueryHandler.php');

    use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;

    class PrsFoMedicalQueryHandler extends MakeNewRequestQueryHandler {

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
            'PSWID.quantities',
            'SWI.id as swi_id',
            'SWI.alternative_name as swi_name',
            'SWI.unit as swi_unit',
            'WIC.id as wic_id',
            'WIC.name as wic_name',
            'WIC.part',
            'WI.id as wi_id',
            'WI.name as wi_name',
            'WI.item_no',
            'WI.unit',
            'WI.direct',
            'SWIC.id as swic_id',
            'SWIC.wbs as wic_wbs',
            'SWI.wbs as swi_wbs',
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
            'PWII.quantities',
            'WIC.id as wic_id',
            'WIC.name as wic_name',
            'WI.id as wi_id',
            'WI.name as wi_name',
            'WI.item_no',
            'WI.unit',
            'WI.direct',
            'WIC.part',
            'WIC.code as wic_wbs',
            'WI.wbs as wi_wbs'
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

        public function selectPsdMaterials($id = false, $psSwiDirectId = false)
        {
            $fields = [
            'PSDM.id',
            'PSDM.msb_supplier_id',
            'PSDM.total_quantity as material_quantity',
            'PSDM.unit_cost',
            'PSDM.total_cost',
            'MSB.id as msb_id',
            'MSBS.code as material_code',
            'MS.id as material_specification_id',
            'MS.specs',
            'LOWER(M.name) as material_name',
            'MC.name as material_category_name',
            'MU.unit',
            // 'MSBS.unit',
            '"1" as direct',
            '"!empty" as status',
            ];

            $joins = [
            'msb_suppliers MSBS'                => 'MSBS.id = PSDM.msb_supplier_id',
            'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
            'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
            'materials M'                       => 'M.id = MS.material_id',
            'material_categories MC'            => 'MC.id = M.material_category_id',
            'material_units MU'                 => 'MU.id = MSBS.material_unit_id',
            ];

            $initQuery = $this->select($fields)
            ->from('psd_materials PSDM')
            ->join($joins)
            ->where(['PSDM.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PSDM.id' => ':id'])                             : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PSDM.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        public function selectPwiMaterials($id = false, $pwiIndirectId = false, $psSwiDirectId = false)
        {
            $fields = [
            'PWIM.id',
            'PWIM.msb_supplier_id',
            'PWIM.total_quantity as material_quantity',
            'PWIM.unit_cost',
            'PWIM.total_cost',
            'MSB.id as msb_id',
            'MSBS.code as material_code',
            'MS.id as material_specification_id',
            'MS.specs',
            'LOWER(M.name) as material_name',
            'MC.name as material_category_name',
            'MU.unit',
            // 'MSBS.unit',
            '"0" as direct',
            '"!empty" as status',
            ];

            $joins = [
            'msb_suppliers MSBS'                => 'MSBS.id = PWIM.msb_supplier_id',
            'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
            'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
            'materials M'                       => 'M.id = MS.material_id',
            'material_categories MC'            => 'MC.id = M.material_category_id',
            'material_units MU'                 => 'MU.id = MSBS.material_unit_id',
            ];

            $initQuery = $this->select($fields)
            ->from('pwi_materials PWIM')
            ->join($joins)
            ->where(['PWIM.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PWIM.id' => ':id'])                             : $initQuery;
            $initQuery = ($pwiIndirectId) ? $initQuery->andWhere(['PWIM.p_wi_indirect_id' => ':p_wi_indirect_id']) : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PWIM.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        public function selectProjectMaterialDeliverySequences($id = false, $psdMaterialId = false, $pwiMaterialId = false)
        {
            $fields = [
                'PMDS.sequence_no',
                'PMDS.delivery_date as delivery_date',
                'PMDS.quantity'
            ];
        
            $initQuery = $this->select($fields)
                ->from('project_material_delivery_sequences PMDS')
                ->where(['PMDS.is_active' => ':is_active']);
        
            $initQuery = ($id)            ? $initQuery->andWhere(['PMDS.id' => ':id'])                           : $initQuery;
            $initQuery = ($psdMaterialId) ? $initQuery->andWhere(['PMDS.psd_material_id' => ':psd_material_id']) : $initQuery;
            $initQuery = ($pwiMaterialId) ? $initQuery->andWhere(['PMDS.pwi_material_id' => ':pwi_material_id']) : $initQuery;
        
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

        public function selectMaterialSpecsImages($specsId = false)
        {
            $fields = array(
                'MSI.id',
                'MSI.image'
            );

            $initQuery = $this->select($fields)
                              ->from('material_specification_images MSI')
                              ->where(array('MSI.is_active' => ':is_active'));

            $initQuery = ($specsId) ? $initQuery->andWhere(array('MSI.material_specification_id' => ':material_specification_id')) : $initQuery;

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

        public function insertPrmDeliverySequences($data = array())
        {
            $initQuery = $this->insert('prm_delivery_sequences', $data);
            
            return $initQuery;
        }

        public function updatePurchaseRequisition($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }
    }
