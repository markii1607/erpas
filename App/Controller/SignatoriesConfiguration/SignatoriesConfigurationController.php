<?php
    namespace App\Controller\SignatoriesConfiguration;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/SignatoriesConfiguration/SignatoriesConfigurationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\SignatoriesConfiguration\SignatoriesConfigurationQueryHandler as QueryHandler;
use Exception;

class SignatoriesConfigurationController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        protected $menu_id = 47;

        /**
         * `$dbCon` Concern in database connection.
         * @var private class
         */
        protected $dbCon;

        /**
         * `$queryHandler` Handles query.
         * @var private class
         */
        protected $queryHandler;

        /**
         * `__construct` Constructor
         * @param object $dbCon        Database connetor
         * @param string $queryHandler Query String
         */
        public function __construct(
            $dbCon
        ) {
            parent::__construct();

            $this->dbCon        = $dbCon;
            $this->queryHandler = new QueryHandler();
        }

        /**
         * `getDetails` Get details needed in menu configuration
         * @param  string $id
         * @return array    
         */
        public function getDetails($data = [])
        {
            $output = [
                'approvers' => $this->getApprovers()
            ];

            return $output;
        }

        public function saveNewApprover($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'approvers'   => json_encode($input),
                    'created_by'  => $_SESSION['user_id'],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $insertApproverData = $this->dbCon->prepare($this->queryHandler->insertApproverSet($entryData));
                $status = $insertApproverData->execute($entryData);
                $newApproverId = $this->dbCon->lastInsertId();
                $this->systemLogs($newApproverId, 'approver_sets', 'Signatories Configuration', 'add');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getApprovers($newApproverId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function saveEditApprover($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'approvers'   => json_encode($input->approvers),
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $updateApproverData = $this->dbCon->prepare($this->queryHandler->updateApproverSet($input->id, $entryData));
                $status = $updateApproverData->execute($entryData);
                $this->systemLogs($input->id, 'approver_sets', 'Signatories Configuration', 'update');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getApprovers($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function archiveApprover($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'   => 0,
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $archiveApproverData = $this->dbCon->prepare($this->queryHandler->updateApproverSet($input->id, $entryData));
                $status = $archiveApproverData->execute($entryData);
                $this->systemLogs($input->id, 'approver_sets', 'Signatories Configuration', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status,
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }

        public function getApprovers($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];
            
            ($id != '') ? $data['id'] = $id : '';

            $approvers = $this->dbCon->prepare($this->queryHandler->selectApprovers($idCondition)->end());
            $approvers->execute($data);

            $result = $approvers->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['approvers'] = json_decode($value['approvers']);
            }

            return $result;
        }
    }