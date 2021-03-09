<?php 
    namespace App\Model\PurchaseRequestSlip;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class PurchaseRequestSlipQueryHandler extends QueryHandler { 

        /**
        * `selectPurchaseRequestSLip` Sekecting all purchase request slip in table 'purchase_request'
        * @param boolean $id
        * @param $items string
        * 
        */
        public function selectPurchaseRequestSlip($id = false)
        {
            $fields = [
                'PRS.id',
                'PRS.item',
                'PRS.requested_by',
                'PRS.description',
                'PRS.quantity',
                'PRS.remark',
                'PRS.work_item_no',
                'PRS.charge_to',
                'PRS.purchase_request_type',
                'PRS.date_needed',
                'PRS.created_by',
                'PRS.created_at',
                'PRS.status',
            ];


            $initQuery = $this->select($fields)
                              ->from('purchase_requests PRS');
            $initQuery = ($id) ? $initQuery->where(['PRS.created_by'=> ':id']) : $initQuery;

            return $initQuery;
        }
        
        /**
         * `selectDepartments` Selecting all departments from table 'departments'
         * @param  boolean $id
         * @param  boolean $department_id
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.name',
            ];
            $initQuery = $this->select($fields)
                              ->from('departments D');
            $initQuery = ($id) ? $initQuery->where(['D.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjects` Selecting all departments from table 'projects'
         * @param  boolean $id
         * @param  boolean $project_name
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code',
            ];
            $initQuery = $this->select($fields)
                              ->from('projects P');
            $initQuery = ($id) ? $initQuery->where(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selecEmployee` Selecting employee data from table 'employees'
         * @param  boolean $id
         * @param  boolean $project_name
         * @return string
         */
        public function selectEmployee($id = false)
        {
            $fields = [
                'E.id',
                'E.fname',
                'E.mname',
                'E.lname',
                'E.position_id',
                'E.department_id',
                'CONCAT(E.fname, " ", E.mname, " ", E.lname) as fullname',
            ];
            $initQuery = $this->select($fields)
                              ->from('employees E');
            $initQuery = ($id) ? $initQuery->where(['E.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEqupment` Selecting equipment data from table 'equipment'
         * @param  boolean $id
         * @param  boolean $equipment_name
         * @return string
         */
        public function selectEquipments($id = false)
        {
            $fields = [
                'ET.id',
                'ET.name',
                'ET.unit'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipment_types ET');

            return $initQuery;
        }


        /**
         * `selectMaterials` Selecting materials data from table 'materials'
         * @param  boolean $id
         * @param  boolean $material_name
         * @return string
         */
        public function selectMaterials($id = false)
        {
            $fields = [
                'M.id',
                'M.material_type_id',
                'M.name',
                'M.brand',
                'M.status',
                // 'M.type',
                'MT.name as material_type_name'
            ];

            $joins = [
                'material_types MT' => 'M.material_type_id = MT.id'
            ];
            $initQuery = $this->select($fields)
                              ->from('materials M')
                              ->join($joins);

            return $initQuery;
        }

        /**
         * `selectWorkItems` Selecting work items data from table 'work_items'
         * @param  boolean $id
         * @param  boolean $work_item_code
         * @return string
         */
        public function selectWorkItems($id = false)
        {
            $fields = [
                'WI.id',
                'WI.work_item_category_id',
                'WI.cost_code',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WI.status',
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI');

            return $initQuery;
        }

    }