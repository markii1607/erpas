<?php

namespace App\Model\PrsApprovals;

require_once('PrsApprovalsQueryHandler.php');

use App\Model\PrsApprovals\PrsApprovalsQueryHandler;

class PrsMaterialQueryHandler extends PrsApprovalsQueryHandler
{
    /**
     * selectPrss
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
    public function selectPrss($id = false, $request_type = false)
    {
        $fields = array(
            'PR.id',
            'PR.project_id',
            'PR.department_id',
            'PR.prs_no',
            'PR.request_type_id',
            'RT.name as request_type_name',
            'PR.signatories',
            'PR.status',
            'PR.for_cancelation',
            'PR.remarks',
            'CONCAT(PI.fname," ",PI.mname," ",PI.lname) as requestor',
            'DATE_FORMAT(PR.date_requested, "%M %d, %Y %l:%i %p (%W)") as date_requested',
            'PR.prev_id',
            'IF(PR.prev_id IS NULL, "", (SELECT prs_no FROM purchase_requisitions WHERE id = PR.prev_id)) as reference_prs_no',
            'IF(PR.prev_id IS NOT NULL, "resubmitted", "new") as resubmission_status',
        );

        $joins = array(
            'request_types RT' => 'RT.id = PR.request_type_id',
            'users U' => 'U.id = PR.user_id',
            'personal_informations PI' => 'PI.id = U.personal_information_id'
        );

        $whereNotInCondition = [
            '25', // 16026
            '26', // 18029
            '27', // 18080
            '24', // 18SG-013
            '9',  // 19001
            '10', // 19002
            '11', // 19003
            '12', // 19004
            '28', // 19005
            '29', // 19006
            '30', // 19007
            '31', // 19008
            '32', // 19009
            '33', // 19010
            '34', // 19011
            '35', // 19012
            '38', // 19013
            '39', // 19015
            '1',  // 19SCDC001
            '7',  // Y03-001
            '4',  // TC-01126
            '2',  // TC-01147
            '3',  // TC01089
            '36', // 19SG-001
            '37', // 19SG-002
            '23', // 19SG-003
            '69', // 19SG-004
        ];

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->join($joins)
            ->where(array('PR.is_active' => ':is_active'))
            ->andWhereNotIn('PR.project_id', $whereNotInCondition);

        $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
        $initQuery = ($request_type) ? $initQuery->andWhere(array('PR.request_type_id' => ':request_type')) : $initQuery;
        $initQuery = $initQuery->andWhereNotNull(array('PR.signatories'));
        $initQuery = $initQuery->logicEx(' ORDER BY PR.prs_no DESC');

        return $initQuery;
    }

    public function selectRequestItems($id = false, $prsId = false, $mode = false)
    {
        $fields = array(
            'PRD.id',
            'PRD.purchase_requisition_id',
            'PRD.material_specification_id',
            'PRD.item_spec_id',
            'PRD.equipment_id',
            'PRD.quantity',
            'PRD.unit_measurement',
            'PRD.material_id',
            'PRD.category',
            'PRD.wi_category',
            'PRD.work_item_id',
            'PRD.work_volume',
            'PRD.work_volume_unit',
            'PRD.wbs',
            'PRD.account_id',
            'PRD.brand_id',
            'PRD.request_type_id',
            'PRD.signatories',
            'PRD.remarks',
            'PRD.status',
            'PRD.date_needed',
            'PWM.total_quantity as pwm_quantity',
            'PSM.total_quantity as psm_quantity'
        );

        $joins = array(
            'pwi_materials PWM' => 'PWM.id = PRD.pm_id',
            'psd_materials PSM' => 'PSM.id = PRD.pm_id'
        );

        $initQuery = $this->select($fields)
            ->from('purchase_requisition_descriptions PRD')
            ->leftJoin($joins)
            ->where(array('PRD.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PRD.id' => ':prd_id')) : $initQuery;
        $initQuery = ($prsId) ? $initQuery->andWhere(array('PRD.purchase_requisition_id' => ':prs_id')) : $initQuery;
        $initQuery = ($mode) ? $initQuery->andWhereNotEqual(array('PRD.status' => ':status')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterials($id = false, $userId = false)
    {
        $fields = array(
            'M.id',
            'M.name',
        );

        $initQuery = $this->select($fields)
            ->from('materials M')
            ->where(array('M.is_active' => ':is_active'));
        $initQuery = ($id) ? $initQuery->andWhere(array('M.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectMaterialSpecs($id = false, $userId = false, $filter = false, $limit = '')
    {
        $fields = array(
            'MS.id',
            'MS.material_id',
            'MS.specs',
            'MS.code',
            // 'CONCAT(MS.code, MSBS.code) as mat_code',
            'MSBS.code as mat_code',
            'MSBS.unit'
        );

        $joins = array(
            'msb_suppliers MSBS' => 'MSBS.material_specification_brand_id = MS.id'
        );

        $initQuery = $this->select($fields)
            ->from('material_specifications MS')
            ->join($joins);
        // $initQuery = ($filter) ? $initQuery->logicEx('JOIN materials M ON M.id = MS.material_id JOIN  ON') : $initQuery;
        $initQuery = $initQuery->where(array('MS.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;
        $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('MS.specs' => ':filter', 'MS.code' => ':filter')) : $initQuery;
        $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT ' . $limit . ', 50') : $initQuery;

        return $initQuery;
    }

    public function selectDeliverySequence($id = false, $prdId = false)
    {
        $fields = array(
            'PRDS.id',
            'PRDS.purchase_requisition_description_id',
            'PRDS.seq_no',
            'PRDS.delivery_date',
            'PRDS.quantity',
            'PRDS.is_consumed',
            'PRDS.is_active',
            'PRDS.created_by',
            'PRDS.created_at',
            'PRDS.updated_by',
            'PRDS.updated_at'
        );

        $initQuery = $this->select($fields)
            ->from('prd_delivery_sequences PRDS')
            ->where(array('PRDS.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PRDS.id' => ':id')) : $initQuery;
        $initQuery = ($prdId) ? $initQuery->andWhere(array('PRDS.purchase_requisition_description_id' => ':prd_id')) : $initQuery;

        return $initQuery;
    }

    public function selectAttachments($id = false, $prdId = false)
    {
        $fields = array(
            'PRA.filename',
        );

        $initQuery = $this->select($fields)
            ->from('prd_attachments PRA')
            ->where(array('PRA.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PRA.id' => ':id')) : $initQuery;
        $initQuery = ($prdId) ? $initQuery->andWhere(array('PRA.purchase_requisition_description_id' => ':prd_id')) : $initQuery;

        return $initQuery;
    }

    public function selectAccounts($id = '')
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

        $initQuery = ($id) ? $initQuery->andWhere(array('A.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectWorkItem($id = false, $projectId = false, $userId = false)
    {
        $fields = array(
            'WI.id',
            'WI.work_item_category_id',
            'WI.code',
            'WI.item_no',
            'WI.name',
            'WI.unit',
            'WI.direct',
        );

        $initQuery = $this->select($fields)
            ->from('work_items WI')
            ->where(array('WI.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('WI.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectWorkItemCategory($id = false, $projectId = false, $userId = false)
    {
        $fields = array(
            'WIC.id',
            'WIC.code',
            'WIC.name',
            'WIC.part'
        );

        $initQuery = $this->select($fields)
            ->from('work_item_categories WIC')
            ->where(array('WIC.is_active' => ':status'));
        $initQuery = ($id) ? $initQuery->andWhere(array('WIC.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectUser($id = false, $pid = false)
    {
        $fields = array(
            'U.account_status',
            'U.personal_information_id',
            'CONCAT(PI.lname,", ",PI.fname," ", PI.mname) as employee',
            'EI.position_id',
            'P.name as position',
            'P.department_id',
            'D.name as department',
        );

        $join = array(
            'personal_informations PI' => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P' => 'EI.position_id = P.id',
            'departments D' => 'P.department_id = D.id'
        );

        $initQuery = $this->select($fields)
            ->from('users U')
            ->leftJoin($join)
            ->where(array('U.is_active' => ':status'));

        $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;
        $initQuery = ($pid) ? $initQuery->andWhere(array('PI.id' => ':p_id')) : $initQuery;

        return $initQuery;
    }

    public function selectProjects($id = false, $userId = false)
    {
        $fields = array(
            'P.id',
            'P.project_code',
            'P.name',
            'P.location',
            'P.is_on_going',
        );

        $joins = array(
            'p_wds PWDS' => 'PWDS.project_id = P.id',
        );

        $initQuery = $this->select($fields)
            ->from('projects P')
            ->join($joins)
            ->where(array('P.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectDepartments($id = false, $userId = false)
    {
        $fields = array(
            'D.id',
            'D.code',
            'D.charging',
            'D.name',
        );

        $initQuery = $this->select($fields)
            ->from('departments D')
            ->where(array('D.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('D.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectDeputies($id = false, $user_id = false)
    {
        $fields = array(
            'UD.id',
            'UD.user_id',
            'UD.deputy_id',
            'UD.status',
        );

        $initQuery = $this->select($fields)
            ->from('user_deputies UD')
            ->where(array('UD.is_active' => ':is_active', 'UD.status' => ':status'));

        $initQuery = ($id) ? $initQuery->andWhere(array('UD.id' => ':id')) : $initQuery;
        $initQuery = ($user_id) ? $initQuery->andWhere(array('UD.user_id' => ':user_id')) : $initQuery;

        return $initQuery;
    }

    public function updateRequest($id = '', $data = array())
    {
        $initQuery = $this->update('purchase_requisitions', $id, $data);
        return $initQuery;
    }

    public function updateRequestMaterial($id = '', $data = array())
    {
        $initQuery = $this->update('purchase_requisition_descriptions', $id, $data);
        return $initQuery;
    }
}
