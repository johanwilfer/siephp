<?php

/**
 * This file is part of the SIE-PHP package.
 *
 * (c) Johan Wilfer <johan@jttech.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SIE\Data;

use SIE\Exception\DomainException;

/**
 * Represents a company with data that could be exported to SIE
 */
class Company
{
    /**
     * #FNAMN - Company name
     *
     * @var string
     */
    protected $companyName;

    /**
     * #ORGNR - Company orgnr, like 555555-5555
     *
     * @var string
     */
    protected $companyNumber;

    /**
     * #KBTYP - type of chart of accounts
     * It's optional, when it's missing BAS95 is used.
     *
     * @var string
     */
    protected $typeOfChartOfAccounts;

    /**
     * #KONTO - Accounts list
     *
     * @var Account[]
     */
    protected $accounts;

    /**
     * Represents verification series
     *
     * @var VerificationSeries[]
     */
    protected $verificationSeries;

    /**
     * Represents dimensions
     *
     * @var Dimension[]
     */
    protected $dimensions;

    /**
     * The fiscal year, used by incoming and outgoing balances.
     *
     * @var FiscalYear[]
     */
    protected $fiscalYears;

    /**
     * Creates a Company object, that could be exported as a SIE-file
     */
    public function __construct()
    {
        $this->accounts = [];
        $this->verificationSeries = [];
        $this->dimensions = [];
        $this->fiscalYears = [];
    }

    /**
     * Get company name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set company name
     *
     * @param string $companyName
     *
     * @return Company
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get company number
     *
     * @return string
     */
    public function getCompanyNumber()
    {
        return $this->companyNumber;
    }

    /**
     * Set company number
     *
     * @param string $companyNumber
     *
     * @return Company
     */
    public function setCompanyNumber($companyNumber)
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }

    /**
     * Set type of chart of accounts.
     *
     * @return Company
     */
    public function setTypeOfChartOfAccounts($type)
    {
        $this->typeOfChartOfAccounts = $type;

        return $this;
    }

    /**
     * Get type of chart of accounts.
     *
     * @return string
     */
    public function getTypeOfChartOfAccounts()
    {
        return $this->typeOfChartOfAccounts;
    }

    /**
     * Add an account
     *
     * @return Company
     *
     * @throws DomainException
     */
    public function addAccount(Account $account)
    {
        $id = $account->getId();
        if (isset($this->accounts[$id])) {
            throw new DomainException('The account id "' . $id . '" is already defined.');
        }
        $this->accounts[$id] = $account;

        return $this;
    }

    /**
     * Search accounts by id
     *
     * @param  integer $id The account number
     *
     * @return Account|null
     */
    public function getAccount($id)
    {
        // search by id
        if (isset($this->accounts[$id])) {
            return $this->accounts[$id];
        }

        // not found
        return null;
    }

    /**
     * Get all accounts
     *
     * @return Account[]
     */
    public function getAccounts()
    {
        // return array sorted by account number
        ksort($this->accounts);

        return $this->accounts;
    }

    /**
     * Add dimension
     *
     * @return Company
     *
     * @throws DomainException
     */
    public function addDimension(Dimension $dimension)
    {
        $id = $dimension->getId();
        if (isset($this->dimensions[$id])) {
            throw new DomainException('The dimension id "' . $id . '" is already defined.');
        }
        $this->dimensions[$id] = $dimension;

        return $this;
    }

    /**
     * Get dimension with id
     *
     * @param integer $id The dimension id
     *
     * @return Dimension|null
     */
    public function getDimension($id)
    {
        // search by id
        if (isset($this->dimensions[$id])) {
            return $this->dimensions[$id];
        }

        // none found
        return null;
    }

    /**
     * Return all dimensions
     *
     * @return Dimension[]
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * Add verification series
     *
     * @return Company
     *
     * @throws DomainException
     */
    public function addVerificationSeries(VerificationSeries $verificationSeries)
    {
        $id = $verificationSeries->getId();
        foreach ($this->verificationSeries as $item) {
            if ($item->getId() == $id) {
                throw new DomainException('This verification series with the id "' . $id . '" is already defined.');
            }
        }
        $this->verificationSeries[] = $verificationSeries;

        return $this;
    }

    /**
     * Get verification series by id.
     *
     * @param string $id Id of verification series
     *
     * @return VerificationSeries|null
     */
    public function getVerificationSeries($id)
    {
        // search by id
        foreach ($this->verificationSeries as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        // not found
        return null;
    }

    /**
     * Return all series used for verifications
     *
     * @return VerificationSeries[]
     */
    public function getVerificationSeriesAll()
    {
        return $this->verificationSeries;
    }

    /**
     * Add fiscal year
     *
     * @return Company
     *
     * @throws DomainException
     */
    public function addFiscalYear(FiscalYear $fiscalYear)
    {
        $this->fiscalYears[] = $fiscalYear;

        return $this;
    }

    /**
     * Get fiscal years
     *
     * @return FiscalYear[]
     */
    public function getFiscalYears()
    {
        return $this->fiscalYears;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format
     *
     * @throws DomainException
     */
    public function validate()
    {
        if (! $this->companyName) {
            throw new DomainException('Mandatory field companyName');
        }
        // validate verifications
        foreach ($this->verificationSeries as $item) {
            $item->validate();
        }
    }
}
