<?php
    namespace App\Model\CcProgressReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class CcProgressReportQueryHandler extends QueryHandler {

        public function selectProjects()
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.revision_no'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->join(array('projects P' => 'P.id = PA.project_id'))
                              ->where(array('PA.is_active' => ':is_active', 'PA.user_id' => ':user_id'));

            return $initQuery;
        }

        public function selectProgressReports($project_id = false)
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

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportWeeklyDetails($work_item_id = false, $progress_report_id = false, $project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'PRWD.id',
                'PRWD.progress_report_id',
                'PRWD.work_item_id',
                'PRWD.weekly_wv_plan as plan_qty',
                'PRWD.plan_project_weight as plan_proj_wt',
                'PRWD.wv_total as actual_qty',
                'PRWD.devt_item_weight as actual_item_wt',
                'PRWD.devt_project_weight as actual_proj_wt',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.project_revision_no',
                'PR.revision_no as doc_revision_no',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRWD')
                              ->join(array('progress_reports PR' => 'PR.id = PRWD.progress_report_id'))
                              ->where(array('PRWD.is_active' => ':is_active', 'PR.is_active' => ':is_active'));

            $initQuery = ($work_item_id)        ? $initQuery->andWhere(array('PRWD.work_item_id' => ':work_item_id')) : $initQuery;
            $initQuery = ($progress_report_id)  ? $initQuery->andWhere(array('PRWD.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($project_id)          ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;


            return $initQuery;
        }

        public function selectLatestRevision($project_id = false, $weekly_calendar_id = false)
        {
            $fields = [
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
            ];

            $initQuery = $this->select($fields)
                              ->from('progress_reports PR')
                              ->where(['PR.is_active' => ':is_active']);

            $initQuery = ($project_id)          ? $initQuery->andWhere(['PR.project_id' => ':project_id']) : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(['PR.weekly_calendar_id' => ':week_id']) : $initQuery;

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

    }