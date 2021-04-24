<?php
    namespace App\Controller\MarketValueRevision;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/MarketValueRevision/MarketValueRevisionQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\MarketValueRevision\MarketValueRevisionQueryHandler as QueryHandler;

    class MarketValueRevisionController extends BaseController {
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
                'market_values' => $this->getMarketValues()
            ];

            return $output;
        }

        public function getSelectionDetails()
        {
            $output = [
                'classifications' => $this->getClassifications(),
                'revision_years'  => $this->getRevisionYears()
            ];

            return $output;
        }

        public function getSubClassSelection($data)
        {
            $output = [
                'sub_classifications' => $this->getSubClassifications('', $data['id'])
            ];

            return $output;
        }

        public function getMarketValues($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $marketValues = $this->dbCon->prepare($this->queryHandler->selectMarketValues($hasId)->end());
            $marketValues->execute($data);

            $result = $marketValues->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['sub_classification'] = $this->getSubClassifications($value['sub_classification_id'])[0];
                $result[$key]['revision_year']      = $this->getRevisionYears($value['revision_year_id'])[0];
                $result[$key]['market_value']       = floatval($value['market_value']);
            }

            return $result;
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

        public function getSubClassifications($id = '', $class_id = '')
        {
            $hasId      = empty($id)        ? false : true;
            $hasClassId = empty($class_id)  ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId)        ? $data['id'] = $id : '';
            ($hasClassId)   ? $data['class_id'] = $class_id : '';

            $subclassifications = $this->dbCon->prepare($this->queryHandler->selectSubClassifications($hasId, $hasClassId)->orderBy('SC.name', 'ASC')->end());
            $subclassifications->execute($data);

            $outputData = $subclassifications->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($outputData as $key => $value) {
                $outputData[$key]['classification'] = $this->getClassifications($value['classification_id'])[0];
            }

            return $outputData;
        }

        public function getRevisionYears($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $revYears = $this->dbCon->prepare($this->queryHandler->selectRevisionYears($hasId)->orderBy('RY.year', 'DESC')->end());
            $revYears->execute($data);

            return $revYears->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function saveNewMarketValue($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'sub_classification_id' => $input->subclassification->id,
                    'revision_year_id'      => $input->revision_year->id,
                    'market_value'          => $input->market_value,
                    'description'           => isset($input->description) ? (!empty($input->description) ? $input->description : null) : null,
                    'created_by'            => $_SESSION['user_id'],
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_by'            => $_SESSION['user_id'],
                    'updated_at'            => date('Y-m-d H:i:s')
                ];

                $insertData = $this->dbCon->prepare($this->queryHandler->insertTblData('market_values', $entryData));
                $status = $insertData->execute($entryData);
                $newMarketValueId = $this->dbCon->lastInsertId();
                $this->systemLogs($newMarketValueId, 'market_values', 'MARKET VALUE CONFIG - REVISION', 'insert');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getMarketValues($newMarketValueId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function saveUpdatedMarketValue($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'sub_classification_id' => $input->subclassification->id,
                    'revision_year_id'      => $input->revision_year->id,
                    'market_value'          => $input->market_value,
                    'description'           => isset($input->description) ? (!empty($input->description) ? $input->description : null) : null,
                    'updated_by'            => $_SESSION['user_id'],
                    'updated_at'            => date('Y-m-d H:i:s')
                ];

                $updateData = $this->dbCon->prepare($this->queryHandler->updateTblData('market_values', $input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'market_values', 'MARKET VALUE CONFIG - REVISION', 'update');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getMarketValues($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function archiveMarketValue($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'    => 0,
                    'updated_by'   => $_SESSION['user_id'],
                    'updated_at'   => date('Y-m-d H:i:s')
                ];

                $archiveData = $this->dbCon->prepare($this->queryHandler->updateTblData('market_values', $input->id, $entryData));
                $status = $archiveData->execute($entryData);
                $this->systemLogs($input->id, 'market_values', 'MARKET VALUE CONFIG - REVISION', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }
    }