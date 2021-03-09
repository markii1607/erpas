<?php
    namespace App\Model\MakeNewRequest;

    require_once('MakeNewRequestQueryHandler.php');

    use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;
    //  use App\AbstractClass\QueryHandler;

    class PrsFoPpeQueryHandler extends MakeNewRequestQueryHandler {
        public function selectNewNumber($prs_no = false)
        {
          $fields = array(
            'PR.id',
            'PR.prs_no'
          );
      
          $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR ORDER BY PR.id DESC LIMIT 0, 1');
      
          $initQuery = ($prs_no) ? $initQuery->andWhere(array('RT.prs_no' => ':prs_no')) : $initQuery;
      
          return $initQuery;
        }
      
        public function selectRequestTypes($id = false, $userId = false)
        {
          $fields = array(
            'RT.id',
            'RT.name',
            'RT.cost_code',
            'RT.updated_by',
            'RT.updated_at',
            'RT.status'
          );
          $initQuery = $this->select($fields)
            ->from('request_types RT')
            ->where(array('RT.status' => ':status'));
          $initQuery = ($id) ? $initQuery->andWhere(array('RT.id' => ':id')) : $initQuery;
      
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
      
        public function selectProjects($id = false, $userId = false)
        {
          $fields = array(
            'P.id',
            'P.project_code',
            'P.name',
            'P.location',
            'P.longitude',
            'P.latitude',
            'P.is_on_going',
          );
      
          $joins = array(
            'p_wds PWDS' => 'PWDS.project_id = P.id',
            'transactions T' => 'T.id = P.transaction_id'
          );
          $initQuery = $this->select($fields)
            ->from('projects P')
            ->join($joins)
            ->where(array('P.is_active' => ':is_active', 'T.status' => ':status'));
      
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
      
        public function selectPsdMaterials($id = false, $psSwiDirectId = false)
        {
          $fields = [
            'PSDM.id',
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
            'MS.material_id'
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
            'MS.material_id'
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
      
        public function selectPsdEquipments($id = false, $psSwiDirectId = false)
        {
          $fields = [
            'pe.id',
            'pe.ps_swi_direct_id',
            'pe.equipment_id',
            // 'pe.mob_plan_from as mob_plan_dateFrom',
            // 'pe.mob_plan_to as mob_plan_dateTo',
            'pe.duration as equipment_days',
            'pe.equipment_days as equipment_days_total',
            'pe.equipment_type_id',
            'ET.name as equipment_type_name',
            'pe.rental_rate',
            'pe.no_of_equipment',
            'pe.total as total_cost',
            'pe.is_active',
            '"1" as direct',
            'E.model as equipment_desc',
            'E.cost_code as equipment_code',
            'DATE_FORMAT( pe.mob_plan_from,  "%m-%d-%Y") as mob_plan_dateFrom',
            'DATE_FORMAT( pe.mob_plan_to,  "%m-%d-%Y") as mob_plan_dateTo'
          ];
      
          $joins = array(
            'equipments E' => 'E.id = pe.equipment_id',
            'equipment_types ET' => 'ET.id = pe.equipment_type_id'
          );
      
          $initQuery = $this->select($fields)
            ->from('psd_equipments pe')
            ->join($joins)
            ->where(['pe.is_active' => 1]);
      
          $initQuery = ($id)            ? $initQuery->andWhere(['pe.id' => ':id'])                             : $initQuery;
          $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['pe.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;
      
          return $initQuery;
        }
      
        public function selectPwiEquipments($id = false, $pwiIndirectId = false, $psSwiDirectId = false)
        {
          $fields = [
            'PWIE.id',
            'PWIE.duration as equipment_days',
            'PWIE.equipment_days as equipment_days_total',
            'PWIE.equipment_type_id',
            'ET.name as equipment_type_name',
            // 'PWIE.equipment_days',
            'PWIE.rental_rate',
            'PWIE.total as total_cost',
            'PWIE.no_of_equipment',
            'PWIE.mat_arr',
            'DATE_FORMAT( PWIE.mob_plan_from,  "%m-%d-%Y") as mob_plan_dateFrom',
            'DATE_FORMAT( PWIE.mob_plan_to,  "%m-%d-%Y") as mob_plan_dateTo',
            'E.cost_code as equipment_code',
            'LOWER(E.model) as equipment_desc',
            '"0" as direct',
          ];
      
          $joins = [
            'equipments E' => 'E.id = PWIE.equipment_id',
            'equipment_types ET' => 'ET.id = PWIE.equipment_type_id'
          ];
      
          $initQuery = $this->select($fields)
            ->from('pwi_equipments PWIE')
            ->join($joins)
            ->where(['PWIE.is_active' => ':is_active']);
      
          $initQuery = ($id)            ? $initQuery->andWhere(['PWIE.id' => ':id'])                             : $initQuery;
          $initQuery = ($pwiIndirectId) ? $initQuery->andWhere(['PWIE.p_wi_indirect_id' => ':p_wi_indirect_id']) : $initQuery;
          $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PWIE.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;
      
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
      
        public function selectPsdLabor($ps_swi_direct_id = false, $p_wi_indirect_id = false)
        {
          $fields = array(
            'PL.id',
            'PL.ps_swi_direct_id',
            'PL.p_wi_indirect_id',
            'IF(PL.ps_swi_direct_id IS NULL, 0,PL.skilled_workers) as indirect_skilled_workers',
            'IF(PL.p_wi_indirect_id IS NULL, 0,PL.skilled_workers) as direct_skilled_workers',
            'IF(PL.ps_swi_direct_id IS NULL, 0,PL.common_workers) as indirect_common_workers',
            'IF(PL.p_wi_indirect_id IS NULL, 0,PL.common_workers) as direct_common_workers',
            // 'PL.common_workers',
            'PL.labor_composition_skilled',
            'PL.labor_composition_common',
            'PL.duration',
            'PL.unit_cost_rate_skilled',
            'PL.unit_cost_rate_common',
            'PL.total_cost_skilled',
            'PL.total_cost_common',
            'PL.total_cost',
            'PL.total_mandays',
            '"saved" as data_status'
          );
      
          $initQuery = $this->select($fields)
            ->from('psd_labors PL')
            ->where(array('PL.is_active' => ':is_active'));
      
          $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PL.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
          $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PL.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;
      
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
      
        public function selectSeries($id = false, $userId = false, $filter = false, $limit = '')
        {
          $fields = array(
            'DISTINCT(MSBS.code) as material_code',
            'MS.id',
            'MS.id as material_specification_id',
            'MS.material_id',
            'MS.specs',
            'MS.code',
            'MU.unit',
            // 'MSBS.unit',
            'M.name as material_name',
            'MC.name as material_category_name'
          );
      
          $joins = array(
            'material_categories MC' => 'MC.id = MS.category_id',
            'materials M' => 'M.id = MS.material_id',
            'msb_suppliers MSBS' => 'MSBS.material_specification_brand_id = MS.id',
            'material_units MU' => 'MU.id = MSBS.material_unit_id'
          );
      
          $initQuery = $this->select($fields)
            ->from('material_specifications MS')
            ->join($joins)
            ->where(array('MS.is_active' => ':is_active'));
      
          $initQuery = $initQuery->andWhere(array('MS.material_id' => '536'));
          $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;
          $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('MS.specs' => ':filter', 'MS.code' => ':filter')) : $initQuery;
          $initQuery = $initQuery->logicEx('ORDER BY MSBS.code ASC ');
          $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT ' . $limit . ', 50') : $initQuery;
      
          return $initQuery;
        }
      
        public function selectEquipmentTypes($id = false, $category = false, $cost_code = false, $classification = false)
        {
          $fields = array(
            'ET.id',
            'ET.equipment_category_id',
            'ET.code',
            'ET.cost_code',
            'ET.name',
            'ET.classification',
          );
      
          $initQuery = $this->select($fields)
            ->from('equipment_types ET')
            ->where(array('ET.is_active' => ':is_active'));
      
          $initQuery = ($id) ? $initQuery->andWhere(array('ET.id' => ':id')) : $initQuery;
          $initQuery = ($category) ? $initQuery->andWhere(array('ET.equipment_category_id' => ':equipment_category_id')) : $initQuery;
          $initQuery = ($cost_code) ? $initQuery->andWhere(array('ET.cost_code' => ':cost_code')) : $initQuery;
          $initQuery = ($classification) ? $initQuery->andWhere(array('ET.classification' => ':classification')) : $initQuery;
      
          return $initQuery;
        }
      
      
        // ---------------------------------------------------------------------------------------------------------------------------------
        public function selectRequests($hasId = false, $hasRequestor = false, $hasAssign_to = false, $hasIs_approval = false, $hasIs_cancellation = false, $hasIs_approved = false, $hasIs_saved = false)
        {
          $fields = array(
            'PR.id',
            'PR.project_id',
            'PR.department_id',
            'PR.request_type_id',
            'PR.prs_no',
            'PR.date_requested',
            'PR.signatories',
            'PR.for_cancelation',
            'PR.remarks',
            'PR.status',
            'PR.assign_to',
            'PR.created_by',
            'PR.created_at',
            'PR.updated_by',
            'PR.updated_at'
          );
      
          $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->where(array('PR.is_active' => ':is_active'));
      
          $initQuery = ($hasId) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
      
          return $initQuery;
        }
      
        public function insertNewRequest($data = array())
        {
          $initQuery = $this->insert('purchase_requisitions', $data);
          return $initQuery;
        }

        public function insertPpeManpowerAttachments($data = array())
        {
          $initQuery = $this->insert('prs_ppe_attachments', $data);
          return $initQuery;
        }

        public function insertNewRequestMaterialSequence($data = array())
        {
          $initQuery = $this->insert('prs_ppe_delivery_sequences', $data);
          return $initQuery;
        }

        public function insertNewNotification($data = array())
        {
          $initQuery = $this->insert('notifications', $data);
          return $initQuery;
        }
        
    }
