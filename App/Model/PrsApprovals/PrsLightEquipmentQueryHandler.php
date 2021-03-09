<?php

namespace App\Model\PrsApprovals;

require_once('PrsApprovalsQueryHandler.php');

use App\Model\PrsApprovals\PrsApprovalsQueryHandler;

class PrsLightEquipmentQueryHandler extends PrsApprovalsQueryHandler
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
      'PR.remarks',
      'PR.request_type_id',
      'RT.name as request_type_name',
      'PR.for_cancelation',
      'PR.signatories',
      'CONCAT(PI.fname," ",PI.mname," ",PI.lname) as requestor',
      'DATE_FORMAT(PR.date_requested, "%M %d, %Y %l:%i %p (%W)") as date_requested'
    );

    $joins = array(
      'request_types RT' => 'RT.id = PR.request_type_id',
      'users U' => 'U.id = PR.user_id',
      'personal_informations PI' => 'PI.id = U.personal_information_id'
    );

    $initQuery = $this->select($fields)
      ->from('purchase_requisitions PR')
      ->join($joins)
      ->where(array('PR.is_active' => ':is_active'));

    $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;
    $initQuery = ($request_type) ? $initQuery->andWhere(array('PR.request_type_id' => ':request_type')) : $initQuery;
    $initQuery = $initQuery->andWhereNotNull(array('PR.signatories'));

    return $initQuery;
  }

     /**
         * `selectEquipments` Query string that will select from table `psd_equipments`.
         * @param  boolean $id
         * @param  boolean $psSwiDirectId
         * @return string
         */
        public function selectEquipments($id = false)
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
                              ->where(['ET.is_active' => ':is_active']);

            $initQuery = ($id)? $initQuery->andWhere(['ET.id' => ':id']) : $initQuery;

            return $initQuery;
        }

  public function selectRequestEquipments($id = false, $prsId = false, $mode = false, $eqtypeid = false)
  {
    $fields = array(
      'PRE.id',
      'PRE.pr_id',
      'PRE.category',
      'PRE.equipment_type_id',
      'PRE.capacity',
      'PRE.no_of_unit',
      'PRE.total_no_of_equipment',
      'DATE_FORMAT(PRE.start_date, "%M %d, %Y") as start_date',
      'PRE.account_id',
      'PRE.signatories',
      'PRE.remarks',
      'PRE.status',
      'PRE.is_active',
      'PRE.created_by',
      'PRE.updated_by',
      'PRE.created_at',
      'PRE.updated_at',
    );

    $initQuery = $this->select($fields)
      ->from('pr_equipments PRE')
      ->where(array('PRE.is_active' => ':is_active'));

    $initQuery = ($eqtypeid) ? $initQuery->andWhere(array('PRE.equipment_type_id' => ':eq_id')) : $initQuery;
    $initQuery = ($id) ? $initQuery->andWhere(array('PRE.id' => ':id')) : $initQuery;
    $initQuery = ($prsId) ? $initQuery->andWhere(array('PRE.pr_id' => ':prs_id')) : $initQuery;
    $initQuery = ($mode) ? $initQuery->andWhereNotEqual(array('PRE.status' => ':status')) : $initQuery;

    return $initQuery;
  }

  public function selectRequestEquipmentsOnly($id = false, $prsId = false, $mode = false)
  {
    $fields = array(
      'PRE.id',
      'PRE.equipment_type_id'
    );

    $initQuery = $this->select($fields)
      ->from('pr_equipments PRE')
      ->where(array('PRE.is_active' => ':is_active'));

    $initQuery = ($id) ? $initQuery->andWhere(array('PRE.id' => ':pr_id')) : $initQuery;
    $initQuery = ($prsId) ? $initQuery->andWhere(array('PRE.pr_id' => ':prs_id')) : $initQuery;
    $initQuery = ($mode) ? $initQuery->andWhereNotEqual(array('PRE.status' => ':status')) : $initQuery;

    return $initQuery;
  }

  public function selectWIequip($id = false)
  {
    $fields = array(
      'PWI.id',
      'PWI.pre_id',
      'PWI.prl_id',
      'PWI.ps_swi_directs_id',
      'PWI.p_wi_indirects_id',
      'PWI.wi_category_id',
      'PWI.wi_id',
      'PWI.remarks as equip_remarks',
      'PWI.work_volume',
      'PWI.wv_unit',
      'PWI.wbs',
      'PWI.no_of_equipment',
      'PWI.start_date',
      'PWI.equipment_days',
      'PWI.status',
      'PWI.is_active',
      'PWI.created_by',
      'PWI.updated_by',
      'PWI.created_at',
      'PWI.updated_at',
    );

    $initQuery = $this->select($fields)
      ->from('pr_work_items PWI')
      ->where(array('PWI.is_active' => ':is_active'));

    $initQuery = ($id) ? $initQuery->andWhere(array('PWI.pre_id' => ':id')) : $initQuery;

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
    $initQuery = ($prdId) ? $initQuery->andWhere(array('PRDS.prs_ppe_descriptions_id' => ':prd_id')) : $initQuery;

    return $initQuery;
  }

  public function selectAttachments($id = false, $preId = false)
  {
    $fields = array(
      'PRLEA.filename',
    );

    $initQuery = $this->select($fields)
      ->from('pr_light_equipment_attachments PRLEA')
      ->where(array('PRLEA.is_active' => ':is_active'));

    $initQuery = ($id) ? $initQuery->andWhere(array('PRLEA.id' => ':id')) : $initQuery;
    $initQuery = ($preId) ? $initQuery->andWhere(array('PRLEA.pr_equipment_id' => ':pre_id')) : $initQuery;

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
      'WI.name as wi_name',
      'WI.unit',
      'WI.direct',
      // 'WIC.part',
      // 'WIC.name as wic_name',
      // 'WIC.code'
    );

    // $joins = array(
    //   'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id',
    // );


    $initQuery = $this->select($fields)
      ->from('work_items WI')
      // ->join($joins)
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
      'U.id',
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
      $initQuery = ($pid) ? $initQuery->andWhere(array('U.personal_information_id' => ':p_id')) : $initQuery;

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

  public function updateRequest($id = '', $data = array())
  {
    $initQuery = $this->update('purchase_requisitions', $id, $data);
    return $initQuery;
  }

  public function updateRequestEquipments($id = '', $data = array())
  {
    $initQuery = $this->update('pr_equipments', $id, $data);
    return $initQuery;
  }
}
