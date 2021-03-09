<?php
    namespace App\Model\Storage;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class StorageQueryHandler extends QueryHandler {

        public function selectMsbInventories($total = false)
        {
            $fields = [
                'MSBI.id',
                'MSBI.project_id',
                'MSBI.department_id',
                'MSBI.warehouse_id',
                'IF(MSBI.project_id IS NULL, CONCAT(D.charging, " - ", D.name), CONCAT(P.project_code, " - ", P.name)) as charging',
                'CONCAT(MS.code, " - ", MS.specs) as material',
                'M.name as material_name',
                'CONCAT(W.name, " - ", W.address) as warehouse',
                'MSBI.quantity',
                'MSBI.unit',
                'IF(MSBI.project_id IS NULL AND MSBI.department_id IS NULL, "buffer_stock", "charged_stock") as stockStatus'
            ];

            $fields = ($total) ? array('COUNT(MSBI.id) as total') : $fields;

            $leftJoins = [
                'projects P'                        =>  'P.id = MSBI.project_id',
                'departments D'                     =>  'D.id = MSBI.department_id',
                'material_specification_brands MSB' =>  'MSB.id = MSBI.material_specification_brand_id',
                'material_specifications MS'        =>  'MS.id = MSB.material_specification_id',
                'materials M'                       =>  'M.id = MS.material_id',
                'warehouses W'                      =>  'W.id = MSBI.warehouse_id'
            ];

            $orWhereCondition = array(
                'P.project_code' => ':filter_val',
                'P.name'         => ':filter_val',
                'D.charging'     => ':filter_val',
                'D.name'         => ':filter_val',
                'MS.code'        => ':filter_val',
                'MS.specs'       => ':filter_val',
                'W.name'         => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('msb_inventories MSBI')
                              ->leftJoin($leftJoins)
                              ->where(['MSBI.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            return $initQuery;
        }

        public function selectMsbiRecord()
        {
            $fields = [
                'MSBI.id',
                'MSBI.project_id',
                'MSBI.department_id',
                'MSBI.warehouse_id',
                'IF(MSBI.project_id IS NULL, CONCAT(D.charging, " - ", D.name), CONCAT(P.project_code, " - ", P.name)) as charging',
                'CONCAT(MS.code, " - ", MS.specs) as material',
                'M.name as material_name',
                'CONCAT(W.name, " - ", W.address) as warehouse',
                'MSBI.quantity',
                'MSBI.unit',
            ];

            $leftJoins = [
                'projects P'                        =>  'P.id = MSBI.project_id',
                'departments D'                     =>  'D.id = MSBI.department_id',
                'material_specification_brands MSB' =>  'MSB.id = MSBI.material_specification_brand_id',
                'material_specifications MS'        =>  'MS.id = MSB.material_specification_id',
                'materials M'                       =>  'M.id = MS.material_id',
                'warehouses W'                      =>  'W.id = MSBI.warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('msb_inventories MSBI')
                              ->leftJoin($leftJoins)
                              ->where(['MSBI.is_active' => ':is_active', 'MSBI.id' => ':id']);

            return $initQuery;
        }

        public function orWhereNotNull($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ";
            $andConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$condition.' IS NOT NULL ';
                } else {
                    $andConditions = $andConditions.$condition.' IS NOT NULL OR ';
                }

                $keyCounter++;
            }

            $query = $this->query.$string.$andConditions;

            return $query;
        }

        public function selectWithdrawals($total = false)
        {
            $fields = [
                'WI.id',
                'WI.withdrawal_id',
                'WI.purchase_requisition_description_id',
                'WI.msb_inventory_id',
                'WI.material_specification_id',
                'WI.withdrawn_quantity',
                'W.ws_no',
                'W.status',
                'IF(W.project_id IS NULL, CONCAT(D.charging, " - ", D.name), CONCAT(P.project_code, " - ", P.name)) as charging',
                'IF(W.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(W.project_id IS NULL, "D", "P") as charging_type',
                'W.project_id',
                'W.department_id',
                'W.is_standalone',
                'W.approved_by',
                'W.confirmed_by',
                'DATE_FORMAT(W.approved_at, "%M %d, %Y") as approved_at',
                'MSBI.quantity as stocks_qty',
                'MSBI.unit',
                'MSBI.warehouse_id',
                'CONCAT(WA.name, " - ", WA.address) as warehouse',
                '"standard" as type'
            ];

            $fields = ($total) ? array('COUNT(WI.id) as total') : $fields;

            $leftJoins = [
                'withdrawals W'                         =>  'W.id = WI.withdrawal_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = WI.msb_inventory_id',
                'warehouses WA'                         =>  'WA.id = MSBI.warehouse_id',
                'projects P'                            =>  'P.id = W.project_id',
                'departments D'                         =>  'D.id = W.department_id',
            ];

            $orWhereCondition = array(
                'P.project_code' => ':filter_val',
                'P.name'         => ':filter_val',
                'D.charging'     => ':filter_val',
                'D.name'         => ':filter_val',
                'W.ws_no'        => ':filter_val',
                'WA.name'        => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->leftJoin($leftJoins)
                              ->where(['WI.is_active' => ':is_active', 'W.is_active' => ':is_active', 'W.status' => ':status'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            return $initQuery;
        }

        public function selectSAWithdrawals($total = false)
        {
            $fields = [
                'SWI.id',
                'SWI.sa_withdrawal_id',
                'SWI.msb_inventory_id',
                'SWI.material_specification_id',
                'SWI.withdrawn_quantity',
                'SW.ws_no',
                'SW.status',
                'IF(SW.project_id IS NULL, CONCAT(D.charging, " - ", D.name), CONCAT(P.project_code, " - ", P.name)) as charging',
                'IF(SW.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(SW.project_id IS NULL, "D", "P") as charging_type',
                'SW.project_id',
                'SW.department_id',
                'SW.approved_by',
                'DATE_FORMAT(SW.approved_at, "%M %d, %Y") as approved_at',
                'MSBI.quantity as stocks_qty',
                'MSBI.unit',
                'MSBI.warehouse_id',
                'CONCAT(WA.name, " - ", WA.address) as warehouse',
                '"standalone" as type'
            ];

            $fields = ($total) ? array('COUNT(SWI.id) as total') : $fields;

            $leftJoins = [
                'sa_withdrawals SW'                     =>  'SW.id = SWI.sa_withdrawal_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = SWI.msb_inventory_id',
                'warehouses WA'                         =>  'WA.id = MSBI.warehouse_id',
                'projects P'                            =>  'P.id = SW.project_id',
                'departments D'                         =>  'D.id = SW.department_id',
            ];

            $orWhereCondition = array(
                'P.project_code' => ':filter_val',
                'P.name'         => ':filter_val',
                'D.charging'     => ':filter_val',
                'D.name'         => ':filter_val',
                'SW.ws_no'       => ':filter_val',
                'WA.name'        => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_items SWI')
                              ->leftJoin($leftJoins)
                              ->where(['SWI.is_active' => ':is_active', 'SW.is_active' => ':is_active', 'SW.status' => ':status'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            return $initQuery;
        }

        public function selectWithdrawalItems($withdrawal_id = false, $warehouse_id = false)
        {
            $fields = [
                'WI.id',
                'WI.withdrawal_id',
                'WI.msb_inventory_id',
                'WI.purchase_requisition_description_id',
                'WI.material_specification_id',
                'WI.withdrawn_quantity',
                'W.is_standalone',
                'PRD.item_spec_id',
                'PRD.quantity as requested_qty',
                'PR.prs_no',
                'MSBI.quantity as stocks',
                'MSBI.unit',
                'MSBI.warehouse_id',
                'MSBI.project_id',
                'MSBI.department_id',
                'IF(MSBI.project_id IS NULL AND MSBI.department_id IS NULL, "buffer_stock", "charged_stock") as stockStatus'
            ];

            $leftJoins = [
                'withdrawals W'                         =>  'W.id = WI.withdrawal_id',
                'purchase_requisition_descriptions PRD' =>  'PRD.id = WI.purchase_requisition_description_id',
                'purchase_requisitions PR'              =>  'PR.id = PRD.purchase_requisition_id',
                'msb_inventories MSBI'                  =>  'MSBI.id = WI.msb_inventory_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->leftJoin($leftJoins)
                              ->where(['WI.is_active' => ':is_active', 'W.is_active' => ':is_active']);

            $initQuery = ($withdrawal_id) ? $initQuery->andWhere(['WI.withdrawal_id' => ':withdrawal_id']) : $initQuery;
            $initQuery = ($warehouse_id)  ? $initQuery->andWhere(['MSBI.warehouse_id' => ':warehouse_id']) : $initQuery;

            return $initQuery;
        }

        public function selectSAWithdrawalItems($sa_withdrawal_id = false, $warehouse_id = false)
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
                'MSBI.quantity as stocks',
                'MSBI.unit',
                'MSBI.warehouse_id',
                'MSBI.project_id',
                'MSBI.department_id',
                'IF(MSBI.project_id IS NULL AND MSBI.department_id IS NULL, "buffer_stock", "charged_stock") as stockStatus'
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_items SWI')
                              ->join(['msb_inventories MSBI' => 'MSBI.id = SWI.msb_inventory_id'])
                              ->where(['SWI.is_active' => ':is_active']);

            $initQuery = ($sa_withdrawal_id) ? $initQuery->andWhere(['SWI.sa_withdrawal_id' => ':sa_withdrawal_id']) : $initQuery;
            $initQuery = ($warehouse_id)  ? $initQuery->andWhere(['MSBI.warehouse_id' => ':warehouse_id']) : $initQuery;

            return $initQuery;
        }

        public function selectMaterialSpecifications($id = false)
        {
            $fields = [
                'MS.id',
                'MS.code',
                'MS.specs',
            ];

            $initQuery = $this->select($fields)
                              ->from('material_specifications MS')
                              ->where(['MS.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['MS.id' => ':id']) : $initQuery;

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

        public function selectTotalReleasedQty()
        {
            $fields = [
                'SUM(WWRI.released_quantity) as total'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_released_items WWRI')
                              ->join(['withdrawal_warehouse_releases WWR' => 'WWR.id = WWRI.withdrawal_warehouse_release_id'])
                              ->where(['WWRI.is_active' => ':is_active', 'WWR.is_active' => ':is_active', 'WWRI.withdrawal_item_id' => ':withdrawal_item_id', 'WWR.warehouse_id' => ':warehouse_id']);

            return $initQuery;
        }

        public function selectSATotalReleasedQty()
        {
            $fields = [
                'SUM(SWWRI.released_quantity) as total'
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_warehouse_released_items SWWRI')
                              ->join(['sa_withdrawal_warehouse_releases SWWR' => 'SWWR.id = SWWRI.sa_withdrawal_warehouse_release_id'])
                              ->where(['SWWRI.is_active' => ':is_active', 'SWWR.is_active' => ':is_active', 'SWWRI.sa_withdrawal_item_id' => ':sa_withdrawal_item_id', 'SWWR.warehouse_id' => ':warehouse_id']);

            return $initQuery;
        }

        public function selectWithdrawalWarehouseReleases()
        {
            $fields = [
                'WWR.id',
                'WWR.withdrawal_id',
                'WWR.warehouse_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_releases WWR')
                              ->where(['WWR.is_active' => ':is_active', 'WWR.withdrawal_id' => ':withdrawal_id', 'WWR.warehouse_id' => ':warehouse_id']);

            return $initQuery;
        }

        public function selectSAWithdrawalWarehouseReleases()
        {
            $fields = [
                'SWWR.id',
                'SWWR.sa_withdrawal_id',
                'SWWR.warehouse_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_warehouse_releases SWWR')
                              ->where(['SWWR.is_active' => ':is_active', 'SWWR.sa_withdrawal_id' => ':sa_withdrawal_id', 'SWWR.warehouse_id' => ':warehouse_id']);

            return $initQuery;
        }

        public function selectProjects()
        {
            $fields = [
                'P.id',
                'IF(P.project_code IS NOT NULL, P.project_code, (SELECT temporary_project_code FROM project_code_requests WHERE project_id = P.id)) as charging_code',
                'P.name as charging_name',
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectDepartments()
        {
            $fields = [
                'D.id',
                'D.charging as charging_code',
                'D.name as charging_name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectWarehouses()
        {
            $fields = [
                'W.id',
                'W.name',
                'W.address',
            ];

            $initQuery = $this->select($fields)
                              ->from('warehouses W')
                              ->where(['W.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectUnits()
        {
            $fields = [
                'MU.id',
                'MU.unit',
            ];

            $initQuery = $this->select($fields)
                              ->from('material_units MU')
                              ->where(['MU.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectMsbSuppliers($msb_id = false, $unit = false, $ms_id = false)
        {
            $fields = array(
                'MSBS.id',
                'MSBS.material_specification_brand_id',
                'MSBS.code',
                'MSBS.unit',
                'MSB.material_specification_id'
            );

            $initQuery = $this->select($fields)
                              ->from('msb_suppliers MSBS')
                              ->join(array('material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id'))
                              ->where(array('MSBS.is_active' => ':is_active'));

            $initQuery = ($msb_id) ? $initQuery->andWhere(array('MSBS.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit)   ? $initQuery->andWhere(array('MSBS.unit' => ':unit')) : $initQuery;
            $initQuery = ($ms_id)  ? $initQuery->andWhere(array('MSB.material_specification_id' => ':ms_id')) : $initQuery;

            return $initQuery;
        }

        public function selectStockRecord($project_id = false, $department_id = false)
        {
            $fields = [
                'MSBI.id',
                'MSBI.project_id',
                'MSBI.department_id',
                'MSBI.material_specification_brand_id as msb_id',
                'MSBI.warehouse_id',
                'MSBI.quantity',
                'MSBI.unit',
                'MSB.material_specification_id as ms_id',
                'IF(MSBI.project_id IS NULL AND MSBI.department_id IS NULL, "buffer_stock", "charged_stock") as stockStatus'
            ];

            $conditions = [
                'MSBI.is_active'                    =>  ':is_active',
                'MSB.material_specification_id'     =>  ':ms_id',
                'MSBI.unit'                         =>  ':unit',
                'MSBI.warehouse_id'                 =>  ':warehouse_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('msb_inventories MSBI')
                              ->join(['material_specification_brands MSB' => 'MSB.id = MSBI.material_specification_brand_id'])
                              ->where($conditions);

            $initQuery = ($project_id)      ?   $initQuery->andWhere(['MSBI.project_id' => ':project_id'])           :   $initQuery;
            $initQuery = ($department_id)   ?   $initQuery->andWhere(['MSBI.department_id' => ':department_id'])     :   $initQuery;

            return $initQuery;
        }

        public function selectMsbInventoryHistories()
        {
            $fields = [
                'MSBIH.id',
                'MSBIH.msb_inventory_id',
                'MSBIH.material_delivery_id',
                'MSBIH.withdrawal_item_id',
                'MSBIH.sa_withdrawal_item_id',
                'MSBIH.material_return_id',
                'MSBIH.purchase_requisition_description_id',
                'MSBIH.supplier_id',
                'MSBIH.brand as msbih_brand',
                'MSBIH.serial_no as msbih_serial_no',
                'MSBIH.receipt_no',
                'MSBIH.quantity',
                'DATE_FORMAT(MSBIH.created_at, "%M %d, %Y %r") as as_of',
                'IF(MSBIH.material_delivery_id IS NULL, IF(MSBIH.sa_withdrawal_item_id IS NULL AND MSBIH.withdrawal_item_id IS NOT NULL, PR.prs_no, SWI.prs_no), PR.prs_no) as prs_no',
                'IF(MSBIH.withdrawal_item_id IS NOT NULL, WI.brand, "") as wi_brand',
                'IF(MSBIH.withdrawal_item_id IS NOT NULL, WI.serial_no, "") as wi_serial_no',
                'IF(MSBIH.sa_withdrawal_item_id IS NOT NULL, SWI.brand, "") as swi_brand',
                'IF(MSBIH.sa_withdrawal_item_id IS NOT NULL, SWI.serial_no, "") as swi_serial_no',
            ];

            $joins = [
                'purchase_requisition_descriptions PRD' =>  'PRD.id = MSBIH.purchase_requisition_description_id',
                'purchase_requisitions PR'              =>  'PR.id  = PRD.purchase_requisition_id',
                'sa_withdrawal_items SWI'               =>  'SWI.id = MSBIH.sa_withdrawal_item_id',
                'withdrawal_items WI'                   =>  'WI.id = MSBIH.withdrawal_item_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('msb_inventory_histories MSBIH')
                              ->leftJoin($joins)
                              ->where(['MSBIH.is_active' => ':is_active', 'MSBIH.msb_inventory_id' => ':msbi_id']);

            return $initQuery;
        }

        public function selectBufferstockHistories()
        {
            $fields = [
                'BSH.id',
                'BSH.msb_inventory_id',
                'BSH.supplier_id',
                'BSH.charging',
                'BSH.prs_no',
                'BSH.brand',
                'BSH.serial_no',
                'BSH.quantity',
                'BSH.type',
                'BSH.receipt_no',
                'BSH.remarks',
                'IF(BSH.date_withdrawn IS NOT NULL, DATE_FORMAT(BSH.date_withdrawn, "%M %d, %Y"), DATE_FORMAT(BSH.created_at, "%M %d, %Y %r")) as as_of',
            ];

            $initQuery = $this->select($fields)
                              ->from('bufferstock_histories BSH')
                              ->where(['BSH.is_active' => ':is_active', 'BSH.msb_inventory_id' => ':msbi_id']);

            return $initQuery;
        }

        public function selectWWReleases($id = false)
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

            $leftjoins = [
                'withdrawals W'     =>  'W.id = WWR.withdrawal_id',
                'projects P'        =>  'P.id = W.project_id',
                'departments D'     =>  'D.id = W.department_id',
                'warehouses Wa'     =>  'Wa.id = WWR.warehouse_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_releases WWR')
                              ->leftJoin($leftjoins)
                              ->where(['WWR.is_active' => ':is_active', 'W.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['WWR.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectSWWReleases($id = false)
        {
            $fields = [
                'SWWR.id',
                'SWWR.sa_withdrawal_id',
                'SWWR.warehouse_id',
                'SWWR.issued_by',
                'DATE_FORMAT(SWWR.created_at, "%M %d, %Y") as date_released',
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

            $leftjoins = [
                'sa_withdrawals SW' =>  'SW.id = SWWR.sa_withdrawal_id',
                'projects P'        =>  'P.id = SW.project_id',
                'departments D'     =>  'D.id = SW.department_id',
                'warehouses Wa'     =>  'Wa.id = SWWR.warehouse_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawal_warehouse_releases SWWR')
                              ->leftJoin($leftjoins)
                              ->where(['SWWR.is_active' => ':is_active', 'SW.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['SWWR.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectSuppliers($id = false)
        {
            $fields = [
                'S.id',
                'S.name',
                'S.address',
            ];

            $initQuery = $this->select($fields)
                              ->from('suppliers S')
                              ->where(['S.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['S.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectPOSupplier($material_delivery_id = false)
        {
            $fields = [
                'MD.id',
                'MD.purchase_order_id',
                'PO.supplier_id',
                'S.name',
                'S.address',
            ];

            $joins = [
                'purchase_orders PO'    =>  'PO.id = MD.purchase_order_id',
                'suppliers S'           =>  'S.id = PO.supplier_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('material_deliveries MD')
                              ->join($joins)
                              ->where(['MD.is_active' => ':is_active']);

            $initQuery = ($material_delivery_id) ? $initQuery->andWhere(['MD.id' => ':md_id']) : $initQuery;

            return $initQuery;
        }

        public function selectDeliveryPrsNo($material_delivery_id = false)
        {
            $fields = [
                'MD.id',
                'MD.purchase_order_id',
                'PO.supplier_id',
                'S.name',
                'S.address',
            ];

            $joins = [
                'purchase_orders PO'    =>  'PO.id = MD.purchase_order_id',
                'suppliers S'           =>  'S.id = PO.supplier_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('material_deliveries MD')
                              ->join($joins)
                              ->where(['MD.is_active' => ':is_active']);

            $initQuery = ($material_delivery_id) ? $initQuery->andWhere(['MD.id' => ':md_id']) : $initQuery;

            return $initQuery;
        }

        public function insertWithdrawalWarehouseReleases($data = [])
        {
            $initQuery = $this->insert('withdrawal_warehouse_releases', $data);

            return $initQuery;
        }

        public function insertSAWithdrawalWarehouseReleases($data = [])
        {
            $initQuery = $this->insert('sa_withdrawal_warehouse_releases', $data);

            return $initQuery;
        }

        public function insertWithdrawalWarehouseReleasedItems($data = [])
        {
            $initQuery = $this->insert('withdrawal_warehouse_released_items', $data);

            return $initQuery;
        }

        public function insertSAWithdrawalWarehouseReleasedItems($data = [])
        {
            $initQuery = $this->insert('sa_withdrawal_warehouse_released_items', $data);

            return $initQuery;
        }

        public function insertMsbInventories($data = [])
        {
            $initQuery = $this->insert('msb_inventories', $data);

            return $initQuery;
        }

        public function insertMsbInventoryHistories($data = [])
        {
            $initQuery = $this->insert('msb_inventory_histories', $data);

            return $initQuery;
        }

        public function insertBufferstockHistories($data = [])
        {
            $initQuery = $this->insert('bufferstock_histories', $data);

            return $initQuery;
        }
        
        public function updateMsbInventories($id = '', $data = [])
        {
            $initQuery = $this->update('msb_inventories', $id, $data);

            return $initQuery;
        }
    }
?>