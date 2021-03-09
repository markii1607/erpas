<?php
    namespace App\Model\WithdrawalMonitoring;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class WithdrawalMonitoringQueryHandler extends QueryHandler {

        public function selectWithdrawals($id = false, $status, $total = false)
        {
            $fields = [
                'W.id',
                'W.ws_no',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y") as date_requested',
                'W.status',
                'W.charge_to as int_charge_to',
                'W.charge_to',
                'IF(W.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(W.project_id IS NULL, D.name, P.name) as charging_name',
                'W.project_id',
                'W.department_id',
                'W.is_standalone',
                'W.is_cancelled',
                'W.created_by',
                '"standard" as type'
            ];

            $fields = ($total) ? array('COUNT(W.id) as total') : $fields;

            $orWhereCondition = array(
                'W.ws_no'                                => ':filter_val',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y")'    => ':filter_val',
            );

            $leftjoins = [
                'projects P'        =>  'P.id = W.project_id',
                'departments D'     =>  'D.id = W.department_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawals W')
                              ->leftJoin($leftjoins)
                              ->where(['W.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['W.id' => ':id']) : $initQuery;

            switch ($status) {

                case 'For Editing':
                    $initQuery->andWhere(['W.status' => '1']);
                    break;

                case 'For Approval':
                    $initQuery->andWhere(['W.status' => '2']);
                    break;

                case 'For Confirmation':
                    $initQuery->andWhere(['W.status' => '3']);
                    break;

                case 'Approved':
                    $initQuery->andWhere(['W.status' => '4']);
                    break;

                case 'Disapproved':
                    $initQuery->andWhere(['W.status' => '5']);
                    break;

                case 'Cancelled':
                    $initQuery->andWhere(['W.status' => '6']);
                    break;

                case 'All':
                default:
                    // select *
                    break;
            }


            return $initQuery;
        }

        public function selectSAWithdrawals($status, $total = false)
        {
            $fields = [
                'SW.id',
                'SW.ws_no',
                'DATE_FORMAT(SW.ws_date, "%M %d, %Y") as date_requested',
                'SW.status',
                'SW.project_id',
                'SW.department_id',
                'IF(SW.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(SW.project_id IS NULL, D.name, P.name) as charging_name',
                'SW.approved_by',
                'SW.approver_status',
                'SW.approver_remarks',
                'SW.created_by',
                '"standalone" as type'
            ];

            $fields = ($total) ? array('COUNT(SW.id) as total') : $fields;

            $orWhereCondition = array(
                'SW.ws_no'                                => ':filter_val',
                'DATE_FORMAT(SW.ws_date, "%M %d, %Y")'    => ':filter_val',
            );

            $leftjoins = [
                'projects P'        =>  'P.id = SW.project_id',
                'departments D'     =>  'D.id = SW.department_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawals SW')
                              ->leftJoin($leftjoins)
                              ->where(['SW.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            switch ($status) {

                case 'For Approval':
                    $initQuery->andWhere(['SW.status' => '1']);
                    break;

                case 'Approved':
                    $initQuery->andWhere(['SW.status' => '2']);
                    break;

                case 'Disapproved':
                    $initQuery->andWhere(['SW.status' => '3']);
                    break;

                case 'All':
                default:
                    // select *
                    break;
            }


            return $initQuery;
        }

        public function selectWithdrawalWarehouseReleases($total = false)
        {
            $fields = [
                'WWR.id',
                'WWR.withdrawal_id',
                'WWR.warehouse_id',
                'WWR.issued_by',
                'DATE_FORMAT(WWR.created_at, "%M %d, %Y") as date_released',
                'DATE_FORMAT(WWR.issued_at, "%M %d, %Y %r") as issued_at',
                'WWR.received_by',
                'DATE_FORMAT(WWR.received_at, "%M %d, %Y") as received_at',
                'WWR.receiver_signature',
                'WWR.is_received',
                'W.ws_no',
                'W.approved_by',
                'DATE_FORMAT(W.approved_at, "%M %d, %Y %r") as approved_at',
                'CONCAT(Wa.name, " - ", Wa.address) as warehouse',
                'IF(W.project_id IS NULL, CONCAT(D.charging, " - ", D.name), CONCAT(P.project_code, " - ", P.name)) as charging',
                'IF(W.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(W.project_id IS NULL, "D", "P") as charging_type',
                '"standard" as type'
            ];

            $fields = ($total) ? array('COUNT(WWR.id) as total') : $fields;

            $orWhereCondition = array(
                'W.ws_no'                                => ':filter_val',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y")'    => ':filter_val',
                'Wa.name'                                => ':filter_val',
            );

            $leftjoins = [
                'withdrawals W'     =>  'W.id = WWR.withdrawal_id',
                'projects P'        =>  'P.id = W.project_id',
                'departments D'     =>  'D.id = W.department_id',
                'warehouses Wa'     =>  'Wa.id = WWR.warehouse_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_releases WWR')
                              ->leftJoin($leftjoins)
                              ->where(['WWR.is_active' => ':is_active', 'W.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            return $initQuery;
        }

        public function selectSAWithdrawalWarehouseReleases($total = false)
        {
            $fields = [
                'SWWR.id',
                'SWWR.sa_withdrawal_id',
                'SWWR.warehouse_id',
                'DATE_FORMAT(SWWR.created_at, "%M %d, %Y") as date_released',
                'SWWR.issued_by',
                'DATE_FORMAT(SWWR.issued_at, "%M %d, %Y %r") as issued_at',
                'SWWR.received_by',
                'DATE_FORMAT(SWWR.received_at, "%M %d, %Y") as received_at',
                'SWWR.receiver_signature',
                'SWWR.is_received',
                'SW.ws_no',
                'SW.approved_by',
                'DATE_FORMAT(SW.approved_at, "%M %d, %Y %r") as approved_at',
                'CONCAT(Wa.name, " - ", Wa.address) as warehouse',
                'IF(SW.project_id IS NULL, CONCAT(D.charging, " - ", D.name), CONCAT(P.project_code, " - ", P.name)) as charging',
                'IF(SW.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(SW.project_id IS NULL, "D", "P") as charging_type',
                '"standalone" as type'
            ];

            $fields = ($total) ? array('COUNT(SWWR.id) as total') : $fields;

            $orWhereCondition = array(
                'SW.ws_no'                                => ':filter_val',
                'DATE_FORMAT(SW.ws_date, "%M %d, %Y")'    => ':filter_val',
                'Wa.name'                                 => ':filter_val',
            );

            $leftjoins = [
                'sa_withdrawals SW' =>  'SW.id = SWWR.sa_withdrawal_id',
                'projects P'        =>  'P.id = SW.project_id',
                'departments D'     =>  'D.id = SW.department_id',
                'warehouses Wa'     =>  'Wa.id = SWWR.warehouse_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_warehouse_releases SWWR')
                              ->leftJoin($leftjoins)
                              ->where(['SWWR.is_active' => ':is_active', 'SW.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            return $initQuery;
        }

        public function selectReleasedItems()
        {
            $fields = [
                'WWRI.id',
                'WWRI.withdrawal_warehouse_release_id',
                'WWRI.withdrawal_item_id',
                'WWRI.released_quantity',
                'WWRI.received_quantity',
                'WWRI.unit',
                'WWRI.remarks',
                'CONCAT(MS.code, " - ", MS.specs) as material',
                'PR.prs_no'
            ];

            $leftjoins = [
                'withdrawal_items WI'                   =>  'WI.id = WWRI.withdrawal_item_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = WI.msb_inventory_id',
                'material_specification_brands MSB'     =>  'MSB.id = MSBI.material_specification_brand_id',
                'material_specifications MS'            =>  'MS.id = MSB.material_specification_id',
                'purchase_requisition_descriptions PRD' =>  'PRD.id = WI.purchase_requisition_description_id',
                'purchase_requisitions PR'              =>  'PR.id = PRD.purchase_requisition_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_released_items WWRI')
                              ->leftJoin($leftjoins)
                              ->where(['WWRI.is_active' => ':is_active', 'WWRI.withdrawal_warehouse_release_id' => ':wwr_id']);

            return $initQuery;
        }

        public function selectSAReleasedItems()
        {
            $fields = [
                'SWWRI.id',
                'SWWRI.sa_withdrawal_warehouse_release_id',
                'SWWRI.sa_withdrawal_item_id',
                'SWWRI.released_quantity',
                'SWWRI.received_quantity',
                'SWWRI.unit',
                'SWWRI.remarks',
                'CONCAT(MS.code, " - ", MS.specs) as material',
                'SWI.prs_no'
            ];

            $leftjoins = [
                'sa_withdrawal_items SWI'               =>  'SWI.id = SWWRI.sa_withdrawal_item_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = SWI.msb_inventory_id',
                'material_specification_brands MSB'     =>  'MSB.id = MSBI.material_specification_brand_id',
                'material_specifications MS'            =>  'MS.id = MSB.material_specification_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_warehouse_released_items SWWRI')
                              ->leftJoin($leftjoins)
                              ->where(['SWWRI.is_active' => ':is_active', 'SWWRI.sa_withdrawal_warehouse_release_id' => ':swwr_id']);

            return $initQuery;
        }

        public function selectWithdrawalItems($withdrawal_id = false)
        {
            $fields = [
                'WI.id',
                'WI.withdrawal_id',
                'WI.purchase_requisition_description_id',
                'WI.msb_inventory_id',
                'WI.material_specification_id',
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
                'CONCAT(W.name, " - ", W.address) as warehouse',
                'MSB.id as msb_id'
            ];

            $joins = [
                'purchase_requisition_descriptions PRD' =>  'PRD.id = WI.purchase_requisition_description_id',
                'purchase_requisitions PR'              =>  'PR.id = PRD.purchase_requisition_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = WI.msb_inventory_id',
                'warehouses W'                          =>  'W.id = MSBI.warehouse_id',
                'material_specification_brands MSB'     =>  'MSB.material_specification_id = PRD.item_spec_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->leftJoin($joins)
                              ->where(['WI.is_active' => ':is_active']);

            $initQuery = ($withdrawal_id) ? $initQuery->andWhere(['WI.withdrawal_id' => ':withdrawal_id']) : $initQuery;

            return $initQuery;
        }

        public function selectSAWithdrawalItems($sa_withdrawal_id = false)
        {
            $fields = [
                'SWI.id',
                'SWI.sa_withdrawal_id',
                'SWI.msb_inventory_id',
                'SWI.material_specification_id',
                'SWI.prs_no',
                'SWI.brand',
                'SWI.serial_no',
                'SWI.unit',
                'SWI.withdrawn_quantity',
                'MSBI.warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_items SWI')
                              ->join(['msb_inventories MSBI' => 'MSBI.id = SWI.msb_inventory_id'])
                              ->where(['SWI.is_active' => ':is_active']);

            $initQuery = ($sa_withdrawal_id) ? $initQuery->andWhere(['SWI.sa_withdrawal_id' => ':sa_withdrawal_id']) : $initQuery;

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

        public function selectMaterialSpecifications($id = false)
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

            $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectMsbInventories($msb_id = false, $project_id = false, $department_id = false, $unit = false, $ms_id = false)
        {
            $fields = [
                'MSBI.id',
                'MSBI.material_specification_brand_id as msb_id',
                'MSBI.project_id',
                'MSBI.department_id',
                'MSBI.warehouse_id',
                'MSBI.quantity',
                'MSBI.unit',
                'MSB.material_specification_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('msb_inventories MSBI')
                              ->join(['material_specification_brands MSB' => 'MSB.id = MSBI.material_specification_brand_id'])
                              ->where(['MSBI.is_active' => ':is_active']);

            $initQuery = ($msb_id)          ? $initQuery->andWhere(['MSBI.material_specification_brand_id' => ':msb_id']) : $initQuery;
            $initQuery = ($project_id)      ? $initQuery->andWhere(['MSBI.project_id' => ':project_id']) : $initQuery;
            $initQuery = ($department_id)   ? $initQuery->andWhere(['MSBI.department_id' => ':department_id']) : $initQuery;
            $initQuery = ($unit)            ? $initQuery->andWhere(['MSBI.unit' => ':unit']) : $initQuery;
            $initQuery = ($ms_id)           ? $initQuery->andWhere(['MSB.material_specification_id' => ':ms_id']) : $initQuery;

            return $initQuery;
        }

        public function selectWarehouses($id = false)
        {
            $fields = [
                'W.id',
                'W.name',
                'W.address',
            ];

            $initQuery  = $this->select($fields)
                               ->from('warehouses W')
                               ->where(['W.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['W.id' => ':id']) : $initQuery;

            return $initQuery;
        }

    }
?>