<?php
    namespace App\Model\ProjectAnalyticalReports;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProjectAnalyticalReportsQueryHandler extends QueryHandler {

        public function selectProjects()
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name as project_title',
                'P.location as project_location',
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(array('P.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectProgressReports($project_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'MAX(PR.revision_no) as revision_no',
                'PR.as_of',
                'WC.week_no',
                'WC.from_date',
                'WC.to_date',
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

        public function selectProgressReportDetails($progress_report_id = false)
        {
            $fields = array(
                'PRWD.id',
                'PRWD.progress_report_id',
                'PRWD.work_item_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRWD')
                              ->where(array('PRWD.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRWD.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdMaterials($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PM.id',
                'PM.ps_swi_direct_id',
                'SUM(PM.total_cost) as total_cost',
                'PW.project_id',
                'null as p_wi_indirect_id',
                'WI.id as work_item_id',
                'SWI.wbs',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
                'WI.item_no',
                'WI.name as item_name',
            );

            $leftJoins = array(
                'ps_swi_directs PSDI'       =>  'PSDI.id = PM.ps_swi_direct_id',
                'pw_sps PS'                 =>  'PS.id = PSDI.pw_sp_id',
                'p_wds PW'                  =>  'PW.id = PS.p_wd_id',
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = PM.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $initQuery = $this->select($fields)
                              ->from('psd_materials PM')
                              ->leftJoin($leftJoins)
                              ->leftJoin($joinsDirect)
                              ->where(array('PM.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PW.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPwiMaterials($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PM.id',
                'PM.p_wi_indirect_id',
                'PM.ps_swi_direct_id',
                'IF(PM.ps_swi_direct_id IS NULL, PWI.project_id, PW.project_id) as project_id',
                'SUM(PM.total_cost) as total_cost',
                'IF(PM.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(PM.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(PM.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(PM.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(PM.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(PM.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
            );

            $leftJoins = array(
                'ps_swi_directs PSDI'       =>  'PSDI.id = PM.ps_swi_direct_id',
                'pw_sps PS'                 =>  'PS.id = PSDI.pw_sp_id',
                'p_wds PW'                  =>  'PW.id = PS.p_wd_id',
                'sw_wis SWI'               => 'SWI.id = PSDI.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = PM.ps_swi_direct_id',
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = PM.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pwi_materials PM')
                              ->leftJoin($leftJoins)
                            //   ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('PM.is_active' => ':is_active'));

            ('PM.ps_swi_direct_id' == null) ? $initQuery->andWhere(array('PWI.project_id' => ':project_id')) : $initQuery->andWhere(array('PW.project_id' => ':project_id'));
            ('PM.ps_swi_direct_id' == null) ? $initQuery->andWhere(array('WIs.id' => ':work_item_id')) : $initQuery->andWhere(array('WI.id' => ':work_item_id'));

            return $initQuery;
        }

        public function selectPsdLabors($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PL.id',
                'PL.project_id',
                'PL.ps_swi_direct_id',
                'PL.p_wi_indirect_id',
                'SUM(PL.total_cost) as total_cost',
                'IF(PL.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(PL.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(PL.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(PL.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(PL.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(PL.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = PL.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = PL.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('psd_labors PL')
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('PL.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PL.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? ('PL.ps_swi_direct_id' == null) ? $initQuery->andWhere(array('WIs.id' => ':work_item_id')) : $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdEquipments($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PE.id',
                'PE.ps_swi_direct_id',
                'PW.project_id',
                'null as p_wi_indirect_id',
                'SUM(PE.total) as total_cost',
                'WI.id as work_item_id',
                'SWI.wbs',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
                'WI.item_no',
                'WI.name as item_name',
            );

            $leftJoins = array(
                'ps_swi_directs PSDI'       =>  'PSDI.id = PE.ps_swi_direct_id',
                'pw_sps PS'                 =>  'PS.id = PSDI.pw_sp_id',
                'p_wds PW'                  =>  'PW.id = PS.p_wd_id',
                'equipment_types ET'        =>  'ET.id = PE.equipment_type_id',
                'equipment_categories EC'   =>  'EC.id = ET.equipment_category_id'
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = PE.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $initQuery = $this->select($fields)
                              ->from('psd_equipments PE')
                              ->leftJoin($leftJoins)
                              ->leftJoin($joinsDirect)
                              ->where(array('PE.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PW.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPwiEquipments($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PE.id',
                'PE.p_wi_indirect_id',
                'PE.ps_swi_direct_id',
                'IF(PE.ps_swi_direct_id IS NULL, PWI.project_id, PW.project_id) as project_id',
                'SUM(PE.total) as total_cost',
                'IF(PE.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(PE.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(PE.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(PE.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(PE.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(PE.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
            );

            $leftJoins = array(
                'ps_swi_directs PSDI'       =>  'PSDI.id = PE.ps_swi_direct_id',
                'pw_sps PS'                 =>  'PS.id = PSDI.pw_sp_id',
                'p_wds PW'                  =>  'PW.id = PS.p_wd_id',
                'equipment_types ET'        =>  'ET.id = PE.equipment_type_id',
                'equipment_categories EC'   =>  'EC.id = ET.equipment_category_id'
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = PE.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = PE.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pwi_equipments PE')
                              ->leftJoin($leftJoins)
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('PE.is_active' => ':is_active'));

            $initQuery = ($project_id) ? ('PE.ps_swi_direct_id' == null) ? $initQuery->andWhere(array('PWI.project_id' => ':project_id')) : $initQuery->andWhere(array('PW.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? ('PE.ps_swi_direct_id' == null) ? $initQuery->andWhere(array('WIs.id' => ':work_item_id')) : $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;


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
    }
