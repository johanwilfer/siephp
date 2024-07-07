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
 * Represents a Fiscal year, can hold account balance sheet items for incoming / outgoing
 */
class FiscalYear
{
    /**
     * Start of Fiscal year
     *
     * @var \DateTime
     */
    protected $dateStart;

    /**
     * End of Fiscal year
     *
     * @var \DateTime
     */
    protected $dateEnd;

    /**
     * Account balances for this fiscal year
     *
     * @var AccountBalance[]
     */
    protected $accountBalances;

    /**
     * Constructor for Fiscal year
     */
    public function __construct()
    {
        // default to this calendar year
        $this->dateStart = new \DateTime('first day of January this year');
        $this->dateEnd = new \DateTime('last day of December this year');
        // initialize array
        $this->accountBalances = [];
    }

    /**
     * Constructs a FiscalYear for the previous year from this instances start.
     *
     * @return FiscalYear A new instance of FiscalYear
     */
    public function createPreviousFiscalYear()
    {
        // create new dates
        $dateEnd = clone $this->dateEnd;
        $dateEnd->modify('-1 year');
        $dateStart = clone $this->dateStart;
        $dateStart->modify('-1 year');
        // create fiscal year and set start & end
        $fiscalYear = new FiscalYear();
        $fiscalYear->setDateEnd($dateEnd);
        $fiscalYear->setDateStart($dateStart);

        // return new FiscalYear
        return $fiscalYear;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @return FiscalYear
     */
    public function setDateStart(\DateTime $dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @return FiscalYear
     */
    public function setDateEnd(\DateTime $dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Add incoming balance for an account
     *
     * @param AccountBalance $accountBalance The account balance object representing balances for this fiscal year
     *
     * @return FiscalYear
     *
     * @throws DomainException
     */
    public function addAccountBalance(AccountBalance $accountBalance)
    {
        $id = $accountBalance->getAccount()->getId();
        if (isset($this->accountBalances[$id])) {
            throw new DomainException('The balances for account id "' . $id . '" is already defined.');
        }

        $this->accountBalances[$id] = $accountBalance;

        return $this;
    }

    /**
     * Get account balance by account id
     *
     * @param string $id Account id
     *
     * @return AccountBalance|null
     */
    public function getAccountBalance($id)
    {
        // not found
        return $this->accountBalances[$id] ?? null;
    }

    /**
     * Get account balances for this fiscal year
     *
     * @return AccountBalance[]
     */
    public function getAccountBalances()
    {
        ksort($this->accountBalances);

        return $this->accountBalances;
    }
}
