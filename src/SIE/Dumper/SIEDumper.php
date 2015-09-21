<?php

/**
 * This file is part of the SIE-PHP package.
 *
 * (c) Johan Wilfer <johan@jttech.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SIE\Dumper;

use SIE\Data;

/**
 * Generates a SIE-file
 */
class SIEDumper
{
    /**
     * Delimiter used for newline.
     * @var string
     */
    protected $delimiter_newline = "\r\n";
    /**
     * Delimiter used for fields.
     * @var string
     */
    protected $delimiter_field = " ";

    /**
     * Hold the options for the SIE-file
     * @var array
     */
    protected $options;

    /**
     * Generates and escapes a line
     * @param $label
     * @param $parameters
     * @return string
     */
    protected function getLine($label, $parameters)
    {
        // we build the line in reverse order to be able to skip empty items (null) at the end of the lines
        $line = '';
        foreach (array_reverse($parameters) as $param)
        {
            // skip null parameters at the end of the line
            if ($param === null && $line === '')
                continue;

            // arrays renders this way: {item1 item2 item3...}
            if (is_array($param))
            {
                $sub_field = '';
                foreach ($param as $item)
                {
                    // insert delimiter if not first
                    if ($sub_field !== '')
                        $sub_field .= $this->delimiter_field;
                    // add value
                    $sub_field .= $this->escapeField($item);
                }

                $line = $this->delimiter_field . '{' . $sub_field . '}' . $line;
                continue;
            }

            // normal value
            $line = $this->delimiter_field . $this->escapeField($param) . $line;
        }

        $line = '#' . $label . $line . $this->delimiter_newline;
        return $line;
    }

    /**
     * Escapes a field
     * @param $unescaped
     * @return string
     */
    protected function escapeField($unescaped)
    {
        if (is_object($unescaped)) {
            var_dump($unescaped);
            die;
        }
        $encoded = iconv('UTF-8', 'CP437', $unescaped);
        $escaped = '';
        $add_quotes = false;

        $str_len = strlen($encoded);
        for ($i=0; $i<$str_len; $i++)
        {
            $char = $encoded[$i];
            $ascii_numeric = ord($char);
            // page 9, 5.7 "There are to be no control characters in text strings. ASCII 0 up to and including ASCII 31 and ASCII 127 are control characters."
            if ($ascii_numeric < 32 || $ascii_numeric == 127)
                continue;
            // page 9, 5.7 "Quotation marks in export fields are to be preceded by a backslash (ASCII 92)."
            if ($ascii_numeric == 34)
                $char = '\"';
            // page 9, 5.7 "All fields are to be in quotation marks (ASCII 34). Quotation marks are however not a requirement and are only required when the field contains spaces."
            if ($ascii_numeric == 32)
                $add_quotes = true;

            $escaped .= $char;
        }
        // add quotes if string contains a space or if empty string or null
        if ($add_quotes || $escaped === '' || $escaped === null)
            $escaped = '"' . $escaped . '"';

        return $escaped;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        // default options
        $this->options = array(
            'generator' => 'SIE-PHP exporter',
            'generated_date' => date('Ymd'),
            'generated_sign' => null,
        );
    }

    /**
     * Dumps the Company and the data to SIE-format. Returns the SIE-contents as a string
     * @param Data\Company $sie
     * @return string
     */
    public function dump(Data\Company $sie)
    {
        // mandatory
        $data  = $this->getLine('FLAGGA', array('0'));
        $data .= $this->getLine('FORMAT', array('PC8'));
        $data .= $this->getLine('SIETYP', array('4'));
        $data .= $this->getLine('PROGRAM', array($this->options['generator']));
        $data .= $this->getLine('GEN', array($this->options['generated_date'], $this->options['generated_sign']));
        $data .= $this->getLine('FNAMN', array($sie->getCompanyName()));
        // optional
        if ($sie->getCompanyNumber() !== null)
            $data .= $this->getLine('ORGNR', array($sie->getCompanyNumber()));
        // accounts
        foreach ($sie->getAccounts() as $account)
            $data .= $this->getLine('KONTO', array($account->getId(), $account->getName()));
        // objects
        foreach ($sie->getDimensions() as $dimension)
            foreach ($dimension->getObjects() as $object)
                $data .= $this->getLine('OBJEKT', array($dimension->getId(), $object->getId(), $object->getName()));
        // fiscal year (we only support one)
        $fiscalYear = $sie->getFiscalYears();
        if (isset($fiscalYear[0]))
        {
            $data .= $this->getLine('RAR', array($fiscalYear[0]->getDateStart()->format('Ymd'), $fiscalYear[0]->getDateEnd()->format('Ymd')));
            foreach ($fiscalYear[0]->getAccountBalances() as $balance)
            {
                $data .= $this->getLine('IB', array(0, $balance->getAccount()->getId(), $balance->getIncomingBalance()));
                $data .= $this->getLine('UB', array(0, $balance->getAccount()->getId(), $balance->getOutgoingBalance()));
            }
        }
        // end head with a blank line (not needed but looks nice)
        $data .= $this->delimiter_newline;

        // verifications
        foreach ($sie->getVerificationSeriesAll() as $series)
        {
            foreach ($series->getVerifications() as $ver)
            {
                $data .= $this->getLine('VER', array(
                    $series->getId(),
                    $ver->getId(),
                    $ver->getDate(),
                    $ver->getText(),
                    $ver->getRegistrationDate(),
                    $ver->GetRegistrationSign()
                ));
                // transactions for this verification
                $data .= '{' . $this->delimiter_newline;
                foreach ($ver->getTransactions() as $trans)
                    $data .= '    ' . $this->getLine('TRANS', array(
                            $trans->getAccount()->getId(),
                            $trans->getObjectsAsArrayPairs(),
                            $trans->getAmount(),
                            // transaction date is not mandatory, but looks strange to leave out. Insert verification date if it is missing.
                            $trans->getDate() ? $trans->getDate() : $ver->getDate(),
                            $trans->getText(),
                            $trans->getQuantity(),
                            $trans->getRegistrationSign(),
                    ));
                $data .= '}' . $this->delimiter_newline;
                $data .= $this->delimiter_newline;
            }
        }

        return $data;
    }
}
