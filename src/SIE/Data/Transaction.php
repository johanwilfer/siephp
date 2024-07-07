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
final class Transaction
{
    /**
     * Account number
     */
    private ?Account $account = null;

    /**
     * Array with objects, uses the dimension as keys, and the value is set to Object.
     *
     * @var DimensionObject[]
     */
    private array $dimensionObjects = [];

    /**
     * Amount of transaction
     */
    private ?float $amount = null;

    /**
     * Date of transaction (optional)
     */
    private ?string $date = null;

    /**
     * Text of transaction (optional)
     */
    private ?string $text = null;

    /**
     * Quantity of transaction (optional)
     */
    private ?string $quantity = null;

    /**
     * Sign (optional)
     */
    private ?string $registrationSign = null;

    /**
     * Construct a Transaction
     */
    public function __construct()
    {
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get all objects for this transaction as an array with pairs for dimension, object
     *
     * @return array<int, int|string|null>
     */
    public function getObjectsAsArrayPairs(): array
    {
        // object list is pairs of: [dimension] [value] ..
        $objectList = [];
        foreach ($this->dimensionObjects as $object) {
            $objectList[] = $object->getDimension()?->getId();
            $objectList[] = $object->getId();
        }

        return $objectList;
    }

    /**
     * Get object with dimension
     */
    public function getObject(int $dimension): ?DimensionObject
    {
        return $this->dimensionObjects[$dimension] ?? null;
    }

    /**
     * Get all objects for this transaction
     *
     * @return DimensionObject[]
     */
    public function getObjects(): array
    {
        return $this->dimensionObjects;
    }

    /**
     * Add object to the transaction
     *
     * @throws DomainException
     */
    public function addObject(DimensionObject $object): self
    {
        $dimensionId = $object->getDimension()?->getId();
        // check that we only add one object per dimension
        if (isset($this->dimensionObjects[$dimensionId])) {
            throw new DomainException('This dimension is already defined on this transaction');
        }
        $this->dimensionObjects[$dimensionId] = $object;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
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

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRegistrationSign(): ?string
    {
        return $this->registrationSign;
    }

    public function setRegistrationSign(string $registrationSign): self
    {
        $this->registrationSign = $registrationSign;

        return $this;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format.
     *
     * @throws DomainException
     */
    public function validate(): void
    {
        if ($this->account === null) {
            throw new DomainException('Mandatory field: account');
        }
        if ($this->amount === null) {
            throw new DomainException('Mandatory field: amount');
        }
    }
}
