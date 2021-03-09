<?php
    namespace App\Model\WithdrawalReceiving;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class WithdrawalReceivingQueryHandler extends QueryHandler {

        public function selectProjects($user_id = false)
        {
            $fields = [
                'P.id',
                'P.name as project_name',
                // 'P.project_code',
                'IF(P.project_code IS NOT NULL, P.project_code, (SELECT temporary_project_code FROM project_code_requests WHERE project_id = P.id)) as project_code',
                'P.location as project_location',
                'PA.project_id',
                'PA.user_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->join(['projects P' => 'P.id = PA.project_id'])
                              ->where(['PA.is_active' => ':is_active', 'P.is_active' => ':is_active']);

            $initQuery = ($user_id) ? $initQuery->andWhere(['PA.user_id' => ':user_id']) : $initQuery;

            return $initQuery;
        }

        public function selectStandardWithdrawalReleases($project_id = false, $filter, $total = false)
        {
            $fields = [
                'WWR.id',
                'WWR.withdrawal_id',
                'WWR.warehouse_id',
                'DATE_FORMAT(WWR.created_at, "%M %d, %Y") as date_released',
                'WWR.issued_by',
                'DATE_FORMAT(WWR.issued_at, "%M %d, %Y") as issued_at',
                'WWR.received_by',
                'DATE_FORMAT(WWR.received_at, "%M %d, %Y") as received_at',
                'WWR.receiver_signature',
                'WWR.is_received',
                'W.ws_no',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y") as date_requested',
                'W.status',
                'W.charge_to',
                'W.project_id',
                'W.approved_by',
                'DATE_FORMAT(W.approved_at, "%M %d, %Y") as approved_at',
                'W.created_by',
                'CONCAT(Wa.name, " - ", Wa.address) as warehouse',
                '"standard" as type',
                'CONCAT(P.project_code, " - ", P.name) as charging'
            ];

            $fields = ($total) ? array('COUNT(WWR.id) as total') : $fields;

            $orWhereCondition = array(
                'W.ws_no'                                => ':filter_val',
                'DATE_FORMAT(W.ws_date, "%M %d, %Y")'    => ':filter_val',
            );

            $leftJoins = [
                'withdrawals W' =>  'W.id = WWR.withdrawal_id',
                'projects P'    =>  'P.id = W.project_id',
                'warehouses Wa' =>  'Wa.id = WWR.warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_releases WWR')
                              ->leftJoin($leftJoins)
                              ->where(['WWR.is_active' => ':is_active', 'W.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($project_id) ? $initQuery->andWhere(['W.project_id' => ':project_id']) : $initQuery;

            switch ($filter) {
                case 'FR':
                    $initQuery->andWhereNull(['WWR.is_received']);
                    break;
                
                case 'RW':
                    $initQuery->andWhereNotNull(['WWR.is_received']);
                    break;
            }

            return $initQuery;
        }

        public function selectStandaloneWithdrawalReleases($project_id = false, $filter, $total = false)
        {
            $fields = [
                'SWWR.id',
                'SWWR.sa_withdrawal_id',
                'SWWR.warehouse_id',
                'DATE_FORMAT(SWWR.created_at, "%M %d, %Y") as date_released',
                'SWWR.issued_by',
                'DATE_FORMAT(SWWR.issued_at, "%M %d, %Y") as issued_at',
                'SWWR.received_by',
                'DATE_FORMAT(SWWR.received_at, "%M %d, %Y") as received_at',
                'SWWR.receiver_signature',
                'SWWR.is_received',
                'SW.ws_no',
                'DATE_FORMAT(SW.ws_date, "%M %d, %Y") as date_requested',
                'SW.status',
                // 'SW.charge_to',
                'SW.project_id',
                'SW.approved_by',
                'DATE_FORMAT(SW.approved_at, "%M %d, %Y") as approved_at',
                'SW.created_by',
                'CONCAT(Wa.name, " - ", Wa.address) as warehouse',
                '"standalone" as type',
                'CONCAT(P.project_code, " - ", P.name) as charging'
            ];

            $fields = ($total) ? array('COUNT(SWWR.id) as total') : $fields;

            $orWhereCondition = array(
                'SW.ws_no'                                => ':filter_val',
                'DATE_FORMAT(SW.ws_date, "%M %d, %Y")'    => ':filter_val',
            );

            $leftJoins = [
                'sa_withdrawals SW' =>  'SW.id = SWWR.sa_withdrawal_id',
                'projects P'        =>  'P.id = SW.project_id',
                'warehouses Wa'     =>  'Wa.id = SWWR.warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_warehouse_releases SWWR')
                              ->leftJoin($leftJoins)
                              ->where(['SWWR.is_active' => ':is_active', 'SW.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($project_id) ? $initQuery->andWhere(['SW.project_id' => ':project_id']) : $initQuery;

            switch ($filter) {
                case 'FR':
                    $initQuery->andWhereNull(['SWWR.is_received']);
                    break;
                
                case 'RW':
                    $initQuery->andWhereNotNull(['SWWR.is_received']);
                    break;
            }

            return $initQuery;
        }

        public function selectWWRItems()
        {
            $fields = [
                'WWRI.id',
                'WWRI.withdrawal_warehouse_release_id',
                'WWRI.withdrawal_item_id',
                'WWRI.released_quantity',
                'WWRI.unit',
                'WWRI.remarks',
                'MS.id as material_specification_id',
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

        public function selectSWWRItems()
        {
            $fields = [
                'SWWRI.id',
                'SWWRI.sa_withdrawal_warehouse_release_id',
                'SWWRI.sa_withdrawal_item_id',
                'SWWRI.released_quantity',
                'SWWRI.unit',
                'SWWRI.remarks',
                'MS.id as material_specification_id',
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

        public function selectUserInformations($user_id = false)
        {
            $fields = [
                'U.id',
                'CONCAT(PI.fname, " ", PI.lname) as fullname',
                'P.name as position',
                'D.name as department',
            ];

            $leftJoins = [
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id',
                'departments D'                 =>  'D.id = P.department_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->leftJoin($leftJoins)
                              ->where(['U.is_active' => ':is_active']);

            $initQuery = ($user_id) ? $initQuery->andWhere(['U.id' => ':user_id']) : $initQuery;

            return $initQuery;
        }

        public function selectProjectMaterialInventories($project_id = false, $material_spec_id = false, $unit)
        {
            $fields = [
                'PMI.id',
                'PMI.project_id',
                'PMI.material_specification_id',
                'PMI.quantity',
                'PMI.unit',
            ];

            $initQuery = $this->select($fields)
                              ->from('project_material_inventories PMI')
                              ->where(['PMI.is_active' => ':is_active']);

            $initQuery = ($project_id)       ? $initQuery->andWhere(['PMI.project_id' => ':project_id']) : $initQuery;
            $initQuery = ($material_spec_id) ? $initQuery->andWhere(['PMI.material_specification_id' => ':spec_id']) : $initQuery;
            $initQuery = ($unit)             ? $initQuery->andWhere(['PMI.unit' => ':unit']) : $initQuery;

            return $initQuery;

        }

        public function insertProjectMaterialInventory($data = [])
        {
            $initQuery = $this->insert('project_material_inventories', $data);

            return $initQuery;
        }

        public function insertProjectMaterialInventoryHistory($data = [])
        {
            $initQuery = $this->insert('project_material_inventory_histories', $data);

            return $initQuery;
        }

        public function updateWWR($id = '', $data = [])
        {
            $initQuery = $this->update('withdrawal_warehouse_releases', $id, $data);

            return $initQuery;
        }

        public function updateSWWR($id = '', $data = [])
        {
            $initQuery = $this->update('sa_withdrawal_warehouse_releases', $id, $data);

            return $initQuery;
        }

        public function updateWWRItem($id = '', $data = [])
        {
            $initQuery = $this->update('withdrawal_warehouse_released_items', $id, $data);

            return $initQuery;
        }

        public function updateSWWRItem($id = '', $data = [])
        {
            $initQuery = $this->update('sa_withdrawal_warehouse_released_items', $id, $data);

            return $initQuery;
        }

        public function updateProjectMaterialInventory($id = '', $data = [])
        {
            $initQuery = $this->update('project_material_inventories', $id, $data);

            return $initQuery;
        }

    }
?>