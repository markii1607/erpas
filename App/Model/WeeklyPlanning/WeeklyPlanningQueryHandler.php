<?php
    namespace App\Model\WeeklyPlanning;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class WeeklyPlanningQueryHandler extends QueryHandler {
        
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
                'transactions T'    =>      'T.id = P.transaction_id'
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                            //   ->join($join)
                            //   ->where(array('P.is_active' => ':is_active', 'T.status' => ':status'));
                              ->where(array('P.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;
            $initQuery = ($project_manager) ? $initQuery->andWhere(array('P.project_manager' => ':head_id')) : $initQuery;

            return $initQuery;
        }

        public function selectWeeklyCalendar($id = false, $to_date = false, $from_date = false)
        {
            $fields = array(
                'WC.id',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%d-%b-%Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%d-%b-%Y") as to_date',
            );

            $initQuery = $this->select($fields)
                              ->from('weekly_calendar WC')
                              ->where(array('WC.is_active' => ':is_active'));

            // $initQuery = ($date)  ? $initQuery->logicEx('AND WC.from_date <= :date_from')  : $initQuery;
            // $initQuery = ($date)  ? $initQuery->logicEx('AND WC.to_date >= :date_from')    : $initQuery;
            $initQuery = ($id)    ? $initQuery->andWhere(array('WC.id' => ':id')) : $initQuery;
            $initQuery = ($to_date) ? $initQuery->andWhere(array('WC.to_date' => ':to_date')) : $initQuery;
            $initQuery = ($from_date) ? $initQuery->andWhere(array('WC.from_date' => ':from_date')) : $initQuery;

            return $initQuery;
        }

        public function selectItemCodesDirect($project_id = false, $work_item_id = false, $revision_no = false)
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
                'PD.quantities as work_volume',
                'PD.weight_factor',
                'PD.revision_no',
                'SW.wbs',
                'IF(SW.unit IS NULL, WI.unit, SW.unit) as unit',
                // '"0.4589" as weight_factor'
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
            $initQuery = ($revision_no)  ? $initQuery->andWhere(['PD.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        public function selectItemCodesIndirect($project_id = false, $work_item_id = false, $revision_no = false)
        {
            $fields = array(
                'PWI.project_id',
                'PWI.quantities as work_volume',
                'PWI.weight_factor',
                'PWI.revision_no',
                'WI.id',
                'WI.wbs',
                'WI.item_no',
                'WI.name as item_name',
                'WI.unit',
                'WIC.id as wic_id',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
                // '"0.4589" as weight_factor'
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
            $initQuery = ($revision_no)  ? $initQuery->andWhere(['PWI.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        public function selectProjectProgressReports($id = false, $project_id = false, $weekly_calendar_id = false, $revision_no = false, $week_to_date = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.project_revision_no',
                '(SELECT project_code FROM projects WHERE id = PR.project_id AND is_active = 1) as project_code',
                'PR.weekly_calendar_id',
                'PR.doc_no',
                'PR.status',
                'PR.revision_no',
                'IF(PR.as_of IS NULL, "", DATE_FORMAT(PR.as_of, "%b-%d-%Y %r")) as as_of',
                'PR.created_by',
                'WC.week_no',
                'WC.from_date',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as week_to_date',
                'DATE_FORMAT(WC.to_date, "%M") as month',
                'DATE_FORMAT(WC.to_date, "%d") as day',
                'DATE_FORMAT(WC.to_date, "%Y") as year'
            );

            $join = array(
                'weekly_calendar WC' => 'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->join($join)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;
            $initQuery = ($revision_no) ? $initQuery->andWhere(array('PR.revision_no' => ':revision_no')) : $initQuery;
            $initQuery = ($week_to_date) ? $initQuery->logicEx('AND WC.to_date > :week_to_date') : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportWeeklyDetails($project_report_id = false, $work_item_id = false)
        {
            $fields = array(
                'PRW.id',
                'PRW.progress_report_id',
                'PRW.work_item_id',
                'PRW.weekly_wv_plan',
                'PRW.weekly_wv_plan as prev_wv_plan',
                'PRW.plan_project_weight',
                'PRW.td_plan_proj_weight',
                'PRW.plan_start_date as prev_start_date',
                'PRW.plan_finish_date as prev_finish_date',
                'DATE_FORMAT(PRW.plan_start_date, "%d-%b-%Y") as plan_start_date',
                'DATE_FORMAT(PRW.plan_finish_date, "%d-%b-%Y") as plan_finish_date',
                'DATE_FORMAT(PRW.actual_start_date, "%d-%b-%Y") as actual_start_date',
                'DATE_FORMAT(PRW.actual_finish_date, "%d-%b-%Y") as actual_finish_date',
                'PRW.wv_total',
                'PRW.devt_project_weight',
                'PRW.devt_item_weight',
                'PRW.td_project_weight',
                'PRW.td_item_weight',
                'PRW.td_work_volume',
                'PRW.td_wv_balance',
                // 'PRW.td_project_slippage',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->where(array('PRW.is_active' => ':is_active'));

            $initQuery = ($project_report_id) ? $initQuery->andWhere(array('PRW.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportDetails($project_id = false, $project_revision_no = false, $work_item_id = false, $weekly_calendar_id = false, $doc_revision = false)
        {
            $fields = array(
                'PRW.id',
                'PRW.progress_report_id',
                'PRW.work_item_id',
                'PRW.weekly_wv_plan',
                'PRW.plan_project_weight',
                'PRW.td_plan_proj_weight',
                'DATE_FORMAT(PRW.plan_start_date, "%d-%b-%Y") as plan_start_date',
                'DATE_FORMAT(PRW.plan_finish_date, "%d-%b-%Y") as plan_finish_date',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.project_revision_no',
                'PR.revision_no as doc_revision_no',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->join(array('progress_reports PR' => 'PR.id = PRW.progress_report_id'))
                              ->where(array('PRW.is_active' => ':is_active', 'PR.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('PR.project_id' => ':project_id'))                     : $initQuery;
            $initQuery = ($project_revision_no) ? $initQuery->andWhere(array('PR.project_revision_no' => ':project_revision_no'))   : $initQuery;
            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id'))                : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id'))     : $initQuery;
            $initQuery = ($doc_revision)        ? $initQuery->andWhere(array('PR.revision_no' => ':doc_revision'))                  : $initQuery;

            return $initQuery;
        }

        public function selectProgressStatusMaterials($progress_report_id = false)
        {
            $fields = array(
                'PSM.id',
                'PSM.progress_report_id',
                'PSM.doc_no',
                'PSM.created_by',
                'PSM.created_at',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_status_materials PSM')
                              ->where(array('PSM.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PSM.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportMaterials($progress_status_material_id = false, $progress_report_id = false)
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
                'PRM.created_by',
                'PRM.created_at',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_materials PRM')
                              ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($progress_status_material_id) ? $initQuery->andWhere(array('PRM.progress_status_material_id' => ':progress_status_material_id')) : $initQuery;
            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRM.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDprMaterials($progress_report_id = false)
        {
            $fields = array(
                'DM.id',
                'DM.progress_report_id',
                'DM.project_id',
                'DM.weekly_calendar_id',
                'DM.date',
                'DM.created_by',
                'DM.created_at',
            );

            $initQuery = $this->select($fields)
                              ->from('dpr_materials DM')
                              ->where(array('DM.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('DM.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectEmployeeInfo($user_id = false)
        {
            $fields = array(
                'CONCAT(PI.fname, " ", LEFT(PI.mname, 1), ". ", PI.lname) as fullname',
                'P.name as position',
                'D.name as department'
            );

            $joins = array(
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id',
                'departments D'                 =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->leftJoin($joins)
                              ->where(array('U.is_active' => ':is_active'));

            $initQuery = ($user_id) ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProjectAccesses()
        {
            $fields = array(
                // 'PA.id',
                'PA.user_id',
                'PA.project_id',
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
                'projects P'  =>  'P.id = PA.project_id'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->join($join)
                              ->where(array('PA.is_active' => ':is_active', 'PA.user_id' => ':user_id'));

            return $initQuery;
        }

        public function selectDailyProgressReports($progress_report_id = false, $activity_date = false)
        {
            $fields = array(
                'DPR.id',
                'DPR.progress_report_id',
                'DPR.project_id',
                'DPR.weekly_calendar_id',
                'DPR.date',
                'DPR.created_by',
                'DPR.created_at',
            );

            $initQuery = $this->select($fields)
                              ->from('daily_progress_reports DPR')
                              ->where(array('DPR.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('DPR.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($activity_date)      ? $initQuery->andWhere(array('DPR.date' => ':date')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportAttachments($dpr_id = false)
        {
            $fields = array(
                'PRA.id',
                'PRA.daily_progress_report_id',
                'PRA.file_name',
                'PRA.created_by',
                'PRA.created_at',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_attachments PRA')
                              ->where(array('PRA.is_active' => ':is_active'));

            $initQuery = ($dpr_id) ? $initQuery->andWhere(array('PRA.daily_progress_report_id' => ':dpr_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDailyProgressReportDetails($dpr_id = false, $prwd_id = false, $pr_id = false)
        {
            $fields = array(
                'DPRD.id',
                'DPRD.daily_progress_report_id',
                'DPRD.progress_report_weekly_detail_id',
                'DPRD.work_item_id',
                'DPRD.actual_work_volume',
                'DPRD.created_by',
                'DPRD.created_at',
                'DPR.progress_report_id',
                'DPR.date',
            );

            $initQuery = $this->select($fields)
                              ->from('daily_progress_report_details DPRD')
                              ->join(array('daily_progress_reports DPR' => 'DPR.id = DPRD.daily_progress_report_id'))
                              ->where(array('DPRD.is_active' => ':is_active', 'DPR.is_active' => ':is_active'));

            $initQuery = ($dpr_id)  ? $initQuery->andWhere(array('DPRD.daily_progress_report_id' => ':dpr_id')) : $initQuery;
            $initQuery = ($prwd_id) ? $initQuery->andWhere(array('DPRD.progress_report_weekly_detail_id' => ':prwd_id')) : $initQuery;
            $initQuery = ($pr_id)   ? $initQuery->andWhere(array('DPR.progress_report_id' => ':progress_report_id')) : $initQuery;

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

        public function insertDailyProgressReports($data = array())
        {
            $initQuery = $this->insert('daily_progress_reports', $data);

            return $initQuery;
        }

        public function insertProgressReportAttachments($data = array())
        {
            $initQuery = $this->insert('progress_report_attachments', $data);

            return $initQuery;
        }

        public function insertDailyProgressReportDetails($data = array())
        {
            $initQuery = $this->insert('daily_progress_report_details', $data);

            return $initQuery;
        }

        public function updateProgressReport($id = '', $data = array())
        {
            $initQuery = $this->update('progress_reports', $id, $data);

            return $initQuery;
        }

        public function updatProgressReportWeeklyDetails($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_weekly_details', $id, $data);

            return $initQuery;
        }

        public function updateProgressStatusMaterial($id = '', $data = array())
        {
            $initQuery = $this->update('progress_status_materials', $id, $data);

            return $initQuery;
        }

        public function updateProgressReportMaterial($id = '', $data = array())
        {
            $initQuery = $this->update('progress_report_materials', $id, $data);

            return $initQuery;
        }

        public function updateDprMaterial($id = '', $data = array())
        {
            $initQuery = $this->update('dpr_materials', $id, $data);

            return $initQuery;
        }
    }
