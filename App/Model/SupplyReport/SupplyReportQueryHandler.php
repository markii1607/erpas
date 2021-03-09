<?php 
    namespace App\Model\SupplyReport;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class SupplyReportQueryHandler extends QueryHandler{
        
       public function selectPRS($id = false, $project = false, $department = false, $date_from = false, $date_to = false,  $mdate_from = false, $mdate_to = false, $ydate_from = false, $ydate_to = false, $rdate_from = false, $rdate_to = false)
    {
        $fields     = array('*');
        $initQuery  = $this->select($fields)
                           ->from('purchase_requisitions PR')
                           ->where(array('PR.is_active' => ':is_active'));

        $initQuery = ($project)    ? $initQuery->andWhereNull(array('PR.department_id'))      : $initQuery;
        $initQuery = ($department) ? $initQuery->andWhereNull(array('PR.project_id'))         : $initQuery;                  
        $initQuery = ($date_from)  ? $initQuery->logicEx('AND PR.created_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND PR.created_at  <= :date_to')    : $initQuery;
        $initQuery = ($mdate_from) ? $initQuery->logicEx('AND PR.created_at  >= :mdate_from') : $initQuery;
        $initQuery = ($mdate_to)   ? $initQuery->logicEx('AND PR.created_at  <= :mdate_to')   : $initQuery;
        $initQuery = ($ydate_from) ? $initQuery->logicEx('AND PR.created_at  >= :ydate_from') : $initQuery;
        $initQuery = ($ydate_to)   ? $initQuery->logicEx('AND PR.created_at  <= :ydate_to')   : $initQuery;
        $initQuery = ($rdate_from) ? $initQuery->logicEx('AND PR.created_at  >= :rdate_from') : $initQuery;
        $initQuery = ($rdate_to)   ? $initQuery->logicEx('AND PR.created_at  <= :rdate_to')   : $initQuery;

        return $initQuery;
    }

    public function selectAllProjects()
    {
        $fields     = array('*');
        $initQuery  = $this->select($fields)
                           ->from('projects P')
                           ->where(array('P.is_active' => ':is_active'));
        return $initQuery;
    }

    ///////////////////////  BREAKDOWN //////////////////////////////////////////////////////////////////////

    public function selectALLprsACTIVE( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $initQuery  = $this->select($fields)
                           ->from('purchase_requisitions prs')
                           ->where(array('prs.is_active' => 1));
        $initQuery = ($date_from)  ? $initQuery->logicEx('AND prs.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND prs.updated_at  <= :date_to')    : $initQuery;         
        

        return $initQuery;
    }


    public function selectPRSbeingPROCESSrfq( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'purchase_requisitions prs' => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('purchase_requisition_descriptions prsd')
                           ->join($join)
                           ->where(array('prs.is_active' => 1));
        $initQuery = ($date_from)  ? $initQuery->logicEx('AND prsd.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND prsd.updated_at  <= :date_to')    : $initQuery;         
        $initQuery =  $initQuery->logicEx('AND prsd.status  = 2');
        

        return $initQuery;
    }

    public function selectPRSbeingPROCESSaob( $date_from = false, $date_to = false)
    {

        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'aob_descriptions aobd'                  => 'aobd.aob_id = aob.id',
            'aob_supply_evaluations aobse'           => 'aobse.aob_description_id = aobd.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('abstract_of_bids aob')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND prsd.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND prsd.updated_at  <= :date_to')    : $initQuery;         
        $initQuery =  $initQuery->logicEx('AND aob.status = 0');

        return $initQuery;



        // $fields     = array('DISTINCT prs.prs_no');

        // $join = array(
        //     'purchase_requisitions prs' => 'prsd.purchase_requisition_id = prs.id',
        // );

        // $initQuery  = $this->select($fields)
        //                    ->from('purchase_requisition_descriptions prsd')
        //                    ->join($join)    
        //                    ->where(array('prs.is_active' => 1));

        // $initQuery = ($date_from)  ? $initQuery->logicEx('AND prsd.updated_at  >= :date_from')  : $initQuery;
        // $initQuery = ($date_to)    ? $initQuery->logicEx('AND prsd.updated_at  <= :date_to')    : $initQuery;     
        // $initQuery =  $initQuery->logicEx('AND prsd.status = 4');
    

        // return $initQuery;
    }

    
    public function selectSENTrfq($date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT rq.rfq_no');

        $initQuery  = $this->select($fields)
                           ->from('request_quotations rq')
                           ->where(array('rq.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND rq.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND rq.updated_at  <= :date_to')    : $initQuery;     
        $initQuery =  $initQuery->logicEx('AND rq.sent = 1');
    

        return $initQuery;
    }

    public function selectRECEIVEDrfq($date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT rq.rfq_no');

        $initQuery  = $this->select($fields)
                           ->from('request_quotations rq')
                           ->where(array('rq.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND rq.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND rq.updated_at  <= :date_to')    : $initQuery;     
        $initQuery =  $initQuery->logicEx('AND rq.received = 1');
    

        return $initQuery;
    }

    public function selectPRSwithCOMPLETErfq( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('request_quotations rq')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND rq.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND rq.updated_at  <= :date_to')    : $initQuery;     
        $initQuery =  $initQuery->logicEx('AND rq.received = 1');
        $initQuery =  $initQuery->logicEx('AND rq.c_unit_price != 0.00 OR rq.p_unit_price != 0.00');    


        return $initQuery;
    }


    
    public function selectPRSwithCOMPLETEaob( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'aob_descriptions aobd'                  => 'aobd.aob_id = aob.id',
            'aob_supply_evaluations aobse'           => 'aobse.aob_description_id = aobd.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('abstract_of_bids aob')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND aob.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND aob.updated_at  <= :date_to')    : $initQuery;         
        $initQuery =  $initQuery->logicEx('AND aob.status = 2');

        return $initQuery;
    }

    public function selectPRSreceivedBYaudit( $date_from = false, $date_to = false)
    {
        $fields = array('DISTINCT prs.prs_no');

        $join = array(
            'abstract_of_bids aob'                   => 'apr.abstract_of_bids_id = aob.id',
            'aob_descriptions aobd'                  => 'aobd.aob_id = aob.id',
            'aob_supply_evaluations aobse'           => 'aobse.aob_description_id = aobd.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('audit_payables_reports apr')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND apr.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND apr.updated_at  <= :date_to')    : $initQuery; 

        return $initQuery;
    }


    public function selectSENTauditTOacc( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'crs_crfs cc'                            => 'gv.id = cc.general_voucher_id ',
            'crs_crf_po ccp'                         => 'cc.id = ccp.crs_crf_id ',
            'purchase_orders po'                     => 'ccp.purchase_order_id = po.id ',
            'aob_supply_evaluations aobse'           => 'aobse.purchase_order_id = po.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('general_voucher gv')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND gv.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND gv.updated_at  <= :date_to')    : $initQuery; 

        return $initQuery;
        // $fields = array('DISTINCT prs.prs_no');

        // $join = array(
        //     'crs_crf_po ccp'                         => 'cc.id = ccp.crs_crf_id',
        //     'purchase_orders po'                     => 'ccp.purchase_order_id = po.id',
        //     'aob_supply_evaluations aobse'           => 'aobse.purchase_order_id = po.id',
        //     'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
        //     'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
        //     'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
        //     'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
        //     'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        // );

        // $initQuery  = $this->select($fields)
        //                    ->from('crs_crfs cc')
        //                    ->join($join)    
        //                    ->where(array('prs.is_active' => 1));

        // $initQuery = ($date_from)  ? $initQuery->logicEx('AND cc.updated_at  >= :date_from')  : $initQuery;
        // $initQuery = ($date_to)    ? $initQuery->logicEx('AND cc.updated_at  <= :date_to')    : $initQuery; 

        // return $initQuery;
    }
    public function selectRECEIVEDacc( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'crs_crfs cc'                            => 'gv.id = cc.general_voucher_id ',
            'crs_crf_po ccp'                         => 'cc.id = ccp.crs_crf_id ',
            'purchase_orders po'                     => 'ccp.purchase_order_id = po.id ',
            'aob_supply_evaluations aobse'           => 'aobse.purchase_order_id = po.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('general_voucher gv')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND gv.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND gv.updated_at  <= :date_to')    : $initQuery; 

        return $initQuery;
    }

    public function selectPRSwithCRSF( $date_from = false, $date_to = false)
    {
        $fields = array('DISTINCT prs.prs_no');

        $join = array(
            'crs_crf_po ccp'                         => 'cc.id = ccp.crs_crf_id',
            'purchase_orders po'                     => 'ccp.purchase_order_id = po.id',
            'aob_supply_evaluations aobse'           => 'aobse.purchase_order_id = po.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('crs_crfs cc')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND cc.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND cc.updated_at  <= :date_to')    : $initQuery; 

        return $initQuery;
    }

    // public function selectPRSsentTOaudit( $date_from = false, $date_to = false)
    // {
    //     $fields     = array('DISTINCT prs.prs_no');

    //     $join = array(
    //         'aob_descriptions aobd'                  => 'aobd.aob_id = aob.id',
    //         'aob_supply_evaluations aobse'           => 'aobse.aob_description_id = aobd.id',
    //         'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
    //         'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
    //         'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
    //         'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
    //         'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
    //     );

    //     $initQuery  = $this->select($fields)
    //                        ->from('abstract_of_bids aob')
    //                        ->join($join)    
    //                        ->where(array('prs.is_active' => 1));

    //     $initQuery = ($date_from)  ? $initQuery->logicEx('AND prsd.updated_at  >= :date_from')  : $initQuery;
    //     $initQuery = ($date_to)    ? $initQuery->logicEx('AND prsd.updated_at  <= :date_to')    : $initQuery;         
    //     $initQuery =  $initQuery->logicEx('AND aob.received = 1');


    //     return $initQuery;
    // }

    public function selectPRSwithPO( $date_from = false, $date_to = false)
    {
        $fields     = array('DISTINCT prs.prs_no');

        $join = array(
            'aob_supply_evaluations aobse'           => 'aobse.purchase_order_id = po.id',
            'request_quotations rq'                  => 'aobse.rfq_id = rq.id',
            'request_quotation_materials rqm'        => 'rq.rfq_material_id = rqm.id',
            'request_quotation_descriptions rqd'     => 'rqd.rfq_material_id = rqm.id',
            'purchase_requisition_descriptions prsd' => 'rqd.purchase_requisition_description_id = prsd.id',
            'purchase_requisitions prs'              => 'prsd.purchase_requisition_id = prs.id',
        );

        $initQuery  = $this->select($fields)
                           ->from('purchase_orders po')
                           ->join($join)    
                           ->where(array('prs.is_active' => 1));

        $initQuery = ($date_from)  ? $initQuery->logicEx('AND prsd.updated_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND prsd.updated_at  <= :date_to')    : $initQuery;         


        return $initQuery;
    }

