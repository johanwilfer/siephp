<?php

declare(strict_types=1);

use SIE\Data\Account;
use SIE\Data\AccountBalance;
use SIE\Data\Company;
use SIE\Data\Dimension;
use SIE\Data\DimensionObject;
use SIE\Data\FiscalYear;
use SIE\Data\Transaction;
use SIE\Data\Verification;
use SIE\Data\VerificationSeries;
use SIE\Dumper\SIEDumper;

/**
 * This file is part of the SIE-PHP package.
 *
 * (c) Johan Wilfer <johan@jttech.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use SIE\Data;

class TSVLoader
{
    /**
     * Parse TSV data and returns array of rows, that holds an array of fields.
     *
     * @param non-empty-string $delimiter
     * @return array<int, array<int, string>>
     */
    private function getTabularData(string $value, string $delimiter): array
    {
        // fix line endings to be \n
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        // split it up ny lines and tabs
        $lines = explode("\n", $value);
        $rows = [];
        foreach ($lines as $line) {
            // don't add blank lines
            if ($line === '') {
                continue;
            }

            $rows[] = explode($delimiter, $line);
        }

        return $rows;
    }

    /**
     * Used for sorting the tsv-data
     *
     * @param array<int, string|int> $a
     * @param array<int, string|int> $b
     */
    private function tabularDataCompareRows(array $a, array $b): int
    {
        // compare ver_no
        $field = 0;
        // same ver_no, compare ver_row instead
        if ($a[$field] === $b[$field]) {
            $field = 14;
        }

        return ($a[$field] < $b[$field]) ? -1 : 1;
    }

    /**
     * Parse balance data from TSV-file
     *
     * @param Data\Company    $company    The company to parse balances for
     * @param Data\FiscalYear $fiscalYear The fiscal year
     * @param int $skipHeaderLines
     */
    public function parseBalance(string $value, Company $company, FiscalYear $fiscalYear, $skipHeaderLines = 1): void
    {
        // parse text
        $rows = $this->getTabularData($value, "\t");
        // kill header lines
        for ($i = 0; $i < $skipHeaderLines; ++$i) {
            array_shift($rows);
        }

        foreach ($rows as $row) {
            $data = [
                'account_id' => (int) $row[0],
                'account_name' => $row[1],
                'incoming' => (float) str_replace([' ', ' ', '.', ','], ['', '', '', '.'], $row[2]),
                'outgoing' => (float) str_replace([' ', ' ', '.', ','], ['', '', '', '.'], $row[5]),
            ];

            // account - try fetch it from the company
            $account = $company->getAccount($data['account_id']);
            // account not found? create it.
            if (! $account instanceof Account) {
                $account = (new Account($data['account_id']))
                    ->setName($data['account_name']);
                $company->addAccount($account);
            }

            // add balances
            $fiscalYear->addAccountBalance(
                (new AccountBalance($account))
                    ->setIncomingBalance($data['incoming'])
                    ->setOutgoingBalance($data['outgoing'])
            );
        }
    }

    /**
     * Parse transaction-data form TSV-file
     */
    public function parseTransactions(string $value, Company $company, int $skipHeaderLines = 1): void
    {
        // parse text
        $rows = $this->getTabularData($value, "\t");
        // kill header lines
        for ($i = 0; $i < $skipHeaderLines; ++$i) {
            array_shift($rows);
        }

        // fix ordering
        usort($rows, [$this, 'tabularDataCompareRows']);

        // add a verification series and two dimensions
        $verificationSeries = new VerificationSeries();
        $company->addVerificationSeries($verificationSeries)
            ->addDimension(new Dimension(Dimension::DIMENSION_COST_CENTRE))
            ->addDimension(new Dimension(Dimension::DIMENSION_PROJECT));

        $lastVerificationId = null;
        foreach ($rows as $row) {
            /* -- Our columns --
             * 0: Verification number
             * 1: Transaction / verification date
             * 3: Account number
             * 4: Account name
             * 5: Result unit
             * 6: Project
             * 13: Verification name
             * 14: Verification row number
             * 15: Transaction name
             * 18: Transaction amount
             */
            $data = [
                'ver_no' => $row[0],
                'date' => $row[1],
                'account_no' => (int) $row[3],
                'account_name' => $row[4],
                'result_unit' => $row[5],
                'project' => $row[6],
                'ver_name' => $row[13],
                'ver_row' => $row[14],
                'trans_text' => $row[15],
                'trans_amount' => (float) str_replace(['.', ','], ['', '.'], $row[18]),
            ];

            // verification
            if ($lastVerificationId !== $data['ver_no']) {
                $verification = (new Verification($data['ver_no']))
                    ->setDate($data['date'])
                    ->setText($data['ver_name']);
                $verificationSeries->addVerification($verification);
            }

            // account
            $account = $company->getAccount($data['account_no']);
            if (! $account instanceof Account) {
                $account = (new Account($data['account_no']))
                    ->setName($data['account_name']);
                $company->addAccount($account);
            }

            // transaction
            $transaction = (new Transaction())
                ->setAccount($account)
                ->setAmount($data['trans_amount'])
                ->setText($data['trans_text']);
            $verification->addTransaction($transaction);

            // dimension - result unit
            if ($data['result_unit'] !== '') {
                // find dimension (pre-defined)
                $dim = $company->getDimension(Dimension::DIMENSION_COST_CENTRE);
                if (!$dim instanceof Dimension) {
                    throw new \LogicException('Expected to find dimension: DIMENSION_COST_CENTRE');
                }

                // find / create object
                $object = $dim->getObject($data['result_unit']);
                if (! $object instanceof DimensionObject) {
                    $object = (new DimensionObject($data['result_unit']))
                        ->setDimension($dim)
                        ->setName('Resultatenhet ' . $data['result_unit']); //We don't have this data, so just set it
                    $dim->addObject($object);
                }

                // add to transaction
                $transaction->addObject($object);
            }

            // dimension - project
            if ($data['project'] !== '') {
                // find dimension (pre-defined)
                $dim = $company->getDimension(Dimension::DIMENSION_PROJECT);
                if (!$dim instanceof Dimension) {
                    throw new \LogicException('Expected to find dimension: DIMENSION_PROJECT');
                }

                // find / create object
                $object = $dim->getObject($data['project']);
                if (! $object instanceof DimensionObject) {
                    $object = (new DimensionObject($data['project']))
                        ->setDimension($dim)
                        ->setName('Projekt ' . $data['project']); //We don't have this data, so just set it
                    $dim->addObject($object);
                }

                // add to transaction
                $transaction->addObject($object);
            }

            $lastVerificationId = $verification->getId();
        }
    }
}

/*
 * paths to example data
 */

$paths = [
    'transaction-data' => __DIR__ . '/data/import-transactions.tsv',
    'balance-data-year-0' => __DIR__ . '/data/import-balance-year-0.tsv',
    'balance-data-year-1' => __DIR__ . '/data/import-balance-year-1.tsv',
];

/*
 * Transaction data for current year
 */

//create company
$company = (new Company())->setCompanyName('Imported company name åäöÅÄÖ');
// load transaction data from TSV
$loader = new TSVLoader();
$loader->parseTransactions((string) file_get_contents($paths['transaction-data']), $company);

/*
 * Balance data
 */

// add fiscal year (defaults to current calendar year)
$fiscalYear = new FiscalYear();
$company->addFiscalYear($fiscalYear);
$loader->parseBalance((string) file_get_contents($paths['balance-data-year-0']), $company, $fiscalYear);

// the year before that
$fiscalYear = $fiscalYear->createPreviousFiscalYear();
$company->addFiscalYear($fiscalYear);
$loader->parseBalance((string) file_get_contents($paths['balance-data-year-1']), $company, $fiscalYear);

/*
 * Dump as SIE
 */

$company->validate();
$dumper = new SIEDumper();
$output = $dumper->dump($company);
echo $output;
