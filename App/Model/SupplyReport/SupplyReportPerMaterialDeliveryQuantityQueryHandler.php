<?php 
    namespace App\Model\SupplyReport;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class SupplyReportPerMaterialDeliveryQuantityQueryHandler extends QueryHandler{

        public function selectPRS($prs_id = false, $project = false, $department = false, $request_type = false, $date_from = false, $date_to = false)
        {
          
            $fields     = array(
                'PR.id',
                'PR.project_id',
                'PR.department_id',
                'PR.user_id',
                'PR.category',
                'PR.request_type_id',
                'PR.work_item_id',
                'PR.ps_swi_direct_id',
                'PR.p_wi_indirect_id',
                'PR.cost_code',
                'PR.prs_no',
                'PR.date_requested',
                'PR.activity_description',
                'PR.date_needed',
                'PR.signatories',
                'PR.prev_id',
                'PR.status',
                'PR.r_status',
                'PR.s_status',
                'PR.assign_to',
                'PR.for_cancelation',
                'PR.head_id',
                'PR.remarks',
                'PR.is_active',
                'PR.created_by',
                'PR.updated_by',
                'PR.created_at',
                'PR.updated_at',
            );
            $initQuery  = $this->select($fields)
                               ->from('purchase_requisitions PR')
                               ->where(array('PR.is_active' => ':is_active'));
    
            $initQuery = ($prs_id)       ? $initQuery->andWhere(array('PR.id' => ':prs_id'))                    : $initQuery; 
            $initQuery = ($project)      ? $initQuery->andWhereNull(array('PR.department_id'))                  : $initQuery;
            $initQuery = ($department)   ? $initQuery->andWhereNull(array('PR.project_id'))                     : $initQuery;   
            $initQuery = ($date_from)    ? $initQuery->logicEx('AND PR.created_at  >= :date_from')              : $initQuery;
            $initQuery = ($date_to)      ? $initQuery->logicEx('AND PR.created_at  <= :date_to')                : $initQuery;
            $initQuery = ($request_type) ? $initQuery->andWhere(array('PR.request_type_id' => ':request_type')) : $initQuery; 
            $initQuery =  $initQuery->logicEx('AND PR.status  >= :status');                        

            return $initQuery;
        }

        public function selectPRSD($prdId = false, $prsId = false, $request_table = '', $dateFrom = false, $dateTo = false, $catId = false, $specsId = false, $unit = false, $charging = false, $requestor = false, $pm = false, $prdstat = false)
        {
            $fields = array(
                'PRD.id',
                'PRD.purchase_requisition_id',
                'PRD.material_id',
                'PRD.material_specification_id',
                'PRD.pm_id',
                'PRD.quantity',
                'PRD.unit_measurement',
                'PRD.item_spec_id',
                'IF(PRD.category = 1, "direct", "indirect") as category',
                'PRD.wi_category',
                'PRD.work_item_id',
                'PRD.work_volume',
                'PRD.work_volume_unit',
                'PRD.wbs',
                'PRD.account_id',
                'PRD.signatories',
                'PRD.remarks',
                'PRD.status',
                'PRD.is_active as PRD_is_active',
                'PRD.created_at',
                'PRD.created_by',
                'PRD.updated_at',
                'PRD.updated_by',
                
                'PR.id as PRS_id',
                'PR.project_id',
                'PR.department_id',
                'PR.user_id',
                'PR.category as PRS_category',
                'PR.request_type_id',
                'PR.prs_no',
                'PR.status as PRS_status',
                'PR.assign_to',
                'PR.for_cancelation',
                'PR.head_id',
                'PR.is_active as PR_is_active',

                'PJ.project_manager'


            );

            
            $whereNotInCondition = [
                '25', // 16026
                '26', // 18029
                '27', // 18080
                '24', // 18SG-013
                '9',  // 19001
                '10', // 19002
                '11', // 19003
                '12', // 19004
                '28', // 19005
                '29', // 19006
                '30', // 19007
                '31', // 19008
                '32', // 19009
                '33', // 19010
                '34', // 19011
                '35', // 19012
                '38', // 19013
                '39', // 19015
                '1',  // 19SCDC001
                '7',  // Y03-001
                '4',  // TC-01126
                '2',  // TC-01147
                '3',  // TC01089
                '36', // 19SG-001
                '37', // 19SG-002
                '23', // 19SG-003
                '69', // 19SG-004
            ];

            $join = array(
                'purchase_requisitions PR' => 'PR.id = PRD.purchase_requisition_id',
                'projects PJ'              => 'PJ.id = PR.project_id',
                'material_specifications MS' => 'MS.id = PRD.item_spec_id'
            );
            
            //PRS Descriptions of PRS 
            $initQuery = $this->select($fields)
                               ->from($request_table . ' PRD')
                               ->join($join)
                               ->where(array('PRD.is_active' => ':is_active'))
                               ->andWhereNotIn('PJ.id', $whereNotInCondition);

           
            $initQuery =  $initQuery->andWhere(array('PR.is_active'   => ':is_active')); 
            // $initQuery = ($prsId)              ? $initQuery->andWhere(array('PR.is_active'   => ':is_active'))                               : $initQuery; 
            $initQuery = ($prdId)              ? $initQuery->andWhere(array('PRD.id'   => ':prd_id'))       : $initQuery; 
            $initQuery = ($prsId)              ? $initQuery->andWhere(array('PRD.purchase_requisition_id'   => ':prs_id'))       : $initQuery; 
            $initQuery = ($specsId)            ? $initQuery->andWhere(array('PRD.item_spec_id' => ':material_specification_id')) : $initQuery; 
            $initQuery = ($unit)               ? $initQuery->andWhere(array('PRD.unit_measurement'          => ':unit'))         : $initQuery; 
            $initQuery = ($dateFrom)           ? $initQuery->logicEx('AND PRD.created_at  >= :date_from')                        : $initQuery;
            $initQuery = ($dateTo)             ? $initQuery->logicEx('AND PRD.created_at  <= :date_to')                          : $initQuery;
            $initQuery = ($catId)              ? $initQuery->andWhere(array('MS.category_id' => ':material_category_id'))        : $initQuery; 
            $initQuery = ($charging)           ? $initQuery->andWhere(array('PR.project_id' => ':charging')) : $initQuery; 
            $initQuery = ($requestor)          ? $initQuery->andWhere(array('PRD.created_by' => ':requestor')) : $initQuery; 
            $initQuery = ($pm)                 ? $initQuery->andWhere(array('PJ.project_manager' => ':pm')) : $initQuery; 
            $initQuery = ($prdstat)            ? $initQuery->logicEx($prdstat)     : $initQuery; 
            $initQuery = $initQuery->logicEx('AND PRD.status  >= :status');
            $initQuery = $initQuery->logicEx('AND PR.for_cancelation IS NULL');
            // $initQuery = $initQuery->andWhere(array('PR.for_cancelation'   => 'IS NULL')); 
            // fn_print_die($initQuery);
            return $initQuery;
        }

        public function selectPRD_Del_Seq($id = false, $prdId = false , $date_from = false, $date_to = false)
        {
          
            $fields     = array(
                'PRDDS.id',
                'PRDDS.purchase_requisition_description_id',
                'PRDDS.seq_no',
                'PRDDS.delivery_date',
                'PRDDS.quantity',
                'PRDDS.is_consumed',
            );
            $initQuery  = $this->select($fields)
                               ->from('prd_delivery_sequences PRDDS')
                               ->where(array('PRDDS.is_active' => ':is_active'));
    
            // $initQuery = ($prs_id)       ? $initQuery->andWhere(array('PR.id' => ':prs_id'))                    : $initQuery; 
            // $initQuery = ($project)      ? $initQuery->andWhereNull(array('PR.department_id'))                  : $initQuery;
            // $initQuery = ($department)   ? $initQuery->andWhereNull(array('PR.project_id'))                     : $initQuery;   
            $initQuery = ($id)           ? $initQuery->andWhere(array('PRDDS.id' => ':id'))                                      : $initQuery; 
            $initQuery = ($prdId)        ? $initQuery->andWhere(array('PRDDS.purchase_requisition_description_id' => ':prd_id')) : $initQuery; 
            $initQuery = ($date_from)    ? $initQuery->logicEx('AND PRDDS.delivery_date  >= :date_from')                            : $initQuery;
            $initQuery = ($date_to)      ? $initQuery->logicEx('AND PRDDS.delivery_date  <= :date_to')                              : $initQuery;
            $initQuery =  $initQuery->logicEx('ORDER BY PRDDS.delivery_date ASC');
           
            // $initQuery = ($request_type) ? $initQuery->andWhere(array('PR.request_type_id' => ':request_type'))               : $initQuery; 
            // $initQuery =  $initQuery->logicEx('AND PR.status  >= :status');                        

            return $initQuery;
        }

        
        public function selectMatCategory()
        {
            $fields     = array(
                'MC.id              as m_material_category_id',
                'MC.name            as m_material_category_name',
                'MC.original_code   as m_material_category_original_code'
            );


            $initQuery = $this->select($fields)
                               ->from('material_categories MC')
                               ->where(array('MC.is_active' => ':is_active'));

            $initQuery = $initQuery->andWhere(array('MC.id' => ':material_cat_id')); 
    
            return $initQuery;
        }


        public function selectMaterial()
        {
            $fields     = array(
                'M.id    as m_material_id',
                'M.name  as m_material_name',
                'MC.id   as m_material_category_id',
                'MC.name as m_material_category_name'
            );

            $join = array(
                'material_categories MC' => 'MC.id = M.material_category_id',
             );

            $initQuery = $this->select($fields)
                               ->from('materials M')
                               ->join($join)    
                               ->where(array('M.is_active' => ':is_active'));

            $initQuery = $initQuery->andWhere(array('M.id' => ':material_id')); 
    
            return $initQuery;
        }

        public function selectMaterialSpecification()
        {   
            $fields     = array(
                'MS.id          as ms_material_specification_id',
                'MS.category_id as ms_category_id',
                'MS.material_id as ms_material_id',
                'MS.code        as ms_material_specification_code',
                'MS.specs       as ms_material_specification',
            );

          

            $initQuery = $this->select($fields)
                               ->from('material_specifications MS')
                            //    ->join($join)    
                               ->where(array('MS.is_active' => ':is_active'));

            $initQuery = $initQuery->andWhere(array('MS.id' => ':material_specification_id')); 
    
            return $initQuery;
        }

        public function selectMaterialUnit($mat_unit)
        {
            $fields     = array(
                'MU.id              as material_unit_id',
                'MU.code            as material_unit_code',
                'MU.unit            as material_unit'
            );


            $initQuery = $this->select($fields)
                               ->from('material_units MU')
                               ->where(array('MU.is_active' => ':is_active'));

            $initQuery = ($mat_unit) ?  $initQuery->andWhere(array('MU.unit' => ':material_unit')) : $initQuery; 
    
            return $initQuery;
        }

        public function selectCharging($project = false, $department = false)
        {   
            $fields     = array(
                'PJ.id',
                'PJ.project_manager',
                'PJ.project_code',
                'PJ.name',
                'PJ.location',
                'PJ.contract_days',
                'PJ.longitude',
                'PJ.latitude',
                'PJ.date_started',
                'PJ.date_finished',
                'PJ.is_on_going',
                'PJ.status',
                'PJ.is_revision',
                'PJ.updated_at',
                'PJ.updated_by',
            );

            $initQuery = $this->select($fields)
                               ->from('projects PJ')
                               ->where(array('PJ.is_active' => ':is_active'));

            $initQuery = $initQuery->andWhere(array('PJ.id' => ':charging_id')); 
    
            return $initQuery;
        }

        public function selectPsdPwiMaterials($category)
        {   
            $psdfield = array(
                'PSDM.id                as PSDM_id              ',
                'PSDM.ps_swi_direct_id  as PSDM_ps_swi_direct_id',
                'PSDM.msb_supplier_id   as PSDM_msb_supplier_id ',
                'PSDM.total_Set         as PSDM_total_Set       ',
                'PSDM.total_quantity    as PSDM_total_quantity  ',
                'PSDM.unit_cost         as PSDM_unit_cost       ',
                'PSDM.total_cost        as PSDM_total_cost      ',
            );

            $pwifield = array(
                'PWI.id                as PWI_id              ',
                'IF(PWI.p_wi_indirect_id, "indirect", "direct_indirect") as PWI_indirect_category',
                'PWI.p_wi_indirect_id  as PWI_p_wi_indirect_id',
                'PWI.ps_swi_direct_id  as PWI_ps_swi_direct_id',
                'PWI.msb_supplier_id   as PWI_msb_supplier_id ',
                'PWI.total_Set         as PWI_total_Set       ',
                'PWI.total_quantity    as PWI_total_quantity  ',
                'PWI.unit_cost         as PWI_unit_cost       ',
                'PWI.total_cost        as PWI_total_cost      ',
            );

            $fields           = ($category == 'direct')? $psdfield : $pwifield ;
            $table_identifier = ($category == 'direct')? 'PSDM' : 'PWI' ;
            $table            = ($category == 'direct')? 'psd_materials': 'pwi_materials' ;

            $initQuery = $this->select($fields)
                               ->from($table . ' ' . $table_identifier)
                               ->where(array($table_identifier . '.is_active' => ':is_active'));

            $initQuery = $initQuery->andWhere(array($table_identifier . '.id' => ':pm_id')); 
    
            return $initQuery;
        }

            //// SELECT WORK ITEM
        public function selectWorkItem($work_item_id = false)
        {
           
            $fields = array(
                'WI.id      as work_item_id',
                'WI.wbs     as work_item_wbs',
                'WI.item_no as work_item_item_no',
                'WI.name    as work_item_name',
                'WIC.part   as wi_cat_part',
                'WIC.name   as wi_cat_name'
            );
        
            $join = array(
                'work_item_categories WIC'              => 'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                               ->from('work_items WI')
                               ->join($join)
                               ->where(array('WI.is_active' => ':is_active'));

            $initQuery = ($work_item_id)? $initQuery->andWhere(array('WI.id' => ':wi_id')) : $initQuery; 

            return $initQuery;
        }


        public function selectWorkItemDirectOnly($rev_is_0)
        {
            $fields = array(
                'PSSWID.id           as PSSWID_id',
                'PSSWID.quantities   as PSSWID_work_volume',
                'PSSWID.reference_id as PSSWID_reference_id',
                'PSSWID.is_active    as PSSWID_is_active',
                'PSSWID.revision_no    as PSSWID_revision_no',
            );

            $col_identifier = ($rev_is_0 == true) ? 'PSSWID.reference_id' : 'PSSWID.id' ;

            $initQuery = $this->select($fields)
                               ->from('ps_swi_directs PSSWID')
                               ->where(array($col_identifier => ':p_wi_p_swi_id'));

            // $initQuery = $initQuery->andWhere(array('PSSWID.id' => ':p_wi_p_swi_id')); 

            return $initQuery;
        }

        public function selectWorkItemIndirectOnly($rev_is_0)
        {
            $fields = array(
                'PWII.id            as PWII_id',
                'PWII.quantities    as PWII_work_volume',
                'PWII.reference_id  as PWII_reference_id',
                'PWII.is_active     as PWII_is_active',
                'PWII.revision_no   as PWII_revision_no',
            );

            $col_identifier = ($rev_is_0 == true) ? 'PWII.reference_id' : 'PWII.id' ;

            $initQuery = $this->select($fields)
                               ->from('p_wi_indirects PWII')
                               ->where(array($col_identifier => ':p_wi_p_swi_id'));

            // $initQuery = $initQuery->andWhere(array('PWII.id' => ':p_wi_p_swi_id')); 
    
            return $initQuery;
        }

        public function selectWorkItemIndirectDirectOnly($rev_is_0)
        {   
            $fields = array(
                'PSSWID.id           as PSSWID_id',
                'PSSWID.quantities   as PSSWID_work_volume',
                'PSSWID.reference_id as PSSWID_reference_id',
                'PSSWID.is_active    as PSSWID_is_active',
                'PSSWID.revision_no  as PSSWID_revision_no',
            );

            $col_identifier = ($rev_is_0 == true) ? 'PSSWID.reference_id' : 'PSSWID.id' ;

            $initQuery = $this->select($fields)
                               ->from('ps_swi_directs PSSWID')
                               ->where(array($col_identifier => ':p_wi_p_swi_id'));

            // $initQuery = $initQuery->andWhere(array('PSSWID.id' => ':p_wi_p_swi_id')); 
    
            return $initQuery;
        }

        public function selectWorkItemDirect($rev_is_0)
        {   
            $fields = array(
                'PSSWID.id as PSSWID_id',
                'PSSWID.quantities as PSSWID_work_volume',
                
                'SWWIS.id as SWWIS_id',
                'SWWIS.wbs as SWWIS_wbs',
                'SWWIS.unit as SWWIS_work_volume_unit',

                'WI.id      as WI_id',
                'WI.item_no as WI_item_no',
                'WI.name    as WI_name',

                'WIC.id    as WIC_id',
                'WIC.part  as WIC_part',
                'WIC.name  as WIC_name'
            );

            $join = array(
                'sw_wis SWWIS'              => 'SWWIS.id = PSSWID.sw_wi_id',
                'work_items WI'             => 'WI.id = SWWIS.work_item_id',
                'work_item_categories WIC'  => 'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                               ->from('ps_swi_directs PSSWID')
                               ->join($join)
                               ->where(array('PSSWID.id' => ':p_wi_p_swi_id'));

            $initQuery = ($rev_is_0)? $initQuery->andWhere(array('PSSWID.is_active' => ':is_active')) : $initQuery; 
            //                    ->where(array('PSSWID.is_active' => ':is_active'));

            // $initQuery = $initQuery->andWhere(array('PSSWID.id' => ':p_wi_p_swi_id')); 
    
            return $initQuery;
        }



        public function selectWorkItemIndirect($rev_is_0)
        {
            $fields = array(
                'PWII.id          as PWII_id',
                'PWII.quantities  as PWII_work_volume',

                'WI.id      as WI_id',
                'WI.wbs     as WI_wbs',
                'WI.item_no as WI_item_no',
                'WI.name    as WI_name',
                'WI.unit    as WI_wv_unit',

                'WIC.id    as WIC_id',
                'WIC.part  as WIC_part',
                'WIC.name  as WIC_name'
                
            );

            $join = array(
                'work_items WI'             => 'WI.id = PWII.work_item_id',
                'work_item_categories WIC'  => 'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                               ->from('p_wi_indirects PWII')
                               ->join($join)
                               ->where(array('PWII.id' => ':p_wi_p_swi_id'));

            $initQuery = ($rev_is_0)? $initQuery->andWhere(array('PWII.is_active' => ':is_active')) : $initQuery; 
            //                    ->where(array('PWII.is_active' => ':is_active'));

            // $initQuery = $initQuery->andWhere(array('PWII.id' => ':p_wi_p_swi_id')); 
            
    
            return $initQuery;
        }

        public function selectWorkItemIndirectDirect($rev_is_0)
        {   
            $fields = array(
                'PSSWID.id         as PSSWID_id',
                'PSSWID.quantities as PSSWID_work_volume',
                'PSSWID.id         as PSSWID_id',
                
                // 'SWWIS.id as SWWIS_id',
                // 'SWWIS.wbs as SWWIS_wbs',
                // 'SWWIS.unit as SWWIS_work_volume_unit',

                'WI.id      as WI_id',
                'WI.wbs     as WI_wbs',
                'WI.item_no as WI_item_no',
                'WI.name    as WI_name',
                'WI.unit    as WI_wv_unit',

                'WIC.id    as WIC_id',
                'WIC.part  as WIC_part',
                'WIC.name  as WIC_name'
                
            );

            $join = array(
                'sw_wis SWWIS'              => 'SWWIS.id = PSSWID.sw_wi_id',
                'work_items WI'             => 'WI.id = SWWIS.work_item_id',
                'work_item_categories WIC'  => 'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                               ->from('ps_swi_directs PSSWID')
                               ->join($join)
                                ->where(array('PSSWID.id' => ':p_wi_p_swi_id'));

            $initQuery = ($rev_is_0)? $initQuery->andWhere(array('PSSWID.is_active' => ':is_active')) : $initQuery; 
            //                    ->where(array('PSSWID.is_active' => ':is_active'));

            // $initQuery = $initQuery->andWhere(array('PSSWID.id' => ':p_wi_p_swi_id')); 
    
            return $initQuery;
        }

        /**
         * `selectUsers` Query string that will select users.
         * @param boolean $id
         * @return string
         */
        public function selectUser($id = false, $userPerso = false)
        {
            $fields = array(
                'U.id as user_id',
                'PI.id as personal_information_id',
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'P.id as position_id',
                'P.name as position_name',
                'D.id as department_id',
                'D.name as department_name',
                'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(PI.mname, ""), NULLIF(PI.lname, "")) as full_name'
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
                                ->where(array('U.is_active' => ':is_active', 'PI.is_active' => ':is_active'));

            $initQuery = ($userPerso == true) ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;
            $initQuery = ($userPerso == false) ? $initQuery->andWhere(array('U.personal_information_id' => ':user_id')) : $initQuery;

            return $initQuery;
        }



  }