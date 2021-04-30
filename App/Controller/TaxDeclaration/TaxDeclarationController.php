<?php
    namespace App\Controller\TaxDeclaration;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/TaxDeclaration/TaxDeclarationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\TaxDeclaration\TaxDeclarationQueryHandler as QueryHandler;
    use Exception;

    class TaxDeclarationController extends BaseController {
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

        public function getSubClassifications($class_id = '')
        {
            $hasClassId = empty($class_id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasClassId) ? $data['class_id'] = $class_id : '';

            $sub_classifications = $this->dbCon->prepare($this->queryHandler->selectSubClassifications($hasClassId)->orderBy('SC.name', 'ASC')->end());
            $sub_classifications->execute($data);

            return $sub_classifications->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getBarangays($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $barangays = $this->dbCon->prepare($this->queryHandler->selectBarangays($hasId)->orderBy('C.name', 'ASC')->end());
            $barangays->execute($data);

            return $barangays->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function archiveTaxDeclaration($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'     => 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $archiveData = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $input->id, $entryData));
                $status = $archiveData->execute($entryData);
                $this->systemLogs($input->id, 'tax_declarations', 'Tax Declaration of Real Property', 'archive');
            
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