<?php
namespace App\Model\SupplyReport;

require_once('../../AbstractClass/QueryHandler.php');
use App\AbstractClass\QueryHandler;

class PurchaseRequisitionQueryHandlerSR extends QueryHandler
{

    public function selectRequest($hasId = false, $hasRequestorId = false, $cancelation = false)
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
        // $initQuery = ($hasRequestorId) ? $initQuery->andWhere(array('PR.created_by' => ':requestor')) : $initQuery = ($cancelation) ? $initQuery->andWhere(array('PR.for_cancelation' => ':cancelation')) : $initQuery->logicEx('AND PR.for_cancelation IS NULL AND PR.signatories IS NOT NULL');
        // if ($hasRequestorId) {
        //     $initQuery->andWhere(array('PR.created_by' => ':requestor'));
        // } else {
        //     if ($cancelation) {
        //         $initQuery->andWhere(array('PR.for_cancelation' => ':cancelation'));
        //     } else {
        //         $initQuery->logicEx('AND PR.for_cancelation IS NULL AND PR.signatories IS NOT NULL');
        //     }
        // }

        return $initQuery;
    }

    public function selectRequestMaterials($hasId = false, $hasPrsId = false)
    {
        $fields = array(
            'PRM.id',
            'PRM.purchase_requisition_id',
            'PRM.material_specification_id',
            'PRM.quantity',
            'PRM.unit_measurement',
            'PRM.item_spec_id',
            'PRM.category',
            'PRM.wi_category',
            'PRM.work_item_id',
            'PRM.work_volume',
            'PRM.work_volume_unit',
            'PRM.wbs',
            'PRM.account_id',
            'PRM.signatories',
            'PRM.remarks',
            'PRM.status',
            'PRM.date_needed',
            'PRM.created_by',
            'PRM.created_at',
            'PRM.updated_by',
            'PRM.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisition_descriptions PRM')
            ->where(array('PRM.is_active' => ':is_active'));

        $initQuery = ($hasId) ? $initQuery->andWhere(array('PRM.id' => ':id')) : $initQuery;
        $initQuery = ($hasPrsId) ? $initQuery->andWhere(array('PRM.purchase_requisition_id' => ':prs_id')) : $initQuery;

        return $initQuery;
    }

    public function selectRequestEquipments($hasId = false, $hasPrsId = false)
    {
        $fields = array(
            'PRE.id',
            'PRE.pr_id',
            'PRE.category',
            'PRE.equipment_type_id',
            'PRE.capacity',
            'PRE.no_of_unit',
            'PRE.start_date',
            'PRE.equipment_days',
            'PRE.account_id',
            'PRE.remarks',
            'PRE.status',
            'PRE.created_by',
            'PRE.created_at',
            'PRE.updated_by',
            'PRE.updated_at'
            // 'PRE.signatories',
        );

        $initQuery = $this->select($fields)
            ->from('pr_equipments PRE')
            ->where(array('PRE.is_active' => ':is_active'));

        $initQuery = ($hasId) ? $initQuery->andWhere(array('PRE.id' => ':id')) : $initQuery;
        $initQuery = ($hasPrsId) ? $initQuery->andWhere(array('PRE.pr_id' => ':prs_id')) : $initQuery;

        return $initQuery;
    }

    public function selectRequestLabors($hasId = false, $hasPrsId = false)
    {
        $fields = array(
            'PRL.id',
            'PRL.pr_id',
            'PRL.category',
            'PRL.position_id',
            'PRL.no_of_employee',
            'PRL.start_date',
            'PRL.mandays',
            'PRL.account_id',
            'PRL.remarks',
            'PRL.status',
            'PRL.created_by',
            'PRL.created_at',
            'PRL.updated_by',
            'PRL.updated_at'
            // 'PRL.signatories',
        );

        $initQuery = $this->select($fields)
            ->from('pr_labors PRL')
            ->where(array('PRL.is_active' => ':is_active'));

        $initQuery = ($hasId) ? $initQuery->andWhere(array('PRL.id' => ':id')) : $initQuery;
        $initQuery = ($hasPrsId) ? $initQuery->andWhere(array('PRL.pr_id' => ':prs_id')) : $initQuery;

        return $initQuery;
    }

    public function selectRequestWorkItems($hasId = false, $hasItemId = false, $type = '')
    {
        $fields = array(
            'PRW.id',
            'PRW.pre_id',
            'PRW.prl_id',
            'PRW.ps_swi_directs_id',
            'PRW.p_wi_indirects_id',
            'PRW.wi_category_id',
            'PRW.wi_id',
            'PRW.work_volume',
            'PRW.wv_unit',
            'PRW.wbs',
            'PRW.start_date',
            'PRW.equipment_days',
            'PRW.mandays',
            'PRW.status',
            'PRW.created_by',
            'PRW.created_at',
            'PRW.updated_by',
            'PRW.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('pr_work_items PRW')
            ->where(array('PRW.is_active' => ':is_active'));

        $initQuery = ($hasId) ? $initQuery->andWhere(array('PRW.id' => ':id')) : $initQuery;
        $initQuery = ($type == 2) ? $initQuery->andWhere(array('PRW.pre_id' => ':item_id')) : $initQuery;
        $initQuery = ($type == 3) ? $initQuery->andWhere(array('PRW.prl_id' => ':item_id')) : $initQuery;

        return $initQuery;
    }

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function selectRequests($id = false, $project = false, $department = false, $requestor = false, $supplyStaff = false, $supplyHead = false, $signatory = false, $cancelations = false)
    {
        $fields = array(
            'R.id',
            'R.project_id',
            'R.department_id',
            'R.work_item_id',
            'R.cost_code',
            'R.prs_no',
            'R.category',
            'R.request_type_id',
            'R.date_requested',
            'R.date_needed',
            'R.activity_description',
            'R.signatories',
            'R.prev_id',
            'R.status',
                // 'R.assign_to',
            'R.created_by',
            'R.created_at',
            'R.updated_by',
            'R.updated_at',
            'R.for_cancelation',

                // temp_fix
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as assign_to',
                'P.name as assign_to_position',
            );

            // temp_fix
            $leftJoins = array(
                'users U'                    => 'U.id = R.assign_to',
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id'
            );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions R')
            ->leftJoin($leftJoins)
            ->where(array('R.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('R.id' => ':id')) : $initQuery;
        $initQuery = ($project) ? $initQuery->andWhereNull(array('R.department_id')) : $initQuery;
        $initQuery = ($department) ? $initQuery->andWhereNull(array('R.project_id')) : $initQuery;
        $initQuery = ($requestor) ? $initQuery->andWhere(array('R.created_by' => ':created_by')) : $initQuery;
        $initQuery = ($supplyStaff) ? $initQuery->andWhere(array('R.assign_to' => ':assign_to', 'R.status' => '3')) : $initQuery;
        $initQuery = ($supplyHead) ? $initQuery->andWhereNull(array('R.assign_to'))->logicEx('AND R.status IN (:status,:status1) AND (R.for_cancelation IN (:forcancel) OR R.for_cancelation IS NULL)') : $initQuery;
        $initQuery = ($signatory) ? $initQuery->andWhere(array('R.status' => ':status')) : $initQuery;
        $initQuery = ($cancelations) ? $initQuery->andWhere(array('R.for_cancelation' => ':for_cancelations')) : $initQuery;

        return $initQuery;
    }
        // public function selectRequests($id = false, $department = false, $project = false, $user_id = false, $supplyStaff = false, $supplyHead = false)
        // {
        //     $fields = array(
        //         'R.id',
        //         'R.project_id',
        //         'R.department_id',
        //         'R.work_item_id',
        //         'R.cost_code',
        //         'R.prs_no',
        //         'R.category',
        //         'R.request_type_id',
        //         'R.date_requested',
        //         'R.date_needed',
        //         'R.activity_description',
        //         'R.signatories',
        //         'R.prev_id',
        //         'R.status',
        //         'R.assign_to',
        //         'R.created_by',
        //         'R.created_at',
        //         'R.updated_by',
        //         'R.updated_at',
        //     );

        //     // $joins = array(
        //     //     'users U' => 'R.created_by = U.id',
        //     //     'employment_informations EI' => 'U.personal_information_id = EI.personal_information_id',
        //     //     'positions P' => 'EI.position_id = P.id',
        //     // );

        //     $initQuery = $this->select($fields)
        //                  ->from('purchase_requisitions R')
        //                 //  ->join($joins)
        //                 //  ->where(array('R.is_active' => ':is_active', 'R.status' => '1'));
        //                  ->where(array('R.is_active' => ':is_active'));

        //     $initQuery = ($id) ? $initQuery->andWhere(array('R.id' => ':id')) : $initQuery;
        //     $initQuery = ($user_id) ? $initQuery->andWhere(array('R.created_by' => ':user_id')): $initQuery;
        //     $initQuery = ($department) ? $initQuery->andWhereNull(array('R.project_id')) : $initQuery;
        //     $initQuery = ($project) ? $initQuery->andWhereNull(array('R.department_id')) : $initQuery;
        //     // $initQuery = ($supplyStaff) ? $initQuery->andWhere(array('R.status' => ':status')): $initQuery;
        //     $initQuery = ($supplyStaff) ? $initQuery->andWhere(array('R.assign_to' => ':assign_id', 'R.status' => '1')): $initQuery;
        //     $initQuery = ($supplyHead) ? $initQuery->andWhere(array('R.status' => ':status')): $initQuery;

        //     return $initQuery;
        // }

    public function selectRequestItems($id = false, $prsId = false, $mode = false)
    {
        $fields = array(
            'PRD.id',
            'PRD.purchase_requisition_id',
            'PRD.material_specification_id',
                // 'PRD.temp_item',
            'PRD.equipment_id',
            'PRD.quantity',
            'PRD.unit_measurement as unit',
            'PRD.item_spec_id',
            'PRD.category',
            'PRD.wi_category',
            'PRD.work_item_id',
            'PRD.work_volume',
            'PRD.work_volume_unit',
            'PRD.wbs',
            'PRD.account_id',
                // 'PRD.temp_item_spec',
            'PRD.brand_id',
                // 'PRD.temp_brand',
            'PRD.request_type_id',
            'PRD.signatories',
                // 'PRD.temp_request_type',
            'PRD.remarks',
            'PRD.status',
            'PRD.date_needed',
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisition_descriptions PRD')
            ->where(array('PRD.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PRD.id' => ':prd_id')) : $initQuery;
        $initQuery = ($prsId) ? $initQuery->andWhere(array('PRD.purchase_requisition_id' => ':prs_id')) : $initQuery;
        $initQuery = ($mode) ? $initQuery->andWhere(array('PRD.status' => ':status')) : $initQuery;

        return $initQuery;
    }

    public function selectRevisions($id = false, $prsNo = false)
    {
        $fields = array(
            'R.id',
            'R.project_id',
            'R.department_id',
            'R.work_item_id',
            'R.cost_code',
            'R.prs_no',
            'R.date_requested',
            'R.date_needed',
            'R.activity_description',
            'R.signatories',
            'R.prev_id',
            'R.status',
            'R.created_by',
            'R.created_at',
            'R.updated_by',
            'R.updated_at',
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions R')
            ->where(array('R.prs_no' => ':prs_no', 'R.is_active' => ':is_active'));
            // $initQuery = ($prsNo) ? $initQuery->andWhere(array('R.prs_no' => ':prs_no')) : $initQuery;
        $initQuery = ($id) ? $initQuery->andWhere(array('R.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpecsDescription($id = false)
    {
        $fields = array(
            'M.id',
            'M.material_category_id',
            'M.name'
        );

        $initQuery = $this->select($fields)
            ->from('materials M')
            ->where(array('M.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('M.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpecsDescriptionCategory($id = false)
    {
        $fields = array(
            'MC.id',
            'MC.name'
        );

        $initQuery = $this->select($fields)
            ->from('material_categories MC')
            ->where(array('MC.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('MC.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpecsImages($id = false, $specsId = false)
    {
        $fields = array(
            'MSI.id',
            'MSI.image'
        );

        $initQuery = $this->select($fields)
            ->from('material_specification_images MSI')
            ->where(array('MSI.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('MSI.id' => ':id')) : $initQuery;
            $initQuery = ($specsId) ? $initQuery->andWhere(array('MSI.material_specification_id' => ':material_specification_id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpecsStocks($id = false, $specsId = false)
    {
        $fields = array(
            'MSI.id',
            'MSI.quantity'
        );

        $initQuery = $this->select($fields)
            ->from('msb_inventories MSI')
            ->where(array('MSI.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('MSI.id' => ':id')) : $initQuery;
            $initQuery = ($specsId) ? $initQuery->andWhere(array('MSI.material_specification_brand_id' => ':material_specification_id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpecsUnit($id = false, $specsId = false)
    {
        $fields = array(
            'MSBS.id',
            'MSBS.unit'
        );

        $initQuery = $this->select($fields)
            ->from('msb_suppliers MSBS')
            ->where(array('MSBS.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('MSBS.id' => ':id')) : $initQuery;
            $initQuery = ($specsId) ? $initQuery->andWhere(array('MSBS.material_specification_brand_id' => ':material_specification_id')) : $initQuery;

        return $initQuery;
    }

    public function selectDeliverySequence ($id = false, $prdId = false)
    {
        $fields = array(
            'PRDS.id',
            'PRDS.purchase_requisition_description_id',
            'PRDS.seq_no',
            'PRDS.delivery_date',
            'PRDS.quantity',
            'PRDS.is_consumed',
            'PRDS.is_active',
            'PRDS.created_by',
            'PRDS.created_at',
            'PRDS.updated_by',
            'PRDS.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('prd_delivery_sequences PRDS')
            ->where(array('PRDS.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PRDS.id' => ':id')) : $initQuery;
            $initQuery = ($prdId) ? $initQuery->andWhere(array('PRDS.purchase_requisition_description_id' => ':prd_id')) : $initQuery;

        return $initQuery;
    }

// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // PRS Material Details

    /**
     * selectMaterials
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
    public function selectMaterials($id = false, $userId = false)
    {
        $fields = array(
            'M.id',
            'M.name',
        );

        $initQuery = $this->select($fields)
            ->from('materials M')
            ->where(array('M.is_active' => ':is_active'));
        $initQuery = ($id) ? $initQuery->andWhere(array('M.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectMaterialSpecs
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
    public function selectMaterialSpecs($id = false, $userId = false, $filter = false, $limit = '')
    {
        $fields = array(
            'MS.id',
            'MS.material_id',
            'MS.specs',
            'MS.code'
        );

        $initQuery = $this->select($fields)
            ->from('material_specifications MS');
        // $initQuery = ($filter) ? $initQuery->logicEx('JOIN materials M ON M.id = MS.material_id JOIN  ON') : $initQuery;
        $initQuery = $initQuery->where(array('MS.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;
        $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('MS.specs' => ':filter','MS.code' => ':filter')) : $initQuery;
        $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

        return $initQuery;
    }

    /**
     * selectUnits
     *
     * @param boolean $id
     * @return void
     */
    public function selectUnits($id = false)
    {
        $fields = array(
            'MSBS.id',
            'MSBS.material_specification_brand_id',
            'MSBS.price',
            'MSBS.unit',
            'MSB.material_specification_id',
            'MS.code',
            'MS.specs',
        );

        $joins = array(
            'material_specification_brands MSB' => 'MSBS.material_specification_brand_id = MSB.id',
            'material_specifications MS' => 'MSB.material_specification_id = MS.id',
        );

        $initQuery = $this->select($fields)
            ->from('msb_suppliers MSBS')
            ->join($joins)
            ->where(array('MSBS.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectStocks
     *
     * @param boolean $id
     * @return void
     */
    public function selectStocks($id = false)
    {
        $fields = array(
            'MSB.material_specification_id',
            'MSBI.quantity'
        );

        $joins = array(
            'msb_inventories MSBI' => 'MSBI.material_specification_brand_id = MSB.id',
        );

        $initQuery = $this->select($fields)
            ->from('material_specification_brands MSB')
            ->join($joins)
            ->where(array('MSB.is_active' => ':is_active'))
            ->groupBy('MSB.material_specification_id');

            // $initQuery = ($id) ? $initQuery->andWhere(array('MS.material_specification_id' => ':id')) : $initQuery;
        $initQuery = ($id) ? $initQuery->andWhere(array('MSB.material_specification_id' => ':id')) : $initQuery;

        return $initQuery;
    }


// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // PRS Equipment Details

    public function selectEquipmentTypes($id = false)
    {
        $fields = array(
            'ET.id',
            'ET.name',
            'ET.cost_code',
        );

        $initQuery = $this->select($fields)
            ->from('equipment_types ET')
            ->where(array('ET.classification' => ':classification'));

        $initQuery = ($id) ? $initQuery->andWhere(array('ET.id' => ':id'))->orderBy('ET.name', 'ASC') : $initQuery->orderBy('ET.name', 'ASC');;

        return $initQuery;
    }

    public function selectEquipment($id = false)
    {
        $fields = array(
            'E.id',
            'E.body_no',
            'E.equipment_type_id',
            'E.eqpt_code',
            'E.brand',
            'E.model',
            'E.capacity',
            'E.c_unit',
            'E.date_acquired',
            'E.dimension',
            'E.d_unit',
            'E.travel_speed',
            'E.ts_unit',
            'E.work_efficiency',
            'E.we_unit',
            'E.horsepower',
            'E.hp_unit',
            'E.break_horsepower',
            'E.bhp_unit',
            'E.fuel_tank',
            'E.ft_unit',
            'E.fuel_depletion',
            'E.fd_unit',
            'E.fuel_consumption',
            'E.fc_unit',
            'E.engine_fuel_consumption',
            'E.efc_unit',
            'E.lube_oil_consumption',
            'E.loc_unit',
            'E.engine_lube_oil_consumption',
            'E.eloc_unit',
            'E.scdc_rental_rate',
            'E.rental_rate_per_day',
            'E.rental_rate_per_hour',
            'E.owned_by',
            'E.other_info',
            'E.status',
            'E.created_by',
            'E.created_at',
            'E.updated_by',
            'E.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('heavy_equipments E')
            ->where(array('E.status' => ':is_active'))
            ->orderBy('E.body_no', 'ASC');

        return $initQuery;
    }

// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // PRS Labor Details

    public function selectPositions($id = false)
    {
        $fields = array(
            'P.id',
            'P.department_id',
            'P.head_id',
            'P.code',
            'P.name',
            'P.default_rate'
        );

        $initQuery = $this->select($fields)
            ->from('positions P')
            ->where(array('P.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * `selectPwds` Query string that will select from table `p_wds`
     * @param  boolean $id
     * @param  boolean $projectId
     * @return string
     */
    public function selectPwds($id = false, $projectId = false)
    {
        $fields = array(
            'PWD.id',
            'WD.id as wd_id',
            'WD.name as wd_name'
        );

        $initQuery = $this->select($fields)
            ->from('p_wds PWD')
            ->join(array('work_disciplines WD' => 'WD.id = PWD.work_discipline_id'))
            ->where(array('PWD.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PWD.id' => ':id')) : $initQuery;
        $initQuery = ($projectId) ? $initQuery->andWhere(array('PWD.project_id' => ':project_id')) : $initQuery;

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
            ->where(['PWSP.is_active' => ':is_active']);

        $initQuery = ($id) ? $initQuery->andWhere(['PWSP.id' => ':id']) : $initQuery;
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
        $fields = array(
            'PSWID.id',
            'PSWID.quantities',
            'SWI.id as swi_id',
            'SWI.alternative_name as swi_name',
            'SWI.unit as swi_unit',
            'SWI.wbs as swi_wbs',
            'WIC.id as wic_id',
            'WIC.name as wic_name',
            'WIC.part as wic_part',
            'WI.id as wi_id',
            'WI.name as wi_name',
            'WI.item_no',
            'WI.unit'
        );

        $joins = array(
            'sw_wis SWI' => 'SWI.id = PSWID.sw_wi_id',
            'spt_wics SWIC' => 'SWIC.id = SWI.spt_wic_id',
            'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
            'work_items WI' => 'WI.id = SWI.work_item_id'
        );

        $initQuery = $this->select($fields)
            ->from('ps_swi_directs PSWID')
            ->join($joins)
            ->where(array('PSWID.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PSWID.id' => ':id')) : $initQuery;
        $initQuery = ($pwSpId) ? $initQuery->andWhere(array('PSWID.pw_sp_id' => ':pw_sp_id')) : $initQuery;

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
        $fields = array(
            'PWII.id',
            'PWII.quantities',
            'WIC.id as wic_id',
            'WIC.name as wic_name',
            'WIC.part as wic_part',
            'WI.id as wi_id',
            'WI.name as wi_name',
            'WI.item_no',
            'WI.unit'
        );

        $joins = array(
            'work_items WI' => 'PWII.work_item_id = WI.id',
            'work_item_categories WIC' => 'WI.work_item_category_id = WIC.id'
        );

        $initQuery = $this->select($fields)
            ->from('p_wi_indirects PWII')
            ->join($joins)
            ->where(array('PWII.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PWII.id' => ':id')) : $initQuery;
        $initQuery = ($projectId) ? $initQuery->andWhere(array('PWII.project_id' => ':project_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectNewNumber
     *
     * @return void
     */
    public function selectNewNumber($prs_no = false)
    {
            // Latest Functional
            /* $fields = array(
                'PR.id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR ORDER BY PR.id DESC LIMIT 0, 1');

            return $initQuery; */

        $fields = array(
            'PR.id',
            'PR.prs_no'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR ORDER BY PR.id DESC LIMIT 0, 1');
                            //   ->where(array('PR.is_active' => '1 ORDER BY PR.id DESC LIMIT 0, 1'));

        $initQuery = ($prs_no) ? $initQuery->andWhere(array('RT.prs_no' => ':prs_no')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectRequestTypes
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
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
     * selectProjects
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
    public function selectProjects($id = false, $userId = false)
    {
        $fields = array(
            'P.id',
            'P.project_code',
            'P.name',
            'P.location',
            'P.longitude',
            'P.latitude',
            // 'P.map_img',
            'P.is_on_going',
        );

        $joins = array(
            'p_wds PWDS' => 'PWDS.project_id = P.id',
        );
        $initQuery = $this->select($fields)
            ->from('projects P')
            ->join($joins)
            ->where(array('P.status' => ':status'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectDepartments
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
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

    /**
     * selectWorkItem
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
    public function selectWorkItem($id = false, $projectId = false, $userId = false)
    {
        $fields = array(
            'WI.id',
            'WI.work_item_category_id',
            'WI.code',
            'WI.item_no',
            'WI.name',
            'WI.unit',
            'WI.direct',
        );

        $initQuery = $this->select($fields)
            ->from('work_items WI')
            ->where(array('WI.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('WI.id' => ':id')) : $initQuery;
            // $initQuery = ($projectId) ? $initQuery->andWhere(array('WI.id' => ':id')) : $initQuery;  

        return $initQuery;
    }

    /**
     * selectWorkItemCategory
     *
     * @param boolean $id
     * @param boolean $userId
     * @param boolean $projectId
     * @return void
     */
    public function selectWorkItemCategory($id = false, $projectId = false, $userId = false)
    {
        $fields = array(
            'WIC.id',
            'WIC.code',
            'WIC.name',
            'WIC.part'
        );

        $initQuery = $this->select($fields)
            ->from('work_item_categories WIC')
            ->where(array('WIC.is_active' => ':status'));
        $initQuery = ($id) ? $initQuery->andWhere(array('WIC.id' => ':id')) : $initQuery;
            // $initQuery = ($projectId) ? $initQuery->andWhere(array('WIC.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectAccounts
     *
     * @param string $id
     * @return void
     */
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
            'positions P' => 'EI.position_id = P.id',
            'departments D' => 'P.department_id = D.id'
        );

        $initQuery = $this->select($fields)
            ->from('personal_informations PI')
            ->leftJoin($join)
            ->where(array('PI.is_active' => ':status'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;
        $initQuery = ($department_id) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectUser
     *
     * @param boolean $id
     * @return void
     */
    public function selectUser($id = false)
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

        return $initQuery;
    }

    public function selectHead($id = false)
    {
        $fields = array(
            'EI.head_id'
        );

        $initQuery = $this->select($fields)
        ->from('employment_informations EI')
        ->where(array('EI.personal_information_id' => ':id'));

        // $initQuery = ($id) ? $initQuery->andWhere(array('U.personal_information_id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function insertNewRequest($data = array())
    {
        $initQuery = $this->insert('purchase_requisitions', $data);
        return $initQuery;
    }

    public function updateRequest($id = '', $data = array())
    {
        $initQuery = $this->update('purchase_requisitions', $id, $data);
        return $initQuery;
    }

    public function updateRequestData($id = '', $data = array())
    {
        $initQuery = $this->update('purchase_requisition_descriptions', $id, $data);
        return $initQuery;
    }

    public function insertNewRequestMaterial($data = array())
    {
        $initQuery = $this->insert('purchase_requisition_descriptions', $data);
        return $initQuery;
    }

    public function insertNewRequestMaterialSequence($data = array())
    {
        $initQuery = $this->insert('prd_delivery_sequences', $data);
        return $initQuery;
    }

    public function insertNewRequestEquipment($data = array())
    {
        $initQuery = $this->insert('pr_equipments', $data);
        return $initQuery;
    }
    public function insertNewRequestLabor($data = array())
    {
        $initQuery = $this->insert('pr_labors', $data);
        return $initQuery;
    }

    public function insertNewWorkItem($data = array())
    {
        $initQuery = $this->insert('pr_work_items', $data);
        return $initQuery;
    }

    public function saveEditRequest($id ='', $data = array())
    {
        $initQuery = $this->update('purchase_requisitions', $id, $data);
        return $initQuery;
    }

    public function saveEditRequestData($id = '', $data = array())
    {
        $initQuery = $this->update('purchase_requisition_descriptions', $id, $data);
        return $initQuery;
    }
}
?>