<?php

namespace BalanceUpdater;

use PhpOffice\PhpSpreadsheet\IOFactory;

function getCellValue($sheet, $col, $row)
{
    return $sheet
        ->getCellByColumnAndRow($col, $row)
        ->getValue();
}

function getClientsList($clientsSheet)
{
    $arrayOfClients = array();
    $highestRow = $clientsSheet->getHighestRow();
    $idCol = 0;
    $nameCol = 1;
    $balanceCol = 2;
    for ($row = 1; $row <= $highestRow; ++$row) {
        $id = getCellValue($clientsSheet, $idCol, $row);
        $name = getCellValue($clientsSheet, $nameCol, $row);
        $balance = getCellValue($clientsSheet, $balanceCol, $row);
        if (is_float($id) and is_float($balance)) {
            $user = new User($id, $name, $balance);
            $arrayOfClients[$user->getId()] = $user;
        } elseif (is_string($id) or is_string($balance)) {
            throw new \Exception("Check please 'first' sheet. There is an error in the row: {$row}");
        }
    }
    return $arrayOfClients;
}

function getTransactions($transactionsSheet)
{
    $transactions = array();
    $highestRow = $transactionsSheet->getHighestRow();
    $clientIdColIndex = 0;
    $sumColIndex = 1;
    for ($row = 1; $row <= $highestRow; ++$row) {
        $transaction = array();
        $clientId = getCellValue($transactionsSheet, $clientIdColIndex, $row);
        $sum = getCellValue($transactionsSheet, $sumColIndex, $row);
        if (is_float($clientId) and is_float($sum)) {
            $transaction['id'] = $clientId;
            $transaction['sum'] = $sum;
        } elseif (is_string($clientId) or is_string($sum)) {
            throw new \Exception("Second spreadsheet has errors in row: {$row}");
        }
        if (sizeof($transaction) == 2) {
            array_push($transactions, $transaction);
        }
    }
    return $transactions;
}

function updateBalance($clients, $transactions)
{
    foreach ($transactions as $transaction) {
        $clientId = $transaction['id'];
        $transactionSum = $transaction['sum'];
        if ($clients[$clientId] === null) {
            throw new \Exception("There is no such id: <b>{$clientId}</b> in the list of clients");
        }
        $clients[$clientId] -> changeBalance($transactionSum);
    }
    return $clients;
}


function makeTable($clients)
{
    $html = "<table class=\"table table-bordered\">";
    $html .= "<thead class=\"thead-default\"><th>Id</th><th>Name</th><th>Balance</th></thead>";
    foreach ($clients as $client) {
        $html .= "<tr>";
        $html .= "<td>" . $client->getId() . "</td>";
        $html .= "<td>" . $client->getName() . "</td>";
        $html .= "<td>" . $client->getBalance() . "</td>";
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

function isExtXlsx($filename)
{
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if ($ext !== 'xlsx') {
        throw new \Exception('File\'s format must be xlsx');
    } else {
        return true;
    }
}

function counter($inputFileName)
{
    $initialDataSheetName = 'first';
    $transactionsSheetName = 'second';

    $spreadsheet = IOFactory::load($inputFileName);
    $sheetNames = ($spreadsheet->getSheetNames());
    if ($sheetNames[0] !== $initialDataSheetName || $sheetNames[1] !== $transactionsSheetName) {
        throw new \Exception('Your file has wrong sheet names');
    }
    $initialSheet = $spreadsheet->getSheetByName($initialDataSheetName);
    $transactionsSheet = $spreadsheet->getSheetByName($transactionsSheetName);

    $clients = getClientsList($initialSheet);
    $transactions = getTransactions($transactionsSheet);
    $clientsChangedBalance = updateBalance($clients, $transactions);
    return $clientsChangedBalance;
}
