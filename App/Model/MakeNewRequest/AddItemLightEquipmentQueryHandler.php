<?php

namespace App\Model\MakeNewRequest;

require_once('MakeNewRequestQueryHandler.php');

use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;

class AddItemLightEquipmentQueryHandler extends MakeNewRequestQueryHandler
{

    /**
     * `selectPwds` Query string that will select from table `p_wds`
     * @param  boolean $id
     * @param  boolean $projectId
     * @return string
     */
    public function selectPwds($id = false, $projectId = false)
    {
        $fields = [
            'PWD.id',
            'WD.id as wd_id',
            'WD.name as wd_name',
            'WD.wbs'
        ];

        $initQuery = $this->select($fields)
            ->from('p_wds PWD')
            ->join(['work_disciplines WD' => 'WD.id = PWD.work_discipline_id'])
            ->where(['PWD.is_active' => ':is_active', 'WD.is_active' => ':is_active']);

        $initQuery = ($id)        ? $initQuery->andWhere(['PWD.id' => ':id'])                 : $initQuery;
        $initQuery = ($projectId) ? $initQuery->andWhere(['PWD.project_id' => ':project_id']) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPwSps` Query string that will select from table `pw_sps`.
     * @param  boolean $id
     * @param  boolean  $pwdId
     * @return string
     */
    public function selectPwSps($id = false, $pwdId = false)
    {
        $fields = [
            'PWSP.id',
            'PWSP.name',
            'SP.id as sp_id',
            'SP.name as sp_name',
            'SP.wbs'
        ];

        $initQuery = $this->select($fields)
            ->from('pw_sps PWSP')
            ->join(['sub_projects SP' => 'SP.id = PWSP.sub_project_id'])
            ->where(['PWSP.is_active' => ':is_active', 'SP.is_active' => ':is_active']);

        $initQuery = ($id)    ? $initQuery->andWhere(['PWSP.id' => ':id'])           : $initQuery;
        $initQuery = ($pwdId) ? $initQuery->andWhere(['PWSP.p_wd_id' => ':p_wd_id']) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPsSwiDirects` Query string that will select from table `ps_swi_directs`.
     * @param  boolean $id
     * @param  boolean  $pwSpId
     * @return string
     */
    public function selectPsSwiDirects($id = false, $pwSpId = false)
    {
        $fields = [
            'PSWID.id',
            'PSWID.quantities',
            'SWI.id as swi_id',
            'SWI.alternative_name as swi_name',
            'SWI.unit as swi_unit',
            'WIC.id as wic_id',
            'WIC.name as wic_name',
            'WIC.part',
            'WI.id as wi_id',
            'WI.name as wi_name',
            'WI.item_no',
            'WI.unit',
            'WI.direct',
            'SWIC.id as swic_id',
            'SWIC.wbs as wic_wbs',
            'SWI.wbs as swi_wbs',
        ];

        $joins = [
            'sw_wis SWI'               => 'SWI.id = PSWID.sw_wi_id',
            'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
            'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
            'work_items WI'            => 'WI.id = SWI.work_item_id'
        ];

        $initQuery = $this->select($fields)
            ->from('ps_swi_directs PSWID')
            ->join($joins)
            ->where(['PSWID.is_active' => ':is_active', 'SWI.is_active' => ':is_active', 'SWIC.is_active' => ':is_active', 'WIC.is_active' => ':is_active', 'WI.is_active' => ':is_active']);

        $initQuery = ($id)    ? $initQuery->andWhere(['PSWID.id' => ':id'])             : $initQuery;
        $initQuery = ($pwSpId) ? $initQuery->andWhere(['PSWID.pw_sp_id' => ':pw_sp_id']) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPwiIndirects` Query string that will select from table `p_wi_indirects`
     * @param  boolean $id
     * @param  boolean $projectId
     * @return string
     */
    public function selectPwiIndirects($id = false, $projectId = false)
    {
        $fields = [
            'PWII.id',
            'PWII.quantities',
            'WIC.id as wic_id',
            'WIC.name as wic_name',
            'WI.id as wi_id',
            'WI.name as wi_name',
            'WI.item_no',
            'WI.unit',
            'WI.direct',
            'WIC.part',
            'WIC.code as wic_wbs',
            'WI.wbs as wi_wbs'
        ];

        $joins = [
            'work_items WI'            => 'PWII.work_item_id = WI.id',
            'work_item_categories WIC' => 'WI.work_item_category_id = WIC.id'
        ];

        $initQuery = $this->select($fields)
            ->from('p_wi_indirects PWII')
            ->join($joins)
            ->where(['PWII.is_active' => ':is_active', 'WI.is_active' => ':is_active', 'WIC.is_active' => ':is_active']);

        $initQuery = ($id)     ? $initQuery->andWhere(['PWII.id' => ':id'])                 : $initQuery;
        $initQuery = ($projectId) ? $initQuery->andWhere(['PWII.project_id' => ':project_id']) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectPsdEquipments` Query string that will select from table `psd_equipments`.
     * @param  boolean $id
     * @param  boolean $psSwiDirectId
     * @return string
     */
    public function selectPsdEquipments($id = false, $psSwiDirectId = false)
    {
        $fields = [
            'PE.id',
            'PE.ps_swi_direct_id',
            'PE.equipment_type_id',
            'PE.capacity',
            'PE.duration',
            'PE.equipment_days',
            'PE.rental_rate',
            'PE.no_of_equipment',
            'PE.total as total_cost',
            'ET.name as equipment_type_name',
            'ET.cost_code as equipment_code',
            'ET.unit',
            'ET.classification',
            'DATE_FORMAT(PE.mob_plan_from, "%m/%d/%Y") as mob_plan_from',
            'DATE_FORMAT(PE.mob_plan_to, "%m/%d/%Y") as mob_plan_to',
            '"1" as direct'
        ];

        $joins = array(
            'equipment_types ET' => 'ET.id = PE.equipment_type_id'
        );

        $initQuery = $this->select($fields)
            ->from('psd_equipments PE')
            ->join($joins)
            ->where(['PE.is_active' => 1]);

        $initQuery = $initQuery->andWhere(['ET.classification' => '"SE"']);
        $initQuery = ($id)            ? $initQuery->andWhere(['PE.id' => ':id'])                             : $initQuery;
        $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PE.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectEquipments` Query string that will select from table `psd_equipments`.
     * @param  boolean $id
     * @param  boolean $psSwiDirectId
     * @return string
     */
    public function selectEquipments()
    {
        $fields = [
            'EC.name as category_name',
            'EC.cost_code as cat_cost_code',
            'ET.name as equipment_type_name',
            'ET.cost_code as equipment_code',
            'ET.unit',
            'ET.classification',
            'ET.id as equip_id_type_id ',
            '"1" as direct'
        ];

        $joins = array(
            'equipment_categories EC' => 'EC.id = ET.equipment_category_id'
        );

        $initQuery = $this->select($fields)
            ->from('equipment_types ET')
            ->join($joins)
            ->where(['ET.is_active' => 1]);

        $initQuery = $initQuery->andWhere(['ET.classification' => '"SE"']);
        return $initQuery;
    }

    /**
     * `selectPwiEquipments` Query string that will select from table `pwi_equipments`
     * @param  boolean $id
     * @param  boolean $pwiIndirectId
     * @return string
     */
    public function selectPwiEquipments($id = false, $pwiIndirectId = false, $psSwiDirectId = false)
    {
        $fields = [
            'PWIE.id',
            'PWIE.duration as equipment_days',
            'PWIE.equipment_type_id',
            'PWIE.equipment_days as equipment_days_total',
            // 'PWIE.equipment_days',
            'PWIE.rental_rate',
            'PWIE.capacity',
            'PWIE.total as total_cost',
            'PWIE.no_of_equipment',
            'PWIE.mat_arr',
            'DATE_FORMAT( PWIE.mob_plan_from,  "%m-%d-%Y") as mob_plan_dateFrom',
            'DATE_FORMAT( PWIE.mob_plan_to,  "%m-%d-%Y") as mob_plan_dateTo',
            'ET.name as equipment_type_name',
            'ET.cost_code as equipment_code',
            'ET.unit',
            'ET.classification',
            '"0" as direct',
        ];

        $joins = [
            'equipment_types ET' => 'ET.id = PWIE.equipment_type_id',
        ];

        $initQuery = $this->select($fields)
            ->from('pwi_equipments PWIE')
            ->join($joins)
            ->where(['PWIE.is_active' => ':is_active']);

        $initQuery = $initQuery->andWhere(['ET.classification' => '"SE"']);
        $initQuery = ($id)            ? $initQuery->andWhere(['PWIE.id' => ':id'])                             : $initQuery;
        $initQuery = ($pwiIndirectId) ? $initQuery->andWhere(['PWIE.p_wi_indirect_id' => ':p_wi_indirect_id']) : $initQuery;
        $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PWIE.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectAccounts` Query string that will select from table `accounts`.
     * @param  boolean $id
     * @return string
     */
    public function selectAccounts($id = false, $accountTypeId = false)
    {
        $fields = array(
            'A.id',
            'A.account_id',
            'A.name',
            'AT.id as type_id',
            'AT.name as type_name',
        );

        $joins = array(
            'account_types AT' => 'AT.id = A.account_type_id',
        );

        $initQuery = $this->select($fields)
            ->from('accounts A')
            ->join($joins)
            ->where(array('A.is_active' => ':is_active'));

        $initQuery = ($id)            ? $initQuery->andWhere(array('A.id' => ':id'))                           : $initQuery;
        $initQuery = ($accountTypeId) ? $initQuery->andWhere(array('A.account_type_id' => ':account_type_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectSignatorySets` Query string that will select from table `signatory_sets`.
     * @param  boolean $id
     * @param  boolean $menuId
     * @return string
     */
    public function selectSignatorySets($id = false, $menuId = false)
    {
        $fields = array(
            'SS.id',
            'SS.menu_id',
            'SS.signatories',
            'SS.no_of_signatory',
        );

        $initQuery = $this->select($fields)
            ->from('signatory_sets SS')
            ->where(array('SS.status' => ':status'));

        $initQuery = ($id)     ? $initQuery->andWhere(array('SS.id' => ':id'))           : $initQuery;
        $initQuery = ($menuId) ? $initQuery->andWhere(array('SS.menu_id' => ':menu_id')) : $initQuery;

        return $initQuery;
    }

    // /**
    //  * `selectPersonalInformations` Query string that will select from table `personal_informations`.
    //  * @param  boolean $id
    //  * @param  boolean $department_id
    //  * @return string
    //  */
    // public function selectPersonalInformations($id = false, $departmentId = false)
    // {
    //     $fields = array(
    //         'PI.id',
    //         'EI.position_id',
    //         'P.department_id',
    //         'P.name as position_name',
    //         'D.charging',
    //         'D.name as department_name',
    //         'CONCAT(PI.lname,", ",PI.fname," ",PI.mname) as fullname'
    //     );

    //     $join = array(
    //         'employment_informations EI' => 'PI.id = EI.personal_information_id',
    //         'positions P'                => 'EI.position_id = P.id',
    //         'departments D'              => 'P.department_id = D.id'
    //     );

    //     $initQuery = $this->select($fields)
    //         ->from('personal_informations PI')
    //         ->leftJoin($join)
    //         ->where(array('PI.is_active' => ':is_active'));

    //     $initQuery = ($id)           ? $initQuery->andWhere(array('PI.id' => ':id'))                      : $initQuery;
    //     $initQuery = ($departmentId) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;

    //     return $initQuery;
    // }

        /** 
         * `selectUsers` Query string that will select users.
         * @param boolean $id
         * @return string
         */
        public function selectPersonalInformations($id = false , $departmentId = false)
        {
            $fields = array(
                'U.id',
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'PI.id as personal_informations_id',
                'P.id as position_id',
                'P.name as position_name',
                'D.id as department_id',
                'D.charging',
                'D.name as department_name',
                'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(PI.mname, ""), NULLIF(PI.lname, "")) as fullname'
            );

            $joins = array(
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            );

            $initQuery = $this->select($fields)
                                ->from('users U')
                                ->join($joins)
                                ->where(array('U.is_active' => ':is_active', 'PI.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;
            // $initQuery = ($id)           ? $initQuery->andWhere(array('PI.id' => ':id'))                      : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;

            return $initQuery;
        }

    /**
     * `insertPrEquipment` Insert details from table `pr_equipments`.
     * @param  array  $data
     * @return 
     */
    public function insertPrEquipment($data = array())
    {
        $initQuery = $this->insert('pr_equipments', $data);

        return $initQuery;
    }

    /**
     * `insertPrWorkItem` Insert details from table `pr_work_items`.
     * @param  array  $data
     * @return 
     */
    public function insertPrWorkItem($data = array())
    {
        $initQuery = $this->insert('pr_work_items', $data);

        return $initQuery;
    }

    /**
     * `updatePurchaseRequisition` Query string that will update to table `purchase_requisitions`
     * @param  string $id
     * @param  array  $data
     * @return string
     */
    public function updatePurchaseRequisition($id = '', $data = [], $fk = '', $fkValue = '')
    {
        $initQuery = $this->update('purchase_requisitions', $id, $data, $fk, $fkValue);

        return $initQuery;
    }

    public function insertLightEquipmentAttachments($data = array())
    {
        $initQuery = $this->insert('pr_light_equipment_attachments', $data);
        return $initQuery;
    }
}
