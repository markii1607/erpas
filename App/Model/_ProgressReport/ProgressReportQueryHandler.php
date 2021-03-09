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
                'PRW.td_project_slippage',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
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
                'DATE_FORMAT(PRW.plan_start_date, "%d-%b-%Y") as plan_start_date',
                'DATE_FORMAT(PRW.plan_finish_date, "%d-%b-%Y") as plan_finish_date',
                'DATE_FORMAT(PRW.actual_start_date, "%d-%b-%Y") as actual_start_date',
                'DATE_FORMAT(PRW.actual_finish_date, "%d-%b-%Y") as actual_finish_date',
                'PRW.wv_total',
                'SUM(PRW.devt_project_weight) as devt_project_weight',
                'SUM(PRW.td_project_weight) as td_project_weight',
                'SUM(PRW.td_project_slippage) as td_project_slippage',
                'WI.id as wi_id',
                'WI.work_item_category_id'
            );

            $join = array(
                'work_items WI' =>  'WI.id = PRW.work_item_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->join($join)
                              ->where(array('PRW.is_active' => ':is_active'));
                            //   ->groupBy('WI.work_item_category_id');

            $initQuery = ($project_report_id) ? $initQuery->andWhere(array('PRW.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }
        
        
        public function selectProjects($id = false)
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
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(array('P.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectWeeklyCalendar($date = false, $id = false, $to_date = false)
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

            $initQuery = ($date)  ? $initQuery->logicEx('AND WC.from_date <= :date_from')  : $initQuery;
            $initQuery = ($date)  ? $initQuery->logicEx('AND WC.to_date >= :date_from')    : $initQuery;
            $initQuery = ($id)    ? $initQuery->andWhere(array('WC.id' => ':id')) : $initQuery;
            $initQuery = ($to_date) ? $initQuery->andWhere(array('WC.to_date' => ':to_date')) : $initQuery;

            return $initQuery;
        }

        public function selectItemCodesDirect($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PW.project_id',
                'WI.id',
                'WI.wbs',
                'WI.item_no',
                'WI.name as item_name',
                'WIC.id as wic_id',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
                'PD.quantities as work_volume',
                'IF(SW.unit IS NULL, WI.unit, SW.unit) as unit',
                '"0.4589" as weight_factor'
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

            return $initQuery;
        }

        public function selectItemCodesIndirect($project_id = false, $work_item_id = false)
        {
            $fields = array(
                'PWI.project_id',
                'PWI.quantities as work_volume',
                'WI.id',
                'WI.wbs',
                'WI.item_no',
                'WI.name as item_name',
                'WI.unit',
                'WIC.id as wic_id',
                'CONCAT("PART ", WIC.part) as part_code',
                'WIC.name as part_name',
                '"0.4589" as weight_factor'
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

            return $initQuery;
        }

        public function selecProgressReportAttachments($progress_report_id = false)
        {
            $fields = array(
                'PRA.id',
                'PRA.progress_report_id',
                'PRA.file_name',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_attachments PRA')
                              ->where(array('PRA.is_active' => ':is_active'));
                            //   ->orderBy('PRA.file_name', 'DESC')
                            //   ->limit(1);

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRA.progress_report_id' => ':progress_report_id')) : $initQuery;

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

        public function insertProgressReportAttachment($data = array())
        {
            $initQuery = $this->insert('progress_report_attachments', $data);

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
    }
