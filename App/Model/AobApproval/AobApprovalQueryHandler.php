<?php

namespace App\Model\AobApproval;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class AobApprovalQueryHandler extends QueryHandler
{
    /**
     * `selectAobSignatories` Query string that will select from table `aob_signatories`.
     * @param  boolean $id
     * @return string
     */
    public function selectAobSignatories($id = false, $positionId = false, $aobId = false, $status = false)
    {
        $fields = array(
            'AOB.id',
            'AOB.aob_no',
            'DATE_FORMAT(AOB.date, "%b %d, %Y") as aob_date',
            'DATE_FORMAT(AOBS.date_approved, "%b %d, %Y") as date_approved',
            'AOBS.status',
            'AOBS.id as aob_signatory_id',
            'AOBS.signatory',
            // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            'CONCAT(PI.fname, " ", PI.lname) as full_name',
            'P.name as position_name',
            'P.code as position_code'
        );

        $joins = array(
            'abstract_of_bids AOB'       => 'AOB.id = AOBS.aob_id',
            'users U'                    => 'AOBS.signatory = U.id',
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_signatories AOBS')
            ->join($joins)
            ->where(array('AOBS.is_active' => ':is_active'));

        $initQuery = ($id)         ? $initQuery->andWhere(array('AOBS.id' => ':id'))                   : $initQuery;
        $initQuery = ($positionId) ? $initQuery->andWhere(array('AOBS.position_id' => ':position_id')) : $initQuery;
        $initQuery = ($aobId)      ? $initQuery->andWhere(array('AOBS.aob_id' => ':aob_id'))           : $initQuery;
        $initQuery = ($status)     ? $initQuery->andWhereIn('AOBS.status', ['"P"', '"D"'])             : $initQuery;

        // $initQuery = $initQuery->orderBy('AOBS.status', 'DESC');

        return $initQuery;
    }

    /**
     * `selectAbstractOfBids` Query string that will select from table `abstract_of_bids`.
     * @param  boolean $id
     * @return string
     */
    public function selectAbstractOfBids($id = false)
    {
        $fields = array(
            'AOB.id',
            'AOB.aob_no',
            'DATE_FORMAT(AOB.date, "%b %d, %Y") as aob_date',
            'AOB.signatory_level',
            'AOB.type',
        );

        $initQuery = $this->select($fields)
            ->from('abstract_of_bids AOB')
            ->where(array('is_active' => ':is_active'));

        $initQuery = ($id)   ? $initQuery->andWhere(array('AOB.id' => ':id'))     : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPurchaseRequisitions` Query string that will select from table `purchase_requisitions`
     * @param  boolean $id
     * @param  boolean $aobId
     * @return string
     */
    public function selectPurchaseRequisitions($id = false, $aobId = false)
    {
        $fields = array(
            'DISTINCT(PR.id) as id',
            'PR.prs_no',
            'P.project_code',
            'D.charging',
            'IF(PR.project_id IS NULL, D.charging, P.project_code) as custom_charging'
        );

        $joins = array(
            'purchase_requisition_descriptions PRD' => 'PRD.purchase_requisition_id = PR.id',
            'request_quotation_descriptions RQD'    => 'RQD.purchase_requisition_description_id = PRD.id',
            'aob_descriptions AOBD'                 => 'AOBD.rfq_material_id = RQD.rfq_material_id',
            'abstract_of_bids AOB'                  => 'AOB.id = AOBD.aob_id',
        );

        $leftJoins = array(
            'projects P'    => 'P.id = PR.project_id',
            'departments D' => 'PR.department_id = D.id'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->join($joins)
            ->leftJoin($leftJoins)
            ->where(array('PR.is_active' => ':is_active', 'RQD.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('PR.id' => ':id'))      : $initQuery;
        $initQuery = ($aobId) ? $initQuery->andWhere(array('AOB.id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPurchaseRequisitions` Query string that will select from table `purchase_requisitions`
     * @param  boolean $id
     * @param  boolean $aobId
     * @return string
     */
    public function selectPrsForAob($id = false, $aobId = false)
    {
        $fields = array(
            'DISTINCT(PR.id) as id',
            'PR.prs_no',
            'P.project_code',
            'D.charging',
            'IF(PR.project_id IS NULL, D.charging, P.project_code) as custom_charging',
            'CONCAT(PI.lname, ", ", PI.fname) as requestor_name',
            'PS.name as requestor_position',
        );

        $joins = array(
            'purchase_requisition_descriptions PRD' => 'PRD.purchase_requisition_id = PR.id',
            'request_quotation_descriptions RQD'    => 'RQD.purchase_requisition_description_id = PRD.id',
            'aob_descriptions AOBD'                 => 'AOBD.rfq_material_id = RQD.rfq_material_id',
            'abstract_of_bids AOB'                  => 'AOB.id = AOBD.aob_id',
            'users U'                    => 'U.id = PR.user_id',
            'personal_informations PI'   => 'PI.id = U.personal_information_id',
            'employment_informations EI' => 'EI.personal_information_id = PI.id',
            'positions PS'                => 'PS.id = EI.position_id',
        );

        $leftJoins = array(
            'projects P'    => 'P.id = PR.project_id',
            'departments D' => 'PR.department_id = D.id'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->join($joins)
            ->leftJoin($leftJoins)
            ->where(array('PR.is_active' => ':is_active', 'RQD.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('PR.id' => ':id'))      : $initQuery;
        $initQuery = ($aobId) ? $initQuery->andWhere(array('AOB.id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * Undocumented function
     *
     * @param boolean $id
     * @param boolean $aobId
     * @return void
     */
    public function selectCustomPayee($id = false, $aobId = false)
    {
        $fields = array(
            'S.name',
            'IF(RQ.rfq_type = "0", IF(RQ.c_unit_price = "0.00", RQ.c_delivery_price, RQ.c_unit_price), IF(RQ.p_unit_price = "0.00", RQ.p_delivery_price, RQ.p_unit_price)) as unit_price',

        );

        $joins = array(
            'aob_descriptions AOBD'                 => 'AOB.id = AOBD.aob_id',
            'aob_supply_evaluations AOBSE'          => 'AOBD.id = AOBSE.aob_description_id',
            'request_quotations RQ'                 => 'RQ.id = AOBSE.rfq_id',
            'suppliers S'                           => 'S.id = RQ.supplier_id',
            'request_quotation_descriptions RQD'    => 'RQD.rfq_material_id = RQ.rfq_material_id',
        );

        $initQuery = $this->select($fields)
            ->from('abstract_of_bids AOB')
            ->join($joins)
            ->where(array('RQD.is_active' => ':is_active', 'AOBSE.is_recommended' => '1'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('RQD.purchase_requisition_description_id' => ':prd_id'))      : $initQuery;
        $initQuery = ($aobId) ? $initQuery->andWhere(array('AOB.id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectRequestQuotations` Query string that will select from table `request_quotations`.
     * @param  boolean $id
     * @param  boolean $aobId
     * @param  boolean $rfqMaterialId
     * @return string
     */
    public function selectRequestQuotations($id = false, $aobId = false, $rfqMaterialId = false)
    {
        $firstFields = array(
            'DISTINCT(RQ.rfq_no) as rfq_no',
            'supplier_id',
            'S.name as supplier_name',
        );

        $secondFields = array(
            'RQ.id',
            'RQ.has_quote',

            'RQ.c_unit_price',
            'RQ.c_delivery_charge',
            'RQ.c_delivery_price',
            // '((RQ.c_unit_price * RM.quantity) + RQ.c_delivery_charge) as c_total',
            // '(RQ.c_unit_price * RM.quantity) as c_total',
            '((IF(RQ.c_unit_price = "0.00", RQ.c_delivery_price, RQ.c_unit_price)) * RM.quantity) as c_total',

            'RQ.p_unit_price',
            'RQ.p_delivery_charge',
            'RQ.p_delivery_price',
            // '((RQ.p_unit_price * RM.quantity) + RQ.p_delivery_charge) as p_total',
            // '(RQ.p_unit_price * RM.quantity) as p_total',
            '((IF(RQ.p_unit_price = "0.00", RQ.p_delivery_price, RQ.p_unit_price)) * RM.quantity) as p_total',

            'IF(RQ.rfq_type = "0", (RQ.c_unit_price * RM.quantity), (RQ.p_unit_price * RM.quantity)) as total_price',

            'RQ.recommended_specification',
            // 'IF(RQ.rfq_type = "0", RQ.c_unit_price, RQ.p_unit_price) as unit_price',
            'IF(RQ.rfq_type = "0", IF(RQ.c_unit_price = "0.00", RQ.c_delivery_price, RQ.c_unit_price), IF(RQ.p_unit_price = "0.00", RQ.p_delivery_price, RQ.p_unit_price)) as unit_price',
            'IF(RQ.rfq_type = "0", RQ.c_delivery_charge, RQ.p_delivery_charge) as delivery_charge',
            'IF(RQ.rfq_type = "0", RQ.c_delivery_price, RQ.p_delivery_price) as delivery_price',
            'RQ.rfq_type',
            'RQ.is_vat',
            'RQ.rfq_date',
            'DATE_FORMAT(RQ.delivery_date, "%m/%d/%Y") as delivery_date',
            'DATE_FORMAT(RQ.validity_of_offer, "%m/%d/%Y") as validity_of_offer',

            'RQ.supplier_id',
            'S.name as supplier_name',
            'AOBSE.is_recommended',
            'AOBSE.system_recommended',
            'AOBSE.remarks',
            'AOBSE.id as aob_supply_evaluation_id',
        );

        $fields = ($rfqMaterialId) ? array_merge($firstFields, $secondFields) : $firstFields;

        $joins = array(
            'suppliers S'                    => 'S.id = RQ.supplier_id',
            'request_quotation_materials RM' => 'RQ.rfq_material_id = RM.id',
            'aob_supply_evaluations AOBSE'   => 'AOBSE.rfq_id = RQ.id',
            'aob_descriptions AOBD'          => 'AOBD.id = AOBSE.aob_description_id',
            'abstract_of_bids AOB'           => 'AOB.id = AOBD.aob_id',
        );

        $initQuery = $this->select($fields)
            ->from('request_quotations RQ')
            ->join($joins)
            ->where(array('RQ.is_active' => ':is_active', 'AOBSE.is_active' => ':is_active'));

        $initQuery = ($id)            ? $initQuery->andWhere(array('RQ.id' => ':id'))                           : $initQuery;
        $initQuery = ($aobId)         ? $initQuery->andWhere(array('AOB.id' => ':aob_id'))                      : $initQuery;
        $initQuery = ($rfqMaterialId) ? $initQuery->andWhere(array('RQ.rfq_material_id' => ':rfq_material_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectCustomChargings` Query string that will select from table `purchase_requisitions` for custom charging selection.
     * @param  boolean $id
     * @param  boolean $aobId
     * @return string
     */
    public function selectCustomChargings($id = false, $aobId = false)
    {
        $fields = array(
            'DISTINCT(PR.id) as id',
            'PR.prs_no',
            'P.project_code',
            'D.charging',
            'P.location'
        );

        $joins = array(
            'purchase_requisition_descriptions PRD' => 'PRD.purchase_requisition_id = PR.id',
            'request_quotation_descriptions RQD'    => 'RQD.purchase_requisition_description_id = PRD.id',
            'aob_descriptions AOBD'                 => 'AOBD.rfq_material_id = RQD.rfq_material_id',
            'abstract_of_bids AOB'                  => 'AOB.id = AOBD.aob_id'
        );

        $leftJoins = array(
            'projects P'    => 'PR.project_id = P.id',
            'departments D' => 'D.id = PR.department_id'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->join($joins)
            ->leftJoin($leftJoins)
            ->where(array('PR.is_active' => ':is_active', 'RQD.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('PR.id' => ':id'))      : $initQuery;
        $initQuery = ($aobId) ? $initQuery->andWhere(array('AOB.id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAobDescriptions` Query string that will select from table `aob_descriptions`.
     * @param  boolean $id
     * @param  boolean $aobId
     * @return string
     */
    public function selectAobDescriptions($id = false, $aobId = false)
    {
        $fields = array(
            'DISTINCT(AOBD.id)',
            'AOBD.rfq_material_id',
            'AOBD.evaluation_no',
            'AOBD.general_comment',
            'AOBD.created_at AS evaluation_date',
            'MSB.id as material_specification_brand_id',
            'RQM.material_specification_id',
            'RQM.material_specification_id as item_spec_id',
            'RQM.quantity',
            'RQM.request_type_id',
            'RT.name as request_type_name',
            'RQM.unit',
            'MS.material_id',
            'MS.specs',
            'MS.code',
            'M.name',
            'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) AS evaluated_by',
            // 'MSBS.unit'
            // 'PRD.unit_measurement'
        );

        $joins = array(
            'request_quotation_materials RQM'       => 'AOBD.rfq_material_id = RQM.id',
            'request_quotation_descriptions RQD'    => 'RQM.id = RQD.rfq_material_id',
            'purchase_requisition_descriptions PRD' => 'PRD.id = RQD.purchase_requisition_description_id',
            'material_specifications MS'            => 'MS.id = PRD.item_spec_id',
            'materials M'                           => 'M.id = MS.material_id',
            'material_specification_brands MSB'     => 'MS.id = MSB.material_specification_id',
            'users U'                               => 'U.id = AOBD.evaluated_by',
            'personal_informations PI'              => 'PI.id = U.personal_information_id',
            'request_types RT'                      => 'RT.id = RQM.request_type_id'
            // 'material_specification_brands MSB'     => 'MSB.material_specification_id = MS.id',
            // 'msb_suppliers MSBS'                    => 'MSBS.material_specification_brand_id = MSB.id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_descriptions AOBD')
            ->join($joins)
            ->where(array('AOBD.is_active' => ':is_active', 'RQD.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('AOBD.id' => ':id'))         : $initQuery;
        $initQuery = ($aobId) ? $initQuery->andWhere(array('AOBD.aob_id' => ':aob_id')) : $initQuery;

        $initQuery = $initQuery->orderBy('AOBD.id', 'DESC');

        return $initQuery;
    }

    /**
     * `selectAobSignatorySets` Query string that will select from table `aob_signatory_sets`
     * @param  boolean $id
     * @param  boolean $aobId
     * @param  boolean $signatoryLevel
     * @return string
     */
    public function selectAobSignatorySets($id = false, $aobId = false, $signatoryLevel = false)
    {
        $fields = array(
            'AOBSS.id',
            'AOBSS.position_id',
            'AOBSS.signatory',
            'AOBSS.signatory_level',
            'DATE_FORMAT(AOBS.date_approved, "%b %d, %Y") as date_approved',
            'AOBS.status',
            'AOBS.remarks',
            // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            'CONCAT(PI.fname, " ", PI.lname) as full_name',
            'P.name as position_name',
            'P.code as position_code'
        );

        $leftJoins = array(
            'aob_signatories AOBS'       => 'AOBS.signatory_set_id = AOBSS.id',
            'users U'                    => 'AOBSS.signatory = U.id',
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_signatory_sets AOBSS')
            ->leftJoin($leftJoins)
            ->where(array('AOBSS.is_active' => ':is_active'));

        $initQuery = ($id)             ? $initQuery->andWhere(array('AOBSS.id' => ':id'))                           : $initQuery;
        $initQuery = ($aobId)          ? $initQuery->andWhere(array('AOBSS.aob_id' => ':aob_id'))                   : $initQuery;
        $initQuery = ($signatoryLevel) ? $initQuery->andWhere(array('AOBSS.signatory_level' => ':signatory_level')) : $initQuery;

        $initQuery = $initQuery->orderBy('AOBSS.signatory_level', 'asc');

        return $initQuery;
    }

    /**
     * `selectPurchaseOrders` Query string that will select from table `purchase_orders`
     * @param  boolean $id
     * @return string
     */
    public function selectPurchaseOrders($id = false)
    {
        $fields = array(
            'PO.id',
            'PO.po_no',
        );

        $initQuery = $this->select($fields)
            ->from('purchase_orders PO')
            ->where(array('PO.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PO.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectRfqAttachments` Query string that will select from table `rfq_attachments`
     * @param  boolean $id
     * @param  boolean $rfqNo [description]
     * @return string
     */
    public function selectRfqAttachments($id = false, $rfqNo = false)
    {
        $fields = array(
            'RFQA.id',
            'RFQA.attachment',
            'RFQA.type',
        );

        $initQuery = $this->select($fields)
            ->from('rfq_attachments RFQA')
            ->where(array('RFQA.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('RFQA.id' => ':id'))         : $initQuery;
        $initQuery = ($rfqNo) ? $initQuery->andWhere(array('RFQA.rfq_no' => ':rfq_no')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAobPaymentSummaries` Query string that will select from table `aob_payment_summaries`
     * @param  boolean $id
     * @param  boolean $aobId
     * @return string
     */
    public function selectAobPaymentSummaries($id = false, $aobId = false)
    {
        $fields = array(
            'AOBPS.id',
            'AOBPS.gross_amount',
            'AOBPS.ewt',
            'AOBPS.net_of_tax',
            'RQ.is_vat',
            'S.name as supplier_name',
        );

        $joins = array(
            'request_quotations RQ' => 'RQ.id = AOBPS.rfq_id',
            'suppliers S'           => 'S.id = RQ.supplier_id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_payment_summaries AOBPS')
            ->join($joins)
            ->where(array('AOBPS.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('AOBPS.id' => ':id'))         : $initQuery;
        $initQuery = ($aobId) ? $initQuery->andWhere(array('AOBPS.aob_id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAobSupplyEvaluationCriterias` Query string that will select from table `aob_supply_evaluation_criterias`.
     * @param  boolean $id
     * @param  boolean $aobseId
     * @return string
     */
    public function selectAobSupplyEvaluationCriterias($id = false, $aobseId = false)
    {
        $fields = array(
            'AOBSEC.id',
            'AOBSEC.criteria',
            'AOBSEC.score',
        );

        $initQuery = $this->select($fields)
            ->from('aob_supply_evaluation_criterias AOBSEC')
            ->where(array('AOBSEC.is_active' => ':is_active'));

        $initQuery = ($id)      ? $initQuery->andWhere(array('AOBSEC.id' => ':id'))                                             : $initQuery;
        $initQuery = ($aobseId) ? $initQuery->andWhere(array('AOBSEC.aob_supply_evaluation_id' => ':aob_supply_evaluation_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPurchaseRequisitionDescriptions` Query string that will select from table `purchase_requisition_descriptions`.
     * @param  boolean $id
     * @param  boolean $prsId
     * @param  boolean $materialSpecificationId
     * @return string
     */
    public function selectPurchaseRequisitionDescriptions($id = false, $prsId = false, $materialSpecificationId = false)
    {
        $fields = array(
            'PRD.id',
            'DATE_FORMAT(PRDDS.delivery_date, "%b %d, %Y") as date_needed',
        );

        $joins = array(
            'prd_delivery_sequences PRDDS' => 'PRDDS.purchase_requisition_description_id = PRD.id'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisition_descriptions PRD')
            ->join($joins)
            ->where(array('PRD.is_active' => ':is_active'));

        $initQuery = ($id)                      ? $initQuery->andWhere(array('PRD.id' => ':id'))                                           : $initQuery;
        $initQuery = ($prsId)                   ? $initQuery->andWhere(array('PRD.purchase_requisition_id' => ':purchase_requisition_id')) : $initQuery;
        $initQuery = ($materialSpecificationId) ? $initQuery->andWhere(array('PRD.item_spec_id' => ':material_specification_id'))          : $initQuery;

        return $initQuery;
    }

    /**
     * `selectMaterialUnits` Query string that will select from table `material_units`
     * @param  boolean $id
     * @return string
     */
    public function selectMaterialUnits($id = false, $unit = false)
    {
        $fields = array(
            'MU.id',
            'MU.code',
            'MU.unit'
        );

        $initQuery = $this->select($fields)
            ->from('material_units MU')
            ->where(array('MU.is_active' => ':is_active'));

        $initQuery = ($id)   ? $initQuery->andWhere(array('MU.id' => ':id'))     : $initQuery;
        $initQuery = ($unit) ? $initQuery->andWhere(array('MU.unit' => ':unit')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAobComments` Query string that will select from table `aob_comments`
     * @param  boolean $id
     * @param  boolean $aobId
     * @param  boolean $referenceId
     * @return string
     */
    public function selectAobComments($id = false, $aobId = false, $referenceId = false)
    {
        $fields = array(
            'AOBC.id',
            'AOBC.aob_id',
            'AOBC.comment',
            'AOBC.post_type',
            'AOBC.comment_by',
            // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            'CONCAT(PI.fname, " ", PI.lname) as full_name',
            'P.name as position_name',
            'AOBC.created_at',
            '"0" as is_reply'
        );

        $joins = array(
            'personal_informations PI'   => 'PI.id = AOBC.comment_by',
            'employment_informations EI' => 'EI.personal_information_id = PI.id',
            'positions P'                => 'P.id = EI.position_id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_comments AOBC')
            ->join($joins)
            ->where(array('AOBC.is_active' => ':is_active'));

        $initQuery = ($id)          ? $initQuery->andWhere(array('AOBC.id' => ':id'))                     : $initQuery;
        $initQuery = ($aobId)       ? $initQuery->andWhere(array('AOBC.aob_id' => ':aob_id'))             : $initQuery;
        $initQuery = ($referenceId) ? $initQuery->andWhere(array('AOBC.reference_id' => ':reference_id')) : $initQuery->andWhereNull(array('AOBC.reference_id'));

        $initQuery = $initQuery->orderBy('AOBC.id', 'ASC');

        return $initQuery;
    }

    /**
     * `selectUsers` Query string that will select from table `users`
     * @param  boolean $id
     * @param  boolean $piId
     * @return string
     */
    public function selectUsers($id = false, $piId = false)
    {
        $fields = array(
            'U.id',
            'U.account_status',
            'EI.position_id'
        );

        $joins = [
            'personal_informations PI'   => 'PI.id = U.personal_information_id',
            'employment_informations EI' => 'EI.personal_information_id = PI.id'
        ];

        $initQuery = $this->select($fields)
            ->from('users U')
            ->join($joins)
            ->where(array('U.is_active' => ':is_active'));

        $initQuery = ($id)   ? $initQuery->andWhere(array('U.id' => ':id'))                                           : $initQuery;
        $initQuery = ($piId) ? $initQuery->andWhere(array('U.personal_information_id' => ':personal_information_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectUserDeputies` Query string that will select from table `user_deputies`
     * @param  boolean $id
     * @param  boolean $userId
     * @param  boolean $status
     * @return string
     */
    public function selectUserDeputies($id = false, $userId = false, $status = false)
    {
        $fields = array(
            'UD.id',
            'UD.deputy_id',
            'UD.status'
        );

        $initQuery = $this->select($fields)
            ->from('user_deputies UD')
            ->where(array('UD.is_active' => ':is_active'));

        $initQuery = ($id)     ? $initQuery->andWhere(array('UD.id' => ':id'))           : $initQuery;
        $initQuery = ($userId) ? $initQuery->andWhere(array('UD.user_id' => ':user_id')) : $initQuery;
        $initQuery = ($status) ? $initQuery->andWhere(array('UD.status' => ':status'))   : $initQuery;

        return $initQuery;
    }

    /**
         * `selectCustomPrDescriptions` Query string that will select from table `purchase_requisition_descriptions`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectCustomPrDescriptions($id = false, $prId = false)
        {
            $fields = array(
                'PRD.id',
                'PRD.category',
                'PRD.item_spec_id',
                'WIC.part',
                'WIC.name as wic_name',
                'WI.item_no',
                'WI.wbs as wi_wbs',
                'WI.name as wi_name',
                'PRD.work_volume',
                'PRD.work_volume_unit',
                'MS.code as ms_code',
                'M.name as material_name',
                'MS.specs',
                'PRD.quantity',
                'PRD.unit_measurement',
                'AC.account_id',
                'AC.name as account_name',
                'AT.name as at_name',
                'PRD.remarks',
            );

            $leftJoins = array(
                'material_specifications MS' => 'PRD.item_spec_id = MS.id',
                'materials M'                => 'MS.material_id = M.id',
                'work_items WI'              => 'WI.id = PRD.work_item_id',
                'work_item_categories WIC'   => 'WIC.id = WI.work_item_category_id',
                'accounts AC'                => 'AC.id = PRD.account_id',
                'account_types AT'           => 'AT.id = AC.account_type_id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->leftJoin($leftJoins)
                              ->where(array('PRD.is_active' => ':is_active'))
                              ->andWhereIn('PRD.status', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

            $initQuery = ($id)   ? $initQuery->andWhere(array('PRD.id' => ':id'))                                           : $initQuery;
            $initQuery = ($prId) ? $initQuery->andWhere(array('PRD.purchase_requisition_id' => ':purchase_requisition_id')) : $initQuery;

            return $initQuery;
        }

    /**
     * `insertAobSignatory` Insert data from table `aob_signatories`
     * @param  array  $data
     * @return string
     */
    public function insertAobSignatory($data = array())
    {
        $initQuery = $this->insert('aob_signatories', $data);

        return $initQuery;
    }

    /**
     * `insertPurchaseOrder` Insert data from table `purchase_orders`
     * @param  array  $data
     * @return string
     */
    public function insertPurchaseOrder($data = array())
    {
        $initQuery = $this->insert('purchase_orders', $data);

        return $initQuery;
    }

    /**
     * `insertMsbSupplier` Insert data from table `msb_suppliers`
     * @param  array  $data
     * @return string
     */
    public function insertMsbSupplier($data = array())
    {
        $initQuery = $this->insert('msb_suppliers', $data);

        return $initQuery;
    }

    /**
     * `insertAobComment` Insert data from table `aob_comments`
     * @param  array  $data
     * @return string
     */
    public function insertAobComment($data = array())
    {
        $initQuery = $this->insert('aob_comments', $data);

        return $initQuery;
    }

    /**
     * `updateAbstractOfBid` Update data in `abstract_of_bids` table.
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updateAbstractOfBid($id = '', $data = array())
    {
        $initQuery = $this->update('abstract_of_bids', $id, $data);

        return $initQuery;
    }

    /**
     * `updateAobSignatory` Update data in `aob_signatories` table.
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updateAobSignatory($id = '', $data = array())
    {
        $initQuery = $this->update('aob_signatories', $id, $data);

        return $initQuery;
    }

    /**
     * `updateAobSupplyEvaluation` Update data in `aob_supply_evaluations` table.
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updateAobSupplyEvaluation($id = '', $data = array(), $fk = '', $fkValue = '')
    {
        $initQuery = $this->update('aob_supply_evaluations', $id, $data);

        return $initQuery;
    }

    /**
     * `updatePurchaseRequisition` Query string that will update to table `purchase_requisitions`
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updatePurchaseRequisition($id = '', $data = [], $fk = '', $fkValue = '')
    {
        $initQuery = $this->update('purchase_requisitions', $id, $data, $fk, $fkValue);

        return $initQuery;
    }

    /**
     * `updateAobComment` Query string that will update to table `aob_comments`
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updateAobComment($id = '', $data = [], $fk = '', $fkValue = '')
    {
        $initQuery = $this->update('aob_comments', $id, $data, $fk, $fkValue);

        return $initQuery;
    }

    /**
     * `updateAobDescription` Update data in `aob_descriptions` table.
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updateAobDescription($id = '', $data = array())
    {
        $initQuery = $this->update('aob_descriptions', $id, $data);

        return $initQuery;
    }

    /**
     * `updateAobPaymentSummary` Query string that will update to table `aob_payment_summaries`
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updateAobPaymentSummary($id = '', $data = [], $fk = '', $fkValue = '')
    {
        $initQuery = $this->update('aob_payment_summaries', $id, $data, $fk, $fkValue);

        return $initQuery;
    }
}
