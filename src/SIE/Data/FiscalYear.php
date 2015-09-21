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
    //FIXME Right now we only support one fiscal year for export to SIE. Add the ability to have multiple fiscal years.
    //FIXME Also, maybe we could use a DateInterval instead of start + end, and generate previous years automatically.

    /**
     * Start of Fiscal year
     * @var \DateTime
     */
    protected $dateStart;

    /**
     * End of Fiscal year
     * @var \DateTime
     */
    protected $dateEnd;

    /**
     * Account balances for this fiscal year
     * @var AccountBalance[]
     */
    protected $accountBalances;

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param \DateTime $dateStart
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
     * @param \DateTime $dateEnd
     * @return FiscalYear
     */
    public function setDateEnd(\DateTime $dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * Add incoming balance for an account
     * @param AccountBalance $accountBalance The account balance object representing balances for this fiscal year
     * @return FiscalYear
     * @throws DomainException
     */
    public function addAccountBalance(AccountBalance $accountBalance)
    {
        $id = $accountBalance->getAccount()->getId();
        if (isset($this->accountBalances[$id]))
            throw new DomainException('The balances for account id "' . $id . '" is already defined.');

        $this->accountBalances[$id] = $accountBalance;
        return $this;
    }

    /**
     * Get account balance by account id
     * @param string $id Account id
     * @return AccountBalance|null
     */
    public function getAccountBalance($id)
    {
        // search by id
        if (isset($this->accountBalances[$id]))
            return $this->accountBalances[$id];
        // not found
        return null;
    }

    /**
     * Get account balances for this fiscal year
     * @return AccountBalance[]
     */
    public function getAccountBalances()
    {
        ksort($this->accountBalances);
        return $this->accountBalances;
    }
}
