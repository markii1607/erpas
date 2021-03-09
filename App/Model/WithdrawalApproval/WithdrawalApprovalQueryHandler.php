<?php
    namespace App\Model\WithdrawalApproval;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class WithdrawalApprovalQueryHandler extends QueryHandler {

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
                'W.approved_by',
                'W.approver_status',
                'W.approver_remarks',
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

            $initQuery = $this->select($fields)
                              ->from('withdrawals W')
                              ->where(['W.is_active' => ':is_active', 'W.approved_by' => ':approved_by'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['W.id' => ':id']) : $initQuery;

            switch ($status) {

                case 'For Approval':
                    $initQuery->andWhere(['W.status' => '2']);
                    break;

                case 'Approved':
                    $initQuery->andWhere(['W.status' => '4']);
                    break;

                case 'Disapproved/Cancelled':
                    $initQuery->andWhere(['W.status' => '5']);
                    break;

                default:
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

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawals SW')
                              ->where(['SW.is_active' => ':is_active', 'SW.approved_by' => ':approved_by'])
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

                default:
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
                'WI.msb_inventory_id',
                'WI.material_specification_id',
                'WI.warehouse_id',
                'WI.total_withdrawn',
                'WI.withdrawn_quantity',
                'WI.disapproved_qty',
                'DATE_FORMAT(WI.delivery_date, "%M %d, %Y") as delivery_date',
                'PRD.purchase_requisition_id',
                'PRD.item_spec_id',
                'PRD.pm_id as p_material_id',
                'PRD.unit_measurement as unit',
                'PRD.quantity as total_qty',
                'PRD.category',
                'PRD.work_item_id',
                'PR.prs_no',
                'MSBI.warehouse_id',
                'W.name as warehouse_name',
                'W.address as warehouse_address',
            ];

            $joins = [
                'purchase_requisition_descriptions PRD' =>  'PRD.id = WI.purchase_requisition_description_id',
                'purchase_requisitions PR'              =>  'PR.id = PRD.purchase_requisition_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = WI.msb_inventory_id',
                'warehouses W'                          =>  'W.id = MSBI.warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->join($joins)
                              ->where(['WI.is_active' => ':is_active']);

            $initQuery = ($withdrawal_id)           ? $initQuery->andWhere(['WI.withdrawal_id' => ':withdrawal_id']) : $initQuery;
            $initQuery = ($purchase_requisition_id) ? $initQuery->andWhere(['PRD.purchase_requisition_id' => ':purchase_requisition_id']) : $initQuery;

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

        public function updateSAWithdrawals($id = '', $data = array())
        {
            $initQuery = $this->update('sa_withdrawals', $id, $data);

            return $initQuery;
        }

    }
?>