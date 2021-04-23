<?php
    namespace App\Controller\MarketValueClassification;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/MarketValueClassification/MarketValueClassificationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\MarketValueClassification\MarketValueClassificationQueryHandler as QueryHandler;

    class MarketValueClassificationController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        protected $menu_id = 214;

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

        public function getDetails()
        {
            $output = [
                'classifications' => $this->getClassifications()
            ];

            return $output;
        }

        public function getClassifications($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $classifications = $this->dbCon->prepare($this->queryHandler->selectClassifications($hasId)->orderBy('C.name', 'ASC')->end());
            $classifications->execute($data);

            return $classifications->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function saveNewClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'name'          => $input->name,
                    'created_by'    => $_SESSION['user_id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $insertData = $this->dbCon->prepare($this->queryHandler->insertClassification($entryData));
                $status = $insertData->execute($entryData);
                $newClassificationId = $this->dbCon->lastInsertId();
                $this->systemLogs($newClassificationId, 'classifications', 'MARKET VALUE CONFIG - CLASSIFICATIONS', 'insert');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getClassifications($newClassificationId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function saveUpdatedClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'name'          => $input->name,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $updateData = $this->dbCon->prepare($this->queryHandler->updateClassification($input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'classifications', 'MARKET VALUE CONFIG - CLASSIFICATIONS', 'edit');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status,
                    'rowData'   => $this->getClassifications($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function archiveClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'     => 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $archiveData = $this->dbCon->prepare($this->queryHandler->updateClassification($input->id, $entryData));
                $status = $archiveData->execute($entryData);
                $this->systemLogs($input->id, 'classifications', 'MARKET VALUE CONFIG - CLASSIFICATIONS', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }
    }