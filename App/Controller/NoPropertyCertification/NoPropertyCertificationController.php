<?php
    namespace App\Controller\NoPropertyCertification;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/NoPropertyCertification/NoPropertyCertificationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\NoPropertyCertification\NoPropertyCertificationQueryHandler as QueryHandler;
    use Exception;

    class NoPropertyCertificationController extends BaseController {
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
                'certifications' => $this->getNoPropertyCertifications()
            ];

            return $output;
        }

        public function getNoPropertyCertifications($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $certifications = $this->dbCon->prepare($this->queryHandler->selectNoPropertyCertifications($hasId)->orderBy('RC.request_date', 'DESC')->end());
            $certifications->execute($data);

            $result = $certifications->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['prepared_by'] = $this->getUsers($value['prepared_by'])[0];
                $result[$key]['verified_by'] = $this->getUsers($value['verified_by'])[0];
                $result[$key]['date'] = [
                    'month' => date('M', strtotime($value['request_date'])),
                    'day'   => date('d', strtotime($value['request_date'])),
                    'year'  => date('Y', strtotime($value['request_date'])),
                ];
            }

            return $result;
        }

        public function getUsers($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $users = $this->dbCon->prepare($this->queryHandler->selectUsers($hasId)->orderBy('U.fname', 'ASC')->end());
            $users->execute($data);

            return $users->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getTaxDeclarationRecords($owner)
        {
            $data = [
                'is_active' => 1,
            ];


            $additionalQry = $this->likeConditionStr($owner);
            $query = $this->queryHandler->selectTaxDeclarationRecords();
            $query = $query->logicEx('AND ('.$additionalQry.')');
            $records = $this->dbCon->prepare($query->end());
            $records->execute($data);


            return $records->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function likeConditionStr($strData)
        {
            $strOutput = '';
            $strCount  = count($strData);
            foreach ($strData as $key => $value) {
                $strLikeCondition = $this->strLike($value);
                $strOutput .= 'TD.owner LIKE "'.$strLikeCondition.'"';
                if($key != ($strCount-1)) $strOutput .= ' OR ';
            }

            return $strOutput;
        }

        public function strLike($str)
        {
            $strExplode = explode(" ", $str);
            $outputStr = '%';
            foreach ($strExplode as $value) {
                $outputStr .= $value.'%';
            }
            
            return $outputStr;
        }
    }