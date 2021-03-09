<?php

namespace App\Model\PayablesReports;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class PayablesReportsQueryHandler extends QueryHandler
{

    /**
     * `selectMenus` Query string that will select from table `menus`.
     * @param  boolean $id`
     * @return string
     */
    public function selectPayablesReports($id = false, $filterVal = false)
    {
        $fields = array(
            'P.id',
            'P.ref_no',
            'P.type_of_request',
            'P.remarks',
            'DATE_FORMAT(P.created_at, "%M %d,%Y") as created_at',
            'DATE_FORMAT(P.updated_at, "%b %d,%Y %h:%i %p") as updated_at',
            'P.task_assigned_to',
            'P.comment_head',
            'P.comment_auditor',
            'P.is_status',
            'P.is_active',
            'P.abstract_of_bids_id',
            'DATE_FORMAT(P.check_at_auditor, "%b %d,%Y %h:%i %p") as check_at_auditor',
            'P.check_by_auditor',
            'CONCAT(PICA.lname,", ",PICA.fname," ", PICA.mname) as check_by_auditor_name',
            'DATE_FORMAT(P.check_at_head, "%b %d,%Y %h:%i %p") as check_at_head',
            'P.check_by_head',
            'CONCAT(PICH.lname,", ",PICH.fname," ", PICH.mname) as check_by_head_name',
            'PCH.name as check_by_head_position',
            'AOB.aob_no',
            'DATE_FORMAT(AOB.date, "%b %d, %Y") as aob_date',
        );

        $orWhereCondition = array(
            'AOB.aob_no'           =>  ':filter_val',
            'P.ref_no'             =>  ':filter_val',
            'P.type_of_request'    =>  ':filter_val',
            'P.remarks'            =>  ':filter_val',
            'P.updated_at'         =>  ':filter_val',
        );

        $joins = array(
            'abstract_of_bids AOB' => 'AOB.id = P.abstract_of_bids_id',
            //check_by_auditor
            'users UCA'                    => 'UCA.id = P.check_by_auditor',
            'personal_informations PICA'   => 'UCA.personal_information_id = PICA.id',
            'employment_informations EICA' => 'PICA.id = EICA.personal_information_id',
            'positions PCA'                => 'EICA.position_id = PCA.id',
            'departments DCA'              => 'PCA.department_id = DCA.id',
            //check_by_head
            'users UCH'                     => 'UCH.id = P.check_by_head',
            'personal_informations PICH'   => 'UCH.personal_information_id = PICH.id',
            'employment_informations EICH' => 'PICH.id = EICH.personal_information_id',
            'positions PCH'                => 'EICH.position_id = PCH.id',
            'departments DCH'              => 'PCH.department_id = DCH.id'

        );

        $initQuery = $this->select($fields)
            ->from('audit_payables_reports P')
            // ->where(array('P.is_active' => ':is_active', 'P.is_status' => ':is_status'))
            ->leftjoin($joins)
            // ->where(array('P.is_active' => ':is_active'))
            ->where(array('P.is_active' => ':is_active', 'AOB.is_active' => ':is_active'))
            ->logicEx('AND')
            ->orWhereLike($orWhereCondition)
            ->logicEx('AND P.is_status BETWEEN 1 AND 5');

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAuditMonitoring`
     *
     * @param boolean $id
     * @return void
     */
    public function selectAuditMonitoring($id = false, $filterVal = false)
    {
        $fields = array(
            'AM.id',
            'AM.payables_reports_id',
            'AM.account_id',
            'AM.date_posted',
            'AM.gross',
            'AM.net_of_vat',
            'AM.it',
            'AM.ewt',
            'AM.net_of_tax',
            'AM.particular',
            'AM.remarks',
            'AM.audit_ref',
            'AM.prciorsi_no',
            'AM.prciorsi_date',
            'AM.eq_id_plate_no',
            'AM.po_no',
            'AM.prs_no',
            'AM.prs_qty',
            'AM.po_qty',
            'AM.prs_balance',
            'AM.period_start',
            'AM.period_end',
            'AM.previous',
            'AM.present',
            'AM.usge',
            'AM.account_no',
            'AM.gv_no',
            'AM.remarks',
            'AM.created_by',
            'AM.updated_by',
            'DATE_FORMAT(AM.created_at, "%M %d,%Y %h:%i %p") as created_at',
            'DATE_FORMAT(AM.updated_at, "%M %d, %Y %h:%i %p") as updated_at',
            'AM.is_active',
        );

        $orWhereCondition = array(
            'AM.date_posted'             =>  ':filter_val',
        );

        $initQuery = $this->select($fields)
            ->from('audit_monitorings AM')
            ->where(array('AM.is_active' => ':is_active'))
            ->logicEx('AND')
            ->orWhereLike($orWhereCondition);

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectPayablesReportsGT($id = false, $userId = false, $status = false)
    {
        $fields = array(
            'P.id',
            'P.ref_no',
            'P.type_of_request',
            'P.remarks',
            'DATE_FORMAT(P.created_at, "%M %d,%Y %h:%i %p") as created_at',
            'P.task_assigned_to',
            'P.is_status',
            'P.is_active',
            'P.abstract_of_bids_id',
            'AOB.aob_no'
        );

        $joins = array(
            'abstract_of_bids AOB' => 'AOB.id = P.abstract_of_bids_id',
        );


        $initQuery = $this->select($fields)
            ->from('audit_payables_reports P')
            ->leftjoin($joins)
            ->where(array('P.is_active' => ':is_active', 'AOB.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;
        $initQuery = ($userId) ? $initQuery->andWhere(array('P.task_assigned_to' => ':task_assigned_to')) : $initQuery;
        $initQuery = ($status) ? $initQuery->andWhere(array('P.is_status' => ':is_status')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAllPayablesReports
     *
     * @param boolean $id
     * @param boolean $userId
     * @param boolean $status
     * @return void
     */
    public function selectAllPayablesReports($id = false, $status = false)
    {
        $fields = array(
            'P.id',
            'P.ref_no',
            'P.type_of_request',
            'P.remarks',
            'P.checked_by',
            'P.comment_head',
            'P.comment_auditor',
            'DATE_FORMAT(P.created_at, "%M %d, %Y %h:%i %p") as created_at',
            'P.task_assigned_to',
            'P.is_status',
            'P.is_active',
            'P.abstract_of_bids_id',
            'P.check_by_auditor',
            'P.check_at_auditor',
            'P.check_by_head',
            'P.check_at_head',
            'AOB.aob_no'
        );

        $joins = [
            'abstract_of_bids AOB' => 'AOB.id = P.abstract_of_bids_id'
        ];

        $initQuery = $this->select($fields)
                          ->from('audit_payables_reports P')
                          ->leftjoin($joins)
                          ->where(array('P.is_active' => ':is_active','AOB.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;
        $initQuery = ($status) ? $initQuery->andWhere(array('P.is_status' => ':is_status')) : $initQuery;

        return $initQuery;
    }

    // SIGNATORIES
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

    public function selectCrf($id = false)
    {
        // Checkque
        $fields = array(
            'C.id',
            'C.seq_no',
            'C.type',
            // 'C.crs_crf_chargings',
            // 'C.particulars',
            // 'C.chargings',
            'C.gross_amount',
            'C.total_vat',
            'C.total_net_of_vat',
            'C.total_tax_withheld',
            'C.total_net_of_tax',
            'C.check_payee',
            'C.date_needed',
            'C.is_active',
            'C.audit_status',
            'C.audit_status',
            'C.created_by',
            'C.updated_by',
            'C.created_at',
            'C.updated_at'
        );


        $initQuery = $this->select($fields)
            ->from('crs_crfs C')
            ->where(array('C.audit_status' => ':audit_status'));
        //   ->logicEx('AND C.gross_amount >= 1500');

        $initQuery = ($id) ? $initQuery->andWhere(array('C.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectCCItems($id = false, $c_id = false)
    {
        $fields = array(
            'CPO.id',
            'CPO.crs_crf_id',
            'CPO.purchase_order_id',
            'CPO.is_active',
            'CPO.created_by',
            'CPO.updated_by',
            'CPO.created_at',
            'CPO.updated_at',
        );

        $initQuery = $this->select($fields)
            ->from('crs_crf_po CPO')
            ->where(array('CPO.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('CPO.id' => ':id')) : $initQuery;
        $initQuery = ($c_id) ? $initQuery->andWhere(array('CPO.crs_crf_id' => ':c_id')) : $initQuery;

        return $initQuery;
    }

    public function selectPO($id = false)
    {
        $fields = array(
            'PO.id',
            'PO.abstract_of_bid_id',
            'PO.supplier_id',
            'PO.po_no',
            'PO.date_issued',
            'PO.due_date',
            'PO.gross_amount',
            'PO.vat',
            'PO.net_of_vat',
            'PO.tax_withheld',
            'PO.net_of_tax',
            'PO.is_active',
            // 'PO.audit_status',
            'PO.created_by',
            'PO.updated_by',
            'PO.created_at',
            'PO.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_orders PO')
            ->where(array('PO.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PO.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectAOB($id = false, $status = false)
    {
        $fields = array(
            'AOB.id',
            'AOB.aob_no',
            'DATE_FORMAT(AOB.date, "%M %d, %Y %h:%i %p") as date',
            'AOB.note',
            'AOB.status',
            'AOB.signatory_level',
            'AOB.is_active',
            'AOB.checked_by',
            'AOB.aob_status',
            'AOB.created_by',
            'AOB.updated_by',
            // 'AOB.created_at',
            'DATE_FORMAT(AOB.created_at, "%b %d, %Y %h:%i %p") as created_at',
            // 'AOB.updated_at'
            'DATE_FORMAT(AOB.updated_at, "% %d, %Y %h:%i %p") as updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('abstract_of_bids AOB')
            // ->where(array('AOB.is_active' => ':is_active', 'AOB.signatory_level' => ':signatory_level'));
            ->where(array('AOB.is_active' => ':is_active'));

        $initQuery = ($id)      ? $initQuery->andWhere(array('AOB.id' => ':id')) : $initQuery;
        $initQuery = ($status)  ? $initQuery->andWhere(array('AOB.aob_status' => ':aob_status')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAOBAuditApproval` selecting DATATABLES AOB Audit Approvals
     *
     * @param boolean $id
     * @param boolean $status
     * @return void
     */
    public function selectAOBAuditApproval($id = false)
    {
        $fields = array(
            'AOB.id',
            'AOB.aob_no',
            'AOB.date',
            'AOB.note',
            'AOB.status',
            'AOB.signatory_level',
            'AOB.is_active',
            'AOB.checked_by',
            'AOB.aob_status',
            'AOB.created_by',
            'AOB.updated_by',
            'AOB.created_at',
            'AOB.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('abstract_of_bids AOB')
            ->where(array('AOB.is_active' => ':is_active'));

        $initQuery = ($id)      ? $initQuery->andWhere(array('AOB.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectAOBDesc($id = false, $aob_id = false)
    {
        $fields = array(
            'AOBD.id',
            'AOBD.aob_id',
            'AOBD.rfq_material_id',
            'AOBD.general_comment',
            'AOBD.is_active',
            'AOBD.created_by',
            'AOBD.updated_by',
            'AOBD.created_at',
            'AOBD.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('aob_descriptions AOBD')
            ->where(array('AOBD.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('AOBD.id' => ':id')) : $initQuery;
        $initQuery = ($aob_id) ? $initQuery->andWhere(array('AOBD.aob_id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    public function selectRfqMaterial($id = false)
    {
        $fields = array(
            'RFQM.id',
            'RFQM.material_specification_id',
            'RFQM.quantity',
            'RFQM.unit',
            'RFQM.status',
            'RFQM.is_active',
            'RFQM.created_by',
            'RFQM.updated_by',
            'RFQM.created_at',
            'RFQM.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('request_quotation_materials RFQM')
            ->where(array('RFQM.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('RFQM.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectRfq($id = false, $rfq_mat_id = false)
    {
        $fields = array(
            'RFQ.id',
            'RFQ.personal_information_id',
            'RFQ.rfq_material_id',
            'RFQ.supplier_id',
            'RFQ.rfq_no',
            'RFQ.c_unit_price',
            'RFQ.c_delivery_charge',
            'RFQ.c_delivery_price',
            // '((RFQ.c_unit_price * RQM.quantity) + RFQ.c_delivery_charge) as c_total',
            '((IF(RFQ.c_unit_price = "0.00", RFQ.c_delivery_price, RFQ.c_unit_price)) * RQM.quantity) as c_total',
            'RFQ.p_unit_price',
            'RFQ.p_delivery_charge',
            'RFQ.p_delivery_price',
            // '((RFQ.p_unit_price * RQM.quantity) + RFQ.p_delivery_charge) as p_total',
            '((IF(RFQ.p_unit_price = "0.00", RFQ.p_delivery_price, RFQ.p_unit_price)) * RQM.quantity) as p_total',
            'RFQ.item_status',
            'RFQ.status',
            'RFQ.is_sales_invoice',
            'RFQ.is_official_receipt',
            'RFQ.is_vat',
            'RFQ.is_active',
            'RFQ.created_by',
            'RFQ.updated_by',
            'RFQ.created_at',
            'RFQ.updated_at',
            'RFQ.recommended_specification',
            'IF(RFQ.rfq_type = "0", RFQ.c_unit_price, RFQ.p_unit_price) as unit_price',
            'IF(RFQ.rfq_type = "0", RFQ.c_delivery_charge, RFQ.p_delivery_charge) as delivery_charge',
            'IF(RFQ.rfq_type = "0", RFQ.c_delivery_price, RFQ.p_delivery_price) as delivery_price',
            'AOBSE.is_recommended as supply_recommendation',
            'S.name as supplier_name'
        );

        $joins = array(
            'suppliers S'                     => 'RFQ.supplier_id = S.id',
            'aob_supply_evaluations AOBSE'    => 'RFQ.id = AOBSE.rfq_id',
            'request_quotation_materials RQM' => 'RQM.id = RFQ.rfq_material_id',
        );

        $initQuery = $this->select($fields)
            ->from('request_quotations RFQ')
            ->join($joins)
            ->where(array('RFQ.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('RFQ.id' => ':id')) : $initQuery;
        $initQuery = ($rfq_mat_id) ? $initQuery->andWhere(array('RFQ.rfq_material_id' => ':rfq_material_id')) : $initQuery;

        return $initQuery;
    }

    public function selectRfqDesc($id = false, $rfq_mat_id = false)
    {
        $fields = array(
            'RFQD.id',
            'RFQD.rfq_material_id',
            'RFQD.purchase_requisition_description_id',
            'RFQD.is_active',
            'RFQD.created_by',
            'RFQD.updated_by',
            'RFQD.created_at',
            'RFQD.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('request_quotation_descriptions RFQD')
            ->where(array('RFQD.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('RFQD.id' => ':id')) : $initQuery;
        $initQuery = ($rfq_mat_id) ? $initQuery->andWhere(array('RFQD.rfq_material_id' => ':rfq_material_id')) : $initQuery;

        return $initQuery;
    }

    public function selectPrd($id = false)
    {
        $fields = array(
            'PRD.purchase_requisition_id',
            'PRD.id',
            'PRD.material_specification_id',
            'PRD.equipment_id',
            'PRD.quantity',
            'PRD.unit_measurement',
            'PRD.item_spec_id',
            'PRD.brand_id',
            'PRD.request_type_id',
            'PRD.category',
            'PRD.wi_category',
            'PRD.work_item_id',
            'PRD.work_volume',
            'PRD.work_volume_unit',
            'PRD.wbs',
            'PRD.account_id',
            'PRD.signatories',
            'PRD.remarks',
            'PRD.status',
            'PRD.r_status',
            'PRD.s_status',
            'PRD.date_needed',
            'PRD.is_active',
            'PRD.created_by',
            'PRD.updated_by',
            'PRD.created_at',
            'PRD.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisition_descriptions PRD')
            ->where(array('PRD.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PRD.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectPrs($id = false)
    {
        $fields = array(
            'PRS.id',
            // 'PRS.remarks',
            'PRS.project_id',
            'PRS.department_id',
            'PRS.user_id',
            'PRS.category',
            'PRS.request_type_id',
            'PRS.work_item_id',
            'PRS.ps_swi_direct_id',
            'PRS.p_wi_indirect_id',
            'PRS.cost_code',
            'PRS.prs_no',
            'PRS.date_requested',
            'PRS.activity_description',
            'PRS.date_needed',
            'PRS.signatories',
            'PRS.prev_id',
            'PRS.status',
            'PRS.r_status',
            'PRS.s_status',
            'PRS.assign_to',
            'PRS.is_active',
            'PRS.created_by',
            'PRS.updated_by',
            'PRS.created_at',
            'PRS.updated_at',
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PRS')
            ->where(array('PRS.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PRS.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function selectProjects($id = false)
    {
        $fields = array(
            'P.project_code',
            'P.name',
            'P.location',
            'P.longitude',
            'P.latitude',
            'P.created_by',
            'P.updated_by',
            'P.created_at',
            'P.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('projects P')
            ->where(array('P.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectUser` Query string that will select from table `users`.
     * @param  boolean $id
     * @param  boolean $piId
     * @return string
     */
    public function selectUser($id = false, $piId = false)
    {
        $fields = array(
            'U.id',
            'U.personal_information_id',
            'U.account_status',
            'CONCAT(PI.lname,", ",PI.fname," ", PI.mname) as employee',
            'EI.position_id',
            'P.name as position',
            'P.department_id',
            'D.name as department',
        );

        $join = array(
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id',
            'departments D'              => 'P.department_id = D.id'
        );

        $initQuery = $this->select($fields)
                          ->from('users U')
                          ->leftJoin($join)
                          ->where(array('U.is_active' => ':is_active', 'P.department_id' => ':department_id'));

        $initQuery = ($id)   ? $initQuery->andWhere(array('U.id' => ':id'))                                           : $initQuery;
        $initQuery = ($piId) ? $initQuery->andWhere(array('U.personal_information_id' => ':personal_information_id')) : $initQuery;

        return $initQuery;
    }

    public function selectSupplier($id = false)
    {
        $fields = array(
            'S.id',
            'S.name',
            'S.address',
            'S.service_offer',
            'S.contact_no',
            'S.contact_person',
            'S.position',
            'S.tel_no',
            'S.fax_no',
            'S.email_add',
            'S.tin_no',
            'S.date_accredited',
            'S.status',
            'S.is_active',
            'S.created_by',
            'S.updated_by',
            'S.created_at',
            'S.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('suppliers S')
            ->where(array('S.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('S.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpec($id = false)
    {
        $fields = array(
            'MS.material_id',
            'MS.code',
            'MS.specs',
            // 'MS.image',
            'MS.is_active',
            'MS.created_by',
            'MS.updated_by',
            'MS.created_at',
            'MS.updated_at',
            'M.name as material_name'
        );

        $joins = array(
            'materials M' => 'M.id = MS.material_id'
        );

        $initQuery = $this->select($fields)
            ->from('material_specifications MS')
            ->join($joins)
            ->where(array('MS.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function insertGeneralVoucher($data = array())
    {
        $initQuery = $this->insert('general_voucher', $data);
        return $initQuery;
    }

    public function insertPayablesReports($data = array())
    {
        $initQuery = $this->insert('audit_payables_reports', $data);
        return $initQuery;
    }

    public function updateCrsCrfs($id = '', $data = array())
    {
        $initQuery = $this->update('crs_crfs', $id, $data);

        return $initQuery;
    }

    //----------------------
    public function selectNewNumber($ref_no = false)
    {

        $fields = array(
            'APR.id',
            'APR.ref_no'
        );

        $initQuery = $this->select($fields)
            ->from('audit_payables_reports APR ORDER BY APR.id DESC LIMIT 0, 1');

        $initQuery = ($ref_no) ? $initQuery->andWhere(array('APR.ref_no' => ':ref_no')) : $initQuery;

        return $initQuery;
    }

    public function selectGeneralVoucher($id = false, $filterVal = false)
    {
        $fields = array(
            'GV.crs_crfs_id',
            'GV.crs_no',
            'GV.gv_no',
            'GV.task_assigned_to',
            'GV.signatories',
            'GV.date_transmitted_to_cashier',
            'GV.lapsed_due_date',
            'GV.no_lead_time',
            'GV.is_active',
            'GV.is_status',
            'GV.created_by',
            'GV.updated_by',
            'DATE_FORMAT(GV.created_at, "%M/%d/%Y") as created_at',
            'GV.updated_at'
        );

        $orWhereCondition = array(
            'GV.crs_crfs_id'  =>  ':filter_val',
            // 'GV.crs_no'  =>  ':filter_val',
            // 'GV.gv_no'   =>  ':filter_val',
        );

        $initQuery = $this->select($fields)
            ->from('general_voucher GV')
            ->where(array('GV.is_active' => ':is_active'))
            ->logicEx('AND')
            ->orWhereLike($orWhereCondition);

        $initQuery = ($id) ? $initQuery->andWhere(array('GV.id' => ':id')) : $initQuery;

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
            'CONCAT(PI.fname, " ", LEFT(PI.mname,1), ". ", PI.lname) as full_name',
            'P.name as position_name',
            'P.code as position_code',
            'P.department_id'
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
            'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
        );

        $joins = array(
            'abstract_of_bids AOB'     => 'AOB.id = AOBS.aob_id',
            'users U'                  => 'AOBS.signatory = U.id',
            'personal_informations PI' => 'U.personal_information_id = PI.id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_signatories AOBS')
            ->join($joins)
            ->where(array('AOBS.is_active' => ':is_active'));

        $initQuery = ($id)         ? $initQuery->andWhere(array('AOBS.id' => ':id'))                   : $initQuery;
        $initQuery = ($positionId) ? $initQuery->andWhere(array('AOBS.position_id' => ':position_id')) : $initQuery;
        $initQuery = ($aobId)      ? $initQuery->andWhere(array('AOBS.aob_id' => ':aob_id'))           : $initQuery;
        $initQuery = ($status)     ? $initQuery->andWhere(array('AOBS.status' => ':status'))           : $initQuery;

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

    public function selectUsers($id = false)
    {
        $fields = array(
            'PI.fname',
            'PI.mname',
            'PI.lname',
            'P.id as position_id',
            'P.name as position_name',
            'D.id as department_id',
            'D.name as department_name',
            'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            'P.is_signatory',
            'EI.head_id'
        );

        $joins = array(
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id',
            'departments D'              => 'P.department_id = D.id'
        );

        $initQuery = $this->select($fields)
            ->from('users U')
            ->join($joins)
            ->where(array('U.is_active' => 1, 'PI.is_active' => 1));

        $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;

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

    public function updateAbstractOfBids($id = '', $data = array())
    {
        $initQuery = $this->update('abstract_of_bids', $id, $data);

        return $initQuery;
    }

    public function updatePayablesReports($id = '', $data = array())
    {
        $initQuery = $this->update('audit_payables_reports', $id, $data);

        return $initQuery;
    }

    public function updatePurchaseRequisition($id = '', $data = array())
    {
        $initQuery = $this->update('purchase_requisitions', $id, $data);

        return $initQuery;
    }

    public function updatePurchaseRequisitionDescription($id = '', $data = array())
    {
        $initQuery   = $this->update('purchase_requisition_descriptions', $id, $data);

        return $initQuery;
    }

    public function selectAobList($id = false)
    {
        $fields = array(
            'P.id',
            'P.ref_no',
            'P.type_of_request',
            'P.remarks',
            'DATE_FORMAT(P.created_at, "%M/%d/%Y") as created_at',
            'P.task_assigned_to',
            'P.is_status',
            'P.is_active',
            'P.abstract_of_bids_id',
            'AOB.aob_no'
        );

        $joins = array(
            'abstract_of_bids AOB' => 'AOB.id = P.abstract_of_bids_id',
        );

        $initQuery = $this->select($fields)
            ->from('audit_payables_reports P')
            ->leftjoin($joins)
            ->where(array('P.is_active' => ':is_active', 'P.task_assigned_to' => ':task_assigned_to', 'AOB.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;
        // $initQuery = ($userId) ? $initQuery->andWhere(array('P.task_assigned_to' => ':task_assigned_to')) : $initQuery;
        $initQuery = $initQuery->logicEx('AND P.is_status IN (2, 3)');

        return $initQuery;
    }

    public function selectAOBWSList($id = false, $status = false)
    {
        $fields = array(
            'AOB.id',
            'AOB.aob_no',
            'AOB.date',
            'AOB.note',
            'AOB.status',
            'AOB.signatory_level',
            'AOB.is_active',
            'AOB.aob_status',
            'AOB.created_by',
            'AOB.updated_by',
            'AOB.created_at',
            'AOB.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('abstract_of_bids AOB')
            ->where(array('AOB.is_active' => ':is_active'));

        $initQuery = ($id)      ? $initQuery->andWhere(array('AOB.id' => ':id')) : $initQuery;
        $initQuery = ($status)  ? $initQuery->andWhere(array('AOB.aob_status' => ':aob_status')) : $initQuery;
        $initQuery = $initQuery->logicEx('AND AOB.signatory_level IN (2, 3, 4)');

        return $initQuery;
    }

    /**
     * `selectRfqAttachments` Query string that will select from table `rfq_attachments`.
     * @param  string $id
     * @param  string $rfqNo
     * @return string
     */
    public function selectRfqAttachments($id = false, $rfqNo = false)
    {
        $fields = array(
            'RA.id',
            'RA.attachment',
            'RA.rfq_no',
        );

        $initQuery = $this->select($fields)
            ->from('rfq_attachments RA')
            ->where(array('RA.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('RA.id' => ':id'))         : $initQuery;
        $initQuery = ($rfqNo) ? $initQuery->andWhere(array('RA.rfq_no' => ':rfq_no')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectRfqAttachments` Query string that will select from table `rfq_attachments`.
     * @param  string $id
     * @param  string $aobdId
     * @return string
     */
    public function selectPriceMatrixAttachments($id = false, $aobdId = false)
    {
        $fields = array(
            'AA.id',
            'AA.attachment',
        );

        $initQuery = $this->select($fields)
            ->from('aob_attachments AA')
            ->where(array('AA.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('AA.id' => ':id'))         : $initQuery;
        $initQuery = ($aobdId) ? $initQuery->andWhere(array('AA.aob_description_id' => ':aob_description_id')) : $initQuery;

        return $initQuery;
    }

    public function selectPaymentSummary($id = false, $aob_id = false)
    {
        $fields = array(
            'APS.id',
            'APS.tax_rate_id',
            'APS.rfq_id',
            'APS.gross_amount',
            'APS.ewt',
            'APS.net_of_tax',
            'S.name as supplier_name',
            'RFQ.is_vat as vat'
        );

        $joins = array(
            'request_quotations RFQ' => 'RFQ.id = APS.rfq_id',
            'suppliers S' => 'S.id = RFQ.supplier_id'
        );

        $initQuery = $this->select($fields)
            ->from('aob_payment_summaries APS')
            ->join($joins)
            ->where(array('APS.is_active' => ':is_active'));

        $initQuery = ($id)    ? $initQuery->andWhere(array('APS.id' => ':id'))         : $initQuery;
        $initQuery = ($aob_id) ? $initQuery->andWhere(array('APS.aob_id' => ':aob_id')) : $initQuery;

        return $initQuery;
    }

    ########################################################
    //New Query for PRS Attachments

    /**
         * `selectPurchaseRequisitions` Query string that will select from table `purchase_requisitions`.
         * @param  boolean $id
         * @return string
         */
        public function selectPurchaseRequisitions($id = false)
        {
            $fields = array(
                'DATE_FORMAT(PR.date_requested, "%b %d, %Y") as date_requested',
                'CONCAT(PI.lname, ", ", PI.fname) as requestor_name',
                'P.name as requestor_position',
                'PR.signatories',
                'PJ.project_code',
                'D.charging'
            );

            $joins = array(
                'users U'                    => 'U.id = PR.user_id',
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id',
            );

            $leftJoins = array(
                'projects PJ'   => 'PJ.id = PR.project_id',
                'departments D' => 'PR.department_id = D.id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
         * @return string
         */
        public function selectPersonalInformations($id = false, $departmentId = false, $isSignatory = false, $userId = false)
        {
            $fields = array(
                'PI.id',
                'CONCAT(PI.lname, ", ", PI.fname) as full_name',
                'P.name as position_name',
                'P.id as position_id',
                'U.id as user_id'
            );

            $joins = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'users U'                    => 'U.personal_information_id = PI.id'
            );

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(array('PI.is_active' => ':is_active'));

            $initQuery = ($id)           ? $initQuery->andWhere(array('PI.id' => ':id'))                      : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($isSignatory)  ? $initQuery->andWhere(array('P.is_signatory' => ':is_signatory'))   : $initQuery;
            $initQuery = ($userId)       ? $initQuery->andWhere(array('U.id' => ':user_id'))                  : $initQuery;

            return $initQuery;
        }

}
