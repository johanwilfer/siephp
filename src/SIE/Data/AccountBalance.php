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
     */
    protected Account $account;

    /**
     * Incoming balance for the account
     *
     * @var float
     */
    protected $incomingBalance;

    /**
     * Outgoing balance for the account
     *
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
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * Get incoming balance
     */
    public function getIncomingBalance(): float
    {
        return $this->incomingBalance;
    }

    /**
     * Set incoming balance
     *
     * @param float $incomingBalance
     */
    public function setIncomingBalance($incomingBalance): self
    {
        $this->incomingBalance = $incomingBalance;

        return $this;
    }

    /**
     * Get outgoing balance
     */
    public function getOutgoingBalance(): float
    {
        return $this->outgoingBalance;
    }

    /**
     * Set outgoing balance
     *
     * @param float $outgoingBalance
     */
    public function setOutgoingBalance($outgoingBalance): self
    {
        $this->outgoingBalance = $outgoingBalance;

        return $this;
    }
}
