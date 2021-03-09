<?php 
    namespace App\Model\MaterialProcurementPlan;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MaterialProcurementPlanQueryHandler extends QueryHandler { 

        /**
         * `selectPurchaseRequisitions` Query string that will fetch prs from table `purchase_requisitions`.
         * @return string
         */
        public function selectPurchaseRequisitions($id = false)
        {
            $fields = [
                'PR.id',
                'IF(PR.project_id IS NULL, D.charging, P.project_code) AS charging',
                'PR.prs_no',
                'WI.item_no',
                'MS.code',
                'CONCAT(M.name, ", ", MS.specs) as material_description',
                'PRDDS.quantity as ds_quantity',
                'PRD.unit_measurement',
                'delivery_date',
                // 'DATE_FORMAT(PRDDS.delivery_date, "%b %d, %Y") as delivery_date',
                'PR.status',
            ];

            $joins = [
                'purchase_requisition_descriptions PRD' => 'PR.id = PRD.purchase_requisition_id',
                'prd_delivery_sequences PRDDS' 			    => 'PRD.id = PRDDS.purchase_requisition_description_id',
                'material_specifications MS' 			      => 'MS.id = PRD.item_spec_id',
                'materials M'							              => 'M.id = MS.material_id',
                'work_items WI' 						            => 'PRD.work_item_id = WI.id'
            ];

            $leftJoins = [
                'projects P'    => 'P.id = PR.project_id',
                'departments D' => 'PR.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->leftJoin($leftJoins)
                              ->join($joins)
                              ->where(['PR.is_active' => ':is_active', 'PRD.is_active' => ':is_active', 'PRDDS.is_active' => ':is_active', 'PR.request_type_id' => ':request_type_id', 'MS.is_active' => ':is_active'])
                              ->andWhereNotEqual(['PRD.status' => '0'])
                              ->logicEx('AND PR.status BETWEEN 2 AND 9')
                              ->andWhereNull(['PR.for_cancelation'])
                              ->andWhereNotNull(['PR.signatories']);

            $initQuery = ($id) ? $initQuery->andWhere(['PR.id' => ':id']) : $initQuery;

            $initQuery = $initQuery->orderBy('M.name', 'ASC');

            return $initQuery;
        }
    }