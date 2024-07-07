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

use SIE\Data\Company;
use SIE\Exception\DomainException;

/**
 * Generates a SIE-file
 */
class SIEDumper
{
    public const DEFAULT_GENERATOR_NAME = 'SIE-PHP exporter';

    public const DEFAULT_GENERATOR_VERSION = '1.0';

    /**
     * Delimiter used for newline.
     */
    private string $delimiter_newline = "\r\n";

    /**
     * Delimiter used for fields.
     */
    private string $delimiter_field = " ";

    /**
     * Hold the options for the SIE-file
     *
     * @var array<string, string|null>
     */
    private array $options;

    /**
     * Generates and escapes a line
     *
     * @param array<mixed> $parameters
     *
     */
    protected function getLine(string $label, array $parameters): string
    {
        // we build the line in reverse order to be able to skip empty items (null) at the end of the lines
        $line = '';
        foreach (array_reverse($parameters) as $param) {
            // skip null parameters at the end of the line
            if ($param === null && $line === '') {
                continue;
            }

            // arrays renders this way: {item1 item2 item3...}
            if (is_array($param)) {
                $sub_field = '';
                foreach ($param as $item) {
                    // insert delimiter if not first
                    if ($sub_field !== '') {
                        $sub_field .= $this->delimiter_field;
                    }
                    // add value
                    $sub_field .= $this->escapeField($item);
                }

                $line = $this->delimiter_field . '{' . $sub_field . '}' . $line;
                continue;
            }

            // normal value
            if (is_string($param) || is_int($param) || is_float($param)) {
                $line = $this->delimiter_field . $this->escapeField($param) . $line;
                continue;
            }

            throw new DomainException('Unexpected type of parameter: ' . gettype($param));
        }

        return '#' . $label . $line . $this->delimiter_newline;
    }

    /**
     * Escapes a field
     */
    protected function escapeField(string|int|float $unescaped): string
    {
        $encoded = (string) iconv('UTF-8', 'CP437', (string) $unescaped);
        $escaped = '';
        $addQuotes = false;

        for ($i = 0; $i < strlen($encoded); $i++) {
            $char = $encoded[$i];
            $ascii_numeric = ord($char);
            // page 9, 5.7 "There are to be no control characters in text strings. ASCII 0 up to and including ASCII 31 and ASCII 127 are control characters."
            if ($ascii_numeric < 32 || $ascii_numeric == 127) {
                continue;
            }
            // page 9, 5.7 "Quotation marks in export fields are to be preceded by a backslash (ASCII 92)."
            if ($ascii_numeric == 34) {
                $char = '\"';
            }
            // page 9, 5.7 "All fields are to be in quotation marks (ASCII 34). Quotation marks are however not a requirement and are only required when the field contains spaces."
            if ($ascii_numeric == 32) {
                $addQuotes = true;
            }

            $escaped .= $char;
        }
        // add quotes if string contains a space or if empty string
        if ($addQuotes || $escaped === '') {
            $escaped = '"' . $escaped . '"';
        }

        return $escaped;
    }

    public function __construct()
    {
        // default options
        $this->options = [
            'generator' => self::DEFAULT_GENERATOR_NAME,
            'generator_version' => self::DEFAULT_GENERATOR_VERSION,
            'generated_date' => date('Ymd'),
            'generated_sign' => null,
        ];
    }

    /**
     * Set generator (custom "PROGRAM" name).
     */
    public function setGenerator(string $generatorName, string $generatorVersion = self::DEFAULT_GENERATOR_VERSION): void
    {
        $this->options['generator'] = $generatorName;
        $this->options['generator_version'] = $generatorVersion;
    }

    /**
     * Dumps the Company and the data to SIE-format. Returns the SIE-contents as a string
     */
    public function dump(Company $sie): string
    {
        // mandatory
        $data = $this->getLine('FLAGGA', ['0']);
        $data .= $this->getLine('FORMAT', ['PC8']);
        $data .= $this->getLine('SIETYP', ['4']);
        $data .= $this->getLine('PROGRAM', [$this->options['generator'], $this->options['generator_version']]);
        $data .= $this->getLine('GEN', [$this->options['generated_date'], $this->options['generated_sign']]);
        $data .= $this->getLine('FNAMN', [$sie->getCompanyName()]);
        // optional
        if ($sie->getCompanyNumber() !== null) {
            $data .= $this->getLine('ORGNR', [$sie->getCompanyNumber()]);
        }
        // optional - type of chart of accounts
        if ($sie->getTypeOfChartOfAccounts() !== null) {
            $data .= $this->getLine('KPTYP', [$sie->getTypeOfChartOfAccounts()]);
        }
        // accounts
        foreach ($sie->getAccounts() as $account) {
            $data .= $this->getLine('KONTO', [$account->getId(), $account->getName()]);
        }
        // objects
        foreach ($sie->getDimensions() as $dimension) {
            foreach ($dimension->getObjects() as $object) {
                $data .= $this->getLine('OBJEKT', [$dimension->getId(), $object->getId(), $object->getName()]);
            }
        }

        // fiscal year - add a #RAR line for each year
        $fiscalYears = $sie->getFiscalYears();
        $year = 0;
        foreach ($fiscalYears as $fiscalYear) {
            $data .= $this->getLine('RAR', [$year--, $fiscalYear->getDateStart()->format('Ymd'), $fiscalYear->getDateEnd()->format('Ymd')]);
        }
        // balance data per fiscal year
        $year = 0;
        foreach ($fiscalYears as $fiscalYear) {
            foreach ($fiscalYear->getAccountBalances() as $balance) {
                $data .= $this->getLine('IB', [$year, $balance->getAccount()->getId(), $balance->getIncomingBalance()]);
                $data .= $this->getLine('UB', [$year, $balance->getAccount()->getId(), $balance->getOutgoingBalance()]);
            }
            $year--;
        }

        // end head with a blank line (not needed but looks nice)
        $data .= $this->delimiter_newline;

        // verifications
        foreach ($sie->getVerificationSeriesAll() as $series) {
            foreach ($series->getVerifications() as $ver) {
                $data .= $this->getLine('VER', [
                    $series->getId(),
                    $ver->getId(),
                    $ver->getDate(),
                    $ver->getText(),
                    $ver->getRegistrationDate(),
                    $ver->GetRegistrationSign(),
                ]);
                // transactions for this verification
                $data .= '{' . $this->delimiter_newline;
                foreach ($ver->getTransactions() as $trans) {
                    $data .= '    ' . $this->getLine('TRANS', [
                        $trans->getAccount()?->getId(),
                        $trans->getObjectsAsArrayPairs(),
                        $trans->getAmount(),
                        // transaction date is not mandatory, but looks strange to leave out. Insert verification date if it is missing.
                        $trans->getDate() ?: $ver->getDate(),
                        $trans->getText(),
                        $trans->getQuantity(),
                        $trans->getRegistrationSign(),
                    ]);
                }
                $data .= '}' . $this->delimiter_newline;
                $data .= $this->delimiter_newline;
            }
        }

        return $data;
    }
}
