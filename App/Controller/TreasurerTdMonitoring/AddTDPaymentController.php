<?php
    namespace App\Controller\TreasurerTdMonitoring;

    require_once("TreasurerTdMonitoringController.php");

    use App\Controller\TreasurerTdMonitoring\TreasurerTdMonitoringController as ModuleController;
    use Exception;

    class AddTDPaymentController extends ModuleController {

        public function getSelectionDetails()
        {
            $output = [
                'td_nos' => $this->getTDNumbers()
            ];

            return $output;
        }
        
        public function getRecords($input)
        {
            $lot_no = isset($input->lot_no)     ? $input->lot_no    : '';
            $td_id  = isset($input->td_no)      ? $input->td_no->id : '';
            $owner  = isset($input->declarant)  ? $input->declarant : '';

            $output = [
                'records' => $this->getTdRecords($lot_no, $td_id, $owner)
            ];

            return $output;
        }

        public function savePaymentDetails($input)
        {
            // print_r($input);
            // die();
            try {
                $this->dbCon->beginTransaction();

                $orEntryData = [
                    'user_id'           => $_SESSION['user_id'],
                    'transaction_date'  => $this->formatDate($input->transaction_date),
                    'or_no'             => $input->or_no,
                    'total_basic'       => $input->total_basic,
                    'total_sef'         => $input->total_sef,
                    'total_amount_paid' => $input->grand_total,
                    'paid_by'           => $input->paid_by,
                    'created_by'        => $_SESSION['user_id'],
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];

                $insertORData = $this->dbCon->prepare($this->queryHandler->insertTblData('paid_tax_declarations', $orEntryData));
                $insertORData->execute($orEntryData);
                $newOrID = $this->dbCon->lastInsertId();
                $this->systemLogs($newOrID, 'paid_tax_declarations', 'TREASURER - PAYMENT TRANSACTION', 'insert');

                foreach ($input->records as $key => $value) {
                    $tdEntryData = [
                        'is_paid'       => 1,
                        'updated_by'    => $_SESSION['user_id'],
                        'updated_at'    => date('Y-m-d H:i:s')
                    ];

                    $updateTdData = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $value->id, $tdEntryData));
                    $updateTdData->execute($tdEntryData);
                    $this->systemLogs($value->id, 'tax_declarations', 'TREASURER - PAYMENT TRANSACTION', 'update');

                    $orDetailEntryData = [
                        'paid_tax_declaration_id'   => $newOrID,
                        'tax_declaration_id'        => $value->id,
                        'tax_due'                   => $value->tax_due,
                        'created_by'                => $_SESSION['user_id'],
                        'created_at'                => date('Y-m-d H:i:s'),
                        'updated_by'                => $_SESSION['user_id'],
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ];

                    $insertORDetailData = $this->dbCon->prepare($this->queryHandler->insertTblData('paid_tax_declaration_details', $orDetailEntryData));
                    $status = $insertORDetailData->execute($orDetailEntryData);
                    $newOrDetailID = $this->dbCon->lastInsertId();
                    $this->systemLogs($newOrDetailID, 'paid_tax_declaration_details', 'TREASURER - PAYMENT TRANSACTION', 'insert');

                    foreach ($value->payments as $pkey => $pvalue) {
                        $installmentEntryData = [
                            'paid_tax_declaration_detail_id'    => $newOrDetailID,
                            'installment_text'                  => $pvalue->effectivity_year,
                            'full_payment'                      => $pvalue->full_payment,
                            'penalty_amount'                    => $pvalue->penalty_amount,
                            'total'                             => $pvalue->total_per_row,
                            'created_by'                        => $_SESSION['user_id'],
                            'created_at'                        => date('Y-m-d H:i:s'),
                            'updated_by'                        => $_SESSION['user_id'],
                            'updated_at'                        => date('Y-m-d H:i:s'),
                        ];
    
                        $insertInstallmentDetailData = $this->dbCon->prepare($this->queryHandler->insertTblData('paid_tax_declaration_detail_installments', $installmentEntryData));
                        $status = $insertInstallmentDetailData->execute($installmentEntryData);
                        $newInstallmentDetailID = $this->dbCon->lastInsertId();
                        $this->systemLogs($newInstallmentDetailID, 'paid_tax_declaration_detail_installments', 'TREASURER - PAYMENT TRANSACTION', 'insert');
                    }
                }
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getGeneratedOrNumbers($newOrID)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }
    }