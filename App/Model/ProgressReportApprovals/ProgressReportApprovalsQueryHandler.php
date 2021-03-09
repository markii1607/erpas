<?php
    namespace App\Model\ProgressReportApprovals;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProgressReportApprovalsQueryHandler extends QueryHandler {

        /**
         * PROGRESS REPORT (Prepared By PE)
         */

        public function selectProgressReports($is_approved = false, $project_id = false)
        {
            $fields = [
                'PRS.id',
                'PRS.progress_report_id',
                'PRS.user_id',
                'PRS.is_approved',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %e, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%M %e, %Y") as to_date',
                'P.project_code',
                'P.name as project_name',
            ];

            $joins = [
                'progress_reports PR'   =>  'PR.id = PRS.progress_report_id',
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id',
                'projects P'            =>  'P.id = PR.project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('progress_report_signatories PRS')
                              ->leftJoin($joins)
                              ->where(['PRS.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'PRS.user_id' => ':user_id']);

            if ($is_approved) {
                $initQuery = $initQuery->andWhere(['PRS.is_approved' => ':approval']);
            } else {
                $initQuery = $initQuery->andWhereNull(['PRS.is_approved']);
            }
            
            $initQuery = ($project_id) ? $initQuery->andWhere(['PR.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReport($id = false, $project_id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no',
                'PR.doc_no',
                'IF(PR.status IS NULL, "empty", PR.status) as status',
                'DATE_FORMAT(PR.as_of, "%b %d, %Y") as_of',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as to_date',
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
                'DATE_FORMAT(P.date_started, "%M %e, %Y") as project_start',
                'DATE_FORMAT(P.date_finished, "%M %e, %Y") as project_finish',
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

        public function selectSubmittedProgressReport($progress_report_id = false, $weekly_calendar_id = false, $status = false)
        {
            $fields = array(
                'SPR.id',
                'SPR.progress_report_id',
                'SPR.remarks',
                'SPR.status',
                'SPR.created_by',
                'DATE_FORMAT(SPR.created_at, "%b %e, %Y") as date_filed',
                'DATE_FORMAT(SPR.created_at, "%b %e, %Y %r") as date_submitted',
                'PR.weekly_calendar_id',
                'P.project_manager'
            );

            $join = array(
                'progress_reports PR'   =>  'PR.id = SPR.progress_report_id',
                'projects P'            =>  'P.id = PR.project_id'
            );

            $initQuery = $this->select($fields)
                              ->from('submitted_progress_reports SPR')
                              ->join($join)
                              ->where(array('SPR.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('SPR.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id'))  : $initQuery;
            $initQuery = ($status) ? $initQuery->andWhere(array('SPR.status' => ':status')) : $initQuery;

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
                // 'PRW.td_project_slippage',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRW')
                              ->where(array('PRW.is_active' => ':is_active'));

            $initQuery = ($project_report_id) ? $initQuery->andWhere(array('PRW.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id) ? $initQuery->andWhere(array('PRW.work_item_id' => ':work_item_id')) : $initQuery;

            return $initQuery;
        }

        public function selectItemCodesDirect($project_id = false, $work_item_id = false, $ps_swi_direct_id = '')
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

            return $initQuery;
        }

        public function selectItemCodesIndirect($project_id = false, $work_item_id = false, $p_wi_indirect_id = false)
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
            $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PWI.id' => ':p_wi_indirect_id')) : $initQuery;

            return $initQuery;
        }

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

        public function updateProgressReportSignatory($id = '', $data = [])
        {
            $initQuery = $this->update('progress_report_signatories', $id, $data);

            return $initQuery;
        }

        public function updateSubmittedProgressReport($id = '', $data = [])
        {
            $initQuery = $this->update('submitted_progress_reports', $id, $data);

            return $initQuery;
        }

        /**
         * PROGRESS REPORT (Prepared By Admin Asst)
         */
        public function selectProgressReportSummaries($id = false)
        {
            $fields = array(
                'PRS.id',
                'PRS.weekly_calendar_id',
                'DATE_FORMAT(PRS.as_of, "%M %e, %Y") as as_of',
                'DATE_FORMAT(PRS.agency_accomp_as_of, "%M %e, %Y") as agency_accomp_as_of',
                'PRS.signatories',
                'PRS.status',
                'PRS.created_by',
                'DATE_FORMAT(PRS.created_at, "%M %e, %Y %r") as created_at',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %e, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%M %e, %Y") as week_to_date',
            );

            $join = array(
                'weekly_calendar WC'    =>  'WC.id = PRS.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_summaries PRS')
                              ->join($join)
                              ->where(array('PRS.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PRS.id' => ':id')) : $initQuery;
            

            return $initQuery;
        }

        public function selectProgressReportSummaryDetails($progress_report_summary_id = false)
        {
            $fields = array(
                'PRSD.id',
                'PRSD.progress_report_summary_id',
                'PRSD.submitted_progress_report_id',
                'PRSD.project_id',
                'PRSD.proj_accomp_prev',
                'PRSD.proj_accomp_devt',
                'PRSD.proj_accomp_todate',
                'PRSD.slippage',
                'PRSD.agency_accomp_schedule',
                'PRSD.agency_accomp_actual',
                'PRSD.agency_accomp_slippage',
                'PRSD.remarks',
                'PRSD.in_charge',
                'P.project_code',
                'P.name as project_name',
                'P.contract_days',
                'C.name as client_name',
                'SPR.progress_report_id',
                'SPR.created_by as submitted_by'
            );

            $leftJoins = array(
                'projects P'                     =>  'P.id = PRSD.project_id',
                'clients C'                      =>  'C.id = P.client_id',
                'submitted_progress_reports SPR' =>  'SPR.id = PRSD.submitted_progress_report_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_summary_details PRSD')
                              ->leftJoin($leftJoins)
                              ->where(array('PRSD.is_active' => ':is_active'));

            $initQuery = ($progress_report_summary_id) ? $initQuery->andWhere(array('PRSD.progress_report_summary_id' => ':pr_summary_id')) : $initQuery;

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

        public function selectProgressReportAttachments($pr_id = false, $dpr_id = false)
        {
            $fields = array(
                'PRA.id',
                'PRA.daily_progress_report_id',
                'PRA.file_name',
                'DPR.progress_report_id',
                'DATE_FORMAT(DPR.date, "%b %d, %Y") as activity_date'
            );

            $joins = array(
                'daily_progress_reports DPR'    =>  'DPR.id = PRA.daily_progress_report_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_attachments PRA')
                              ->join($joins)
                              ->where(array('PRA.is_active' => ':is_active'));

            $initQuery = ($pr_id)  ? $initQuery->andWhere(array('DPR.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($dpr_id) ? $initQuery->andWhere(array('DPR.id' => ':dpr_id')) : $initQuery;

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

        public function selectProgressReportOverrideRequests($status = false, $project_id = false)
        {
            $fields = array(
                'PROR.id',
                'PROR.progress_report_id',
                'PROR.justification',
                'PROR.approved_by',
                'PROR.approver_remarks',
                'DATE_FORMAT(PROR.approved_at, "%b %d, %Y %r") as approved_at',
                'PROR.status',
                'PROR.created_by',
                'DATE_FORMAT(PROR.created_at, "%b %d, %Y") as created_at',
                'PR.project_id',
                'PR.weekly_calendar_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_override_requests PROR')
                              ->join(['progress_reports PR' => 'PR.id = PROR.progress_report_id'])
                              ->where(array('PROR.is_active' => ':is_active', 'PROR.approved_by' => ':approved_by'));

            if ($status) {
                $initQuery = $initQuery->andWhere(array('PROR.status' => ':status'));
            } else {
                $initQuery = $initQuery->andWhereNull(array('PROR.status'));
            }

            $initQuery = ($project_id) ? $initQuery->andWhere(array('PR.project_id' => ':project_id')) : $initQuery;
            

            return $initQuery;
        }

        public function updateProgressReportSummary($id = '', $data = [])
        {
            $initQuery = $this->update('progress_report_summaries', $id, $data);

            return $initQuery;
        }

        public function updatePrOverrideRequest($id = '', $data = [])
        {
            $initQuery = $this->update('progress_report_override_requests', $id, $data);

            return $initQuery;
        }
    }
