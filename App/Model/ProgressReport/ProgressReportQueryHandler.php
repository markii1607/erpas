<?php
    namespace App\Model\ProgressReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProgressReportQueryHandler extends QueryHandler {

        public function selectProjectProgressReports($id = false, $project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.doc_no',
                'PR.status',
                'PR.revision_no',
                'IF(PR.as_of IS NULL, "", DATE_FORMAT(PR.as_of, "%b %d, %Y %r")) as as_of',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportWeeklyDetails($project_report_id = false, $work_item_id = false)
        {
            $fields = array(
                'PRW.id',
                'PRW.progress_report_id',
                'PRW.work_item_id',
                'PRW.weekly_wv_plan',
                'PRW.plan_project_weight',
                'PRW.td_plan_proj_weight',
                'IF(PRW.plan_start_date IS NOT NULL, DATE_FORMAT(PRW.plan_start_date, "%d-%b-%Y"), "") as plan_start_date',
                'IF(PRW.plan_finish_date IS NOT NULL, DATE_FORMAT(PRW.plan_finish_date, "%d-%b-%Y"), "") as plan_finish_date',
                'DATE_FORMAT(PRW.actual_start_date, "%d-%b-%Y") as actual_start_date',
                'DATE_FORMAT(PRW.actual_finish_date, "%d-%b-%Y") as actual_finish_date',
                'PRW.wv_total',
                'PRW.devt_project_weight',
                'PRW.devt_item_weight',
                'PRW.td_project_weight',
                'PRW.td_item_weight',
                'PRW.td_work_volume',
                'PRW.td_wv_balance',
                'PRW.entry_type',
                'PRW.actual_revision_no',
                'PRW.is_with_revision',
                // 'PRW.td_project_slippage',
                'WIC.part as part_code',
            );

            $leftJoins = array(
                'work_items WI'              =>      'WI.id = PRW.work_item_id',
                'work_item_categories WIC'   =>      'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->leftJoin($leftJoins)
                              ->where(array('PRW.is_active' => ':is_active'));

            $initQuery = ($project_report_id) ? $initQuery->andWhere(array('PRW.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectGroupedProgressReportWeeklyDetails($project_report_id = false, $work_item_id = false)
        {
            $fields = array(
                'PRW.id',
                'PRW.progress_report_id',
                'PRW.work_item_id',
                'PRW.weekly_wv_plan',
                'IF(PRW.plan_start_date IS NOT NULL, DATE_FORMAT(PRW.plan_start_date, "%d-%b-%Y"), "") as plan_start_date',
                'IF(PRW.plan_finish_date IS NOT NULL, DATE_FORMAT(PRW.plan_finish_date, "%d-%b-%Y"), "") as plan_finish_date',
                'DATE_FORMAT(PRW.actual_start_date, "%d-%b-%Y") as actual_start_date',
                'DATE_FORMAT(PRW.actual_finish_date, "%d-%b-%Y") as actual_finish_date',
                'PRW.wv_total',
                'SUM(PRW.plan_project_weight) as plan_project_weight',
                'SUM(PRW.td_plan_proj_weight) as td_plan_proj_weight',
                'SUM(PRW.devt_project_weight) as devt_project_weight',
                'SUM(PRW.td_project_weight) as td_project_weight',
                // 'SUM(PRW.td_project_slippage) as td_project_slippage',
                'WI.id as wi_id',
                'WI.work_item_category_id',
                'WIC.part as part_code'
            );

            $leftJoins = array(
                'work_items WI'             =>  'WI.id = PRW.work_item_id',
                'work_item_categories WIC'  =>  'WIC.id = WI.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->leftJoin($leftJoins)
                              ->where(array('PRW.is_active' => ':is_active'));
                            //   ->groupBy('WI.work_item_category_id');

            $initQuery = ($project_report_id) ? $initQuery->andWhere(array('PRW.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectWeeklyCalendar($id = false, $to_date = false)
        {
            $fields = array(
                'WC.id',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as to_date',
            );

            $initQuery = $this->select($fields)
                              ->from('weekly_calendar WC')
                              ->where(array('WC.is_active' => ':is_active'));

            // $initQuery = ($date)  ? $initQuery->logicEx('AND WC.from_date <= :date_from')  : $initQuery;
            // $initQuery = ($date)  ? $initQuery->logicEx('AND WC.to_date >= :date_from')    : $initQuery;
            $initQuery = ($id)    ? $initQuery->andWhere(array('WC.id' => ':id')) : $initQuery;
            $initQuery = ($to_date) ? $initQuery->andWhere(array('WC.to_date' => ':to_date')) : $initQuery;

            return $initQuery;
        }

        public function selectItemCodesDirect($project_id = false, $work_item_id = false, $ps_swi_direct_id = '', $wic_id = false, $revision_no = false)
        {
            $fields = array(
                'PW.project_id',
                'WI.id',
                'WI.wbs as wi_wbs',
                'WI.item_no',
                'WI.name as item_name',
                'WIC.id as wic_id',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
                'PD.id as ps_swi_direct_id',
                'PD.quantities as work_volume',
                'PD.weight_factor',
                'SW.wbs',
                'IF(SW.unit IS NULL, WI.unit, SW.unit) as unit',
            );


            $joins = array(
                'pw_sps PS'                 =>      'PS.p_wd_id = PW.id',
                'ps_swi_directs PD'         =>      'PD.pw_sp_id = PS.id',
                'sw_wis SW'                 =>      'SW.id = PD.sw_wi_id',
                'work_items WI'             =>      'WI.id = SW.work_item_id',
                'work_item_categories WIC'  =>      'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                              ->from('p_wds PW')
                              ->leftJoin($joins)
                              ->where(array('PW.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PW.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;
            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PD.id' => ':ps_swi_direct_id')) : $initQuery;
            $initQuery = ($wic_id) ? $initQuery->andWhere(array('WI.work_item_category_id' => ':wic_id')) : $initQuery;
            $initQuery = ($revision_no)  ? $initQuery->andWhere(['PD.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        public function selectItemCodesIndirect($project_id = false, $work_item_id = false, $p_wi_indirect_id = false, $wic_id = false, $revision_no = false)
        {
            $fields = array(
                'PWI.id as p_wi_indirect_id',
                'PWI.project_id',
                'PWI.quantities as work_volume',
                'PWI.weight_factor',
                'WI.id',
                'WI.wbs',
                'WI.item_no',
                'WI.name as item_name',
                'WI.unit',
                'WIC.id as wic_id',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
            );
            

            $joins = array(
                'work_items WI'             =>      'WI.id = PWI.work_item_id',
                'work_item_categories WIC'  =>      'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                              ->from('p_wi_indirects PWI')
                              ->leftJoin($joins)
                              ->where(array('PWI.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PWI.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;
            $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PWI.id' => ':p_wi_indirect_id')) : $initQuery;
            $initQuery = ($wic_id) ? $initQuery->andWhere(array('WI.work_item_category_id' => ':wic_id')) : $initQuery;
            $initQuery = ($revision_no)  ? $initQuery->andWhere(['PWI.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportAttachments($daily_progress_report_id = false, $id = false)
        {
            $fields = array(
                'PRA.id',
                'PRA.daily_progress_report_id',
                'PRA.file_name',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_attachments PRA')
                              ->where(array('PRA.is_active' => ':is_active'));
                            //   ->orderBy('PRA.file_name', 'DESC')
                            //   ->limit(1);

            $initQuery = ($daily_progress_report_id) ? $initQuery->andWhere(array('PRA.daily_progress_report_id' => ':daily_progress_report_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('PRA.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjects($id = false, $project_manager = false)
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.contract_days as project_duration',
                'DATE_FORMAT(P.date_started, "%M %d, %Y") as project_start',
                'DATE_FORMAT(P.date_finished, "%M %d, %Y") as project_finish',
                'P.date_started',
                'P.date_finished',
                'P.project_manager',
                'P.revision_no',
            );

            $join = array(
                'transactions T'    =>   'T.id = P.transaction_id'
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                            //   ->join($join)
                            //   ->where(array('P.is_active' => ':is_active', 'T.status' => ':status'));
                              ->where(array('P.is_active' => ':is_active'));

            $initQuery = ($id)              ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;
            $initQuery = ($project_manager) ? $initQuery->andWhere(array('P.project_manager' => ':head_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectAccesses()
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.contract_days as project_duration',
                'DATE_FORMAT(P.date_started, "%M %d, %Y") as project_start',
                'DATE_FORMAT(P.date_finished, "%M %d, %Y") as project_finish',
                'P.date_started',
                'P.date_finished',
                'P.project_manager',
                'P.revision_no'
            );

            $join = array(
                'projects P'  =>  'P.id = PA.project_id'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->join($join)
                              ->where(array('PA.is_active' => ':is_active', 'PA.user_id' => ':user_id'));

            return $initQuery;
        }

        public function selectPsdLabors($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PL.id',
                'PL.project_id',
                'PL.ps_swi_direct_id',
                'PL.p_wi_indirect_id',
                'PL.skilled_workers',
                'PL.common_workers',
                'PL.unit_cost_rate_skilled as skilled_rate',
                'PL.unit_cost_rate_common as common_rate',
                'PL.duration as no_of_days',
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
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('IF(PL.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;
            // $initQuery = ($work_item_id) ? ('PL.ps_swi_direct_id' == null) ? $initQuery->andWhere(array('WIs.id' => ':work_item_id')) : $initQuery->andWhere(array('WI.id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdEquipments($project_id = false, $work_item_id = false, $equipment_type_id = false, $equip_type_classification = false, $capacity = false)
        {
            $fields = array(
                'PE.id',
                'PE.ps_swi_direct_id',
                'PW.project_id',
                'null as p_wi_indirect_id',
                'PE.equipment_id',
                'PE.equipment_type_id',
                'PE.capacity',
                'PE.heavy_equipment_id',
                'PE.light_equipment_id',
                'SUM(PE.no_of_equipment) as no_of_unit',
                'SUM(PE.duration) as equip_work_duration',
                'PE.rental_rate',
                'WI.id as work_item_id',
                'SWI.wbs',
                'ET.cost_code as equipment_code',
                'ET.name as equipment_description',
                'ET.unit',
                'EC.name as equip_category_name',
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
            $initQuery = ($equipment_type_id) ? $initQuery->andWhere(array('PE.equipment_type_id' => ':equip_type_id')) : $initQuery;
            $initQuery = ($equip_type_classification) ? $initQuery->andWhere(array('ET.classification' => ':classification')) : $initQuery;
            $initQuery = ($capacity) ? $initQuery->andWhere(array('PE.capacity' => ':capacity')) : $initQuery;

            return $initQuery;
        }

        public function selectPwiEquipments($project_id = false, $work_item_id = false, $equipment_type_id = false, $equip_type_classification = false, $capacity = false)
        {
            $fields = array(
                'PE.id',
                'PE.p_wi_indirect_id',
                'PE.ps_swi_direct_id',
                'IF(PE.ps_swi_direct_id IS NULL, PWI.project_id, PW.project_id) as project_id',
                'PE.equipment_id',
                'PE.equipment_type_id',
                'PE.capacity',
                'PE.heavy_equipment_id',
                'PE.light_equipment_id',
                'SUM(PE.no_of_equipment) as no_of_unit',
                'SUM(PE.duration) as equip_work_duration',
                'PE.rental_rate',
                'IF(PE.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(PE.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'ET.cost_code as equipment_code',
                'ET.name as equipment_description',
                'ET.unit',
                'EC.name as equip_category_name',
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

            $initQuery = ($project_id) ? $initQuery->andWhere(array('IF(PE.ps_swi_direct_id IS NULL, PWI.project_id, PW.project_id)' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('IF(PE.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;
            $initQuery = ($equipment_type_id) ? $initQuery->andWhere(array('PE.equipment_type_id' => ':equip_type_id')) : $initQuery;
            $initQuery = ($equip_type_classification) ? $initQuery->andWhere(array('ET.classification' => ':classification')) : $initQuery;
            $initQuery = ($capacity) ? $initQuery->andWhere(array('PE.capacity' => ':capacity')) : $initQuery;
            // $initQuery = ($heavy_equipment) ? $initQuery->andWhereNotNull(array('PE.heavy_equipment_id')) : $initQuery;
            // $initQuery = ($light_equipment) ? $initQuery->andWhereNotNull(array('PE.light_equipment_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDaReports($project_id = false, $week_calendar_id = false)
        {
            $fields = array(
                'DAR.id',
                'DAR.project_id',
                'DAR.week_calendar_id',
                // 'COUNT(*) as no',
            );

            $initQuery = $this->select($fields)
                              ->from('da_reports DAR')
                              ->where(array('DAR.is_active' => ':is_active', 'DAR.status' => ':status'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('DAR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($week_calendar_id) ? $initQuery->andWhere(array('DAR.week_calendar_id' => ':week_calendar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDarActivities($project_id = false, $week_calendar_id = false, $work_item_id = false, $position_type = false, $activity_date = false)
        {
            $fields = array(
                'DA.id',
                'DA.dar_id',
                'DA.ps_swi_direct_id',
                'DA.p_wi_indirect_id',
                'DA.form_type',
                'DR.project_id',
                'DR.requisition_manpower_id',
                'DR.week_calendar_id',
                'DR.activity_date',
                'DR.status',
                'SUM(DR.total_rate) as total_rate',
                'PL.position_id',
                'P.position_type',
                'IF(DA.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(DA.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(DA.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(DA.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(DA.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(DA.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
                // 'COUNT(*) as no_of_workers'
            );

            $joins = array(
                'da_reports DR'             =>      'DR.id = DA.dar_id',
                'requisition_manpower RM'   =>      'RM.id = DR.requisition_manpower_id',
                'pr_labors PL'              =>      'PL.id = RM.pr_labor_id',
                'positions P'               =>      'P.id = PL.position_id'
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = DA.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = DA.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('dar_activities DA')
                              ->join($joins)
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('DA.is_active' => ':is_active', 'DR.status' => ':status'))
                              ->andWhereNull(array('DA.form_type'))
                              ->andWhereNotNull(array('P.position_type'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('DR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($week_calendar_id)    ? $initQuery->andWhere(array('DR.week_calendar_id' => ':week_calendar_id')) : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('IF(DA.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;
            $initQuery = ($position_type)       ? $initQuery->andWhere(array('P.position_type' => ':position_type')) : $initQuery;
            $initQuery = ($activity_date)       ? $initQuery->andWhere(array('DR.activity_date' => ':activity_date')) : $initQuery;

            return $initQuery;
        }

        public function selectLaborActivities($project_id = false, $week_calendar_id = false, $work_item_id = false, $position_type = false, $activity_date = false)
        {
            $fields = array(
                'LA.lar_id',
                'LA.ps_swi_direct_id',
                'LA.p_wi_indirect_id',
                'IF(LA.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(LA.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(LA.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(LA.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(LA.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(LA.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
                'LR.project_id',
                'LR.position_id',
                'LR.week_calendar_id',
                'SUM(LA.no_labor) as no_of_workers',
                'SUM(LA.subtotal_st) as total_st',
                'SUM(LA.subtotal_ot) as total_ot',
                'LR.project_id',
                '(SELECT project_code FROM projects WHERE id = LR.project_id) as project_code',
                'LR.activity_date',
                'LR.status',
                'P.position_type',
                'P.name as position_name',
                'IF(P.position_type = 1, "Skilled", "Common") as pos_type'
            );

            $leftJoins = array(
                'la_reports LR'     =>      'LR.id = LA.lar_id',
                'positions P'       =>      'P.id = LR.position_id'
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = LA.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = LA.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('labor_activities LA')
                              ->leftJoin($leftJoins)
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('LA.is_active' => ':is_active', 'LR.is_active' => ':is_active', 'LR.status' => ':status'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('LR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($week_calendar_id) ? $initQuery->andWhere(array('LR.week_calendar_id' => ':week_calendar_id')) : $initQuery;
            $initQuery = ($position_type) ? $initQuery->andWhere(array('P.position_type' => ':position_type')) : $initQuery;
            $initQuery = ($activity_date) ? $initQuery->andWhere(array('LR.activity_date' => ':activity_date')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('IF(LA.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;


            return $initQuery;
        }

        public function selectEquipmentReports($project_id = false, $week_calendar_id = false, $work_item_id = false, $equipment_type_id = false, $capacity = false)
        {
            $fields = array(
                'EA.id',
                'EA.ear_id',
                'EA.ps_swi_direct_id',
                'EA.p_wi_indirect_id',
                'ER.id as er_id',
                'ER.heavy_equipment_id',
                'ER.small_equipment_id',
                'ER.project_id',
                'ER.week_calendar_id',
                'ER.activity_date',
                'IF(ER.heavy_equipment_id IS NULL, SE.equipment_type_id, HE.equipment_type_id) as equip_type_id',
                'IF(ER.heavy_equipment_id IS NULL, SE.capacity, HE.capacity) as capacity',
                'IF(ER.heavy_equipment_id IS NULL, SE.cost_code, HE.cost_code) as equipment_code',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(EA.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(EA.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
                
            );

            $joins = array(
                'ea_reports ER'             =>      'ER.id = EA.ear_id',
                'heavy_equipments HE'       =>      'HE.id = ER.heavy_equipment_id',
                'small_equipments SE'       =>      'SE.id = ER.small_equipment_id',
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = EA.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = EA.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('ear_activities EA')
                              ->leftJoin($joins)
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('EA.is_active' => ':is_active', 'ER.status' => ':status'));

            $initQuery = ($project_id)        ? $initQuery->andWhere(array('ER.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($week_calendar_id)  ? $initQuery->andWhere(array('ER.week_calendar_id' => ':week_calendar_id')) : $initQuery;
            $initQuery = ($work_item_id)      ? $initQuery->andWhere(array('IF(EA.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;
            $initQuery = ($equipment_type_id) ? $initQuery->andWhere(array('IF(ER.heavy_equipment_id IS NULL, SE.equipment_type_id, HE.equipment_type_id)' => ':equipment_type_id')) : $initQuery;
            $initQuery = ($capacity)          ? $initQuery->andWhere(array('IF(ER.heavy_equipment_id IS NULL, SE.capacity, HE.capacity)' => ':capacity')) : $initQuery;


            return $initQuery;
        }

        public function selectEarActivities($project_id = false, $week_calendar_id = false, $report_type = '', $heavy_equipment_id = false, $small_equipment_id = false, $work_item_id = false, $equipment_type_id = false, $capacity = false)
        {
            $fields = array(
                'EA.id',
                // 'EA.ear_id',
                'EA.ps_swi_direct_id',
                'EA.p_wi_indirect_id',
                'ER.id as er_id',
                'ER.heavy_equipment_id',
                'ER.small_equipment_id',
                'ER.project_id',
                'ER.week_calendar_id',
                'ER.activity_date',
                'IF(ER.heavy_equipment_id IS NULL, ER.small_equipment_id, ER.heavy_equipment_id) as equipment_id',
                'IF(ER.heavy_equipment_id IS NULL, SE.equipment_type_id, HE.equipment_type_id) as equip_type_id',
                'IF(ER.heavy_equipment_id IS NULL, (SELECT name FROM equipment_types WHERE id = SE.equipment_type_id), (SELECT name FROM equipment_types WHERE id = HE.equipment_type_id)) as equipment_description',
                'IF(ER.heavy_equipment_id IS NULL, (SELECT unit FROM equipment_types WHERE id = SE.equipment_type_id), (SELECT unit FROM equipment_types WHERE id = HE.equipment_type_id)) as equipment_unit',
                'IF(ER.heavy_equipment_id IS NULL, SE.capacity, HE.capacity) as capacity',
                'IF(ER.heavy_equipment_id IS NULL, SE.cost_code, HE.cost_code) as equipment_code',
                'IF(ER.heavy_equipment_id IS NULL, SE.body_no, HE.body_no) as body_no',
                'IF(ER.heavy_equipment_id IS NULL, "0", HE.rental_rate_per_hour) as hourly_rental_rate',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.wbs, SWI.wbs) as wbs',
                'IF(EA.ps_swi_direct_id IS NULL, CONCAT("PART ", WICs.part), CONCAT("PART ", WIC.part)) as part_code',
                'IF(EA.ps_swi_direct_id IS NULL, WICs.name, WIC.name) as part_name',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.item_no, WI.item_no) as item_no',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.name, WI.name) as item_name',
                'SUM(EA.subtotal_st) as total_st',
                'SUM(EA.subtotal_ot) as total_ot',
                // 'COUNT(*) as deployed_units',
                '(SELECT project_code FROM projects WHERE id = ER.project_id) as project_code',
                'IF(ER.heavy_equipment_id IS NULL, "SE", "HE") as type',
            );

            $joins = array(
                'ea_reports ER'             =>      ($report_type == 'HE') ? 'ER.id = EA.ear_id' : 'ER.id = EA.lar_id',
                'heavy_equipments HE'       =>      'HE.id = ER.heavy_equipment_id',
                'small_equipments SE'       =>      'SE.id = ER.small_equipment_id',
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = EA.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = EA.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            if ($report_type == 'HE') {

                $initQuery = $this->select($fields)
                                  ->from('ear_activities EA')
                                  ->leftJoin($joins)
                                  ->leftJoin($joinsDirect)
                                  ->leftJoin($joinsIndirect)
                                  ->where(array('EA.is_active' => ':is_active', 'ER.status' => ':status'));

            } else if ($report_type == 'SE') {

                $initQuery = $this->select($fields)
                                  ->from('lar_activities EA')
                                  ->leftJoin($joins)
                                  ->leftJoin($joinsDirect)
                                  ->leftJoin($joinsIndirect)
                                  ->where(array('EA.is_active' => ':is_active', 'ER.status' => ':status'));
            }
            

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('ER.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($week_calendar_id)    ? $initQuery->andWhere(array('ER.week_calendar_id' => ':week_calendar_id')) : $initQuery;
            $initQuery = ($heavy_equipment_id)  ? $initQuery->andWhere(array('ER.heavy_equipment_id' => ':heavy_equipment_id')) : $initQuery;
            $initQuery = ($small_equipment_id)  ? $initQuery->andWhere(array('ER.small_equipment_id' => ':small_equipment_id')) : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('IF(EA.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;
            // $initQuery = ($activity_date)       ? $initQuery->andWhere(array('ER.activity_date' => ':activity_date')) : $initQuery;
            if ($report_type == 'HE') {
                $initQuery->andWhereNotNull(array('ER.heavy_equipment_id'));
                $initQuery = ($equipment_type_id)   ? $initQuery->andWhere(array('HE.equipment_type_id' => ':equipment_type_id')) : $initQuery;
                $initQuery = ($capacity)            ? $initQuery->andWhere(array('HE.capacity' => ':capacity')) : $initQuery;
            } else if ($report_type == 'SE') {
                $initQuery->andWhereNotNull(array('ER.small_equipment_id'));
                $initQuery = ($equipment_type_id)   ? $initQuery->andWhere(array('SE.equipment_type_id' => ':equipment_type_id')) : $initQuery;
                $initQuery = ($capacity)            ? $initQuery->andWhere(array('SE.capacity' => ':capacity')) : $initQuery;
            }
            

            return $initQuery;
        }

        // unused
        /* public function selectWithdrawalWarehouseReleasedItems()
        {
            $fields = array(
                'WWRI.id',
                'WWRI.withdrawal_warehouse_release_id',
                'WWRI.purchase_requisition_description_id',
                'WWRI.material_specification_id',
                'WWRI.quantity as withdrawn_qty',
                'WWRI.unit',
                '(SELECT ws_no FROM withdrawals WHERE id = WWR.withdrawal_id) as ws_no',
                'PRD.id as prd_id',
                'PRD.work_item_id',
            );

            $joins = array(
                'withdrawal_warehouse_releases WWR'     =>  'WWR.id = WWRI.withdrawal_warehouse_release_id',
                'purchase_requisition_descriptions PRD' =>  'PRD.id = WWRI.purchase_requisition_description_id'
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_warehouse_released_items WWRI')
                              ->leftJoin($joins)
                              ->where(array('WWRI.is_active' => ':is_active', 'WWR.is_active' => ':is_active'));

            return $initQuery;
        } */

        public function selectProgressStatusMaterials($id = false, $progress_report_id = false)
        {
            $fields = array(
                'PSM.id',
                'PSM.progress_report_id',
                'PSM.doc_no',
                'DATE_FORMAT(PSM.created_at, "%d-%b-%Y") as report_date',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_status_materials PSM')
                              ->where(array('PSM.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PSM.id' => ':id')) : $initQuery;
            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PSM.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReports($id = false, $project_id = false, $weekly_calendar_id = false, $week_to_date = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.project_revision_no',
                'PR.weekly_calendar_id',
                'PR.revision_no',
                'PR.doc_no',
                'IF(PR.status IS NULL, "empty", PR.status) as status',
                'DATE_FORMAT(PR.as_of, "%b %d, %Y") as as_of',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as to_date',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as week_to_date',
                'DATE_FORMAT(WC.to_date, "%M") as month', 
                'DATE_FORMAT(WC.to_date, "%d") as day', 
                'DATE_FORMAT(WC.to_date, "%Y") as year'
            );

            $join = array(
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->join($join)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($week_to_date) ? $initQuery->logicEx('AND WC.to_date > :week_to_date') : $initQuery;

            return $initQuery;
        }

        public function selectWithdrawalSiteDeliveryItems($project_id = false, $work_item_id = false, $msb_id = false, $unit = false, $delivery_date = false)
        {
            $fields = array(
                'WSDI.id',
                'WSDI.purchase_requisition_description_id',
                'WSDI.material_specification_brand_id',
                'WSDI.actual_qty_received',
                'DATE_FORMAT(WSDI.created_at, "%d-%b-%Y") as date_delivered',
                'WSDI.unit',
                'WSDI.material_condition',
                'IF(WSDI.purchase_requisition_description_id IS NOT NULL, PRD.work_item_id, WSDI.work_item_id) as work_item_id',
                'IF(WSD.project_id IS NULL, WWR.project_id, WSD.project_id) as project_id',
                'WSD.date_received',
                'WWR.withdrawal_id',
                'W.ws_no',
                'W.is_standalone',
            );

            $joins = array(
                'withdrawal_site_deliveries WSD'        =>      'WSD.id = WSDI.withdrawal_site_delivery_id',
                'withdrawal_security_releases WSR'      =>      'WSR.id = WSD.withdrawal_security_release_id',
                'withdrawal_warehouse_releases WWR'     =>      'WWR.id = WSR.withdrawal_warehouse_release_id',
                'withdrawals W'                         =>      'W.id   = WWR.withdrawal_id',
                'purchase_requisition_descriptions PRD' =>      'PRD.id = WSDI.purchase_requisition_description_id'
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_site_delivery_items WSDI')
                              ->leftJoin($joins)
                              ->where(array('WSDI.is_active' => ':is_active', 'WSDI.material_condition' => ':material_condition'));

            /* if ('WSD.project_id' == null) {
                $initQuery = ($project_id)    ? $initQuery->andWhere(array('WWR.project_id' => ':project_id')) : $initQuery;
            } else {
                $initQuery = ($project_id)    ? $initQuery->andWhere(array('WSD.project_id' => ':project_id')) : $initQuery;
            } */
            
            $initQuery = ($project_id)    ? $initQuery->andWhere(array('IF(WSD.project_id IS NULL, WWR.project_id, WSD.project_id)' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id)  ? $initQuery->andWhere(array('PRD.work_item_id' => ':work_item_id')) : $initQuery;
            $initQuery = ($msb_id)        ? $initQuery->andWhere(array('WSDI.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit)          ? $initQuery->andWhere(array('WSDI.unit' => ':unit')) : $initQuery;
            // $initQuery = ($delivery_date) ? $initQuery->andWhereRange('DATE_FORMAT(WSDI.created_at, "%Y-%m-%d")', array(':fromDate', ':toDate')) : $initQuery;
            $initQuery = ($delivery_date) ? $initQuery->andWhereRange('DATE_FORMAT(WSD.date_received, "%Y-%m-%d")', array(':fromDate', ':toDate')) : $initQuery;

            return $initQuery;
        }

        public function selectSAWithdrawalSiteDeliveryItems($project_id = false, $work_item_id = false, $msb_id = false, $unit = false, $delivery_date = false)
        {
            $fields = array(
                'WSDI.id',
                'WSDI.purchase_requisition_description_id',
                'WSDI.material_specification_brand_id',
                'WSDI.material_specification_brand_id as msb_id',
                'SUM(WSDI.actual_qty_received) as total_delivered_qty',
                'DATE_FORMAT(WSDI.created_at, "%d-%b-%Y") as date_delivered',
                'WSDI.unit',
                'WSDI.material_condition',
                'IF(WSDI.purchase_requisition_description_id IS NOT NULL, PRD.work_item_id, WSDI.work_item_id) as work_item_id',
                'IF(WSD.project_id IS NULL, WWR.project_id, WSD.project_id) as project_id',
                'WSD.date_received',
                'WWR.withdrawal_id',
                'W.ws_no',
                'W.is_standalone',
                '"!empty" as status',
                'MS.specs',
                'M.name as material_name',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = WSDI.material_specification_brand_id AND unit = WSDI.unit LIMIT 1) as material_code'
            );

            $leftJoins = array(
                'withdrawal_site_deliveries WSD'        =>      'WSD.id = WSDI.withdrawal_site_delivery_id',
                'withdrawal_security_releases WSR'      =>      'WSR.id = WSD.withdrawal_security_release_id',
                'withdrawal_warehouse_releases WWR'     =>      'WWR.id = WSR.withdrawal_warehouse_release_id',
                'withdrawals W'                         =>      'W.id   = WWR.withdrawal_id',
                'purchase_requisition_descriptions PRD' =>      'PRD.id = WSDI.purchase_requisition_description_id'
            );

            $joins = array(
                'material_specification_brands MSB'     =>      'MSB.id = WSDI.material_specification_brand_id',
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id'
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawal_site_delivery_items WSDI')
                              ->leftJoin($leftJoins)
                              ->join($joins)
                              ->where(array('WSDI.is_active' => ':is_active', 'WSDI.material_condition' => ':material_condition', 'W.is_standalone' => ':is_standalone'));

            $initQuery = ($project_id)    ? $initQuery->andWhere(array('IF(WSD.project_id IS NULL, WWR.project_id, WSD.project_id)' => ':project_id')) : $initQuery;
            $initQuery = ($work_item_id)  ? $initQuery->andWhere(array('PRD.work_item_id' => ':work_item_id')) : $initQuery;
            $initQuery = ($msb_id)        ? $initQuery->andWhere(array('WSDI.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit)          ? $initQuery->andWhere(array('WSDI.unit' => ':unit')) : $initQuery;
            $initQuery = ($delivery_date) ? $initQuery->andWhereRange('DATE_FORMAT(WSD.date_received, "%Y-%m-%d")', array(':fromDate', ':toDate')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportMaterials($psm_id = false, $wi_id = false, $msb_id = false, $unit = false)
        {
            $fields = array(
                'PRM.id',
                'PRM.progress_status_material_id',
                'PRM.progress_report_id',
                'PRM.work_item_id',
                'PRM.material_specification_brand_id',
                'PRM.unit',
                'PRM.D_input',
                'PRM.D_toDate',
                'PRM.C_input',
                'PRM.C_toDate',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = PRM.material_specification_brand_id LIMIT 1) as material_code',
                'M.name as description',
                'MS.specs as specification',
            );

            $joins = array(
                'material_specification_brands MSB'     =>      'MSB.id = PRM.material_specification_brand_id',
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_materials PRM')
                              ->join($joins)
                              ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($psm_id) ? $initQuery->andWhere(array('PRM.progress_status_material_id' => ':psm_id')) : $initQuery;
            $initQuery = ($wi_id)  ? $initQuery->andWhere(array('PRM.work_item_id' => ':wi_id')) : $initQuery;
            $initQuery = ($msb_id) ? $initQuery->andWhere(array('PRM.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit)   ? $initQuery->andWhere(array('PRM.unit' => ':unit')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportLabors($progress_report_id = false, $work_item_id = false, $position_type = false)
        {
            $fields = array(
                'PRLID.id',
                'PRLID.position_type',
                'PRLID.actual_workers',
                'PRLID.actual_working_days',
                'PRLID.to_date_workers',
                'PRLID.to_date_working_days',
                'PRLI.work_item_id',
                'PRL.id as report_labor_id',
                'PRL.progress_report_id',
            );

            $join = array(
                'progress_report_labor_items PRLI'  =>  'PRLI.id = PRLID.progress_report_labor_item_id',
                'progress_report_labors PRL'        =>  'PRL.id = PRLI.progress_report_labor_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labor_item_details PRLID')
                              ->join($join)
                              ->where(array('PRLID.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRL.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id)  ? $initQuery->andWhere(array('PRLI.work_item_id' => ':work_item_id')) : $initQuery;
            $initQuery = ($position_type) ? $initQuery->andWhere(array('PRLID.position_type' => ':position_type')) : $initQuery;

            return $initQuery;
        }

        public function selectExistingLaborReport($progress_report_id = false)
        {
            $fields = array(
                'PRL.id',
                'PRL.progress_report_id',
                'PRL.doc_no',
                'DATE_FORMAT(PRL.created_at, "%d-%b-%Y") as report_date',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labors PRL')
                              ->where(array('PRL.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRL.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportEquipments($progress_report_id = false)
        {
            $fields = array(
                'PRE.id',
                'PRE.progress_report_id',
                'PRE.doc_no',
                'PRE.is_he_saved',
                'PRE.is_se_saved',
                'DATE_FORMAT(PRE.created_at, "%d-%b-%Y") as report_date'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipments PRE')
                              ->where(array('PRE.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRE.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportEquipmentItems($progress_report_equipment_id = false, $equipment_type_id = false, $capacity = false)
        {
            $fields = array(
                'PREI.id',
                'PREI.progress_report_equipment_id',
                // 'PREI.work_item_id',
                'PREI.equipment_type_id',
                'PREI.capacity',
                'PREI.td_deployed_units',
                'PREI.actual_equipment_days',
                'PREI.td_equipment_days',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipment_items PREI')
                              ->where(array('PREI.is_active' => ':is_active'));

            $initQuery = ($progress_report_equipment_id) ? $initQuery->andWhere(array('PREI.progress_report_equipment_id' => ':progress_report_equipment_id')) : $initQuery;
            // $initQuery = ($work_item_id)                 ? $initQuery->andWhere(array('PREI.work_item_id' => ':work_item_id')) : $initQuery;
            $initQuery = ($equipment_type_id)            ? $initQuery->andWhere(array('PREI.equipment_type_id' => ':equipment_type_id')) : $initQuery;
            $initQuery = ($capacity)                     ? $initQuery->andWhere(array('PREI.capacity' => ':capacity')) : $initQuery;

            return $initQuery;
        }

        public function selectSpecificReports($weekly_calendar_id = false, $project_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
                'IF(PR.as_of IS NOT NULL, DATE_FORMAT(PR.as_of, "%b %d, %Y"), "") as as_of',
                'IF(PR.status IS NULL, "empty", PR.status) as status',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function selectExistingWeekNos($project_id = false, $week_no = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
                'IF(PR.as_of IS NOT NULL, DATE_FORMAT(PR.as_of, "%b %d, %Y"), "") as as_of',
                'WC.id as wc_id',
                'WC.week_no',
                'WC.from_date',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as fromDate',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as toDate',
            );

            $join = array(
                'weekly_calendar WC'    =>      'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->join($join)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            // view details up to the selected week no
            $initQuery = ($week_no)    ? $initQuery->logicEx('AND WC.week_no <= :week_no') : $initQuery;

            return $initQuery;
        }

        public function selectReportPerWeekNo($project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
                'IF(PR.revision_no IS NULL, "!with_plan", "with_plan") as plan_stat',
                // 'MAX(PR.revision_no) as revision_no',
                // 'IF(MAX(PR.revision_no) IS NULL, "!with_plan", "with_plan") as plan_stat',
                'IF(PR.status IS NULL, "!with_actual", "with_actual") as status'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportDetails($progress_report_id = false, $work_item_id = false, $id = false)
        {
            $fields = array(
                'PRW.id',
                'PRW.progress_report_id',
                'PRW.work_item_id',
                'PRW.weekly_wv_plan as plan_wv',
                'PRW.wv_total as actual_wv',
                'PRW.plan_project_weight as plan_percentage',
                'PRW.devt_project_weight as actual_percentage',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->where(array('PRW.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRW.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id)       ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id'))             : $initQuery;
            $initQuery = ($id)                 ? $initQuery->andWhere(array('PRW.id' => ':id'))                                 : $initQuery;

            return $initQuery;
        }

        public function selectDailyProgressReports($project_id = false, $weekly_calendar_id = false, $date = false, $progress_report_id = false)
        {
            $fields = array(
                'DPR.id',
                'DPR.progress_report_id',
                'DPR.date',
                'DATE_FORMAT(DPR.date, "%M %d, %Y") as activity_date',
                'DPR.id as pr_id',
                'DPR.project_id',
                'DPR.weekly_calendar_id',
            );

            /* $join = array(
                'progress_reports PR'   =>  'PR.id = DPR.progress_report_id'
            ); */

            $initQuery = $this->select($fields)
                              ->from('daily_progress_reports DPR')
                              ->where(array('DPR.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('DPR.project_id' => ':project_id'))                 : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('DPR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($date)                ? $initQuery->andWhere(array('DPR.date' => ':date'))                             : $initQuery;
            $initQuery = ($progress_report_id)  ? $initQuery->andWhere(array('DPR.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDprMaterials($progress_report_id = false, $date = false)
        {
            $fields = array(
                'DM.id',
                'DM.progress_report_id',
                'DM.project_id',
                'DM.weekly_calendar_id',
                'DM.date',
            );

            $initQuery = $this->select($fields)
                              ->from('dpr_materials DM')
                              ->where(array('DM.is_active' => ':is_active'));

            $initQuery = ($progress_report_id)  ? $initQuery->andWhere(array('DM.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($date)                ? $initQuery->andWhere(array('DM.date' => ':date'))                             : $initQuery;

            return $initQuery;
        }

        public function selectHeavyEquipments($id = false)
        {
            $fields = [
                'HE.id',
                'HE.body_no',
                'HE.cost_code',
                'HE.brand',
                'HE.model',
                'HE.description',
                'CONCAT(HE.capacity, " ", HE.c_unit) as capacities',
                'ET.name as equipment_type',
                'ET.unit as equipment_unit',
                '"HE" as type'
            ];

            $joins = [
                'equipment_types ET'    => 'ET.id = HE.equipment_type_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('heavy_equipments HE')
                              ->join($joins)
                              ->where(['HE.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['HE.id' => ':id']) : $initQuery;

            return $initQuery;

        }

        public function selectLightEquipments($id = false)
        {
            $fields = [
                'SE.id',
                'SE.model',
                'SE.cost_code',
                'ET.name as equipment_type',
                'ET.unit as equipment_unit',
                '"SE" as type'
            ];

            $joins = [
                'equipment_types ET'    => 'ET.id = SE.equipment_type_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('small_equipments SE')
                              ->join($joins)
                              ->where(['SE.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['SE.id' => ':id']) : $initQuery;

            return $initQuery;

        }

        public function selectFilteredSummary($project_id = false, $weekly_calendar_id = false, $work_item_id = false, $date = false)
        {
            $fields = array(
                'DRD.daily_progress_report_id',
                'DRD.progress_report_weekly_detail_id',
                'DRD.actual_work_volume',
                'DPR.progress_report_id',
                'DPR.project_id',
                'DPR.weekly_calendar_id',
                'DATE_FORMAT(DPR.date, "%b %d, %Y") as date',
                'PRW.work_item_id',
                '(SELECT project_code FROM projects WHERE id = DPR.project_id) as project_code',
            );

            /* $str1 = 'MIN(DATE_FORMAT(DPR.date, "%b %d, %Y")) as fromDate';
            $str2 = 'IF(COUNT(*) != 1, MAX(DATE_FORMAT(DPR.date, "%b %d, %Y")), "") as toDate';
            if((!$weekly_calendar_id) || ($weekly_calendar_id == true && !$date)) {
                array_push($fields, $str1);
                array_push($fields, $str2);
            } */

            $leftJoins = array(
                'daily_progress_reports DPR'            =>  'DPR.id = DRD.daily_progress_report_id',
                'progress_report_weekly_details PRW'    =>  'PRW.id = DRD.progress_report_weekly_detail_id'
            );

            $initQuery = $this->select($fields)
                              ->from('daily_progress_report_details DRD')
                              ->leftJoin($leftJoins)
                              ->where(array('DRD.is_active' => ':is_active', 'DPR.is_active' => ':is_active', 'PRW.is_active' => ':is_active'));
            
            $initQuery = ($project_id)         ? $initQuery->andWhere(array('DPR.project_id' => ':project_id'))                 : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('DPR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($work_item_id)       ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id'))             : $initQuery;
            $initQuery = ($date)               ? $initQuery->andWhere(array('DPR.date' => ':date'))                             : $initQuery;

            return $initQuery;
        }

        public function selectFilteredMaterialData($project_id = false, $weekly_calendar_id = false, $work_item_id = false, $date = false, $msb_id = false, $unit = false)
        {
            $fields = array(
                'DMD.dpr_material_id',
                'DMD.progress_report_material_id',
                'DMD.consumed_qty',
                'DMD.unit',
                'DM.progress_report_id',
                'DM.project_id',
                'DM.weekly_calendar_id',
                'DATE_FORMAT(DM.date, "%b %d, %Y") as date',
                'PRM.work_item_id',
                'PRM.material_specification_brand_id as msb_id',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = PRM.material_specification_brand_id LIMIT 1) as material_code',
                '(SELECT project_code FROM projects WHERE id = DM.project_id) as project_code',
                'M.name as description',
                'MS.specs as specification',
            );

            /* $str1 = 'MIN(DATE_FORMAT(DM.date, "%b %d, %Y")) as fromDate';
            $str2 = 'IF(COUNT(*) != 1, MAX(DATE_FORMAT(DM.date, "%b %d, %Y")), "") as toDate';
            if((!$weekly_calendar_id) || ($weekly_calendar_id == true && !$date)) {
                array_push($fields, $str1);
                array_push($fields, $str2);
            } */

            $leftJoins = array(
                'dpr_materials DM'                  =>  'DM.id = DMD.dpr_material_id',
                'progress_report_materials PRM'     =>  'PRM.id = DMD.progress_report_material_id'
            );

            $joins = array(
                'material_specification_brands MSB'     =>      'MSB.id = PRM.material_specification_brand_id',
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id',
            );

            $initQuery = $this->select($fields)
                              ->from('dpr_material_details DMD')
                              ->leftJoin($leftJoins)
                              ->join($joins)
                              ->where(array('DMD.is_active' => ':is_active', 'DM.is_active' => ':is_active', 'PRM.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('DM.project_id' => ':project_id'))                     : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('DM.weekly_calendar_id' => ':weekly_calendar_id'))     : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('PRM.work_item_id' => ':work_item_id'))                : $initQuery;
            $initQuery = ($date)                ? $initQuery->andWhere(array('DM.date' => ':date'))                                 : $initQuery;
            $initQuery = ($msb_id)              ? $initQuery->andWhere(array('PRM.material_specification_brand_id' => ':msb_id'))   : $initQuery;
            $initQuery = ($unit)                ? $initQuery->andWhere(array('DMD.unit' => ':unit'))                                : $initQuery;

            return $initQuery;
        }

        public function selectFilteredEquipmentData($project_id = false, $weekly_calendar_id = false, $work_item_id = false, $date = false, $heavy_equipment_id = false, $small_equipment_id = false, $condition = false)
        {
            $fields = array(
                'EA.id',
                'EA.ps_swi_direct_id',
                'EA.p_wi_indirect_id',
                'IF(EA.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'ER.project_id',
                'ER.heavy_equipment_id',
                'ER.small_equipment_id',
                'ER.week_calendar_id as weekly_calendar_id',
                'DATE_FORMAT(ER.activity_date, "%b %d, %Y") as date',
                '(SELECT project_code FROM projects WHERE id = ER.project_id) as project_code',
                'IF(ER.heavy_equipment_id IS NULL, "SE", "HE") as type',
                'IF(ER.heavy_equipment_id IS NULL, (SELECT cost_code FROM small_equipments WHERE id = ER.small_equipment_id), (SELECT cost_code FROM heavy_equipments WHERE id = ER.heavy_equipment_id)) as cost_code'
            );

            $str1 = 'MIN(DATE_FORMAT(ER.activity_date, "%b %d, %Y")) as fromDate';
            $str2 = 'IF(COUNT(*) != 1, MAX(DATE_FORMAT(ER.activity_date, "%b %d, %Y")), "") as toDate';
            if((!$weekly_calendar_id) || ($weekly_calendar_id == true && !$date)) {
                array_push($fields, $str1);
                array_push($fields, $str2);
            }

            $joins = array(
                'ea_reports ER'         =>  'ER.id = EA.ear_id',
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = EA.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = EA.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('ear_activities EA')
                              ->join($joins)
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('EA.is_active' => ':is_active', 'ER.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('ER.project_id' => ':project_id'))                     : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('ER.week_calendar_id' => ':weekly_calendar_id'))       : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('IF(EA.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id'))  : $initQuery;
            // $initQuery = ($ps_swi_direct_id)    ? $initQuery->andWhere(array('EA.ps_swi_direct_id' => ':ps_swi_direct_id'))         : $initQuery;
            // $initQuery = ($p_wi_indirect_id)    ? $initQuery->andWhere(array('EA.p_wi_indirect_id' => ':p_wi_indirect_id'))         : $initQuery;
            $initQuery = ($date)                ? $initQuery->andWhere(array('ER.activity_date' => ':date'))                        : $initQuery;
            $initQuery = ($heavy_equipment_id)  ? $initQuery->andWhere(array('ER.heavy_equipment_id' => ':heavy_equipment_id'))     : $initQuery;
            $initQuery = ($small_equipment_id)  ? $initQuery->andWhere(array('ER.small_equipment_id' => ':small_equipment_id'))     : $initQuery;

            return $initQuery;
        }

        public function selectFilteredLaborData($project_id = false, $weekly_calendar_id = false, $work_item_id = false, $date = false, $position_type = false, $condition = false)
        {
            $fields = array(
                'DA.id',
                'DA.ps_swi_direct_id',
                'DA.p_wi_indirect_id',
                'IF(DA.ps_swi_direct_id IS NULL, WIs.id, WI.id) as work_item_id',
                'DA.form_type',
                'DR.project_id',
                '(SELECT project_code FROM projects WHERE id = DR.project_id) as project_code',
                'DR.requisition_manpower_id',
                'DR.week_calendar_id as weekly_calendar_id',
                'DATE_FORMAT(DR.activity_date, "%b %d, %Y") as date',
                'CONCAT(RM.fname, " ", RM.lname) as laborer_name',
                'PL.position_id',
                'P.name as position_name',
                'P.position_type',
                'IF(P.position_type = 1, "Skilled", "Common") as pos_type'
            );

            $str1 = 'MIN(DATE_FORMAT(DR.activity_date, "%b %d, %Y")) as fromDate';
            $str2 = 'IF(COUNT(*) != 1, MAX(DATE_FORMAT(DR.activity_date, "%b %d, %Y")), "") as toDate';
            if((!$weekly_calendar_id) || ($weekly_calendar_id == true && !$date)) {
                array_push($fields, $str1);
                array_push($fields, $str2);
            }

            $joins = array(
                'da_reports DR'             =>  'DR.id = DA.dar_id',
                'requisition_manpower RM'   =>  'RM.id = DR.requisition_manpower_id',
                'pr_labors PL'              =>  'PL.id = RM.pr_labor_id',
                'positions P'               =>  'P.id  = PL.position_id'
            );

            $joinsDirect = array(
                'ps_swi_directs PSD'       => 'PSD.id = DA.ps_swi_direct_id',
                'sw_wis SWI'               => 'SWI.id = PSD.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            );

            $joinsIndirect = array(
                'p_wi_indirects PWI'        => 'PWI.id = DA.p_wi_indirect_id',
                'work_items WIs'            => 'WIs.id = PWI.work_item_id',
                'work_item_categories WICs' => 'WICs.id = WIs.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('dar_activities DA')
                              ->join($joins)
                              ->leftJoin($joinsDirect)
                              ->leftJoin($joinsIndirect)
                              ->where(array('DA.is_active' => ':is_active', 'DR.is_active' => ':is_active', 'DR.status' => ':status'))
                              ->andWhereNull(array('DA.form_type'))
                              ->andWhereNotNull(array('P.position_type'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('DR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('DR.week_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('IF(DA.ps_swi_direct_id IS NULL, WIs.id, WI.id)' => ':work_item_id')) : $initQuery;
            // $initQuery = ($ps_swi_direct_id)    ? $initQuery->andWhere(array('DA.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
            // $initQuery = ($p_wi_indirect_id)    ? $initQuery->andWhere(array('DA.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;
            $initQuery = ($date)                ? $initQuery->andWhere(array('DR.activity_date' => ':date')) : $initQuery;
            $initQuery = ($position_type)       ? $initQuery->andWhere(array('P.position_type' => ':position_type')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectMaterialInventories($project_id = false, $msb_id = false, $unit = false)
        {
            $fields = array(
                'PMI.id',
                'PMI.project_id',
                'PMI.material_specification_brand_id',
                'PMI.quantity',
                'PMI.unit',
            );

            $initQuery = $this->select($fields)
                              ->from('project_material_inventories PMI')
                              ->where(array('PMI.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PMI.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($msb_id)     ? $initQuery->andWhere(array('PMI.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit)       ? $initQuery->andWhere(array('PMI.unit' => ':unit')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectEmployees
         *
         * @return void
         */
        public function selectEmployees($id = false, $department_id = false, $user_id = false)
        {
            $fields = array(
                'PI.id',
                'EI.position_id',
                'P.department_id',
                'P.name as position_name',
                'D.charging',
                'D.name as department_name',
                'CONCAT(PI.fname," ",LEFT(PI.mname,1),". ",PI.lname) as fullname',
                'U.id as user_id'
            );

            $join = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id',
                'users U'                    => 'U.personal_information_id = PI.id'
            );

            $initQuery = $this->select($fields)
                         ->from('personal_informations PI')
                         ->leftJoin($join)
                         ->where(array('PI.is_active' => ':status'));

            $initQuery = ($id)              ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;
            $initQuery = ($department_id)   ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($user_id)         ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;
 
            return $initQuery;
        }

        public function selectSubmittedProgressReport($progress_report_id = false, $project_id = false)
        {
            $fields = array(
                'SPR.id',
                'SPR.progress_report_id',
                'SPR.remarks',
                'SPR.status',
                'SPR.created_by',
                'DATE_FORMAT(SPR.created_at, "%b %e, %Y %r") as date_submitted',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
                'DATE_FORMAT(PR.as_of, "%b %d, %Y") AS as_of',
                'WC.week_no', 
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") AS week_from_date', 
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") AS week_to_date',
                'DATE_FORMAT(WC.to_date, "%M") as month', 
                'DATE_FORMAT(WC.to_date, "%d") as day', 
                'DATE_FORMAT(WC.to_date, "%Y") as year'
            );

            $joins = [
                'progress_reports PR'   =>  'PR.id = SPR.progress_report_id',
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('submitted_progress_reports SPR')
                              ->join($joins)
                              ->where(array('SPR.is_active' => ':is_active'));

            $initQuery = ($progress_report_id)  ? $initQuery->andWhere(array('SPR.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($project_id)          ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportSignatories($progress_report_id = false)
        {
            $fields = array(
                'PRS.id',
                'PRS.progress_report_id',
                'PRS.user_id',
                'PRS.is_approved',
                'IF(PRS.is_approved IS NULL, "Pending", IF(PRS.is_approved = 1, "Approved", "Disapproved")) as approval_status',
                'PRS.remarks',
                'IF(PRS.is_approved IS NOT NULL, DATE_FORMAT(PRS.updated_at, "%b %e, %Y %r"), "") as date_approved',
                'CONCAT(PI.fname," ",LEFT(PI.mname,1),". ",PI.lname) as fullname',
                'P.name as position_name',
                'D.name as department_name',
            );

            $joins = array(
                'users U'                       =>      'U.id = PRS.user_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_signatories PRS')
                              ->join($joins)
                              ->where(array('PRS.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRS.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectActualDates($prwd_id = false)
        {
            $fields = [
                'MIN(DATE_FORMAT(DPR.date, "%d-%b-%Y")) as actual_from_date',
                'MAX(DATE_FORMAT(DPR.date, "%d-%b-%Y")) as actual_to_date',
            ];

            $join = [
                'daily_progress_reports DPR'    =>      'DPR.id = DPRD.daily_progress_report_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('daily_progress_report_details DPRD')
                              ->join($join)
                              ->where(['DPRD.is_active' => ':is_active', 'DPR.is_active' => ':is_active']);

            $initQuery = ($prwd_id) ? $initQuery->andWhere(['DPRD.progress_report_weekly_detail_id' => ':prwd_id']) : $initQuery;

            return $initQuery;
        }

        public function selectDataPerRevision($project_id = false, $weekly_calendar_id = false, $revision_no = false, $work_item_id = false, $id = false)
        {
            $fields = array(
                'PRWD.id',
                'PRWD.work_item_id',
                'PRWD.weekly_wv_plan as plan_wv',
                'PRWD.plan_project_weight as plan_percentage',
                'PRWD.plan_project_weight',
                'PRWD.td_plan_proj_weight',
                'PRWD.wv_total',
                'PRWD.devt_project_weight',
                'PRWD.devt_item_weight',
                'PRWD.td_project_weight',
                'PRWD.td_item_weight',
                'PRWD.td_work_volume',
                'PRWD.td_wv_balance',
                'PRWD.entry_type',
                'PRWD.actual_revision_no',
                'PRWD.is_with_revision',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRWD')
                              ->join(array('progress_reports PR' => 'PR.id = PRWD.progress_report_id'))
                              ->where(array('PRWD.is_active' => ':is_active', 'PR.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($revision_no)         ? $initQuery->andWhere(array('PR.revision_no' => ':revision_no')) : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('PRWD.work_item_id' => ':work_item_id')) : $initQuery;
            $initQuery = ($id)                  ? $initQuery->andWhere(array('PRWD.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectDailyProgressReportDetails($daily_progress_report_id = false, $prwd_id = false)
        {
            $fields = array(
                'DPRD.id',
                'DPRD.daily_progress_report_id',
                'DPRD.progress_report_weekly_detail_id',
                'DPRD.work_item_id',
                'DPRD.actual_work_volume',
                'PR.project_id',
                'PR.project_revision_no',
                '"saved" as data_status'
            );

            $joins = array(
                'daily_progress_reports DPR'    =>  'DPR.id = DPRD.daily_progress_report_id',
                'progress_reports PR'           =>  'PR.id = DPR.progress_report_id'
            );

            $initQuery = $this->select($fields)
                              ->from('daily_progress_report_details DPRD')
                              ->join($joins)
                              ->where(array('DPRD.is_active' => ':is_active', 'DPR.is_active' => ':is_active', 'PR.is_active' => ':is_active'));

            $initQuery = ($daily_progress_report_id) ? $initQuery->andWhere(array('DPRD.daily_progress_report_id' => ':dpr_id')) : $initQuery;
            $initQuery = ($prwd_id)                  ? $initQuery->andWhere(array('DPRD.progress_report_weekly_detail_id' => ':prwd_id')) : $initQuery;

            return $initQuery;
        }

        public function selectActualProjectStartDate()
        {
            $fields = array(
                'DPR.project_id',
                'DPR.weekly_calendar_id',
                'DATE_FORMAT(DPR.date, "%b %d, %Y") as date',
            );

            $initQuery = $this->select($fields)
                              ->from('daily_progress_reports DPR')
                              ->where(array('DPR.is_active' => ':is_active', 'DPR.project_id' => ':project_id'))
                              ->orderBy('DPR.date', 'ASC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectOverrideRequests($id = false, $project_id = false)
        {
            $fields = array(
                'PROR.id',
                'PROR.progress_report_id',
                'PROR.justification',
                'PROR.approved_by',
                'PROR.created_by',
                'DATE_FORMAT(PROR.created_at, "%b %d, %Y") as created_at',
                'DATE_FORMAT(PROR.approved_at, "%b %d, %Y %r") as approved_at',
                'PROR.status',
                'PROR.approver_remarks',
                'PROR.is_updated',
                'PR.project_id',
                'PR.weekly_calendar_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_override_requests PROR')
                              ->join(array('progress_reports PR' => 'PR.id = PROR.progress_report_id'))
                              ->where(array('PROR.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'PROR.created_by' => ':created_by'));
                            //   ->andWhereNull(array('PROR.is_updated'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('PROR.id' => ':id')) : $initQuery;
            $initQuery = ($project_id)  ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;

            return $initQuery;                
        }

        public function insertProgressReport($data = array())
        {
            $initQuery = $this->insert('progress_reports', $data);

            return $initQuery;
        }

        public function insertProgressReportWeeklyDetails($data = array())
        {
            $initQuery = $this->insert('progress_report_weekly_details', $data);

            return $initQuery;
        }

        public function insertProgressStatusMaterials($data = array())
        {
            $initQuery = $this->insert('progress_status_materials', $data);

            return $initQuery;
        }

        public function insertProgressReportMaterials($data = array())
        {
            $initQuery = $this->insert('progress_report_materials', $data);

            return $initQuery;
        }

        public function insertProgressReportAttachment($data = array())
        {
            $initQuery = $this->insert('progress_report_attachments', $data);

            return $initQuery;
        }

        public function insertProgressReportLabors($data = array())
        {
            $initQuery = $this->insert('progress_report_labors', $data);

            return $initQuery;
        }

        public function insertProgressReportLaborItems($data = array())
        {
            $initQuery = $this->insert('progress_report_labor_items', $data);

            return $initQuery;
        }

        public function insertProgressReportLaborItemDetails($data = array())
        {
            $initQuery = $this->insert('progress_report_labor_item_details', $data);

            return $initQuery;
        }

        public function insertProgressReportEquipment($data = array())
        {
            $initQuery = $this->insert('progress_report_equipments', $data);

            return $initQuery;
        }

        public function insertProgressReportEquipmentItems($data = array())
        {
            $initQuery = $this->insert('progress_report_equipment_items', $data);

            return $initQuery;
        }

        public function insertProgressReportEquipmentWiItems($data = array())
        {
            $initQuery = $this->insert('progress_report_equipment_wi_items', $data);

            return $initQuery;
        }

        public function insertDailyProgressReports($data = array())
        {
            $initQuery = $this->insert('daily_progress_reports', $data);

            return $initQuery;
        }

        public function insertDailyProgressReportDetails($data = array())
        {
            $initQuery = $this->insert('daily_progress_report_details', $data);

            return $initQuery;
        }

        public function insertDprMaterials($data = array())
        {
            $initQuery = $this->insert('dpr_materials', $data);

            return $initQuery;
        }

        public function insertDprMaterialDetails($data = array())
        {
            $initQuery = $this->insert('dpr_material_details', $data);

            return $initQuery;
        }

        public function insertProjectMaterialInventory($data = array())
        {
            $initQuery = $this->insert('project_material_inventories', $data);

            return $initQuery;
        }

        public function insertProjectMaterialInventoryHistory($data = array())
        {
            $initQuery = $this->insert('project_material_inventory_histories', $data);

            return $initQuery;
        }

        public function insertProgressReportSignatories($data = array())
        {
            $initQuery = $this->insert('progress_report_signatories', $data);

            return $initQuery;
        }

        public function insertSubmittedProgressReports($data = array())
        {
            $initQuery = $this->insert('submitted_progress_reports', $data);

            return $initQuery;
        }

        public function insertOverrideRequests($data = array())
        {
            $initQuery = $this->insert('progress_report_override_requests', $data);

            return $initQuery;
        }

        public function insertProgressReportWdRevisions($data = array())
        {
            $initQuery = $this->insert('progress_report_wd_revisions', $data);

            return $initQuery;
        }

        public function insertProgressReportWdRevisionDetails($data = array())
        {
            $initQuery = $this->insert('progress_report_wd_revision_details', $data);

            return $initQuery;
        }

        public function updateProgressReport($id = '', $data = array())
        {
            $initQuery = $this->update('progress_reports', $id, $data);

            return $initQuery;
        }

        public function updateProgressReportWeeklyDetails($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_weekly_details', $id, $data);

            return $initQuery;
        }

        public function updateDailyProgressReportDetails($id = '', $data = array())
        {
            $initQuery = $this->update('daily_progress_report_details', $id, $data);

            return $initQuery;
        }

        public function updateLaborProgressReport($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_labor_item_details', $id, $data);

            return $initQuery;
        }
        
        public function updateProgressReportEquipment($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_equipments', $id, $data);

            return $initQuery;
        }

        public function updateProgressReportMaterials($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_materials', $id, $data);

            return $initQuery;
        }

        public function updateProjectMaterialInventory($id = '', $data = array())
        {
            $initQuery = $this->update('project_material_inventories', $id, $data);

            return $initQuery;
        }

        public function updateProgressReportAttachment($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_attachments', $id, $data);

            return $initQuery;
        }

        public function updateProgressReportOverrideRequest($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_override_requests', $id, $data);

            return $initQuery;
        }
    }