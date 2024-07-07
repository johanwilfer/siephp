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
final class Company
{
    /**
     * #FNAMN - Company name
     */
    private ?string $companyName = null;

    /**
     * #ORGNR - Company orgnr, like 555555-5555
     */
    private ?string $companyNumber = null;

    /**
     * #KBTYP - type of chart of accounts
     * It's optional, when it's missing BAS95 is used.
     */
    private ?string $typeOfChartOfAccounts = null;

    /**
     * #KONTO - Accounts list
     *
     * @var Account[]
     */
    private array $accounts = [];

    /**
     * Represents verification series
     *
     * @var VerificationSeries[]
     */
    private array $verificationSeries = [];

    /**
     * Represents dimensions
     *
     * @var Dimension[]
     */
    private array $dimensions = [];

    /**
     * The fiscal year, used by incoming and outgoing balances.
     *
     * @var FiscalYear[]
     */
    private array $fiscalYears = [];

    /**
     * Creates a Company object, that could be exported as a SIE-file
     */
    public function __construct()
    {
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCompanyNumber(): ?string
    {
        return $this->companyNumber;
    }

    public function setCompanyNumber(string $companyNumber): self
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }

    public function setTypeOfChartOfAccounts(string $type): self
    {
        $this->typeOfChartOfAccounts = $type;

        return $this;
    }

    public function getTypeOfChartOfAccounts(): ?string
    {
        return $this->typeOfChartOfAccounts;
    }

    /**
     * Add an account
     *
     * @throws DomainException
     */
    public function addAccount(Account $account): self
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
     */
    public function getAccount(int $id): ?Account
    {
        return $this->accounts[$id] ?? null;
    }

    /**
     * Get all accounts
     *
     * @return Account[]
     */
    public function getAccounts(): array
    {
        // return array sorted by account number
        ksort($this->accounts);

        return $this->accounts;
    }

    /**
     * Add dimension
     *
     * @throws DomainException
     */
    public function addDimension(Dimension $dimension): self
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
     */
    public function getDimension(int $id): ?Dimension
    {
        return $this->dimensions[$id] ?? null;
    }

    /**
     * Return all dimensions
     *
     * @return Dimension[]
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    /**
     * Add verification series
     *
     * @throws DomainException
     */
    public function addVerificationSeries(VerificationSeries $verificationSeries): self
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
     */
    public function getVerificationSeries(int $id): ?VerificationSeries
    {
        // search by id
        foreach ($this->verificationSeries as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Return all series used for verifications
     *
     * @return VerificationSeries[]
     */
    public function getVerificationSeriesAll(): array
    {
        return $this->verificationSeries;
    }

    /**
     * Add fiscal year
     *
     * @throws DomainException
     */
    public function addFiscalYear(FiscalYear $fiscalYear): self
    {
        $this->fiscalYears[] = $fiscalYear;

        return $this;
    }

    /**
     * Get fiscal years
     *
     * @return FiscalYear[]
     */
    public function getFiscalYears(): array
    {
        return $this->fiscalYears;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format
     *
     * @throws DomainException
     */
    public function validate(): void
    {
        if ($this->companyName === null) {
            throw new DomainException('Mandatory field companyName');
        }
        // validate verifications
        foreach ($this->verificationSeries as $item) {
            $item->validate();
        }
    }
}
