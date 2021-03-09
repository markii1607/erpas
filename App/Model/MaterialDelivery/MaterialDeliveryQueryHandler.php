<?php
    namespace App\Model\MaterialDelivery;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MaterialDeliveryQueryHandler extends QueryHandler {

        public function selectPoDeliveries($po_id = false)
        {
            $fields = [
                'MD.id',
                'MD.purchase_order_id',
                'MD.updated_by',
                'DATE_FORMAT(MD.updated_at, "%M %d, %Y %r") as as_of',
                'PO.po_no',
                'S.name as supplier_name',
                'S.address as supplier_address',
            ];

            $initQuery = $this->select($fields)
                              ->from('material_deliveries MD')
                              ->join(['purchase_orders PO' => 'PO.id = MD.purchase_order_id', 'suppliers S' => 'S.id = PO.supplier_id'])
                              ->where(['MD.is_active' => ':is_active'])
                              ->andWhereNotNull(['MD.purchase_order_id']);

            $initQuery = ($po_id) ? $initQuery->andWhere(['MD.purchase_order_id' => ':po_id']) : $initQuery;

            return $initQuery;
        }

        public function selectMaterialDeliveries($id = false, $poId = false, $msId = false, $projectId = false, $deptId = false, $prdId = false, $condition = false)
        {
            if (!$condition) {
                $fields = array(
                    'MD.id',
                    'MD.purchase_order_id',
                    'MD.project_id',
                    'MD.department_id',
                    'MD.material_specification_id',
                    'MD.receipt_no',
                    'MD.updated_by',
                    'DATE_FORMAT(MD.updated_at, "%M %d, %Y %r") as updated_at',
                    'DATE_FORMAT(MD.created_at, "%M %d, %Y %r") as created_at',
                    'MSBIH.id as msbihId',
                    'MSBIH.purchase_requisition_description_id',
                    'MSBIH.warehouse_id',
                    'MSBIH.quantity as stock',
                    'MS.id as msId',
                    'MS.code',
                    'MS.specs',
                    'MSB.id as msb_id',
                    'MSBI.unit',
                    'P.id as pId',
                    'P.name as project_name',
                    'P.project_code',
                    'D.name as department_name',
                    'D.charging',
                    'W.name as warehouse',
                    'W.address',
                    'PRD.quantity as requested_stock',
                    'PR.prs_no',
                );
            } else {
                $fields = array(
                    'MD.id',
                    'MD.purchase_order_id',
                    'MD.project_id',
                    'MD.department_id',
                    'MD.material_specification_id',
                    'MSBIH.purchase_requisition_description_id',
                    'SUM(MSBIH.quantity) as total_delivered_qty',
                    'MS.id as msId',
                    'MS.code',
                    'MS.specs',
                    'MSB.id as msb_id',
                );
            }
            

            $joins = array(
                'msb_inventory_histories MSBIH'         => 'MSBIH.material_delivery_id = MD.id',
                'msb_inventories MSBI'                  => 'MSBI.id = MSBIH.msb_inventory_id',
                'material_specifications MS'            => 'MS.id = MD.material_specification_id',
                'material_specification_brands MSB'     => 'MSB.material_specification_id = MS.id',
                'projects P'                            => 'P.id = MD.project_id',
                'departments D'                         => 'D.id = MD.department_id',
                'warehouses W'                          => 'W.id = MSBIH.warehouse_id',
                'purchase_requisition_descriptions PRD' => 'PRD.id = MSBIH.purchase_requisition_description_id',
                'purchase_requisitions PR'              => 'PR.id = PRD.purchase_requisition_id',
            );

            $initQuery = $this->select($fields)
                              ->from('material_deliveries MD')
                              ->leftJoin($joins)
                              ->where(array('MD.is_active' => ':is_active'));

            $initQuery = ($id)       ? $initQuery->andWhere(array('MD.id' => ':id'))                                                : $initQuery;
            $initQuery = ($poId)     ? $initQuery->andWhere(array('MD.purchase_order_id' => ':purchase_order_id'))                  : $initQuery;
            $initQuery = ($msId)     ? $initQuery->andWhere(array('MD.material_specification_id' => ':material_specification_id'))  : $initQuery;
            $initQuery = ($projectId)? $initQuery->andWhere(array('MD.project_id' => ':project_id'))                                : $initQuery;
            $initQuery = ($deptId)   ? $initQuery->andWhere(array('MD.department_id' => ':department_id'))                          : $initQuery;
            $initQuery = ($prdId)    ? $initQuery->andWhere(array('PRD.id' => ':prd_id'))                                           : $initQuery;

            return $initQuery;
        }

        public function selectChargeToViaPurchaseOrder($id = false)
        {
            $fields = array(
                'PO.id',
                'PO.po_no',
                'P.id as project_id',
                'P.name as project_name',
                'P.project_code as project_code',
                'D.id as department_id',
                'D.name as department_name',
                'D.charging',
                'PRD.id as prdId',
                'PRD.item_spec_id',
                'PRD.quantity as requested_stock',
                'PRD.unit_measurement as unit',
                'PR.project_id as proj_id',
                'PR.department_id as dept_id',
                'PR.prs_no'
            );

            $joins = array(
                'aob_supply_evaluations AOBSE'            => 'AOBSE.purchase_order_id = PO.id',
                'aob_descriptions AOBD'                   => 'AOBD.id = AOBSE.aob_description_id',
                'request_quotation_materials RQM'         => 'RQM.id = AOBD.rfq_material_id',
                'request_quotation_descriptions RQD'      => 'RQD.rfq_material_id = RQM.id',
                'purchase_requisition_descriptions PRD'   => 'PRD.id = RQD.purchase_requisition_description_id',
                'purchase_requisitions PR'                => 'PR.id = PRD.purchase_requisition_id',
                'projects P'                              => 'P.id = PR.project_id',
                'departments D'                           => 'D.id = PR.department_id',
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_orders PO')
                              ->leftJoin($joins)
                              ->where(array('PO.is_active' => ':is_active', 'RQM.material_specification_id' => ':specs_id', 'RQD.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PO.id' => ':id')) : $initQuery;

            return $initQuery;
        }
        
        public function selectMaterials($id = false, $poId = false)
        {
            $fields = array(
                'M.id',
                'M.name',
                'MS.id as ms_id',
                'MS.code',
                'MS.specs',
                'RQM.quantity',
                'RQM.unit',
                'IF(RQ.rfq_type = "0", IF(RQ.c_unit_price = "0.00", RQ.c_delivery_price, RQ.c_unit_price), IF(RQ.p_unit_price = "0.00", RQ.p_delivery_price, RQ.p_unit_price)) as unit_price',
                // 'IF(RQ.rfq_type = "0", RQ.c_unit_price, RQ.p_unit_price) as unit_price',
                'IF(RQ.rfq_type = "0", RQ.c_delivery_charge, RQ.p_delivery_charge) as delivery_charge',
            );

            $joins = array(
                'material_specifications MS'      => 'M.id = MS.material_id',
                'request_quotation_materials RQM' => 'MS.id = RQM.material_specification_id',
                'request_quotations RQ'           => 'RQM.id = RQ.rfq_material_id',
                'aob_supply_evaluations AOBSE'    => 'RQ.id = AOBSE.rfq_id',
                'purchase_orders PO'              => 'AOBSE.purchase_order_id = PO.id'
            );

            $initQuery = $this->select($fields)
                                ->from('materials M')
                                ->join($joins)
                                ->where(array('M.is_active' => ':is_active'));

            $initQuery = ($id)   ? $initQuery->andWhere(array('M.id' => ':id'))                 : $initQuery;
            $initQuery = ($poId) ? $initQuery->andWhere(array('PO.id' => ':purchase_order_id')) : $initQuery;

            return $initQuery;
        }

        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'PI.id as personal_information_id',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'P.name as position_name',
            ];

            $joins = [
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectPurchaseOrders()
        {
            $fields = [
                'PO.id',
                'PO.po_no',
                'S.name as supplier_name',
                'S.address as supplier_address',
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_orders PO')
                              ->join(['suppliers S' => 'S.id = PO.supplier_id'])
                              ->where(['PO.is_active' => 1]);
                              
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

        public function selectMsbInventories($id = false, $projectId = false, $departmentId = false, $warehouseId = false, $materialId = false, $unit = false)
        {
            $fields = array(
                'MSBI.id',
                'MSBI.material_specification_brand_id',
                'MSBI.project_id',
                'MSBI.department_id',
                'MSBI.warehouse_id',
                'MSBI.quantity'
            );

            $initQuery = $this->select($fields)
                              ->from('msb_inventories MSBI')
                              ->where(array('MSBI.is_active' => ':is_active'));

            $initQuery = ($id)           ? $initQuery->andWhere(array('MSBI.id' => ':id')) : $initQuery;
            $initQuery = ($projectId)    ? $initQuery->andWhere(array('MSBI.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(array('MSBI.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($warehouseId)  ? $initQuery->andWhere(array('MSBI.warehouse_id' => ':warehouse_id')) : $initQuery;
            $initQuery = ($materialId)   ? $initQuery->andWhere(array('MSBI.material_specification_brand_id' => ':material_specification_brand_id')) : $initQuery;
            $initQuery = ($unit)         ? $initQuery->andWhere(array('MSBI.unit' => ':unit')) : $initQuery;

            return $initQuery;
        }

        public function selectMaterialDeliveryAttachments($purchase_order_id = false)
        {
            $fields = [
                'MDA.id',
                'MDA.purchase_order_id',
                'MDA.filename',
            ];

            $initQuery = $this->select($fields)
                              ->from('material_delivery_attachments MDA')
                              ->where(['MDA.is_active' => ':is_active']);

            $initQuery = ($purchase_order_id) ? $initQuery->andWhere(['MDA.purchase_order_id' => ':po_id']) : $initQuery;

            return $initQuery;
        }

        public function insertMaterialDeliveries($data = [])
        {
            $initQuery = $this->insert('material_deliveries', $data);

            return $initQuery;
        }

        public function insertMsbInventory($data = [])
        {
            $initQuery = $this->insert('msb_inventories', $data);

            return $initQuery;
        }

        public function insertMsbInventoryHistory($data = [])
        {
            $initQuery = $this->insert('msb_inventory_histories', $data);

            return $initQuery;
        }

        public function insertMaterialDeliveryAttachments($data = [])
        {
            $initQuery = $this->insert('material_delivery_attachments', $data);

            return $initQuery;
        }

        public function updateMsbInventories($id = '', $data = [])
        {
            $initQuery = $this->update('msb_inventories', $id, $data);

            return $initQuery;
        }

        public function updatePurchaseRequisitionDescription($id = '', $data = [])
        {
            $initQuery = $this->update('purchase_requisition_descriptions', $id, $data);

            return $initQuery;
        }
    }