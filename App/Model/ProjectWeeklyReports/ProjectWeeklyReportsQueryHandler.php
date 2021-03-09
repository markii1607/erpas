<?php
    namespace App\Model\ProjectWeeklyReports;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProjectWeeklyReportsQueryHandler extends QueryHandler {

        public function selectOnGoingProjects($id = false)
        {
            $fields = array(
                'P.id',
                'P.name as project_title',
                'P.project_code',
                'P.location as project_location',
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(array('P.is_active' => ':is_active', 'P.is_on_going' => ':is_on_going'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectExistingWeekNos($project_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'WC.id as wc_id',
                'WC.week_no',
                'WC.from_date',
                'DATE_FORMAT(WC.from_date, "%b-%d-%Y") as fromDate',
                'DATE_FORMAT(WC.to_date, "%b-%d-%Y") as toDate',
            );

            $join = array(
                'weekly_calendar WC'    =>      'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->join($join)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReport($project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectCostSummary($project_id = false)
        {
            $fields = array(
                'PCS.id',
                'PCS.project_id',
                'PCS.material_total_direct_cost',
                'PCS.material_total_indirect_cost',
                'PCS.labor_total_direct_cost',
                'PCS.labor_total_indirect_cost',
                'PCS.equipment_total_direct_cost',
                'PCS.equipment_total_indirect_cost',
                'PCS.grand_total_unit_cost',
            );

            $initQuery = $this->select($fields)
                              ->from('project_cost_summaries PCS')
                              ->where(array('PCS.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PCS.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectReportMaterials($progress_report_id = false)
        {
            $fields = array(
                'PRM.id',
                'PRM.progress_report_id',
                'PRM.work_item_id',
                'PRM.material_specification_brand_id',
                'PRM.unit',
                'PRM.C_input',
                '(SELECT price FROM msb_suppliers WHERE material_specification_brand_id = PRM.material_specification_brand_id ORDER BY price_date DESC LIMIT 1) as latest_price',
                'SUM((SELECT price FROM msb_suppliers WHERE material_specification_brand_id = PRM.material_specification_brand_id ORDER BY price_date DESC LIMIT 1) * PRM.C_input) as total_cost',
                'WI.work_item_category_id',
            );

            $join = array(
                'work_items WI' =>  'WI.id = PRM.work_item_id'
            );

            $initQuery = $this->select($fields)
                                ->from('progress_report_materials PRM')
                                ->join($join)
                                ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRM.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectReportLabor($progress_report_id = false)
        {
            $fields = array(
                'SUM(PRLID.total_cost) as total_cost',
                'PRLID.position_type',
                'PRLID.progress_report_labor_item_id',
                'PRLI.progress_report_labor_id',
                'PRLI.work_item_id',
                'PRL.progress_report_id',
                'WI.work_item_category_id'
            );

            $joins = array(
                'progress_report_labor_items PRLI'  =>  'PRLI.id = PRLID.progress_report_labor_item_id',
                'progress_report_labors PRL'        =>  'PRL.id = PRLI.progress_report_labor_id',
                'work_items WI'                     =>  'WI.id = PRLI.work_item_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labor_item_details PRLID')
                              ->leftJoin($joins)
                              ->where(array('PRLID.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRL.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectReportEquipments($progress_report_id = false)
        {
            $fields = array(
                'SUM(PREI.actual_total_cost) as total_cost',
                'PREI.work_item_id',
                'PREI.equipment_type_id',
                'PREI.capacity',
                'PRE.progress_report_id',
                'WI.work_item_category_id'
            );

            $joins = array(
                'progress_report_equipments PRE'    =>  'PRE.id = PREI.progress_report_equipment_id',
                'work_items WI'                     =>  'WI.id = PREI.work_item_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipment_items PREI')
                              ->leftJoin($joins)
                              ->where(array('PREI.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRE.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }
    }
