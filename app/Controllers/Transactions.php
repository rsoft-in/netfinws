<?php

namespace App\Controllers;

use App\Models\AccountsModel;
use App\Models\TransactionsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use TCPDF;

class Transactions extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return $this->failUnauthorized();
    }

    public function getTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $transactionsModel = new TransactionsModel();
        $accountsModel = new AccountsModel();

        $filt = "";
        if (!isset($json->cid)) {
            return $this->failUnauthorized();
        }
        if (!empty($json->fdate))
            $filt .= " AND (txn_date >= '" . $json->fdate . "')";
        if (!empty($json->tdate))
            $filt .= " AND (txn_date <= '" . $json->tdate . "')";

        $data['transactions'] = $transactionsModel->getTransaction($json->acnt_id, $filt, $json->ps, $json->pn * $json->ps);
        $data['op_balance'] = $accountsModel->builder()
            ->select('acnt_opbal, accountgroups.ag_type')
            ->join('accountgroups', 'accountgroups.ag_id = accounts.acnt_ag_id', 'inner')
            ->where('acnt_id', $json->acnt_id)->get()->getResult();
        $data['records'] = $transactionsModel->getTransactionsCount($json->acnt_id, $filt);
        $data['op_totals'] = $transactionsModel->getOpeningTotals($json->acnt_id, $json->fdate);
        $data['cl_totals'] = $transactionsModel->getClosingTotals($json->acnt_id, $json->tdate);
        return $this->respond($data);
    }

    public function addTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $transactionsModel = new transactionsModel();
        $data = [
            'txn_id' => $json->txn_id,
            'txn_date' => $json->txn_date,
            'txn_ref_nr' => $json->txn_ref_nr,
            'txn_client_id' => $json->txn_client_id,
            'txn_acnt_id_dr' => $json->txn_acnt_id_dr,
            'txn_acnt_id_cr' => $json->txn_acnt_id_cr,
            'txn_amount_dr' => $json->txn_amount_dr,
            'txn_amount_cr' => $json->txn_amount_cr,
            'txn_remarks' => $json->txn_remarks,
            'txn_type' => $json->txn_type,
            'txn_user_id' => $json->txn_user_id,
            'txn_modified' => $today->toDateTimeString()
        ];
        $transactionsModel->addTransaction($data);
        if ($transactionsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($transactionsModel->db->error());
    }

    public function updateTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $transactionsModel = new TransactionsModel;
        $data = [
            'txn_id' => $json->txn_id,
            'txn_date' => $json->txn_date,
            'txn_ref_nr' => $json->txn_ref_nr,
            'txn_client_id' => $json->txn_client_id,
            'txn_acnt_id_dr' => $json->txn_acnt_id_dr,
            'txn_acnt_id_cr' => $json->txn_acnt_id_cr,
            'txn_amount_dr' => $json->txn_amount_dr,
            'txn_amount_cr' => $json->txn_amount_cr,
            'txn_remarks' => $json->txn_remarks,
            'txn_type' => $json->txn_type,
            'txn_user_id' => $json->txn_user_id,
            'txn_modified' => $today->toDateTimeString()
        ];
        $transactionsModel->updateTransaction($data);
        if ($transactionsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($transactionsModel->db->error());
    }

    public function deleteTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $transactionsModel = new TransactionsModel;
        $transactionsModel->deleteTransaction($json->txn_id);
        if ($transactionsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($transactionsModel->db->error());
    }

    public function print()
    {
        $json = json_decode($this->request->getGet('postdata'));
        $transactionsModel = new TransactionsModel();
        $accountsModel = new AccountsModel();

        $filter = "";
        if (!isset($json->cid)) {
            return $this->failUnauthorized();
        }
        if (!empty($json->fdate))
            $filter .= " AND (txn_date >= '" . $json->fdate . "')";
        if (!empty($json->tdate))
            $filter .= " AND (txn_date <= '" . $json->tdate . "')";

        $data['transactions'] = $transactionsModel->getTransaction($json->acnt_id, $filter, null, 0);
        $data['account'] = $accountsModel->builder()
            ->select('acnt_name, acnt_opbal, accountgroups.ag_type')
            ->join('accountgroups', 'accountgroups.ag_id = accounts.acnt_ag_id', 'inner')
            ->where('acnt_id', $json->acnt_id)->get()->getResult();
        $data['records'] = $transactionsModel->getTransactionsCount($json->acnt_id, $filter);
        $data['op_totals'] = $transactionsModel->getOpeningTotals($json->acnt_id, $json->fdate);

        $pagelayout = array(
            297,
            210
        );
        $pdf = new TCPDF('P', 'mm', $pagelayout, true, 'UTF-8', false);
        $pdf->SetMargins(10, PDF_MARGIN_TOP, 10, true);
        $pdf->SetHeaderData(
            '',
            0,
            'Ledger of ' . $data['account'][0]->acnt_name,
            Time::parse($json->fdate)->toLocalizedString('dd.MM.yyyy') . ' to ' . Time::parse($json->tdate)->toLocalizedString('dd.MM.yyyy'),
            array(0, 0, 0),
            array(255, 255, 255)
        );
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 10]);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 8);

        $tbl = "<table cellpadding=\"2\" cellspacing=\"0\">
            <thead>
                <tr>
                    <th style=\"border-bottom: 1px solid #000;\" width=\"55\">Date</th>
                    <th style=\"border-bottom: 1px solid #000;\" width=\"55\">Ref.Nr.</th>
                    <th style=\"border-bottom: 1px solid #000;\" width=\"205\">Particulars</th>
                    <th style=\"border-bottom: 1px solid #000; text-align: right;\" width=\"113\">Debit</th>
                    <th style=\"border-bottom: 1px solid #000; text-align: right;\" width=\"113\">Credit</th>
                </tr>
            </thead>
        </tbody>";
        $ledger = strtolower(str_replace(' ', '_', $data['account'][0]->acnt_name));
        $type = $data['account'][0]->ag_type;
        $totalDebit = 0;
        $totalCredit = 0;
        $openingBalance = $data['account'][0]->acnt_opbal + $data['op_totals'][0]->sum_amount_dr - $data['op_totals'][0]->sum_amount_cr;
        if ($type == 'BL' || $type == 'BA') {
            $tbl .= "<tr>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">" . Time::parse($json->fdate)->toLocalizedString('dd.MM.yyyy') . "</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">&nbsp;</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\"  width=\"205\">Opening Balance</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\">" . ($openingBalance > 0 ? number_format($openingBalance, 2) : "&nbsp;") . "</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\">" . ($openingBalance < 0 ? number_format(-$openingBalance, 2) : "&nbsp;") . "</td>";
            $tbl .= "</tr>";
            if ($openingBalance > 0) {
                $totalDebit += $openingBalance;
            } else {
                $totalCredit += - ($openingBalance);
            }
        }
        foreach ($data['transactions'] as $transaction) {
            $tbl .= "<tr>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">" . Time::parse($transaction->txn_date)->toLocalizedString('dd.MM.yyyy') . "</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">" . $transaction->txn_ref_nr . "</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\"  width=\"205\">" . ($json->acnt_id == $transaction->txn_acnt_id_dr ? $transaction->acnt_name_cr : $transaction->acnt_name_dr) . "<br><span style=\"color: #666;\"><i>" . $transaction->txn_remarks . "</i></span></td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\">" . ($json->acnt_id == $transaction->txn_acnt_id_dr ?  number_format($transaction->txn_amount, 2) : '&nbsp;') . "</td>";
            $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\">" . ($json->acnt_id == $transaction->txn_acnt_id_dr ? '&nbsp;' : number_format($transaction->txn_amount, 2)) . "</td>";
            $tbl .= "</tr>";
            if ($json->acnt_id == $transaction->txn_acnt_id_dr) {
                $totalDebit += $transaction->txn_amount;
            } else {
                $totalCredit += $transaction->txn_amount;
            }
        }
        $tbl .= "<tr>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">&nbsp;</td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">&nbsp;</td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\"  width=\"205\">&nbsp;</td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\"><strong>" . number_format($totalDebit, 2) . "</strong></td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\"><strong>" . number_format($totalCredit, 2) . "</strong></td>";
        $tbl .= "</tr>";
        $closingBalance = $totalDebit - $totalCredit;
        $tbl .= "<tr>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">&nbsp;</td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\" width=\"55\">&nbsp;</td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc;\"  width=\"205\">&nbsp;</td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\"><strong>" . ($closingBalance > 0 ? number_format($closingBalance, 2) : "&nbsp;") . "</strong></td>";
        $tbl .= "<td style=\"border-bottom: 0.5px solid #ccc; text-align: right;\" width=\"113\"><strong>" . ($closingBalance < 0 ? number_format(-$closingBalance, 2) : "&nbsp;") . "</strong></td>";
        $tbl .= "</tr>";
        $tbl .= "</tbody></table>";

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output($ledger . ".pdf", 'I');
    }
}
