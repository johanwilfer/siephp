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
     */
    protected float $incomingBalance;

    /**
     * Outgoing balance for the account
     */
    protected float $outgoingBalance;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getIncomingBalance(): float
    {
        return $this->incomingBalance;
    }

    public function setIncomingBalance(float $incomingBalance): self
    {
        $this->incomingBalance = $incomingBalance;

        return $this;
    }

    public function getOutgoingBalance(): float
    {
        return $this->outgoingBalance;
    }

    public function setOutgoingBalance(float $outgoingBalance): self
    {
        $this->outgoingBalance = $outgoingBalance;

        return $this;
    }
}
