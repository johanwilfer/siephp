<?php

/*
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
     * @param string $value
     * @param string $delimiter
     * @return array
     */
    protected function getTabularData($value, $delimiter = "\t")
    {
        // fix line endings to be \n
        $value = str_replace(array("\r\n", "\r"), "\n", $value);
        // split it up ny lines and tabs
        $lines = explode("\n", $value);
        $rows = array();
        foreach ($lines as $line)
        {
            // don't add blank lines
            if ($line == '') continue;
            $rows[] = explode($delimiter, $line);
        }

        return $rows;
    }

    /**
     * Used for sorting the tsv-data
     */
    protected function tabularDataCompareRows($a, $b)
    {
        // compare ver_no
        $field = 0;
        // same ver_no, compare ver_row instead
        if ($a[$field] == $b[$field])
            $field = 14;

        return ($a[$field] < $b[$field]) ? -1 : 1;
    }

    /**
     * @param $value
     * @param int $skipHeaderLines
     * @return SIE\Data\Company
     */
    public function parse($value, $skipHeaderLines = 1)
    {
        // parse text
        $rows = $this->getTabularData($value);
        // kill header lines
        for ($i=0; $i < $skipHeaderLines; $i++)
            array_shift($rows);
        // fix ordering
        usort($rows, array($this, 'tabularDataCompareRows'));

        // create company and add a verification series and two dimensions
        $verificationSeries = new Data\VerificationSeries();
        $company = (new SIE\Data\Company())
            ->setCompanyName('Imported company name åäöÅÄÖ')
            ->addVerificationSeries($verificationSeries)
            ->addDimension(new Data\Dimension(Data\Dimension::DIMENSION_COST_CENTRE))
            ->addDimension(new Data\Dimension(Data\Dimension::DIMENSION_PROJECT))
        ;

        $last_verification_id = null;
        foreach ($rows as $row)
        {
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
            $data = array(
                'ver_no'       => $row[0],
                'date'         => $row[1],
                'account_no'   => $row[3],
                'account_name' => $row[4],
                'result_unit'  => $row[5],
                'project'      => $row[6],
                'ver_name'     => $row[13],
                'ver_row'      => $row[14],
                'trans_text'   => $row[15],
                'trans_amount' => (float) (str_replace(array('.', ','), array('', '.'), $row[18])),
            );

            // verification
            if ($last_verification_id !== $data['ver_no'])
            {
                $verification = (new Data\Verification($data['ver_no']))
                    ->setDate($data['date'])
                    ->setText($data['ver_name']);
                $verificationSeries->addVerification($verification);
            }

            // account
            $account = $company->getAccount($row[3]);
            if ($account === null)
            {
                $account = (new Data\Account($data['account_no']))
                    ->setName($data['account_name']);
                $company->addAccount($account);
            }

            // transaction
            $transaction = (new Data\Transaction())
                ->setAccount($account)
                ->setAmount($data['trans_amount'])
                ->setText($data['trans_text']);
            $verification->addTransaction($transaction);

            // dimension - result unit
            if ($data['result_unit'])
            {
                // find dimension (pre-defined)
                $dim = $company->getDimension(Data\Dimension::DIMENSION_COST_CENTRE);
                // find / create object
                $object = $dim->getObject($data['result_unit']);
                if ($object === null)
                {
                    $object = (new Data\Object($data['result_unit']))
                        ->setDimension($dim)
                        ->setName('Resultatenhet ' . $data['result_unit']); //We don't have this data, so just set it
                    $dim->addObject($object);
                }
                // add to transaction
                $transaction->addObject($object);
            }

            // dimension - project
            if ($data['project'])
            {
                // find dimension (pre-defined)
                $dim = $company->getDimension(Data\Dimension::DIMENSION_PROJECT);
                // find / create object
                $object = $dim->getObject($data['project']);
                if ($object === null)
                {
                    $object = (new Data\Object($data['project']))
                        ->setDimension($dim)
                        ->setName('Projekt ' . $data['project']); //We don't have this data, so just set it
                    $dim->addObject($object);
                }
                // add to transaction
                $transaction->addObject($object);
            }

            $last_verification_id = $verification->getId();
        }

        $company->validate();
        return $company;
    }
}

$loader = new TSVLoader();
$company = $loader->parse(file_get_contents(__DIR__ . '/data/import.tsv'));

$dumper = new \SIE\Dumper\SIEDumper();
$output = $dumper->dump($company);
echo $output;

