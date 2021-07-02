<?php
    namespace App\Controller\QrAssessmentReport;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/QrAssessmentReport/QrAssessmentReportQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\QrAssessmentReport\QrAssessmentReportQueryHandler as QueryHandler;
    use Exception;

    class QrAssessmentReportController extends BaseController {

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

        public function getDetails($data)
        {
            $dateExplode = explode(' - ', $data->date_range);
            if (count($dateExplode) == 2) {
                $from_date  = $this->formatDate($dateExplode[0]);
                $to_date    = $this->formatDate($dateExplode[1]);

                $taxable_classifications = [
                    'Residential',
                    'Agricultural',
                    'Cultural',
                    'Industrial',
                    'Mineral',
                    'Timber',
                    'Special (Sec, 218(d))',
                    'Machineries',
                    'Cultural',
                    'Scientific',
                    'Hospital',
                    'Local Water Utilities Administration (LWUA)',
                    'GOCC - Water/Electric',
                    'Recreation',
                    'Others',
                ];

                $exempt_classifications = [
                    'Government',
                    'Religious',
                    'Charitable',
                    'Educational',
                    'Machineries - Local Water District (LWD)',
                    'Machineries - GOCC',
                    'Pollution Control and Environmental Protection',
                    'Reg. Coop. (R.A. 6938)',
                    'Others',
                ];

                $records = [
                    'taxable' => [],
                    'exempt'  => []
                ];

                

            } else {
                $output = [
                    'input_error' => true
                ];
            }

            return $output;
        }

        public function getTotalLandArea($from_date, $to_date, $tax_type, $classification)
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
                'from_date' => $from_date,
                'to_date'   => $to_date,
            ];

            $query = $this->queryHandler->selectTotalLandArea();
            $query = ($tax_type == 'taxable') ? $query->andWhereNotNull(['TD.is_taxable']) : $query->andWhereNotNull(['TD.is_exempt']);
            $query = $query->logicEx('AND (C.name LIKE "'.$classification.'" OR TDC.actual_use LIKE "'.$classification.'")');
            
            $totalLandArea = $this->dbCon->prepare($query->end());
            $totalLandArea->execute($data);

            return $totalLandArea->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getTotalNumberOfRPU($from_date, $to_date, $tax_type, $property_kind, $classification)
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
                'prop_kind' => $this->formatStr($property_kind),
                'from_date' => $from_date,
                'to_date'   => $to_date,
            ];

            $query = $this->queryHandler->selectTotalNumberOfRPU();
            $query = ($tax_type == 'taxable') ? $query->andWhereNotNull(['TD.is_taxable']) : $query->andWhereNotNull(['TD.is_exempt']);
            $query = $query->logicEx('AND (C.name LIKE "'.$classification.'" OR TDC.actual_use LIKE "'.$classification.'")');

            $totalNumberOfRPU = $this->dbCon->prepare($query->end());
            $totalNumberOfRPU->execute($data);

            return $totalNumberOfRPU->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getTotalMarketValue($from_date, $to_date, $tax_type, $property_kind, $classification, $mvLimit = '')
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
                'prop_kind' => $this->formatStr($property_kind),
                'from_date' => $from_date,
                'to_date'   => $to_date,
            ];

            $query = $this->queryHandler->selectTotalMarketValue();
            $query = ($tax_type == 'taxable') ? $query->andWhereNotNull(['TD.is_taxable']) : $query->andWhereNotNull(['TD.is_exempt']);
            if (strtoupper($property_kind) == 'BUILDING') {
                if (strtoupper($classification) == 'RESIDENTIAL') {
                    $query = $query->logicEx(
                        'AND 
                            (
                                C.name LIKE "'.$this->formatStr($classification).'" OR 
                                C.name LIKE "%improvement%" OR 
                                TDC.actual_use LIKE "'.$this->formatStr($classification).'" OR 
                                TDC.actual_use LIKE "%improvement%"
                            )
                        '
                    );
                    if ($mvLimit == 'below') {
                        $query = $query->logicEx('AND TDC.market_value < 175000');
                    } else if ($mvLimit == 'above') {
                        $query = $query->logicEx('AND TDC.market_value >= 175000');
                    }
                } else {
                    $query = $query->logicEx('AND (C.name LIKE "'.$this->formatStr($classification).'" OR TDC.actual_use LIKE "'.$this->formatStr($classification).'")');
                }
                
            } else {
                $query = $query->logicEx('AND (C.name LIKE "'.$this->formatStr($classification).'" OR TDC.actual_use LIKE "'.$this->formatStr($classification).'")');
            }
            

            $totalMarketValue = $this->dbCon->prepare($query->end());
            $totalMarketValue->execute($data);

            return $totalMarketValue->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getTotalAssessedValue($from_date, $to_date, $tax_type, $property_kind, $classification)
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
                'prop_kind' => $this->formatStr($property_kind),
                'from_date' => $from_date,
                'to_date'   => $to_date,
            ];

            $query = $this->queryHandler->selectTotalAssessedValue();
            $query = ($tax_type == 'taxable') ? $query->andWhereNotNull(['TD.is_taxable']) : $query->andWhereNotNull(['TD.is_exempt']);
            $query = $query->logicEx('AND (C.name LIKE "'.$classification.'" OR TDC.actual_use LIKE "'.$classification.'")');

            $totalAssessedValue = $this->dbCon->prepare($query->end());
            $totalAssessedValue->execute($data);

            return $totalAssessedValue->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function formatStr($str)
        {
            $strExplode = explode(" ", $str);
            $outputStr = '%';
            foreach ($strExplode as $value) {
                $outputStr .= $value.'%';
            }
            
            return $outputStr;
        }
    }