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

/**
 * Account balance, for a fiscal year
 */
class AccountBalance
{
    /**
     * The account
     * @var Account
     */
    protected $account;

    /**
     * Incoming balance for the account
     * @var float
     */
    protected $incomingBalance;

    /**
     * Outgoing balance for the account
     * @var float
     */
    protected $outgoingBalance;

    /**
     * Constructor
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Get account
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Get incoming balance
     * @return float
     */
    public function getIncomingBalance()
    {
        return $this->incomingBalance;
    }

    /**
     * Set incoming balance
     * @param float $incomingBalance
     * @return AccountBalance
     */
    public function setIncomingBalance($incomingBalance)
    {
        $this->incomingBalance = $incomingBalance;
        return $this;
    }

    /**
     * Get outgoing balance
     * @return float
     */
    public function getOutgoingBalance()
    {
        return $this->outgoingBalance;
    }

    /**
     * Set outgoing balance
     * @param float $outgoingBalance
     * @return AccountBalance
     */
    public function setOutgoingBalance($outgoingBalance)
    {
        $this->outgoingBalance = $outgoingBalance;
        return $this;
    }
}
