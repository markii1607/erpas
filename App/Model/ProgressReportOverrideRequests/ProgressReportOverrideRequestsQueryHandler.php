<?php
    namespace App\Model\ProgressReportOverrideRequests;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProgressReportOverrideRequestsQueryHandler extends QueryHandler {

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
                'PA.user_id',
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

            $join = array(
                'projects P'  =>  'P.id = PA.project_id'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->join($join)
                              ->where(array('PA.is_active' => ':is_active', 'PA.user_id' => ':user_id'));

            return $initQuery;
        }

        public function selectDailyProgressReports($project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'DPR.id',
                'DPR.revision_no',
                'DPR.progress_report_id',
                'DPR.project_id',
                'DPR.weekly_calendar_id',
                'DATE_FORMAT(DPR.date, "%M %d, %Y") as activity_date',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%M %d, %Y") as to_date',
            );

            $join = array(
                'weekly_calendar WC'    =>  'WC.id = DPR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('daily_progress_reports DPR')
                              ->join($join)
                              ->where(array('DPR.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('DPR.project_id' => ':project_id'))                 : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('DPR.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDprMaterials($project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'DM.id',
                'DM.revision_no',
                'DM.progress_report_id',
                'DM.project_id',
                'DM.weekly_calendar_id',
                'DATE_FORMAT(DM.date, "%M %d, %Y") as activity_date',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%M %d, %Y") as to_date',
            );

            $join = array(
                'weekly_calendar WC'    =>  'WC.id = DM.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('dpr_materials DM')
                              ->join($join)
                              ->where(array('DM.is_active' => ':is_active'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('DM.project_id' => ':project_id'))                 : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('DM.weekly_calendar_id' => ':weekly_calendar_id')) : $initQuery;

            return $initQuery;
        }

        public function selectLaReports($project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'LR.id',
                'LR.revision_no',
                // 'LR.progress_report_id',
                'LR.project_id',
                'LR.week_calendar_id',
                'DATE_FORMAT(LR.activity_date, "%M %d, %Y") as activity_date',
                'LR.status',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%M %d, %Y") as to_date',
            );

            $join = array(
                'weekly_calendar WC'    =>  'WC.id = LR.week_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('la_reports LR')
                              ->join($join)
                              ->where(array('LR.is_active' => ':is_active', 'LR.status' => ':status'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('LR.project_id' => ':project_id'))                 : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('LR.week_calendar_id' => ':weekly_calendar_id'))   : $initQuery;

            return $initQuery;
        }

        public function selectEaReports($project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'ER.id',
                'ER.revision_no',
                // 'ER.progress_report_id',
                'ER.project_id',
                'ER.week_calendar_id',
                'DATE_FORMAT(ER.activity_date, "%M %d, %Y") as activity_date',
                'ER.status',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%M %d, %Y") as to_date',
            );

            $join = array(
                'weekly_calendar WC'    =>  'WC.id = ER.week_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('ea_reports ER')
                              ->join($join)
                              ->where(array('ER.is_active' => ':is_active', 'ER.status' => ':status'));

            $initQuery = ($project_id)          ? $initQuery->andWhere(array('ER.project_id' => ':project_id'))                 : $initQuery;
            $initQuery = ($weekly_calendar_id)  ? $initQuery->andWhere(array('ER.week_calendar_id' => ':weekly_calendar_id'))   : $initQuery;

            return $initQuery;
        }

        public function selectUserProjectAccesses($project_id = false)
        {
            $fields = array(
                'PA.id',
                'PA.user_id',
                'PA.project_id',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as fullname',
                'P.name as position',
                'D.name as department'
            );

            $leftJoins = array(
                'users U'                       =>      'U.id = PA.user_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->leftJoin($leftJoins)
                              ->where(array('PA.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PA.project_id' => ':project_id')) : $initQuery;

            return $initQuery;
        }

        public function insertOverrideRequest($data = array())
        {
            $initQuery = $this->insert('progress_report_override_requests', $data);

            return $initQuery;
        }
    }
