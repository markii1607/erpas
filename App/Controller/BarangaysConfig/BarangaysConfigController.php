<?php
    namespace App\Controller\BarangaysConfig;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/BarangaysConfig/BarangaysConfigQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\BarangaysConfig\BarangaysConfigQueryHandler as QueryHandler;

    class BarangaysConfigController extends BaseController {
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
                'barangays' => $this->getBarangays()
            ];

            return $output;
        }

        public function getBarangays($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $barangays = $this->dbCon->prepare($this->queryHandler->selectBarangays($hasId)->orderBy('B.code', 'ASC')->end());
            $barangays->execute($data);

            return $barangays->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function saveNewBarangay($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'code'           => $input->code,
                    'name'           => $input->name,
                    'no_of_sections' => $input->no_of_sections,
                    'created_by'     => $_SESSION['user_id'],
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_by'     => $_SESSION['user_id'],
                    'updated_at'     => date('Y-m-d H:i:s')
                ];

                $insertData = $this->dbCon->prepare($this->queryHandler->insertBarangay($entryData));
                $status = $insertData->execute($entryData);
                $newBarangayId = $this->dbCon->lastInsertId();
                $this->systemLogs($newBarangayId, 'barangays', 'BARANGAYS CONFIGURATION', 'insert');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getBarangays($newBarangayId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function saveUpdatedBarangay($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'code'           => $input->code,
                    'name'           => $input->name,
                    'no_of_sections' => $input->no_of_sections,
                    'updated_by'     => $_SESSION['user_id'],
                    'updated_at'     => date('Y-m-d H:i:s')
                ];

                $updateData = $this->dbCon->prepare($this->queryHandler->updateBarangay($input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'barangays', 'BARANGAYS CONFIGURATION', 'edit');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status,
                    'rowData'   => $this->getBarangays($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function archiveBarangay($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'     => 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $archiveData = $this->dbCon->prepare($this->queryHandler->updateBarangay($input->id, $entryData));
                $status = $archiveData->execute($entryData);
                $this->systemLogs($input->id, 'barangays', 'BARANGAYS CONFIGURATION', 'archive');
            
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