public function selectWithDeliveredMaterialStorage()
    {

        $fields = array(
            'DISTINCT PRS.prs_no'
        );
        
        $join = array(
            'msb_inventories MSBI'                   => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_specification_brands MSB'      => 'MSBI.material_specification_brand_id = MSB.id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('msb_inventory_histories MSBIH')
                     ->join($join)
                     ->where(array('MSBIH.is_active' => 1));
                   
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  >= :date_from');  /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  <= :date_to');    /// TEMPORARY

        return $initQuery;  
    }

    public function selectWithApprovedVoucher()
    {

        $fields = array(
            'DISTINCT SPR.prs_no',
            'CC.id'
        );
        
        $join = array(
            'crs_crfs CC'                             => 'GV.id = CC.general_voucher_id ',
            'crs_crf_po CCP'                          => 'CC.id = CCP.crs_crf_id',
            'purchase_orders PO'                      => 'CCP.purchase_order_id = PO.id',
            'aob_supply_evaluations AOBSE'            => 'PO.id = AOBSE.purchase_order_id',
            'aob_descriptions AOBD'                   => 'AOBSE.aob_description_id = AOBD.id',
            'request_quotation_materials RQM'         => 'AOBD.rfq_material_id = RQM.id',
            'request_quotation_descriptions RQD'      => 'RQM.id = RQD.rfq_material_id',
            'purchase_requisition_descriptions SPRD'  => 'RQD.purchase_requisition_description_id = SPRD.id',
            'purchase_requisitions SPR'               => 'SPRD.purchase_requisition_id = SPR.id'
        );  

        $initQuery = $this->select($fields)
                     ->from('general_voucher GV')
                     ->join($join)
                     ->where(array('CC.is_active' => 1));
        $initQuery = $initQuery->logicEx('AND CC.accounting_status = 1');     /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND GV.status < 3');                /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND GV.created_at  >= :date_from'); /// TEMPORARY    
        $initQuery = $initQuery->logicEx('AND GV.created_at  <= :date_to');   /// TEMPORARY

        return $initQuery;  
    }

    public function selectApprovers()
    {

        $fields = array(
            'DISTINCT SPR.prs_no',
            'CC.id', 
            'GV.signatories'
        );
        
        $join = array(
            'crs_crfs CC'                             => 'GV.id = CC.general_voucher_id ',
            'crs_crf_po CCP'                          => 'CC.id = CCP.crs_crf_id',
            'purchase_orders PO'                      => 'CCP.purchase_order_id = PO.id',
            'aob_supply_evaluations AOBSE'            => 'PO.id = AOBSE.purchase_order_id',
            'aob_descriptions AOBD'                   => 'AOBSE.aob_description_id = AOBD.id',
            'request_quotation_materials RQM'         => 'AOBD.rfq_material_id = RQM.id',
            'request_quotation_descriptions RQD'      => 'RQM.id = RQD.rfq_material_id',
            'purchase_requisition_descriptions SPRD'  => 'RQD.purchase_requisition_description_id = SPRD.id',
            'purchase_requisitions SPR'               => 'SPRD.purchase_requisition_id = SPR.id'
        );  

        $initQuery = $this->select($fields)
                     ->from('general_voucher GV')
                     ->join($join)
                     ->where(array('CC.is_active' => 1));
        $initQuery = $initQuery->logicEx('AND CC.accounting_status = 1');     /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND GV.created_at  >= :date_from'); /// TEMPORARY    
        $initQuery = $initQuery->logicEx('AND GV.created_at  <= :date_to');   /// TEMPORARY

        return $initQuery;  
    }



