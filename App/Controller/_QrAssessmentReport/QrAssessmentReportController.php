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
                    [
                        'key'   => 'residential',
                        'value' => 'Residential',
                    ],
                    [
                        'key'   => 'agricultural',
                        'value' => 'Agricultural',
                    ],
                    [
                        'key'   => 'cultural',
                        'value' => 'Cultural',
                    ],
                    [
                        'key'   => 'industrial',
                        'value' => 'Industrial',
                    ],
                    [
                        'key'   => 'mineral',
                        'value' => 'Mineral',
                    ],
                    [
                        'key'   => 'timber',
                        'value' => 'Timber',
                    ],
                    [
                        'key'   => 'special',
                        'value' => 'Special (Sec, 218(d))',
                    ],
                    [
                        'key'   => 'sp_machineries',
                        'value' => 'Machineries',
                    ],
                    [
                        'key'   => 'sp_cultural',
                        'value' => 'Cultural',
                    ],
                    [
                        'key'   => 'sp_scientific',
                        'value' => 'Scientific',
                    ],
                    [
                        'key'   => 'sp_hospital',
                        'value' => 'Hospital',
                    ],
                    [
                        'key'   => 'sp_lwua',
                        'value' => 'Local water Utilities Administraton (LWUA)',
                    ],
                    [
                        'key'   => 'sp_gocc',
                        'value' => 'GOCC - Water/Electric',
                    ],
                    [
                        'key'   => 'sp_recreation',
                        'value' => 'Recreation',
                    ],
                    [
                        'key'   => 'sp_others',
                        'value' => 'Others',
                    ]
                ];

                $exempt_classifications = [
                    [
                        'key'   => 'government',
                        'value' => 'Government',
                    ],
                    [
                        'key'   => 'religious',
                        'value' => 'Religious',
                    ],
                    [
                        'key'   => 'charitable',
                        'value' => 'Charitable',
                    ],
                    [
                        'key'   => 'educational',
                        'value' => 'Educational',
                    ],
                    [
                        'key'   => 'machineries_lwd',
                        'value' => 'Machineries - Local Water District (LWD)',
                    ],
                    [
                        'key'   => 'machineries_gocc',
                        'value' => 'Machineries - GOCC',
                    ],
                    [
                        'key'   => 'pcep',
                        'value' => 'Pollution Control and Environmental Protection',
                    ],
                    [
                        'key'   => 'reg_coop',
                        'value' => 'Reg. Coop. (R.A. 6938)',
                    ],
                    [
                        'key'   => 'others',
                        'value' => 'Others',
                    ]
                ];

                $records = [
                    'taxable' => [],
                    'exempt'  => [],
                    'brgys'   => []
                ];

                foreach ($taxable_classifications as $key => $value) {
                    $records['taxable'][$value['key']] = [
                        'total_land_area_sqm' => $this->getTotalLandArea($from_date, $to_date, 'taxable', $value['value'], 'land_area'),
                        'total_brgys'         => $this->getTotalLandArea($from_date, $to_date, 'taxable', $value['value'], 'brgys'),
                        'no_rpu'              => [
                            'land'      => $this->getTotalNumberOfRPU($from_date, $to_date, 'taxable', 'land', $value['value']),
                            'building'  => $this->getTotalNumberOfRPU($from_date, $to_date, 'taxable', 'building', $value['value']),
                            'machinery' => $this->getTotalNumberOfRPU($from_date, $to_date, 'taxable', 'machinery', $value['value']),
                            'others'    => $this->getTotalNumberOfRPU($from_date, $to_date, 'taxable', 'others', $value['value']),
                        ],
                        'market_value'  => [
                            'land'      => $this->getTotalMarketValue($from_date, $to_date, 'taxable', 'land', $value['value']),
                            'building'  => [
                                'below_limit'   => $this->getTotalMarketValue($from_date, $to_date, 'taxable', 'building', $value['value'], 'below'),
                                'above_limit'   => $this->getTotalMarketValue($from_date, $to_date, 'taxable', 'building', $value['value'], 'above'),
                                'building'      => $this->getTotalMarketValue($from_date, $to_date, 'taxable', 'building', $value['value']),
                            ],
                            'machinery' => $this->getTotalMarketValue($from_date, $to_date, 'taxable', 'machinery', $value['value']),
                            'others'    => $this->getTotalMarketValue($from_date, $to_date, 'taxable', 'others', $value['value']),
                        ],
                        'assessed_value' => [
                            'land'      => $this->getTotalAssessedValue($from_date, $to_date, 'taxable', 'land', $value['value']),
                            'building'  => $this->getTotalAssessedValue($from_date, $to_date, 'taxable', 'building', $value['value']),
                            'machinery' => $this->getTotalAssessedValue($from_date, $to_date, 'taxable', 'machinery', $value['value']),
                            'others'    => $this->getTotalAssessedValue($from_date, $to_date, 'taxable', 'others', $value['value']),
                        ],
                    ];

                    $records['taxable'][$value['key']]['total_av']  = floatval($records['taxable'][$value['key']]['assessed_value']['land']) + floatval($records['taxable'][$value['key']]['assessed_value']['building']) + floatval($records['taxable'][$value['key']]['assessed_value']['machinery']) + floatval($records['taxable'][$value['key']]['assessed_value']['others']);
                    $records['taxable'][$value['key']]['basic_tax'] = floatval($records['taxable'][$value['key']]['total_av']) * 0.01;
                    $records['taxable'][$value['key']]['sef_tax']   = floatval($records['taxable'][$value['key']]['total_av']) * 0.01;

                    foreach ($records['taxable'][$value['key']]['total_brgys'] as $bkey => $bvalue) {
                        array_push($records['brgys'], $bvalue);
                    }
                }

                foreach ($exempt_classifications as $key => $value) {
                    $records['exempt'][$value['key']] = [
                        'total_land_area_sqm' => $this->getTotalLandArea($from_date, $to_date, 'exempt', $value['value'], 'land_area'),
                        'total_brgys'         => $this->getTotalLandArea($from_date, $to_date, 'exempt', $value['value'], 'brgys'),
                        'no_rpu'              => [
                            'land'      => $this->getTotalNumberOfRPU($from_date, $to_date, 'exempt', 'land', $value['value']),
                            'building'  => $this->getTotalNumberOfRPU($from_date, $to_date, 'exempt', 'building', $value['value']),
                            'machinery' => $this->getTotalNumberOfRPU($from_date, $to_date, 'exempt', 'machinery', $value['value']),
                            'others'    => $this->getTotalNumberOfRPU($from_date, $to_date, 'exempt', 'others', $value['value']),
                        ],
                        'market_value'  => [
                            'land'      => $this->getTotalMarketValue($from_date, $to_date, 'exempt', 'land', $value['value']),
                            'building'  => [
                                'below_limit'   => $this->getTotalMarketValue($from_date, $to_date, 'exempt', 'building', $value['value'], 'below'),
                                'above_limit'   => $this->getTotalMarketValue($from_date, $to_date, 'exempt', 'building', $value['value'], 'above'),
                                'building'      => $this->getTotalMarketValue($from_date, $to_date, 'exempt', 'building', $value['value']),
                            ],
                            'machinery' => $this->getTotalMarketValue($from_date, $to_date, 'exempt', 'machinery', $value['value']),
                            'others'    => $this->getTotalMarketValue($from_date, $to_date, 'exempt', 'others', $value['value']),
                        ],
                        'assessed_value' => [
                            'land'      => $this->getTotalAssessedValue($from_date, $to_date, 'exempt', 'land', $value['value']),
                            'building'  => $this->getTotalAssessedValue($from_date, $to_date, 'exempt', 'building', $value['value']),
                            'machinery' => $this->getTotalAssessedValue($from_date, $to_date, 'exempt', 'machinery', $value['value']),
                            'others'    => $this->getTotalAssessedValue($from_date, $to_date, 'exempt', 'others', $value['value']),
                        ],
                    ];

                    $records['exempt'][$value['key']]['total_av'] = floatval($records['exempt'][$value['key']]['assessed_value']['land']) + floatval($records['exempt'][$value['key']]['assessed_value']['building']) + floatval($records['exempt'][$value['key']]['assessed_value']['machinery']) + floatval($records['exempt'][$value['key']]['assessed_value']['others']);
                    foreach ($records['exempt'][$value['key']]['total_brgys'] as $bkey => $bvalue) {
                        array_push($records['brgys'], $bvalue);
                    }
                }

                $records['brgys'] = $this->filterDuplicates($records['brgys'], 'barangay_id');

                $output = [
                    'records'       => $records,
                    'mun_assessor'  => !empty($this->getMunicipalAssessorData()) ? $this->getMunicipalAssessorData()[0] : []
                ];

            } else {
                $output = [
                    'input_error' => true
                ];
            }

            return $output;
        }

        public function getTotalLandArea($from_date, $to_date, $tax_type, $classification, $condition)
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
                'from_date' => $from_date,
                'to_date'   => $to_date,
            ];

            $query = $this->queryHandler->selectTotalLandArea($condition);
            $query = ($tax_type == 'taxable') ? $query->andWhereNotNull(['TD.is_taxable']) : $query->andWhereNotNull(['TD.is_exempt']);
            $query = $query->logicEx('AND (C.name LIKE "'.$classification.'" OR TDC.actual_use LIKE "'.$classification.'")');
            
            if($condition == 'brgys') $query = $query->groupBy('TD.barangay_id');

            $totalLandArea = $this->dbCon->prepare($query->end());
            $totalLandArea->execute($data);

            $result = $totalLandArea->fetchAll(\PDO::FETCH_ASSOC);

            if ($condition == 'land_area') {
                return !empty($result) ? (($result[0]['total_area'] != null) ? floatval($result[0]['total_area']) : 0) : 0;
            } else if ($condition == 'brgys') {
                return $result;
            }
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

            $result = $totalNumberOfRPU->fetchAll(\PDO::FETCH_ASSOC);

            return !empty($result) ? (($result[0]['total_rpu'] != null) ? floatval($result[0]['total_rpu']) : 0) : 0;
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
                                C.name LIKE "'.$classification.'" OR 
                                C.name LIKE "%improvement%" OR 
                                TDC.actual_use LIKE "'.$classification.'" OR 
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
                    $query = $query->logicEx('AND (C.name LIKE "'.$classification.'" OR TDC.actual_use LIKE "'.$classification.'")');
                }
                
            } else {
                $query = $query->logicEx('AND (C.name LIKE "'.$classification.'" OR TDC.actual_use LIKE "'.$classification.'")');
            }
            

            $totalMarketValue = $this->dbCon->prepare($query->end());
            $totalMarketValue->execute($data);

            $result = $totalMarketValue->fetchAll(\PDO::FETCH_ASSOC);

            return !empty($result) ? (($result[0]['total_market_value'] != null) ? floatval($result[0]['total_market_value']) : 0) : 0;
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

            $result = $totalAssessedValue->fetchAll(\PDO::FETCH_ASSOC);

            return !empty($result) ? (($result[0]['total_assessed_value'] != null) ? floatval($result[0]['total_assessed_value']) : 0) : 0;
        }

        public function getMunicipalAssessorData()
        {
            $data = [
                'is_active' => 1,
                'position'  => 'Municipal Assessor'
            ];

            $mun_assessor = $this->dbCon->prepare($this->queryHandler->selectMunicipalAssessorData()->end());
            $mun_assessor->execute($data);

            return $mun_assessor->fetchAll(\PDO::FETCH_ASSOC);
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

        public function filterDuplicates($data, $itemKey)
        {
            $temp_array = array();
            $key_array  = array();
        
            foreach($data as $key => $val) {
                if (!in_array($val[$itemKey], $key_array)) {
                    $key_array[$key]  = $val[$itemKey];
                    $temp_array[$key] = $val;
                }
            }

            return array_values($temp_array);
        }
    }