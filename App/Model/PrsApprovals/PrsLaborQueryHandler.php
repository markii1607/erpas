<?php

namespace App\Model\PrsApprovals;

require_once('PrsApprovalsQueryHandler.php');

use App\Model\PrsApprovals\PrsApprovalsQueryHandler;

class PrsLaborQueryHandler extends PrsApprovalsQueryHandler
{
  /**
   * selectPrss
   *
   * @param boolean $id
   * @param boolean $userId
   * @return void
   */
  public function selectPrss($id = false, $request_type = false, $status = false)
  {
    $fields = array(
      'PR.id',
      'PR.project_id',
      'PR.department_id',
      'PR.user_id',
      'PR.prs_no',
      'PR.request_type_id',
      'PR.head_id',
      'PR.for_cancelation',
      'RT.name as request_type_name',
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
    $initQuery = ($status) ? $initQuery->andWhere(array('PR.status' => ':status')) : $initQuery;
    $initQuery = $initQuery->andWhereNotNull(array('PR.signatories'));

    return $initQuery;
  }

  public function selectRequestLaborItems($id = false, $prsId = false, $mode = false)
  {
    $fields = array(
      'PRL.id',
      'PRL.pr_id',
      'PRL.category',
      'PRL.position_id',
      'PRL.mandays',
      'PRL.no_of_labor',
      'DATE_FORMAT(PRL.start_date, "%m/%d/%Y") as start_date',
      'PRL.labor_days',
      'PRL.account_id',
      'PRL.signatories',
      'PRL.remarks',
      'PRL.status',
      'P.name as position_name',
    );

    $joins = array(
      'positions P' => 'PRL.position_id = P.id',
    );

    $initQuery = $this->select($fields)
      ->from('pr_labors PRL')
      ->join($joins)
      ->where(array('PRL.is_active' => ':is_active'));

    $initQuery = ($id) ? $initQuery->andWhere(array('PRL.id' => ':prd_id')) : $initQuery;
    $initQuery = ($prsId) ? $initQuery->andWhere(array('PRL.pr_id' => ':prs_id')) : $initQuery;
    $initQuery = ($mode) ? $initQuery->andWhereNotEqual(array('PRL.status' => ':status')) : $initQuery;

    return $initQuery;
  }

  public function selectLaborWorkItems($id = false, $prlId = false)
  {
    $fields = array(
      'PRLW.id',
      'PRLW.pre_id',
      'PRLW.prl_id',
      'PRLW.ps_swi_directs_id',
      'PRLW.p_wi_indirects_id',
      'PRLW.wi_category_id',
      'PRLW.wi_id',
      'PRLW.wic_id',
      'PRLW.swic_id',
      'PRLW.work_volume',
      'PRLW.wv_unit',
      'PRLW.wbs',
      'DATE_FORMAT(PRLW.mobilization_date, "%m/%d/%Y") as mobilization_date',
      'DATE_FORMAT(PRLW.date_from, "%m/%d/%Y") as date_from',
      'PRLW.requested_labor',
      'PRLW.manpower',
      'PRLW.man_days',
    );
    

    $initQuery = $this->select($fields)
      ->from('prl_work_items PRLW')
      ->where(array('PRLW.is_active' => ':is_active'));
    $initQuery = ($id) ? $initQuery->andWhere(array('PRLW.id' => ':id')) : $initQuery;
    $initQuery = ($prlId) ? $initQuery->andWhere(array('PRLW.prl_id' => ':prl_id')) : $initQuery;


    return $initQuery;
  }

  
  public function selectAttachments($id = false, $preId = false)
  {
    $fields = array(
      'PLA.filename',
    );

    $initQuery = $this->select($fields)
      ->from('pr_labor_attachments PLA')
      ->where(array('PLA.is_active' => ':is_active'));

    $initQuery = ($id) ? $initQuery->andWhere(array('PLA.id' => ':id')) : $initQuery;
    $initQuery = ($preId) ? $initQuery->andWhere(array('PLA.pr_labor_id' => ':pre_id')) : $initQuery;

    return $initQuery;
  }


  // public function selectMaterialSpecs($id = false, $userId = false, $filter = false, $limit = '')
  // {
  //   $fields = array(
  //     'MS.id',
  //     'MS.material_id',
  //     'MS.specs',
  //     'MS.code',
  //     // 'CONCAT(MS.code, MSBS.code) as mat_code',
  //     'MSBS.code as mat_code',
  //     'MSBS.unit'
  //   );

  //   $joins = array(
  //     'msb_suppliers MSBS' => 'MSBS.material_specification_brand_id = MS.id'
  //   );

  //   $initQuery = $this->select($fields)
  //     ->from('material_specifications MS')
  //     ->join($joins);
  //   // $initQuery = ($filter) ? $initQuery->logicEx('JOIN materials M ON M.id = MS.material_id JOIN  ON') : $initQuery;
  //   $initQuery = $initQuery->where(array('MS.is_active' => ':is_active'));

  //   $initQuery = ($id) ? $initQuery->andWhere(array('MS.id' => ':id')) : $initQuery;
  //   $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('MS.specs' => ':filter', 'MS.code' => ':filter')) : $initQuery;
  //   $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT ' . $limit . ', 50') : $initQuery;

  //   return $initQuery;
  // }

  // public function selectDeliverySequence($id = false, $prdId = false)
  // {
  //   $fields = array(
  //     'PRDS.id',
  //     'PRDS.purchase_requisition_description_id',
  //     'PRDS.seq_no',
  //     'PRDS.delivery_date',
  //     'PRDS.quantity',
  //     'PRDS.is_consumed',
  //     'PRDS.is_active',
  //     'PRDS.created_by',
  //     'PRDS.created_at',
  //     'PRDS.updated_by',
  //     'PRDS.updated_at'
  //   );

  //   $initQuery = $this->select($fields)
  //     ->from('prd_delivery_sequences PRDS')
  //     ->where(array('PRDS.is_active' => ':is_active'));

  //   $initQuery = ($id) ? $initQuery->andWhere(array('PRDS.id' => ':id')) : $initQuery;
  //   $initQuery = ($prdId) ? $initQuery->andWhere(array('PRDS.purchase_requisition_description_id' => ':prd_id')) : $initQuery;

  //   return $initQuery;
  // }

   /**
   * selectPsdLabor
   *
   * @param boolean $ps_swi_direct_id
   * @param boolean $p_wi_indirect_id
   * @return void
   */
  public function selectPsdLabor($id = false, $ps_swi_direct_id = false, $p_wi_indirect_id = false)
  {
      $fields = array(
      'PL.id',
      'PL.p_wi_indirect_id',
      'PL.ps_swi_direct_id',
      'IF(PL.p_wi_indirect_id IS NULL, 0,PL.skilled_workers) as indirect_skilled_workers',
      'IF(PL.ps_swi_direct_id IS NULL, 0,PL.skilled_workers) as direct_skilled_workers',
      'IF(PL.p_wi_indirect_id IS NULL, 0,PL.common_workers) as indirect_common_workers',
      'IF(PL.ps_swi_direct_id IS NULL, 0,PL.common_workers) as direct_common_workers',
      'DATE_FORMAT(PL.date_from, "%m/%d/%Y") as date_from',
      );

      $initQuery = $this->select($fields)
      ->from('psd_labors PL')
      ->where(array('PL.is_active' => ':is_active'));

      $initQuery = ($id) ? $initQuery->andWhere(array('PL.id' => ':id')) : $initQuery;
      $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PL.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
      $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PL.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;

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

  public function updateRequest($id = '', $data = array())
  {
    $initQuery = $this->update('purchase_requisitions', $id, $data);
    return $initQuery;
  }

  public function updateRequestLabor($id = '', $data = array())
  {
    $initQuery = $this->update('pr_labors', $id, $data);
    return $initQuery;
  }
}
