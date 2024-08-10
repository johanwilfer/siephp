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

use SIE\Exception\DomainException;

/**
 * Verification, see section 11#VER at page 37 in "SIE_filformat_ver_4B_ENGLISH.pdf"
 */
final class Verification
{
    /**
     * Verification no
     */
    private readonly string $id;

    /**
     * Verification date
     */
    private ?string $date = null;

    /**
     * Verification text (optional)
     */
    private ?string $text = null;

    /**
     * Registration date (optional)
     */
    private ?string $registrationDate = null;

    /**
     * Sign can be the name, signature or user id of the person or process that generated the
     * transaction item or last edited the transaction item. Signature can be omitted.
     */
    private ?string $registrationSign = null;

    /**
     * Transactions for this Verification
     *
     * @var Transaction[]
     */
    private array $transactions = [];

    public function __construct(string $verificationId)
    {
        $this->id = $verificationId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getRegistrationDate(): ?string
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(string $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getRegistrationSign(): ?string
    {
        return $this->registrationSign;
    }

    public function setRegistrationSign(?string $registrationSign): self
    {
        $this->registrationSign = $registrationSign;

        return $this;
    }

    public function addTransaction(Transaction $transaction): self
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format.
     *
     * @throws DomainException
     */
    public function validate(): void
    {
        if ($this->date === null) {
            throw new DomainException('Mandatory field date');
        }

        if ($this->transactions === []) {
            throw new DomainException(  'No transactions for verification id "' . $this->id . '".' );
        }

        // validate verifications
        $sum = 0;
        foreach ($this->transactions as $transaction) {
            // validate all our transactions
            $transaction->validate();
            // calculate sum of all transactions
            $sum += $transaction->getAmount();
        }

        // validate that our transactions equal zero
        if (round($sum, 2) !== 0.0) {
            throw new DomainException('The verification id "' . $this->id . '" have a non-zero sum: ' . $sum);
        }
    }
}