////////////////////////// END OF BREAKDOWN ////////////////////////////////////////////////////////////////////

    public function selectPRSCount()
    {
        $fields     = array('id');
        $initQuery  = $this->select($fields)
                           ->from('purchase_requisitions PR')
                           ->where(array('PR.is_active' => 1));

        return $initQuery;
    }


    // SELECT COUNT(*) FROM TableName

    public function selectPRSDcount($id = false)
    {
        $fields     = array('status' , 'purchase_requisition_id');
        $initQuery  = $this->select($fields)
                           ->from('purchase_requisition_descriptions  prd')
                           ->where(array('prd.is_active' => ':is_active'));
        $initQuery = ($id)   ? $initQuery->logicEx('AND prd.purchase_requisition_id = :id AND prd.status IS NOT NULL' )   : $initQuery;

         return $initQuery;
    }


    public function selectCancelled($id = false, $project = false, $department = false, $date_from = false, $date_to = false){
        $fields     = array(
        'prs.prs_no',
        'prs.project_id',
        'CONCAT(pro.name,"  -  ",pro.location) as projectDesc',
        'prs.request_type_id',
        'rqt.name',
        'prs.created_by',
        'CONCAT(pi.lname,", ",pi.fname," ", pi.mname) as cancelled_by',
        'prs.status',
        'CONCAT(pei.lname,", ",pei.fname," ", pei.mname) as updated_by',
        'prs.updated_at'
        );

        $join = array(
            'projects pro'               => 'prs.project_id = pro.id',
            'request_types rqt'          => 'prs.request_type_id = rqt.id',
            'users u'                    => 'prs.created_by = u.id',
            'personal_informations pi'   => 'u.personal_information_id = pi.id',
            'users us'                   => 'prs.updated_by = us.id',
            'personal_informations pei'  => 'us.personal_information_id = pei.id',
        );

        $initQuery = $this->select($fields)
                          ->from('purchase_requisitions prs')
                          ->leftJoin($join)
                          ->where(array('prs.is_active' => ':is_active'))
                          ->andWhere(array('prs.for_cancelation' => 1));

        $initQuery = ($project)    ? $initQuery->andWhereNull(array('prs.department_id'))      : $initQuery;
        $initQuery = ($department) ? $initQuery->andWhereNull(array('prs.project_id'))         : $initQuery;  
        $initQuery = ($date_from)  ? $initQuery->logicEx('AND prs.created_at  >= :date_from')  : $initQuery;
        $initQuery = ($date_to)    ? $initQuery->logicEx('AND prs.created_at  <= :date_to')    : $initQuery;         
        return $initQuery;
    }
    
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
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id',
            'departments D'              => 'P.department_id = D.id'
        );
        
        $initQuery = $this->select($fields)
                     ->from('users U')
                     ->leftJoin($join)
                     ->where(array('U.is_active' => ':status'));

        $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectReportNoList($data = array())
    {
        $fields = array(
            'report_no_desc'
        );
        $initQuery = $this->select($fields)
                          ->from('prs_report')
                          ->where(array('prs_report.is_active' => ':status'));
        $initQuery = $initQuery->andWhere(array('prs_report.report_type' => ':report_type'));
        
        return $initQuery;
    }

    public function selectReport($data = array())
    {
        $fields = array('*');
        $initQuery = $this->select($fields)
                          ->from('prs_report')
                          ->where(array('prs_report.is_active' => ':status'));
        $initQuery = $initQuery->andWhere(array('prs_report.report_no_desc' => ':report_no'));
        
        return $initQuery;
    }

         
    public function selectReportNo()
    {
        $fields = array('report_no, created_at');
        $initQuery = $this->select($fields)
                          ->from('prs_report')
                          ->where(array('prs_report.report_type' => ':report_type'));
        $initQuery = $initQuery->logicEx('ORDER BY id DESC LIMIT 1');
        
        return $initQuery;
    }

    public function selectMaterials($data = array())
    {
        $fields = array(
            'm.id as mat_id',  
            'm.material_category_id as mat_cat_id',
            'm.name as mat_name',
            'mc.name as mat_cat_name'
        );
        $join = array(
            'material_categories mc' => 'mc.id = m.material_category_id',
        );


        $initQuery = $this->select($fields)
                          ->from('materials m')
                          ->join($join)
                          ->where(array('m.is_active' => ':status'));
        return $initQuery;
    }

     public function selectMaterialsSpecs($data = array())
    {
        $fields = array(
            
            'MS.id as mat_spec_id',
            'MS.code as mat_spec_code',
            'MS.specs as mat_specification'
        );


        $initQuery = $this->select($fields)
                          ->from('material_specifications MS')
                          ->where(array('MS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND MS.material_id  = :mat_id');

        return $initQuery;
    }

    
    public function selectMaterialsSpecsUnit()
    {
        $fields = array(
            'DISTINCT MSBS.code as code',
            'MSBS.unit as unit',
        );

        $join = array(
            'material_specification_brands MSB'          => 'MSBS.material_specification_brand_id = MSB.id',
            
        );

        $initQuery = $this->select($fields)
                        ->from('msb_suppliers MSBS')
                        ->Join($join)
                        ->where(array('MSBS.is_active' => ':status'));
        
        $initQuery = $initQuery->logicEx('AND MSB.material_specification_id  = :mat_spec_id');
                          
        return $initQuery;
    }

 

    // public function selectMaterialsSpec($mat_id = false)
    // {
    //     $fields = array(
    //         'MSBS.unit as unit',
    //         'MS.id as mat_spec_id', 
    //         'MS.material_id', 
    //         'MS.code',
    //         'MSBS.code as code',
    //         'MS.specs as description'
        
    //     );

    //     $join = array(
    //         'material_specification_brands MSB'          => 'MSBS.material_specification_brand_id = MSB.id',
    //         'material_specifications MS'                 => 'MS.id = MSB.material_specification_id'
            
    //     );

    //     $initQuery = $this->select($fields)
    //                     ->from('msb_suppliers MSBS')
    //                     ->Join($join)
    //                     ->where(array('MSBS.is_active' => ':status'));
        
    //     $initQuery = ($mat_id)  ? $initQuery->logicEx('AND MS.material_id  = :mat_id')  : $initQuery;
                          
    //     return $initQuery;
    // }

    
    // public function selectMaterials()
    // {
    //     $fields = array(
    //         'MSBS.unit as unit',
    //         'MS.id as mat_spec_id', 
    //         'MS.material_id', 
    //         'MS.code',
    //         'MSBS.code as code',
    //         'MS.specs as description'
        
    //     );

    //     $join = array(
    //         'material_specification_brands MSB'          => 'MSBS.material_specification_brand_id = MSB.id',
    //         'material_specifications MS'                 => 'MS.id = MSB.material_specification_id'
            
    //     );

    //     $initQuery = $this->select($fields)
    //                     ->from('msb_suppliers MSBS')
    //                     ->Join($join)
    //                     ->where(array('MSBS.is_active' => ':status'));
        
    //     $initQuery = ($mat_id)  ? $initQuery->logicEx('AND MS.material_id  = :mat_id')  : $initQuery;
                          
    //     return $initQuery;
    // }


    // public function selectMaterialsSpec($mat_id = false)
    // {
    //     $fields = array(
    //         'id as mat_spec_id', 
    //         'material_id', 
    //         'code',
    //         'specs as description'
    //     );
    //     $initQuery = $this->select($fields)
    //                       ->from('material_specifications')
    //                       ->where(array('material_specifications.is_active' => ':status'));
        
    //     $initQuery = ($mat_id)  ? $initQuery->logicEx('AND material_specifications.material_id  = :mat_id')  : $initQuery;
                          
    //     return $initQuery;
    // }   

    public function selectPmCtQ($code, $desc, $spec, $unit)
    {
        $fields = array(
            'DISTINCT MS.code as material_code',
            'M.name',
            'MS.specs',
            'PRS.prs_no as prs_no',
            'PO.po_no as po_no',
            'PO.date_issued as date_issued',
            'RQM.quantity as quantity',
            'PRSD.unit_measurement as unit',
            // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
            'if(RQ.c_unit_price = 0, RQ.p_unit_price, RQ.c_unit_price) as unit_price',
            'W.ws_no as withdrawal_slip_no',
            'WI.withdrawn_quantity as withdrawn_quantity',
            'P.project_code as project_id',
            'P.name as project_name',
            'P.location as project_location', 
            'DATE_FORMAT( WSR.updated_at,  "%Y-%m-%d") as date_delivered'
        );
        
        $join = array(
            'withdrawals W'                          => 'WI.withdrawal_id = W.id',
            'msb_inventory_histories MSBIH'          => 'MSBIH.withdrawal_item_id = WI.id',
            'msb_inventories MSBI'                   => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_specification_brands MSB'      => 'MSBI.material_specification_brand_id = MSB.id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
            'withdrawal_warehouse_releases WWR'      => 'WWR.withdrawal_id = W.id',
            'withdrawal_security_releases WSR'       => 'WSR.withdrawal_warehouse_release_id = WWR.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('withdrawal_items WI')
                     ->join($join)
                     ->where(array('WI.is_active' => ':status'));
                   
        $initQuery = ($code)? $initQuery->andWhere(array('MS.code'  => ':mat_code')):$initQuery;
        $initQuery = ($desc)? $initQuery->andWhere(array('M.name'   => ':mat_desc')):$initQuery;
        $initQuery = ($spec)? $initQuery->andWhere(array('MS.specs' => ':mat_spec')):$initQuery;
        $initQuery = ($unit)? $initQuery->andWhere(array('PRSD.unit_measurement' => ':mat_unit')):$initQuery;
        $initQuery = $initQuery->logicEx('AND PRSD.created_at  >= :date_from');
        $initQuery = $initQuery->logicEx('AND PRSD.created_at  <= :date_to');
                          
        return $initQuery;  
    }
    
    public function selectPmCtQc($code, $desc, $spec, $unit)
    {
        $fields = array(
            'M.name',
            'MS.specs',
            'PRS.prs_no as prs_no',
            'PO.po_no as po_no',
            'PO.date_issued as date_issued',
            'MSBIH.quantity as quantity',
            'MSBI.unit as unit',
            // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
            'if(RQ.c_unit_price = 0, RQ.p_unit_price, RQ.c_unit_price) as unit_price',
            'P.project_code as project_id',
            'P.name as project_name',
            'P.location as project_location' 
        );
        
        $join = array(
            'msb_inventory_histories MSBIH'          => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_deliveries MD'                 => 'MD.id = MSBIH.material_delivery_id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('msb_inventories MSBI')
                     ->join($join)
                     ->where(array('MSBIH.is_active' => ':status'));
                   
        $initQuery = ($code)? $initQuery->andWhere(array('MS.code'  => ':mat_code')):$initQuery;
        $initQuery = ($desc)? $initQuery->andWhere(array('M.name'   => ':mat_desc')):$initQuery;
        $initQuery = ($spec)? $initQuery->andWhere(array('MS.specs' => ':mat_spec')):$initQuery;
        $initQuery = ($unit)? $initQuery->andWhere(array('MSBI.unit' => ':mat_unit')):$initQuery;
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  >= :date_from');
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  <= :date_to');

                          
        return $initQuery;  
    }

    public function selectPMCT()
    {
        $fields = array(
            
        );
        
        $join = array(
            'msb_inventory_histories MSBIH'          => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_deliveries MD'                 => 'MD.id = MSBIH.material_delivery_id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('msb_inventories MSBI')
                     ->join($join)
                     ->where(array('MSBIH.is_active' => ':status'));
                   
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  >= :date_from');
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  <= :date_to');

                          
        return $initQuery;  
    }


    // public function selectPmCtQ($code, $desc, $spec, $unit)
    // {
        

    //     $fields = array(
    //         // 'DISTINCT ms.code',
    //         // 'm.name',
    //         // 'ms.specs',
    //         'prs.created_at as date_issued_prs',
    //         'prs.prs_no',
    //         // 'proj.location as project_loc',
    //         'prsd.quantity as prsd_quantity',   // RQM QUANTITY
    //         // 'rqm.unit',
    //         'prsd.id as prsd_id',
    //         // 'po.po_no',
    //         // 'po.date_issued',
    //         // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
    //         // 'proj.id as project_id',
    //         // 'proj.name as project_desc',
    //         // 'rqm.quantity as rqm_quantity',
    //         // 'w.ws_no',
    //         // 'wi.withdrawn_quantity',
    //         // 'aobse.id'
    //     );
        
    //     $join = array(
    //         'purchase_requisition_descriptions prsd'    => ' prsd.purchase_requisition_id = prs.id',
    //         'material_specifications ms'                => 'prsd.item_spec_id = ms.id',
    //         'materials m'                               => 'ms.material_id = m.id',
    //         // 'request_quotation_descriptions rqd'        => 'rqd.purchase_requisition_description_id = prsd.id',
    //         // 'request_quotation_materials rqm'           => 'rqd.rfq_material_id = rqm.id', 
    //         // 'aob_descriptions aobd'                     => 'aobd.rfq_material_id = rqm.id',
    //         // 'aob_supply_evaluations aobse'              => 'aobd.id = aobse.aob_description_id',
    //         // 'request_quotations rq'                     => 'rq.id = aobse.rfq_id',
    //         // 'purchase_orders po'                        => 'po.id = aobse.purchase_order_id ',
    //         // 'projects proj'                             => 'prs.project_id = proj.id',
    //         // // 'crs_crf_po ccpo'                           => 'po.id = ccpo.purchase_order_id',
    //         // // 'crs_crfs cc'                               => 'ccpo.crs_crf_id = cc.id',
    //         // // 'general_voucher gv'                        => 'gv.crs_crfs_id = cc.id',
    //         // 'withdrawal_items wi'                       => 'wi.purchase_requisition_description_id = prsd.id',
    //         // 'withdrawals w'                             => 'wi.withdrawal_id = w.id'
    //     );


                     
    //     $initQuery = $this->select($fields)
    //     ->from('purchase_requisitions prs')
    //     ->leftJoin($join)
    //     ->where(array('prs.is_active' => ':status '));
                   
    //     $initQuery = ($code)? $initQuery->andWhere(array('ms.code'  => ':mat_code')):$initQuery;
    //     $initQuery = ($desc)? $initQuery->andWhere(array('m.name'   => ':mat_desc')):$initQuery;
    //     $initQuery = ($spec)? $initQuery->andWhere(array('ms.specs' => ':mat_spec')):$initQuery;
    //     $initQuery = ($unit)? $initQuery->andWhere(array('prsd.unit_measurement' => ':mat_unit')):$initQuery;
    //     $initQuery = $initQuery->logicEx('AND prs.created_at  >= :date_from');
    //     $initQuery = $initQuery->logicEx('AND prs.created_at  <= :date_to');

                          
    //     return $initQuery;  
    // }
    // public function selectApMcT()
    // {
    //     $fields = array(
    //         'DISTINCT 
    //       prs.created_at as date_issued_prs',
    //         'prs.prs_no',
    //         'proj.location as project_loc',
    //         'prsd.quantity',
    //         'prsd.unit_measurement',
    //         'prsd.id as prsd_id',
    //         'po.po_no as po_number',
    //         'po.date_issued',
    //         'if(rq.c_unit_price = 0, rq.p_unit_price, rq.c_unit_price) as unit_price',
    //         'proj.id as project_id',
    //         'proj.name as project_desc',
    //         'gv.gv_no', 
    //         'cc.id as voucher_id', 
    //         'cc.accounting_status', 
    //         'gv.status as voucher_status'
    //     );

    //     $join = array(
    //         'purchase_requisition_descriptions prsd'     => ' prsd.purchase_requisition_id = prs.id',
    //         'material_specifications ms'                 => 'prsd.item_spec_id = ms.id',
    //         'materials m'                                => 'ms.material_id = m.id',
    //         'request_quotation_descriptions rqd'         => 'rqd.purchase_requisition_description_id = prsd.id',
    //         'request_quotation_materials rqm'            => 'rqd.rfq_material_id = rqm.id', 
    //         'aob_descriptions aobd'                      => 'aobd.rfq_material_id = rqm.id',
    //         'aob_supply_evaluations aobse'               => 'aobd.id = aobse.aob_description_id',
    //         'request_quotations rq'                      => 'rq.id = aobse.rfq_id',
    //         'purchase_orders po'                         => 'po.id = aobse.purchase_order_id ',
    //         'projects proj'                              => 'prs.project_id = proj.id',
    //         'crs_crf_po ccpo'                            => 'po.id = ccpo.purchase_order_id',
    //         'crs_crfs cc'                                => 'ccpo.crs_crf_id = cc.id',
    //         'general_voucher gv'                         => 'gv.crs_crfs_id = cc.id'
    //     );
            
    //     $initQuery = $this->select($fields)
    //                  ->from('purchase_requisitions prs')
    //                  ->leftJoin($join)
    //                  ->where(array('prs.is_active' => ':status AND aobse.purchase_order_id IS NOT NULL'));
                
    //     $initQuery = $initQuery->logicEx('AND gv.id IS NOT NULL');             
    //     $initQuery = $initQuery->logicEx('AND prs.created_at  >= :date_from');
    //     $initQuery = $initQuery->logicEx('AND prs.created_at  <= :date_to');

                          
    //     return $initQuery;  
    // }

 




    public function selectAPMCT()
        {
            $fields = [
                'DISTINCT(PR.project_id)',
                'P.name',
                'P.location',
                // 'PR.created_at',

                
                //This is Temporary
                //Must be from Date Received from Withdrawals or upon receiving receipt from Cashier
               
                //'gv.created_at',


                '(SELECT count( DISTINCT PO.id) 
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id 
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN request_quotation_descriptions RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD 
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = PR.project_idjbn
                AND GV.created_at >= :date_from
                AND GV.created_at <= :date_to) as total_po',

                
                '(SELECT count(DISTINCT CC.id) 
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id 
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id 
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN request_quotation_descriptions RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD 
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = PR.project_id 
                AND GV.created_at >= :date_from
                AND GV.created_at <= :date_to) as total_voucher',

                '(SELECT count( DISTINCT CC.id) 
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id 
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN request_quotation_descriptions RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD 
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = PR.project_id 
                AND CC.accounting_status = 0 
                AND GV.status < 3
                AND GV.created_at >= :date_from
                AND GV.created_at <= :date_to) as without_voucher',
              
                '(SELECT count(CC.id)   
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id 
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id 
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN     RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = PR.project_id 
                AND CC.accounting_status = 1 
                AND GV.status >= 3
                AND GV.created_at >= :date_from
                AND GV.created_at <= :date_to) as with_voucher',

                'P.date_started',
                'P.date_finished',

                // '(SELECT sum((SPRD.quantity * IF(RQ.rfq_type = "0", RQ.c_unit_price, RQ.p_unit_price))) 
                
                '(SELECT sum((SPRD.quantity * if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price))) 
                 FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id 
                JOIN request_quotations RQ 
                ON AOBSE.rfq_id = RQ.id
                JOIN request_quotation_descriptions RQD 
                ON RQ.rfq_material_id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD 
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
          
                WHERE SPR.project_id = PR.project_id
                AND SPR.created_at >= :date_from
                AND PO.created_at <= :date_to) as purchase_amount',
                
            ];

            $joins = [
                'purchase_requisition_descriptions PRD'      => 'PR.id = PRD.purchase_requisition_id',
                'projects P'                                 => 'PR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->where(['PR.is_active' => 1]);
            $initQuery = $initQuery->logicEx('AND P.date_started  >= :date_from');
            $initQuery = $initQuery->logicEx('AND P.date_finished  <= :date_to');


            return $initQuery;
       }

       public function selectAPMCTall()
        {
            $fields = [
                'P.id',
                'P.name',
                'P.project_code',
                'P.location',
                'P.date_started',
                'P.date_finished',

                // '(SELECT count( DISTINCT PO.id) 
                // FROM general_voucher GV 
                // JOIN crs_crfs CC 
                // ON GV.id = CC.general_voucher_id 
                // JOIN crs_crf_po CCP 
                // ON CC.id = CCP.crs_crf_id 
                // JOIN purchase_orders PO 
                // ON CCP.purchase_order_id = PO.id
                // JOIN aob_supply_evaluations AOBSE 
                // ON PO.id = AOBSE.purchase_order_id 
                // JOIN aob_descriptions AOBD 
                // ON AOBSE.aob_description_id = AOBD.id 
                // JOIN request_quotation_materials RQM 
                // ON AOBD.rfq_material_id = RQM.id 
                // JOIN request_quotation_descriptions RQD 
                // ON RQM.id = RQD.rfq_material_id 
                // JOIN purchase_requisition_descriptions SPRD 
                // ON RQD.purchase_requisition_description_id = SPRD.id 
                // JOIN purchase_requisitions SPR 
                // ON SPRD.purchase_requisition_id = SPR.id 
                // WHERE SPR.project_id = P.id) as total_po',
                
                '(SELECT count( DISTINCT PO.id)
                FROM purchase_orders PO
                JOIN aob_supply_evaluations AOBSE
                ON PO.id = AOBSE.purchase_order_id
                JOIN aob_descriptions AOBD
                ON AOBSE.aob_description_id = AOBD.id
                JOIN request_quotation_materials RQM
                ON AOBD.rfq_material_id = RQM.id
                JOIN request_quotation_descriptions RQD
                ON RQM.id = RQD.rfq_material_id
                JOIN purchase_requisition_descriptions SPRD
                ON RQD.purchase_requisition_description_id = SPRD.id
                JOIN purchase_requisitions SPR
                ON SPRD.purchase_requisition_id = SPR.id
                WHERE SPR.project_id = P.id) as total_po',

                '(SELECT count(DISTINCT CC.id) 
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id 
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id 
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN request_quotation_descriptions RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD 
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = P.id ) as total_voucher',

                '(SELECT count( DISTINCT CC.id) 
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id 
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN request_quotation_descriptions RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD 
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = P.id 
                AND (CC.accounting_status = 1 OR CC.accounting_status = 0)
                AND NOT GV.status >= 3) as without_voucher',
              
                '(SELECT count(DISTINCT(CC.id)) 
                FROM general_voucher GV 
                JOIN crs_crfs CC 
                ON GV.id = CC.general_voucher_id 
                JOIN crs_crf_po CCP 
                ON CC.id = CCP.crs_crf_id 
                JOIN purchase_orders PO 
                ON CCP.purchase_order_id = PO.id 
                JOIN aob_supply_evaluations AOBSE 
                ON PO.id = AOBSE.purchase_order_id 
                JOIN aob_descriptions AOBD 
                ON AOBSE.aob_description_id = AOBD.id 
                JOIN request_quotation_materials RQM 
                ON AOBD.rfq_material_id = RQM.id 
                JOIN request_quotation_descriptions RQD 
                ON RQM.id = RQD.rfq_material_id 
                JOIN purchase_requisition_descriptions SPRD
                ON RQD.purchase_requisition_description_id = SPRD.id 
                JOIN purchase_requisitions SPR 
                ON SPRD.purchase_requisition_id = SPR.id 
                WHERE SPR.project_id = P.id 
                AND CC.accounting_status = 1 
                AND GV.status >= 3) as with_voucher',
                
            ];

            // $joins = [
            //     'projects P'                                 => 'PR.project_id = P.id',
            // ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              //->join($joins)
                              ->where(['P.is_active' => 1]);


            return $initQuery;
       }

       public function selectPMBSW()
    {

        $fields = array(
            'DISTINCT MS.code as material_code',
            'PRSD.unit_measurement as unit',
            'M.name as description',
            'MS.specs as specification',
            'PRS.prs_no',
            'P.id',
            'P.project_code',
            'MSBI.quantity as qt_balance',
            'WI.withdrawn_quantity as withdrawn_quantity',
            // 'WI.created_at as date_withdrawn',
            'DATE_FORMAT( WI.created_at,  "%Y-%m-%d") as date_withdrawn',
            'DATE_FORMAT( WSR.updated_at,  "%Y-%m-%d") as date_delivered'

            // 'M.name',
            // 'MS.specs',
            // 'PRS.prs_no as prs_no',
            // 'PO.po_no as po_no',
            // 'PO.date_issued as date_issued',
            // 'RQM.quantity as quantity',
            // 'PRSD.unit_measurement as unit',
            // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
            // 'W.ws_no as withdrawal_slip_no',
            // 'WI.withdrawn_quantity as withdrawn_quantity',
            // 'P.project_code as project_id',
            // 'P.name as project_name',
            // 'P.location as project_location' 
        );
        
        $join = array(
            'withdrawals W'                          => 'WI.withdrawal_id = W.id',
            'msb_inventory_histories MSBIH'          => 'MSBIH.withdrawal_item_id = WI.id',
            'msb_inventories MSBI'                   => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_specification_brands MSB'      => 'MSBI.material_specification_brand_id = MSB.id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
            'withdrawal_warehouse_releases WWR'      => 'WWR.withdrawal_id = W.id',
            'withdrawal_security_releases WSR'       => 'WSR.withdrawal_warehouse_release_id = WWR.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('withdrawal_items WI')
                     ->join($join)
                     ->where(array('WI.is_active' => ':status'));
        
        $initQuery = $initQuery->logicEx('AND P.project_code = :project_code');
        $initQuery = $initQuery->logicEx('AND WI.created_at  >= :date_from');  /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND WI.created_at  <= :date_to');    /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND WSR.status      =  2');

        // $fields = array(
        //     'MS.code as material_code',
        //     'PRSD.unit_measurement as unit',
        //     'M.name as material_description',
        //     'MS.specs as specification',
        //     'PRS.prs_no'
        // );

        // $join = array(
        //     'purchase_requisition_descriptions PRSD'     => ' PRSD.purchase_requisition_id = PRS.id',
        //     'material_specifications MS'                 => 'PRSD.item_spec_id = MS.id',
        //     'materials M'                                => 'MS.material_id = M.id',
        //     'material_specification_brands MSB'          => 'MSB.material_specification_id = ms.id',
        //     'msb_inventories MI'                         => 'MI.material_specification_brand_id = MSB.id',
        // );
            
        // $initQuery = $this->select($fields)
        //              ->from('purchase_requisitions PRS')
        //              ->leftJoin($join)
        //              ->where(array('PRS.is_active' => ':status'));

        // $initQuery = $initQuery->logicEx('AND PRS.created_at  >= :date_from');
        // $initQuery = $initQuery->logicEx('AND PRS.created_at  <= :date_to');

                          
        return $initQuery;  
    }
    public function selectMPM()
    {
        $fields = array(
            'DISTINCT MS.code as material_code',
            'M.name as material_desc',
            'MS.specs as material_spec',
            'RQM.unit',
        );

        $join = array(
            'material_specifications MS'                  => 'MS.material_id = M.id',
            'purchase_requisition_descriptions PRSD'      => 'PRSD.item_spec_id = MS.id', 
            'purchase_requisitions PRS'                   => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'          => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'             => 'RQM.id = RQD.rfq_material_id',
            'request_quotations RQ'                       => 'RQ.rfq_material_id = RQM.id',
            'suppliers S'                                 => 'RQ.supplier_id = S.id'
        );

        $initQuery = $this->select($fields)
                     ->from('materials M')
                     ->leftJoin($join)
                     ->where(array('PRS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc');   
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');            
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  >= :date_from');
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  <= :date_to');
        $initQuery = $initQuery->logicEx('AND RQM.unit = :mat_unit');
       // $initQuery = $initQuery->logicEx('AND S.name = :supplier');

    
        return $initQuery;  
    }

    public function selectPriceSupDate()
    {
        $fields = array( 
            'DISTINCT MAX(RQ.validity_of_offer) as validity_offer',
        );

        $join = array(
            'material_specifications MS'                  => 'MS.material_id = M.id',
            'purchase_requisition_descriptions PRSD'      => 'PRSD.item_spec_id = MS.id', 
            'purchase_requisitions PRS'                   => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'          => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'             => 'RQM.id = RQD.rfq_material_id',
            'request_quotations RQ'                       => 'RQ.rfq_material_id = RQM.id',
            'suppliers S'                                 => 'RQ.supplier_id = S.id'
        );
            
        $initQuery = $this->select($fields)
                     ->from('materials M')
                     ->leftJoin($join)
                     ->where(array('PRS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc'); 
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');             
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  >= :date_from');
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  <= :date_to');
        $initQuery = $initQuery->logicEx('AND RQM.unit = :mat_unit');
        $initQuery = $initQuery->logicEx('AND S.name = :supplier');

      
                          
        return $initQuery;  
    }

    public function selectPriceSupPrice()
    {
        $fields = array( 
            'DISTINCT  if(RQ.c_delivery_price = 0, RQ.p_delivery_price, RQ.c_delivery_price) as unit_price',
            'S.name',
            'RQ.validity_of_offer'
        );

        $join = array(
            'material_specifications MS'                 => 'MS.material_id = M.id',
            'purchase_requisition_descriptions PRSD'     => 'PRSD.item_spec_id = MS.id', 
            'purchase_requisitions PRS'                  => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'         => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'            => 'RQM.id = RQD.rfq_material_id',
            'request_quotations RQ'                      => 'RQ.rfq_material_id = RQM.id',
            'suppliers S'                                => 'RQ.supplier_id = S.id'
        );
            
        $initQuery = $this->select($fields)
                     ->from('materials M')
                     ->leftJoin($join)
                     ->where(array('PRS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc'); 
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');             
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  = :validity');
        $initQuery = $initQuery->logicEx('AND RQM.unit = :mat_unit');
        $initQuery = $initQuery->logicEx('AND S.name = :supplier');
                          
        return $initQuery;  
    }
    
    
    public function selectSuppliers()
    {
        $fields = array(
            'DISTINCT S.name'
        );

        $join = array(
            'material_specifications MS'                 => 'MS.material_id = M.id',
            'purchase_requisition_descriptions PRSD'     => 'PRSD.item_spec_id = MS.id', 
            'purchase_requisitions PRS'                   => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'         => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'             => 'RQM.id = RQD.rfq_material_id',
            'request_quotations RQ'                       => 'RQ.rfq_material_id = RQM.id',
            'suppliers S'                                => 'RQ.supplier_id = S.id'
        );
            
        $initQuery = $this->select($fields)
                     ->from('materials M')
                     ->leftJoin($join)
                     ->where(array('PRS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc');
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  >= :date_from');
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  <= :date_to');
        $initQuery = $initQuery->logicEx('AND RQM.unit = :mat_unit');

                          
        return $initQuery;      
    }


/////////////////////////////////   PMCT QUERIES /////////////////////////
            //  Alternate if Target = MSBInventories
    public function select_pmctMSBI()
    {
        $fields = array(
            'PRS.project_id  as PRSid',
            'MD.project_id as MDprojID',
            // 'ms.specs',  //FOR CHECKING
            'P.project_code as project_id',
            'MS.code as material_code',
            'M.name as material_name',
            'MS.specs as material_specs',
            'P.name as project_name',
            'P.location as project_location',
            'DATE_FORMAT(PRS.created_at, "%Y-%m-%d") as date_of_prs',
            // 'PRS.created_at as date_of_prs',
            'PO.po_no as po_no',
            'PO.date_issued as date_of_po',
            'MSBIH.quantity as quantity',
            'MSBI.unit as unit',
            // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
            'if(rq.c_unit_price = 0, rq.p_unit_price, rq.c_unit_price) as unit_price',
        );
        
        $join = array(
            'msb_inventory_histories MSBIH'          => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_deliveries MD'                 => 'MD.id = MSBIH.material_delivery_id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('msb_inventories MSBI')
                     ->join($join)
                     ->where(array('MSBIH.is_active' => ':status'));
                   
        $initQuery = $initQuery->logicEx('AND MSBIH.material_delivery_id IS NOT NULL');
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  >= :date_from');
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  <= :date_to');

                          
        return $initQuery;  
    }

      //  Alternate if Target = Withdrawal Items
    public function select_pmctWI()
    {
        $fields = array(
            'PRS.project_id  as PRSid',
            //'MD.project_id as MDprojID',
            // 'ms.specs',  //FOR CHECKING
            'P.project_code as project_id',
            'MS.code as material_code',
            'M.name as material_name',
            'MS.specs as material_specs',
            'P.name as project_name',
            'P.location as project_location',
            'DATE_FORMAT(PRS.created_at, "%Y-%m-%d") as date_of_prs',
            // 'PRS.created_at as date_of_prs',
            'PO.po_no as po_no',
            'PO.date_issued as date_of_po',
            'WI.withdrawn_quantity as quantity',
            'MSBI.unit as unit',
            // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
            'if(RQ.c_unit_price = 0, RQ.p_unit_price, RQ.c_unit_price) as unit_price',
        );
        
        $join = array(
            'withdrawals W'                          => 'WI.withdrawal_id = W.id',
            'msb_inventory_histories MSBIH'          => 'MSBIH.withdrawal_item_id = WI.id',
            'msb_inventories MSBI'                   => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_specification_brands MSB'      => 'MSBI.material_specification_brand_id = MSB.id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('withdrawal_items WI')
                     ->join($join)
                     ->where(array('WI.is_active' => ':status'));
                   
    //    $initQuery = $initQuery->logicEx('AND MSBIH.material_delivery_id IS NOT NULL');
        $initQuery = $initQuery->logicEx('AND WI.created_at  >= :date_from');
        $initQuery = $initQuery->logicEx('AND WI.created_at  <= :date_to');

                          
        return $initQuery;  
    }

    public function select_ApmctWI()
    {
        $fields = array(
            'PRS.project_id  as PRSid',
            //'MD.project_id as MDprojID',
            // 'ms.specs',  //FOR CHECKING
            'P.project_code as project_id',
            'MS.code as material_code',
            'M.name as material_name',
            'MS.specs as material_specs',
            'P.name as project_name',
            'P.location as project_location',
            'DATE_FORMAT(PRS.created_at, "%Y-%m-%d") as date_of_prs',
            
            // 'PRS.created_at as date_of_prs',
            'PO.po_no as po_no',
            'PO.date_issued as date_of_po',
            'WI.withdrawn_quantity as quantity',
            'MSBI.unit as unit',
            // 'if(rq.c_delivery_price = 0, rq.p_delivery_price, rq.c_delivery_price) as unit_price',
            'if(RQ.c_unit_price = 0, RQ.p_unit_price, RQ.c_unit_price) as unit_price',
        );
        
        $join = array(
            'withdrawals W'                          => 'WI.withdrawal_id = W.id',
            'msb_inventory_histories MSBIH'          => 'MSBIH.withdrawal_item_id = WI.id',
            'msb_inventories MSBI'                   => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_specification_brands MSB'      => 'MSBI.material_specification_brand_id = MSB.id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('withdrawal_items WI')
                     ->join($join)
                     ->where(array('WI.is_active' => ':status'));
                   
    //    $initQuery = $initQuery->logicEx('AND MSBIH.material_delivery_id IS NOT NULL');
        // $initQuery = $initQuery->logicEx('AND WI.created_at  >= :date_from');
        // $initQuery = $initQuery->logicEx('AND WI.created_at  <= :date_to');

                          
        return $initQuery;  
    }

    ////////////////////////////////// UNIT QUERIES /////////////////////////////
    public function selectMatUnit()
    {
        $fields = array(
            'DISTINCT MSBS.unit as unit'
        );

        $join = array(
            'material_specification_brands MSB'          => 'MSBS.material_specification_brand_id = MSB.id',
            'material_specifications MS'                 => 'MS.id = MSB.material_specification_id'
        );

        $initQuery = $this->select($fields)
                     ->from('msb_suppliers MSBS')
                     ->Join($join)
                     ->where(array('MSBS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND MS.code  = :mat_code');


        return $initQuery;  
    }       


    public function selectUnitRQ()
    {
        $fields = array(
            'DISTINCT MSBI.unit'
        );

        // $join = array(
        //     'material_specifications MS'                 => 'MS.material_id = M.id',
        //     'purchase_requisition_descriptions PRSD'     => 'PRSD.item_spec_id = MS.id', 
        //     'purchase_requisitions PRS'                  => 'PRSD.purchase_requisition_id = PRS.id',
        //     'request_quotation_descriptions RQD'         => 'RQD.purchase_requisition_description_id = PRSD.id',
        //     'request_quotation_materials RQM'            => 'RQM.id = RQD.rfq_material_id',
        //     'request_quotations RQ'                      => 'RQ.rfq_material_id = RQM.id',
        //     'suppliers S'                                => 'RQ.supplier_id = S.id'
        // );

        $join = array(
            'material_specifications MS'                 => 'MS.material_id = M.id',
            'material_specification_brands MSB'          => 'MSB.material_specification_id = MS.id', 
            'msb_inventories MSBI'                       => 'MSBI.material_specification_brand_id = MSB.id',
            'msb_inventory_histories MSBIH'              => 'MSBIH.msb_inventory_id = MSBI.id',
        );

        $initQuery = $this->select($fields)
                     ->from('materials M')
                     ->leftJoin($join)
                     ->where(array('MSBI.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc');
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');  
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  >= :date_from');   /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  <= :date_to');     /// TEMPORARY
        $initQuery = $initQuery->logicEx("AND  MSBI.unit <> '' ");     /// TEMPORARY

        /////////  REMAKRS /////////////
        // Date From and Date To must be based from DATE DELIVERED AT JOBSITE
        //                  



        return $initQuery;  
    }      

    public function selectUnitMSB()
    {
        $fields = array(
            'MSBI.unit'
        );

        $join = array(
            'msb_inventory_histories MSBIH'          => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_deliveries MD'                 => 'MD.id = MSBIH.material_delivery_id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('msb_inventories MSBI')
                     ->join($join)
                     ->where(array('MSBIH.is_active' => ':status'));
                   
      $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc');
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');  
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  >= :date_from');
        $initQuery = $initQuery->logicEx('AND MSBIH.created_at  <= :date_to');

        /////////  REMAKRS /////////////
        // Date From and Date To must be based from DATE DELIVERED AT JOBSITE
        //                  



        return $initQuery;  
    }

    public function selectUnitPRSD()
    {
        $fields = array(
            'PRSD.unit_measurement'
        );
        
        $join = array(
            'withdrawals W'                          => 'WI.withdrawal_id = W.id',
            'msb_inventory_histories MSBIH'          => 'MSBIH.withdrawal_item_id = WI.id',
            'msb_inventories MSBI'                   => 'MSBIH.msb_inventory_id = MSBI.id',
            'material_specification_brands MSB'      => 'MSBI.material_specification_brand_id = MSB.id',
            'purchase_requisition_descriptions PRSD' => 'MSBIH.purchase_requisition_description_id = PRSD.id',
            'material_specifications MS'             => 'PRSD.item_spec_id = MS.id',
            'materials M'                            => 'MS.material_id = M.id',
            'purchase_requisitions PRS'              => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'     => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'        => 'RQD.rfq_material_id =  RQM.id',
            'request_quotations RQ'                  => 'RQ.rfq_material_id = RQM.id',
            'aob_supply_evaluations AOBSE'           => 'AOBSE.rfq_id = RQ.id',
            'purchase_orders PO'                     => 'AOBSE.purchase_order_id = PO.id',
            'projects P'                             => 'PRS.project_id = P.id',
        );  

        $initQuery = $this->select($fields)
                     ->from('withdrawal_items WI')
                     ->join($join)
                     ->where(array('WI.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc');
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');  
        $initQuery = $initQuery->logicEx('AND PRSD.created_at  >= :date_from');   /// TEMPORARY
        $initQuery = $initQuery->logicEx('AND PRSD.created_at  <= :date_to');     /// TEMPORARY
        // $initQuery = $initQuery->logicEx("AND  MSBI.unit <> '' ");     /// TEMPORARY

        /////////  REMAKRS /////////////
        // Date From and Date To must be based from DATE DELIVERED AT JOBSITE
        //  
        return $initQuery;  
    }

    public function selectUnitMPM()
    {
        $fields = array(
            'DISTINCT RQM.unit'
        );

        $join = array(
            'material_specifications MS'                 => 'MS.material_id = M.id',
            'purchase_requisition_descriptions PRSD'     => 'PRSD.item_spec_id = MS.id', 
            'purchase_requisitions PRS'                  => 'PRSD.purchase_requisition_id = PRS.id',
            'request_quotation_descriptions RQD'         => 'RQD.purchase_requisition_description_id = PRSD.id',
            'request_quotation_materials RQM'            => 'RQM.id = RQD.rfq_material_id',
            'request_quotations RQ'                      => 'RQ.rfq_material_id = RQM.id',
            'suppliers S'                                => 'RQ.supplier_id = S.id'
        );
            
        $initQuery = $this->select($fields)
                     ->from('materials M')
                     ->leftJoin($join)
                     ->where(array('PRS.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND M.name  = :mat_desc');
        $initQuery = $initQuery->logicEx('AND MS.specs  = :mat_spec');  
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  >= :date_from');
        $initQuery = $initQuery->logicEx('AND RQ.validity_of_offer  <= :date_to');

                          
        return $initQuery;  
    }



    public function selectHead()
    {
        $fields = array(
            'u.id as id',
            'p.name as position',
            'd.name as department',
            'concat(pi.lname, ", ", pi.mname, " ",  pi.fname) as head_name'
        );

        $join = array(
            'employment_informations ei'    => 'ei.personal_information_id = pi.id',
            'positions p'                   => 'p.id = ei.position_id',
            'departments d'                 => 'p.department_id = d.id',
            'users u'                       => 'u.personal_information_id = pi.id'
            
        );
            
        $initQuery = $this->select($fields)
                     ->from('personal_informations pi')
                     ->leftJoin($join)
                     ->where(array('pi.is_active' => ':status'));

        $initQuery = $initQuery->logicEx('AND p.is_signatory  = :is_signatory');

                          
        return $initQuery;  
    }




    public function insertNewReport($data = array())
    {
        $initQuery = $this->insert('prs_report', $data);
        return $initQuery;
    }

    public function updateReport($id = '', $data = array())
    {
        $initQuery = $this->update('prs_report', $id, $data);
        return $initQuery;
    }
  }