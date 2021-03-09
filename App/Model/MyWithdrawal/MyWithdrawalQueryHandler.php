<?php
    namespace App\Model\MyWithdrawal;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class MyWithdrawalQueryHandler extends QueryHandler {

        public function selectWithdrawals($id = false, $status, $total = false)
        {
            $fields = [
                'W.id',
                'W.ws_no',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y") as date_requested',
                'W.status',
                'W.charge_to as int_charge_to',
                'W.project_id',
                'W.department_id',
                'W.is_standalone',
                'W.is_cancelled',
                'W.confirmed_by',
                'W.approved_by',
                'DATE_FORMAT(W.approved_at, "%M %d, %Y %r") as approved_at',
                'W.created_by',
            ];

            $fields = ($total) ? array('COUNT(W.id) as total') : $fields;

            $orWhereCondition = array(
                'W.ws_no'                                => ':filter_val',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y")'    => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawals W')
                              ->where(['W.is_active' => ':is_active', 'W.confirmed_by' => ':requestor'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['W.id' => ':id']) : $initQuery;

            switch ($status) {

                case 'FC':
                    $initQuery->andWhere(['W.status' => '3']);
                    break;

                case 'AT':
                default:
                    // select *
                    break;
            }


            return $initQuery;
        }

        public function selectWithdrawalItems($withdrawal_id = false, $purchase_requisition_id = false)
        {
            $fields = [
                'WI.id',
                'WI.withdrawal_id',
                'WI.purchase_requisition_description_id',
                'WI.material_specification_id',
                'WI.msb_inventory_id',
                'WI.actual_sequence_id',
                'WI.warehouse_id',
                'WI.total_withdrawn',
                'WI.withdrawn_quantity',
                'WI.disapproved_qty',
                'PRD.purchase_requisition_id',
                'PRD.item_spec_id',
                'PRD.pm_id as p_material_id',
                'PRD.unit_measurement as unit',
                'PRD.quantity as total_qty',
                'PRD.category',
                'PRD.work_item_id',
                'PR.prs_no',
                'CONCAT(Wa.name, " - ", Wa.address) as warehouse'
            ];

            $joins = [
                'purchase_requisition_descriptions PRD' =>  'PRD.id = WI.purchase_requisition_description_id',
                'purchase_requisitions PR'              =>  'PR.id = PRD.purchase_requisition_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = WI.msb_inventory_id',
                'warehouses Wa'                         =>  'Wa.id = MSBI.warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->join($joins)
                              ->where(['WI.is_active' => ':is_active']);

            $initQuery = ($withdrawal_id)           ? $initQuery->andWhere(['WI.withdrawal_id' => ':withdrawal_id']) : $initQuery;
            $initQuery = ($purchase_requisition_id) ? $initQuery->andWhere(['PRD.purchase_requisition_id' => ':purchase_requisition_id']) : $initQuery;

            return $initQuery;
        }

        public function selectWithdrawalSignatories($withdrawal_id = false)
        {
            $fields = [
                'WS.id',
                'WS.withdrawal_id',
                'WS.signatory_id',
                'WS.status',
                'WS.w_status',
                'WS.is_approved',
                'WS.remarks',
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_signatories WS')
                              ->where(['WS.is_active' => ':is_active']);

            $initQuery = ($withdrawal_id) ? $initQuery->andWhere(['WS.withdrawal_id' => ':withdrawal_id']) : $initQuery;

            return $initQuery;
        }

        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code as charging_code',
                'P.name as charging_name',
                'P.revision_no',
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.charging as charging_code',
                'D.name as charging_name',
            ];
            
            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['D.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectUserInformation($user_id = false)
        {
            $fields = [
                'U.id as user_id',
                'CONCAT(PI.fname, " ", LEFT(PI.mname,1), ". ", PI.lname) as fullname',
                'P.name as position',
                'D.name as department',
            ];

            $joins = [
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id',
                'departments D'                 =>  'D.id = P.department_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => ':is_active']);

            $initQuery = ($user_id) ? $initQuery->andWhere(['U.id' => ':user_id']) : $initQuery;

            return $initQuery;
        }

        public function selectTotalQtyPerPrdActual($prd_id = false)
        {
            $fields = array(
                'ADS.purchase_requisition_description_id',
                'ADS.segregation_id',
                'SUM(ADS.quantity) as total_qty'
            );

            $initQuery = $this->select($fields)
                              ->from('prd_actual_sequences ADS')
                              ->where(array('ADS.is_active' => ':is_active'));

            $initQuery = ($prd_id) ? $initQuery->andWhere(array('ADS.purchase_requisition_description_id' => ':prd_id')) : $initQuery;

            return $initQuery;
        }

        public function selectLatestTotalWithdrawn()
        {
            $fields = array(
                'WI.id',
                'WI.purchase_requisition_description_id',
                'WI.total_withdrawn',
                'WI.withdrawn_quantity'
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->where(array('WI.is_active' => ':is_active', 'WI.purchase_requisition_description_id' => ':purchase_requisition_description_id'))
                              ->orderBy('WI.qty_updated_at', 'DESC')
                              ->limit(1);

            // $initQuery = ($prdId) ? $initQuery->andWhere(array('WI.purchase_requisition_description_id' => ':purchase_requisition_description_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrdDeliverySequenceSegregations($prd_id = false, $id = false, $date = false)
        {
            $fields = array(
                'DS.id',
                'DS.purchase_requisition_description_id',
                'DS.date',
                'DS.quantity'
            );

            $initQuery = $this->select($fields)
                              ->from('prd_delivery_sequence_segregations DS')
                              ->where(array('DS.is_active' => ':is_active'));

            $initQuery = ($prd_id) ? $initQuery->andWhere(array('DS.purchase_requisition_description_id' => ':prd_id')) : $initQuery;
            $initQuery = ($id)     ? $initQuery->andWhere(array('DS.id' => ':id')) : $initQuery;
            $initQuery = ($date)   ? $initQuery->andWhere(array('DS.date' => ':current_date')) : $initQuery;


            return $initQuery;
        }

        public function selectLatestActualSequencePerPrd()
        {
            $fields = array(
                'ADS.id',
                'ADS.purchase_requisition_description_id',
                'ADS.segregation_id',
                'ADS.seq_no',
                'ADS.quantity',
                'ADS.date_needed',
                'ADS.is_processed',
                'DSS.date'
            );

            $join = array(
                'prd_delivery_sequence_segregations DSS' => 'DSS.id = ADS.segregation_id'
            );

            $initQuery = $this->select($fields)
                              ->from('prd_actual_sequences ADS')
                              ->join($join)
                              ->where(array('ADS.is_active' => ':is_active', 'ADS.purchase_requisition_description_id' => ':prd_id', 'DSS.date' => ':date'))
                              ->orderBy('ADS.seq_no', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectWarehouse( $id = false )
        {
            $fields = array(
                'W.id',
                'W.name',
                'W.address',
            );

            $initQuery = $this->select($fields)
                              ->from('warehouses W')
                              ->where(array('W.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('W.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectMsbSuppliers($msb_id = false, $unit = false)
        {
            $fields = array(
                'MSBS.id',
                'MSBS.material_specification_brand_id',
                'MSBS.code',
                'MSBS.unit'
            );

            $initQuery = $this->select($fields)
                                ->from('msb_suppliers MSBS')
                                ->where(array('MSBS.is_active' => ':is_active'));

                $initQuery = ($msb_id) ? $initQuery->andWhere(array('MSBS.material_specification_brand_id' => ':msb_id')) : $initQuery;
                $initQuery = ($unit)   ? $initQuery->andWhere(array('MSBS.unit' => ':unit')) : $initQuery;

            return $initQuery;
        }

        public function selectMaterialSpecifications($id = false, $material_id = false, $spec_id = false)
        {
            $fields = array(
                'MS.id',
                'MS.material_id',
                'MS.code',
                'MS.specs',
                'M.name as material_name'
            );

            $join = array(
                'materials M'   =>  'M.id = MS.material_id'
            );

            $initQuery = $this->select($fields)
                              ->from('material_specifications MS')
                              ->join($join)
                              ->where(array('MS.is_active' => ':is_active'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;
            $initQuery = ($material_id) ? $initQuery->andWhere(array('MS.material_id' => ':material_id')) : $initQuery;
            $initQuery = ($spec_id)     ? $initQuery->andWhereNot(array('MS.id' => ':spec_id'))  : $initQuery;

            return $initQuery;
        }

        public function selectWithdrawalWarehouseReleases($withdrawal_id = false)
        {
            $fields = array(
                'WR.id',
                'WR.withdrawal_id',
                'WR.warehouse_id',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_releases WR')
                              ->where(array('WR.is_active' => ':is_active'));

            $initQuery = ($withdrawal_id) ? $initQuery->andWhere(array('WR.withdrawal_id' => ':withdrawal_id')) : $initQuery;

            return $initQuery;
        }

        public function selectWithdrawalItemsPerPRD($id = false)
        {
            $fields = array(
                'WI.id',
                'WI.purchase_requisition_description_id',
                'SUM(WI.withdrawn_quantity) as total_withdrawn_quantity'
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->where(array('WI.is_active' => ':is_active', 'WI.purchase_requisition_description_id' => ':purchase_requisition_description_id'));
            
            $initQuery = ($id) ? $initQuery->andWhereNotIn('WI.id', array('WI.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectCurrentTotalWithdrawn()
        {
            $fields = array(
                'WI.id',
                'WI.purchase_requisition_description_id',
                'WI.total_withdrawn',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->where(array('WI.is_active'  => ':is_active', 'WI.purchase_requisition_description_id' => ':purchase_requisition_description_id'))
                              ->orderBy('WI.qty_updated_at', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectLastWithdrawalSlipNumber()
        {
            $fields = array(
                'W.id',
                'W.ws_no',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawals W')
                              ->where(array('W.is_active' => ':is_active'))
                              ->andWhereNotNull(array('W.status'))
                              ->orderBy('W.ws_no', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectLastOfSequenceOrder()
        {
            $fields = array(
                'PR.id',
                'PR.purchase_requisition_description_id',
                'PR.seq_no',
                'PR.quantity',
                'PR.date_needed',
            );

            $initQuery = $this->select($fields)
                              ->from('prd_actual_sequences PR')
                              ->where(array('PR.is_active' => ':is_active', 'PR.purchase_requisition_description_id' => ':prd_id'))
                              ->orderBy('PR.seq_no', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectTotalWithdrawnQuantity()
        {
            $fields = array(
                'WI.id',
                'WI.purchase_requisition_description_id',
                'SUM(WI.withdrawn_quantity) as total_withdrawn_quantity'
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->where(array('WI.is_active' => ':is_active', 'WI.purchase_requisition_description_id' => ':purchase_requisition_description_id'));
            
            return $initQuery;
        }

        public function insertWithdrawal($data = [])
        {
            $initQuery = $this->insert('withdrawals', $data);

            return $initQuery;
        }

        public function insertWithdrawalItems($data = [])
        {
            $initQuery = $this->insert('withdrawal_items', $data);

            return $initQuery;
        }

        public function insertActualDeliverySequence($data = [])
        {
            $initQuery = $this->insert('prd_actual_sequences', $data);

            return $initQuery;
        }

        public function updateWithdrawalItems($id = '', $data = array())
        {
            $initQuery = $this->update('withdrawal_items', $id, $data);

            return $initQuery;
        }

        public function updateActualSequenceQuantity($id = '', $data = array())
        {
            $initQuery = $this->update('prd_actual_sequences', $id, $data);

            return $initQuery;
        }

        public function updateWithdrawals($id = '', $data = array())
        {
            $initQuery = $this->update('withdrawals', $id, $data);

            return $initQuery;
        }

    }
?>