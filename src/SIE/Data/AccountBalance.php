<?php

declare(strict_types=1);

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
final class AccountBalance
{
    /**
     * The account
     */
    private readonly Account $account;

    /**
     * Incoming balance for the account
     */
    private float $incomingBalance;

    /**
     * Outgoing balance for the account
     */
    private float $outgoingBalance;

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
