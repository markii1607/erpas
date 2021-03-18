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

    public function selectPefUsers()
    {
        $fields = [
            'PU.id',
            'PU.lastname',
            'PU.employee_no',
        ];

        $initQuery = $this->select($fields)
                        ->from('pef_users PU')
                        ->where(['PU.is_active' => ':is_active', 'PU.password' => ':password']);

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
            'P.is_signatory',
            'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(PI.mname, ""), NULLIF(PI.lname, "")) as full_name',
            'P.id as position_id',
            'P.name as position_name',
            'P.code as position_code',
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
        ];

        $initQuery = $this->select($fields)
            ->from('users U')
            ->leftJoin($joins)
            ->where($whereCondition);

        return $initQuery;
    }

        /**
     * `selectUsersDevMode` Query String that will select active users from table `users` that does not require password.
     * @return string
     */
    public function selectUsersDevMode()
    {
        $fields = [
            'U.id',
            'U.account_status',
            'U.personal_information_id',
            'U.account_status',
            'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(PI.mname, ""), NULLIF(PI.lname, "")) as full_name',
            'P.is_signatory',
            'P.id as position_id',
            'P.name as position_name',
            'P.code as position_code',
        ];

        $whereCondition = [
            'U.is_active'  => 1,
            'U.username'   => ':username',
            'PI.is_active' => ':is_active'
        ];

        $joins = [
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id',
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
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
         * @param  boolean $code
         * @return string
         */
        public function selectPersonalInformations($id = false, $code = false)
        {
            $fields = [
                'PI.id',
                // 'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'CONCAT(PI.fname, " ", LEFT(PI.mname, 1), ". ", PI.lname) as full_name',
                'PI.lname',
                'PI.fname',
                'PI.mname',
                'PI.sname',
                'PI.citizenship',
                'PI.sex',
                'DATE_FORMAT(PI.birthdate, "%m/%d/%Y") as birthdate',
                'PI.age',
                'PI.height',
                'PI.weight',
                'PI.religion',
                'PI.birthplace',
                'PI.civil_status',
                'PI.no_of_dependents',
                'PI.tel_no',
                'PI.mobile_no as mobile_number',
                'PI.email',
                'PI.address_condition',
                'PI.ps_region_id',
                'PI.ps_province_id',
                'PI.ps_city_id',
                'PI.ps_barangay_id',
                'PI.ps_house_no_street',
                'PI.ps_type',
                'R.name as ps_region_name',
                'PR.name as ps_province_name',
                'CM.name as ps_city_name',
                'BR.name as ps_barangay_name',
                'PI.pr_region_id',
                'PI.pr_province_id',
                'PI.pr_city_id',
                'PI.pr_barangay_id',
                'PI.pr_house_no_street',
                'PI.pr_type',
                'R.name as pr_region_name',
                'PR.name as pr_province_name',
                'CM.name as pr_city_name',
                'BR.name as pr_barangay_name',
                'PI.signature',
                'EI.employee_no',
                'EI.position_id',
                'DATE_FORMAT(EI.date_hired, "%m/%d/%Y") as date_hired',
                'EI.status',
                'P.name as position_name',
                'P.department_id',
                'D.name as department_name',
                'file_name as photo',
                'U.username as username'
            ];

            $joins = [
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'users U'                    => 'U.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id',
                'departments D'              => 'D.id = P.department_id',
            ];
            
            $leftJoins = [
                'pi_photos PP'               => 'PI.id = PP.personal_information_id',
                'regions R'                  => 'PI.ps_region_id = R.id',
                'provinces PR'               => 'PR.id = PI.ps_province_id',
                'city_municipalities CM'     => 'PI.ps_city_id = CM.id',
                'barangays BR'               => 'BR.id = PI.ps_barangay_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PI.id' => ':id']) : $initQuery;

            return $initQuery;
        }

    /**
         * `selectPositions` Query String that will select from table `positions`
         * @return string
         */
        public function selectPositions($id = false, $like = false)
        {
            $fields = [
                'P.id',
                'P.name',
                'P.department_id',
                'P.head_id',
                'P.is_signatory',
                'D.name as department_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->leftJoin(['departments D' => 'D.id = P.department_id'])
                              ->where(['P.is_active' => 1]);

            $initQuery = ($like) ? $initQuery->andWhereLike(['P.name' => ':search'])->limit(10) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments($id = false, $like = false)
        {
            $fields = [
                'D.id',
                'D.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => 1]);

            $initQuery = ($like) ? $initQuery->andWhereLike(['D.name' => ':search'])->limit(10) : $initQuery;

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
         * `insertPersonalInformation` Query string that will insert to table `personal_informations`
         * @return string
         */
        public function insertPersonalInformation($data = [])
        {
            $initQuery = $this->insert('personal_informations', $data);

            return $initQuery;
        }

        /**
         * `insertEmploymentInformation` Query string that will insert to table `employment_informations`
         * @return string
         */
        public function insertEmploymentInformation($data = [])
        {
            $initQuery = $this->insert('employment_informations', $data);

            return $initQuery;
        }

          /**
         * `insertUser` Query string that will insert to table `users`
         * @return string
         */
        public function insertUser($data = [])
        {
            $initQuery = $this->insert('users', $data);

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

        /**
         * `updatePefUser` Query string that will update specific department information from table `pef_users`
         * @return string
         */
        public function updatePefUser($id = '', $data = [])
        {
            $initQuery = $this->update('pef_users', $id, $data);

            return $initQuery;
        }
}
