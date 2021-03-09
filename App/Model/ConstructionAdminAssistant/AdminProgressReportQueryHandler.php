<?php
    namespace App\Model\ConstructionAdminAssistant;

    require_once('ConstructionAdminAssistantQueryHandler.php');

    use App\Model\ConstructionAdminAssistant\ConstructionAdminAssistantQueryHandler;

    class AdminProgressReportQueryHandler extends ConstructionAdminAssistantQueryHandler {
        
        public function selectSubmittedProgressReports($weekly_calendar_id = false, $status = false)
        {
            $fields = array(
                'SPR.id',
                'SPR.progress_report_id',
                'SPR.remarks',
                'SPR.status',
                'SPR.created_by',
                'DATE_FORMAT(SPR.created_at, "%M %d, %Y %r") as date_submitted',
                'PR.doc_no',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.revision_no as plan_revision_no',
                'DATE_FORMAT(PR.as_of, "%M %d, %Y %r") as revised_as_of',
                'P.name as project_name',
                'P.project_code',
                'P.project_manager',
                'P.contract_days',
                'C.name as client_name',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as week_to_date',
            );

            $leftJoins = array(
                'progress_reports PR'   =>  'PR.id = SPR.progress_report_id',
                'projects P'            =>  'P.id = PR.project_id',
                'clients C'             =>  'C.id = P.client_id',
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('submitted_progress_reports SPR')
                              ->leftJoin($leftJoins)
                              ->where(array('SPR.is_active' => ':is_active'));

            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PR.weekly_calendar_id' => ':weekly_calendar_id'))  : $initQuery;
            $initQuery = ($status) ? $initQuery->andWhere(array('SPR.status' => ':status')) : $initQuery;

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
                'DATE_FORMAT(SPR.created_at, "%b %e, %Y %r") as date_filed',
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

        public function selectConsolidatedReport($progress_report_id = false)
        {
            $fields = array(
                'PRWD.progress_report_id',
                'SUM(PRWD.plan_project_weight) as plan_proj_weight',
                'SUM(PRWD.td_plan_proj_weight) as td_plan_proj_weight',
                'SUM(PRWD.devt_project_weight) as devt_proj_weight',
                'SUM(PRWD.td_project_weight) as td_proj_weight',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_weekly_details PRWD')
                              ->where(array('PRWD.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRWD.progress_report_id' => ':progress_report_id')) : $initQuery;

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

        public function selectProgressReports($id = false, $project_id = false, $weekly_calendar_id = false)
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

        public function selectProgressStatusMaterials($progress_report_id = false)
        {
            $fields = array(
                'PSM.id',
                'PSM.progress_report_id',
                'PSM.doc_no mwr_doc_no',
                'DATE_FORMAT(PSM.created_at, "%b %d, %Y") as report_date',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.doc_no war_doc_no',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as week_to_date',
            );

            $leftJoins = array(
                'progress_reports PR'   =>  'PR.id = PSM.progress_report_id',
                'projects P'            =>  'P.id = PR.project_id',
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_status_materials PSM')
                              ->leftJoin($leftJoins)
                              ->where(array('PSM.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PSM.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportMaterials($psm_id = false)
        {
            $fields = array(
                'PRM.id',
                'PRM.material_specification_brand_id',
                'PRM.work_item_id',
                'PRM.unit',
                'PRM.D_input as delivered_quantity',
                'PRM.D_toDate as td_delivered_quantity',
                'PRM.C_input as consumed_quantity',
                'PRM.C_toDate as td_consumed_quantity',
                'MS.specs as material_specs',
                'M.name as material_description',
                '(SELECT code FROM msb_suppliers WHERE material_specification_brand_id = PRM.material_specification_brand_id LIMIT 1) as material_code',
                'WI.item_no',
                'WI.wbs',
                'WI.name as item_name',
                'WC.name as part_name',
                'CONCAT("PART ", WC.part) as part_code'
            );

            $leftJoins = array(
                'work_items WI'                     =>      'WI.id = PRM.work_item_id',
                'work_item_categories WC'           =>      'WC.id = WI.work_item_category_id',
                'material_specification_brands MSB' =>      'MSB.id = PRM.material_specification_brand_id',
                'material_specifications MS'        =>      'MS.id = MSB.material_specification_id',
                'materials M'                       =>      'M.id = MS.material_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_materials PRM')
                              ->leftJoin($leftJoins)
                              ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($psm_id) ? $initQuery->andWhere(array('PRM.progress_status_material_id' => ':psm_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrMaterials($progress_report_id = false, $work_item_id = false, $msb_id = false, $unit = false)
        {
            $fields = array(
                'PRM.id',
                'PRM.material_specification_brand_id',
                'PRM.work_item_id',
                'PRM.unit',
                'PRM.D_input as delivered_quantity',
                'PRM.D_toDate as td_delivered_quantity',
                'PRM.C_input as consumed_quantity',
                'PRM.C_toDate as td_consumed_quantity',
                'PSM.progress_report_id'
            );

            $join = array(
                'progress_status_materials PSM'     =>      'PSM.id = PRM.progress_status_material_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_materials PRM')
                              ->join($join)
                              ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRM.progress_report_id' => ':progress_report_id'))  : $initQuery;
            $initQuery = ($work_item_id)       ? $initQuery->andWhere(array('PRM.work_item_id' => ':work_item_id'))              : $initQuery;
            $initQuery = ($msb_id)             ? $initQuery->andWhere(array('PRM.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit)               ? $initQuery->andWhere(array('PRM.unit' => ':unit'))                              : $initQuery;

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
                'W.ws_no'
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

        public function selectProgressReportLabors($progress_report_id = false)
        {
            $fields = array(
                'PRL.id',
                'PRL.progress_report_id',
                'PRL.doc_no lwr_doc_no',
                'DATE_FORMAT(PRL.created_at, "%b %d, %Y") as report_date',
                'PRL.created_by',
                'PR.doc_no war_doc_no',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as week_to_date',
            );

            $leftJoins = array(
                'progress_reports PR'   =>  'PR.id = PRL.progress_report_id',
                'projects P'            =>  'P.id = PR.project_id',
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labors PRL')
                              ->leftJoin($leftJoins)
                              ->where(array('PRL.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRL.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportLaborItems($prl_id = false)
        {
            $fields = array(
                'PRLI.id',
                'PRLI.progress_report_labor_id',
                'PRLI.work_item_id',
                'WI.name as item_name',
                'WI.item_no',
                'WI.wbs',
                'WIC.part as part_code',
                'WIC.name as part_name',
            );

            $joins = array(
                'work_items WI'             =>      'WI.id = PRLI.work_item_id',
                'work_item_categories WIC'  =>      'WIC.id = WI.work_item_category_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labor_items PRLI')
                              ->join($joins)
                              ->where(array('PRLI.is_active' => ':is_active'));

            $initQuery = ($prl_id) ? $initQuery->andWhere(array('PRLI.progress_report_labor_id' => ':prl_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportLaborItemDetails($prli_id = false)
        {
            $fields = array(
                'PRLID.id',
                'PRLID.progress_report_labor_item_id',
                'IF(PRLID.position_type = 1, "SKILLED LABORER", "COMMON LABORER") as position_type',
                'PRLID.position_type as pos_type',
                'PRLID.actual_workers',
                'PRLID.actual_working_days',
                'PRLID.to_date_workers',
                'PRLID.to_date_working_days',
                'PRLID.overtime_days',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labor_item_details PRLID')
                              ->where(array('PRLID.is_active' => ':is_active'));

            $initQuery = ($prli_id) ? $initQuery->andWhere(array('PRLID.progress_report_labor_item_id' => ':prli_id')) : $initQuery;


            return $initQuery;
        }

        public function selectPrLaborItemDetails($progress_report_id = false, $work_item_id = false, $position_type = false)
        {
            $fields = array(
                'PRLID.id',
                'PRLID.position_type',
                'PRLID.actual_workers',
                'PRLID.actual_working_days',
                'PRLID.to_date_workers',
                'PRLID.to_date_working_days',
                'PRLID.overtime_days',
                'PRLI.work_item_id',
                'PRL.progress_report_id',
            );

            $joins = array(
                'progress_report_labor_items PRLI'  =>  'PRLI.id = PRLID.progress_report_labor_item_id',
                'progress_report_labors PRL'        =>  'PRL.id = PRLI.progress_report_labor_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_labor_item_details PRLID')
                              ->join($joins)
                              ->where(array('PRLID.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRL.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($work_item_id)       ? $initQuery->andWhere(array('PRLI.work_item_id' => ':work_item_id'))            : $initQuery;
            $initQuery = ($position_type)      ? $initQuery->andWhere(array('PRLID.position_type' => ':position_type'))         : $initQuery;

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

        public function selectProgressReportEquipments($progress_report_id = false)
        {
            $fields = array(
                'PRE.id',
                'PRE.progress_report_id',
                'PRE.doc_no ewr_doc_no',
                'PRE.is_he_saved',
                'PRE.is_se_saved',
                'DATE_FORMAT(PRE.created_at, "%b %d, %Y") as report_date',
                'PR.project_id',
                'PR.weekly_calendar_id',
                'PR.doc_no war_doc_no',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%b %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%b %d, %Y") as week_to_date',
            );

            $leftJoins = array(
                'progress_reports PR'   =>  'PR.id = PRE.progress_report_id',
                'projects P'            =>  'P.id = PR.project_id',
                'weekly_calendar WC'    =>  'WC.id = PR.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipments PRE')
                              ->leftJoin($leftJoins)
                              ->where(array('PRE.is_active' => ':is_active'));

            $initQuery = ($progress_report_id) ? $initQuery->andWhere(array('PRE.progress_report_id' => ':progress_report_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportEquipmentItems($pre_id = false)
        {
            $fields = array(
                'PREI.id',
                'PREI.progress_report_equipment_id',
                // 'PREI.work_item_id',
                'PREI.equipment_type_id',
                'PREI.capacity',
                'PREI.actual_deployed_units',
                'PREI.td_deployed_units',
                'PREI.actual_equipment_days',
                'PREI.td_equipment_days',
                'PREI.st_working_hours',
                'PREI.ot_working_hours',
                'PREI.st_working_days',
                'PREI.ot_working_days',
                'PREI.overtime_equip_days',
                // 'WI.name as item_name',
                // 'WI.item_no',
                // 'WIC.name as part_name',
                // 'WIC.part as part_code',
                'ET.cost_code as equip_code',
                'ET.name as equip_name',
                'ET.unit as equip_unit',
            );

            $join = array(
                // 'work_items WI'             =>  'WI.id = PREI.work_item_id',
                // 'work_item_categories WIC'  =>  'WIC.id = WI.work_item_category_id',
                'equipment_types ET'        =>  'ET.id = PREI.equipment_type_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipment_items PREI')
                              ->join($join)
                              ->where(array('PREI.is_active' => ':is_active'));

            $initQuery = ($pre_id) ? $initQuery->andWhere(array('PREI.progress_report_equipment_id' => ':pre_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportEquipmentWiItems($prei_id = false)
        {
            $fields = array(
                'PREWI.id',
                'PREWI.progress_report_equipment_item_id',
                'PREWI.work_item_id',
                'PREWI.st_working_hours',
                'PREWI.ot_working_hours',
                'PREWI.st_working_days',
                'PREWI.ot_working_days',
                'WI.wbs',
                'WI.name as item_name',
                'WI.item_no',
                'WIC.name as part_name',
                'WIC.part as part_code',
            );

            $join = array(
                'work_items WI'             =>  'WI.id = PREWI.work_item_id',
                'work_item_categories WIC'  =>  'WIC.id = WI.work_item_category_id',
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipment_wi_items PREWI')
                              ->join($join)
                              ->where(array('PREWI.is_active' => ':is_active'));

            $initQuery = ($prei_id) ? $initQuery->andWhere(array('PREWI.progress_report_equipment_item_id' => ':prei_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrEquipmentItems($progress_report_id = false, $equipment_type_id = false, $capacity = false)
        {
            $fields = array(
                'PREI.id',
                'PREI.progress_report_equipment_id',
                'PREI.equipment_type_id',
                'PREI.capacity',
                'PREI.actual_deployed_units',
                'PREI.td_deployed_units',
                'PREI.actual_equipment_days',
                'PREI.td_equipment_days',
                'PREI.st_working_hours',
                'PREI.ot_working_hours',
                'PREI.st_working_days',
                'PREI.ot_working_days',
                'PREI.overtime_equip_days',
                'PRE.progress_report_id'
            );

            $join = array(
                'progress_report_equipments PRE'    =>  'PRE.id = PREI.progress_report_equipment_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_equipment_items PREI')
                              ->join($join)
                              ->where(array('PREI.is_active' => ':is_active'));

            $initQuery = ($progress_report_id)  ? $initQuery->andWhere(array('PRE.progress_report_id' => ':progress_report_id')) : $initQuery;
            $initQuery = ($equipment_type_id)   ? $initQuery->andWhere(array('PREI.equipment_type_id' => ':equipment_type_id')) : $initQuery;
            $initQuery = ($capacity)            ? $initQuery->andWhere(array('PREI.capacity' => ':capacity')) : $initQuery;

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

        public function selectProgressReportSummaries($id = false, $weekly_calendar_id = false)
        {
            $fields = array(
                'PRS.id',
                'PRS.weekly_calendar_id',
                'DATE_FORMAT(PRS.as_of, "%M %d, %Y") as as_of',
                'DATE_FORMAT(PRS.agency_accomp_as_of, "%M %d, %Y") as agency_accomp_as_of',
                'PRS.signatories',
                'PRS.created_by',
                'DATE_FORMAT(PRS.created_at, "%M %e, %Y %r") as created_at',
                'WC.week_no',
                'DATE_FORMAT(WC.from_date, "%M %d, %Y") as week_from_date',
                'DATE_FORMAT(WC.to_date, "%M %d, %Y") as week_to_date',
            );

            $join = array(
                'weekly_calendar WC'    =>      'WC.id = PRS.weekly_calendar_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_summaries PRS')
                              ->join($join)
                              ->where(array('PRS.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PRS.id' => ':id')) : $initQuery;
            $initQuery = ($weekly_calendar_id) ? $initQuery->andWhere(array('PRS.weekly_calendar_id' => ':week_id')) : $initQuery;

            return $initQuery;
        }

        public function selectProgressReportSummaryDetails($progress_report_summary_id = false)
        {
            $fields = array(
                'PRSD.id',
                'PRSD.progress_report_summary_id',
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
            );

            $join = array(
                'projects P'    =>  'P.id = PRSD.project_id',
                'clients C'     =>  'C.id = P.client_id'
            );

            $initQuery = $this->select($fields)
                              ->from('progress_report_summary_details PRSD')
                              ->join($join)
                              ->where(array('PRSD.is_active' => ':is_active'));

            $initQuery = ($progress_report_summary_id) ? $initQuery->andWhere(array('PRSD.progress_report_summary_id' => ':pr_summary_id')) : $initQuery;

            return $initQuery;
        }

        public function insertProgressReportSummaries($data = [])
        {
            $initQuery = $this->insert('progress_report_summaries', $data);

            return $initQuery;
        }

        public function insertProgressReportSummaryDetails($data = [])
        {
            $initQuery = $this->insert('progress_report_summary_details', $data);

            return $initQuery;
        }

        public function updateProgressReportSummaries($id = '', $data = [])
        {
            $initQuery = $this->update('progress_report_summaries', $id, $data);

            return $initQuery;
        }

        public function updateProgressReportSummaryDetails($id = '', $data = [])
        {
            $initQuery = $this->update('progress_report_summary_details', $id, $data);

            return $initQuery;
        }

    }
