<?php
    namespace App\Controller\MarketValueSubclassification;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/MarketValueSubclassification/MarketValueSubclassificationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\MarketValueSubclassification\MarketValueSubclassificationQueryHandler as QueryHandler;
    use Exception;

    class MarketValueSubclassificationController extends BaseController {
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
                'subClassifications' => $this->getSubClassifications()
            ];

            return $output;
        }

        public function getSelectionDetails()
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

        public function getSubClassifications($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $subclassifications = $this->dbCon->prepare($this->queryHandler->selectSubClassifications($hasId)->orderBy('SC.name', 'ASC')->end());
            $subclassifications->execute($data);

            $outputData = $subclassifications->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($outputData as $key => $value) {
                $outputData[$key]['classification'] = $this->getClassifications($value['classification_id'])[0];
            }

            return $outputData;
        }

        public function saveNewSubClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'classification_id' => $input->classification->id,
                    'name'              => $input->name,
                    'created_by'        => $_SESSION['user_id'],
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s')
                ];

                $insertData = $this->dbCon->prepare($this->queryHandler->insertSubClassification($entryData));
                $status = $insertData->execute($entryData);
                $newSubClassificationId = $this->dbCon->lastInsertId();
                $this->systemLogs($newSubClassificationId, 'sub_classifications', 'MARKET VALUE CONFIG - SUB-CLASSIFICATIONS', 'insert');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getSubClassifications($newSubClassificationId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function saveUpdatedSubClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'classification_id' => $input->classification->id,
                    'name'              => $input->name,
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s')
                ];

                $updateData = $this->dbCon->prepare($this->queryHandler->updateSubClassification($input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'sub_classifications', 'MARKET VALUE CONFIG - SUB-CLASSIFICATIONS', 'edit');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status,
                    'rowData'   => $this->getSubClassifications($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function archiveSubClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'     => 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $archiveData = $this->dbCon->prepare($this->queryHandler->updateSubClassification($input->id, $entryData));
                $status = $archiveData->execute($entryData);
                $this->systemLogs($input->id, 'sub_classifications', 'MARKET VALUE CONFIG - SUB-CLASSIFICATIONS', 'archive');
            
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