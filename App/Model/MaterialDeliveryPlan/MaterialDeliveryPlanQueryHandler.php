<?php 
    namespace App\Model\MaterialDeliveryPlan;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MaterialDeliveryPlanQueryHandler extends QueryHandler { 
        
        public function selectProjects($id = false)
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name as project_title',
                'P.location as project_location',
            );

            $joins = array(
                'transactions T'    =>   'T.id = P.transaction_id'
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              // ->join($joins)
                              // ->where(array('P.is_active' => ':is_active', 'T.status' => ':status'));
                              ->where(array('P.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrsNumbers()
        {
            $fields = array(
                'PR.id',
                'PR.prs_no',
                'PR.status',
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->where(array('PR.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectRequestedMaterials($filter = false, $limit = '')
        {
            $fields = array(
                'PRD.id',
                'PRD.item_spec_id',
                'MSB.id as msb_id',
                'MS.specs as material_specs',
                'M.name as material_description',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = MSB.id LIMIT 1) as material_code'
            );

            $joins = array(
                'material_specifications MS'            =>      'MS.id = PRD.item_spec_id',
                'material_specification_brands MSB'     =>      'MSB.material_specification_id = MS.id',
                'materials M'                           =>      'M.id = MS.material_id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->leftJoin($joins)
                              ->where(array('PRD.is_active' => ':is_active'))
                              ->groupBy('PRD.item_spec_id');

            // $initQuery = ($item_spec_id) ? $initQuery->andWhere(array('PRD.item_spec_id' => ':item_spec_id')) : $initQuery;
            $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = MSB.id LIMIT 1)' => ':filter','M.name' => ':filter', 'MS.specs' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 20') : $initQuery;

            return $initQuery;
        }

        public function selectMaterialDeliveryPlan($project_id = false, $prs_no = false, $item_spec_id = false, $delivery_date = false, $total = false)
        {
            $fields = array(
                'PRD.id',
                'PRD.purchase_requisition_id',
                'PRD.work_item_id',
                'PRD.item_spec_id',
                'PRD.quantity',
                'PRD.unit_measurement as unit',
                'PR.project_id',
                'PR.prs_no',
                'PRDDS.purchase_requisition_description_id',
                'DATE_FORMAT(PRDDS.delivery_date, "%d-%b-%Y") as delivery_date',
                'WI.item_no',
                'WI.name as item_description',
                '(SELECT project_code FROM projects WHERE id = PR.project_id) as project_code',
                'MS.specs as material_specs',
                'M.name as material_description',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = MSB.id LIMIT 1) as material_code'
            );

            $fields = ($total) ? array('COUNT(PRD.id) as total') : $fields;
            
            $leftJoins = array(
                'purchase_requisitions PR'      =>   'PR.id = PRD.purchase_requisition_id',
                'prd_delivery_sequences PRDDS'  =>   'PRDDS.purchase_requisition_description_id = PRD.id',
                'work_items WI'                 =>   'WI.id = PRD.work_item_id',
                'material_specifications MS'    =>   'MS.id = PRD.item_spec_id',
                'material_specification_brands MSB' => 'MSB.material_specification_id = MS.id',
                'materials M'                   =>   'M.id = MS.material_id'
            );

            // $orWhereCondition = array(
            //     'PR.prs_no'  => ':filter_val'
            // );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->leftJoin($leftJoins)
                              ->where(array('PRD.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'PRDDS.is_active' => ':is_active', 'PR.status' => ':status'));
                            //   ->logicEx('AND')
                            //   ->orWhereLike($orWhereCondition);

            $initQuery = ($project_id)     ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($prs_no)         ? $initQuery->andWhere(array('PR.prs_no' => ':prs_no')) : $initQuery;
            $initQuery = ($item_spec_id)         ? $initQuery->andWhere(array('PRD.item_spec_id' => ':item_spec_id')) : $initQuery;
            $initQuery = ($delivery_date)  ? $initQuery->andWhereRange('PRDDS.delivery_date', array(':fromDate', ':toDate')) : $initQuery;

            return $initQuery;
        }
    }