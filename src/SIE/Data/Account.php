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
 * Transaction, see section 11#TRANS at page 33 in "SIE_filformat_ver_4B_ENGLISH.pdf"
 */
final class Account
{
    /**
     * Account number
     */
    private int $id;

    /**
     * Account name
     */
    private ?string $name = null;

    public function __construct(int $accountNumber)
    {
        $this->id = $accountNumber;
    }

    /**
     * Get account number
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get account name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set account name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format.
     *
     * @throws DomainException
     */
    public function validate(): void
    {
        if ($this->name === null) {
            throw new DomainException('AccountName must be set.');
        }
    }
}
