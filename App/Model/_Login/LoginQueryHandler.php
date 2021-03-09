<?php

namespace App\Model\Login;

require_once("../../AbstractClass/QueryHandler.php");

use App\AbstractClass\QueryHandler;

class LoginQueryHandler extends QueryHandler
{

    /**
     * [selectSample description]
     * @return [type] [description]
     *
     * PO - 11
     *     AOBSE - 4, 7, 9, 11
     *         AOBD - 2, 3, 4, 5
     *             RQM - 2, 4, 3, 5
     *                 RQD - 2, 3, 4, 5, 6, 7
     *                     PRD - 3, 6, 4, 7, 5, 8.
     *                         PR - 3, 4, 5
     *                             P - 113, 114, 129
     *
     * PO - 1
     *     AOBSE - 2
     *         AOBD - 1
     *             RQM - 1
     *                 RQD - 1
     *                     PRD - 2
     *                         PR - 2
     *                             P - 124
     *
     */
    public function selectSample()
    {
        $fields = [
            'DISTINCT(PR.project_id)',
            'P.name',
            'P.location',
            '(SELECT count(DISTINCT(PO.id)) FROM purchase_orders PO JOIN aob_supply_evaluations AOBSE ON PO.id = AOBSE.purchase_order_id JOIN aob_descriptions AOBD ON AOBSE.aob_description_id = AOBD.id JOIN request_quotation_materials RQM ON AOBD.rfq_material_id = RQM.id JOIN request_quotation_descriptions RQD ON RQM.id = RQD.rfq_material_id JOIN purchase_requisition_descriptions SPRD ON RQD.purchase_requisition_description_id = SPRD.id JOIN purchase_requisitions SPR ON SPRD.purchase_requisition_id = SPR.id WHERE SPR.project_id = PR.project_id) as total_po',
            '(SELECT count(CC.id) FROM general_voucher GV JOIN crs_crfs CC ON GV.id = CC.general_voucher_id JOIN crs_crf_po CCP ON CC.id = CCP.crs_crf_id JOIN purchase_orders PO ON CCP.purchase_order_id = PO.id JOIN aob_supply_evaluations AOBSE ON PO.id = AOBSE.purchase_order_id JOIN aob_descriptions AOBD ON AOBSE.aob_description_id = AOBD.id JOIN request_quotation_materials RQM ON AOBD.rfq_material_id = RQM.id JOIN request_quotation_descriptions RQD ON RQM.id = RQD.rfq_material_id JOIN purchase_requisition_descriptions SPRD ON RQD.purchase_requisition_description_id = SPRD.id JOIN purchase_requisitions SPR ON SPRD.purchase_requisition_id = SPR.id WHERE SPR.project_id = PR.project_id AND CC.accounting_status = 0 AND GV.status < 3) as without_voucher',
            '(SELECT count(CC.id) FROM general_voucher GV JOIN crs_crfs CC ON GV.id = CC.general_voucher_id JOIN crs_crf_po CCP ON CC.id = CCP.crs_crf_id JOIN purchase_orders PO ON CCP.purchase_order_id = PO.id JOIN aob_supply_evaluations AOBSE ON PO.id = AOBSE.purchase_order_id JOIN aob_descriptions AOBD ON AOBSE.aob_description_id = AOBD.id JOIN request_quotation_materials RQM ON AOBD.rfq_material_id = RQM.id JOIN request_quotation_descriptions RQD ON RQM.id = RQD.rfq_material_id JOIN purchase_requisition_descriptions SPRD ON RQD.purchase_requisition_description_id = SPRD.id JOIN purchase_requisitions SPR ON SPRD.purchase_requisition_id = SPR.id WHERE SPR.project_id = PR.project_id AND CC.accounting_status = 1 AND GV.status >= 3) as with_voucher',
            '(SELECT sum((SPRD.quantity * IF(RQ.rfq_type = "0", RQ.c_unit_price, RQ.p_unit_price))) FROM purchase_orders PO JOIN aob_supply_evaluations AOBSE ON PO.id = AOBSE.purchase_order_id JOIN aob_descriptions AOBD ON AOBSE.aob_description_id = AOBD.id JOIN request_quotation_materials RQM ON AOBD.rfq_material_id = RQM.id JOIN request_quotations RQ ON RQM.id = RQ.rfq_material_id JOIN request_quotation_descriptions RQD ON RQM.id = RQD.rfq_material_id JOIN purchase_requisition_descriptions SPRD ON RQD.purchase_requisition_description_id = SPRD.id JOIN purchase_requisitions SPR ON SPRD.purchase_requisition_id = SPR.id WHERE SPR.project_id = PR.project_id) as purchase_amount',
        ];

        $joins = [
            'purchase_requisition_descriptions PRD' => 'PR.id = PRD.purchase_requisition_id',
            'projects P' => 'P.id = PR.project_id'
        ];

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->join($joins)
            ->where(['PR.is_active' => 1]);

        return $initQuery;
    }

    /**
     * `selectUsers` Query String that will select active users from table `users`.
     * @return string
     */
    public function selectUsers()
    {
        $fields = [
            'U.id',
            'U.account_status',
            'U.personal_information_id',
            'U.account_status',
            'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(PI.mname, ""), NULLIF(PI.lname, "")) as full_name',
            'P.is_signatory',
            'EI.head_id',

            'P.id as position_id',
            'P.name as position_name',
            'P.code as position_code',

            'D.id as department_id',
            'D.name as department_name'
        ];

        $whereCondition = [
            'U.is_active'  => 1,
            'U.username'   => ':username',
            'U.password'   => ':password',
            'PI.is_active' => ':is_active'
        ];

        $joins = [
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id',
            'departments D'              => 'P.department_id = D.id'
        ];

        $initQuery = $this->select($fields)
            ->from('users U')
            ->leftJoin($joins)
            ->where($whereCondition);

        return $initQuery;
    }

    /**
     * `selectUserName` Query String that will select username from table `users`.
     * @return string
     */
    public function selectUserName($user_id = false)
    {
        $fields = [
            'U.id',
            'U.username',
        ];

        $initQuery = $this->select($fields)
            ->from('users U');

        $initQuery = ($user_id) ? $initQuery->Where(array('U.id' => ':user_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectSessionLogs` Query String that will select existing entry of IP loggen in table `session_logs`.
     * @return string
     */
    public function selectSessionLogs($hasIp = false)
    {
        $fields = [
            'SL.id',
            'SL.ip_address',
            'SL.user_id',
            'SL.session_data',
            'SL.status',
        ];

        $initQuery = $this->select($fields)
            ->from('session_logs SL');

        $initQuery = ($hasIp) ? $initQuery->Where(array('SL.ip_address' => ':ip_address')) : $initQuery;

        return $initQuery;
    }
    /**
     * `insertSessionLogs` Query String that will insert Login Data to table `session_logs`.
     * @return string
     */
    public function insertSessionLogs($data = array())
    {
        $initQuery = $this->insert('session_logs', $data);
        return $initQuery;
    }

    /**
     * `updateSessionLogs` Query String that will update Login Data to table `session_logs`.
     * @return string
     */
    public function updateSessionLogs($id = '', $data = [])
    {
        $initQuery = $this->update('session_logs', $id, $data);

        return $initQuery;
    }
}
