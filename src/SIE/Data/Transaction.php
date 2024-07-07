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
class Transaction
{
    /**
     * Account number
     *
     * @var Account
     */
    protected $account;

    /**
     * Array with objects, uses the dimension as keys, and the value is set to Object.
     *
     * @var object[]
     */
    protected $objects = [];

    /**
     * Amount of transaction
     *
     * @var float
     */
    protected $amount;

    /**
     * Date of transaction (optional)
     *
     * @var string
     */
    protected $date;

    /**
     * Text of transaction (optional)
     *
     * @var string
     */
    protected $text;

    /**
     * Quantity of transaction (optional)
     *
     * @var string
     */
    protected $quantity;

    /**
     * Sign (optional)
     *
     * @var string
     */
    protected $registrationSign;

    /**
     * Construct a Transaction
     */
    public function __construct()
    {
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set account
     */
    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get all objects for this transaction as an array with pairs for dimension, object
     */
    public function getObjectsAsArrayPairs(): array
    {
        // object list is pairs of: [dimension] [value] ..
        $object_list = [];
        foreach ($this->objects as $object) {
            $object_list[] = $object->getDimension()->getId();
            $object_list[] = $object->getId();
        }

        return $object_list;
    }

    /**
     * Get object with dimension
     *
     * @param int $dimension Dimension to search
     *
     * @return object|null
     */
    public function getObject($dimension)
    {
        // not found
        return $this->objects[$dimension] ?? null;
    }

    /**
     * Get all objects for this transaction
     *
     * @return object[]
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Add object to the transaction
     *
     * @throws DomainException
     */
    public function addObject(Object $object): self
    {
        $dimensionId = $object->getDimension()->getId();
        // check that we only add one object per dimension
        if (isset($this->objects[$dimensionId])) {
            throw new DomainException('This dimension is already defined on this transaction');
        }
        $this->objects[$dimensionId] = $object;

        return $this;
    }

    /**
     * Get amount
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     */
    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param string $date
     */
    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param string $text
     */
    public function setText($text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     */
    public function setQuantity($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get registration sign
     *
     * @return string
     */
    public function getRegistrationSign()
    {
        return $this->registrationSign;
    }

    /**
     * Set registration sign
     *
     * @param string $registrationSign
     */
    public function setRegistrationSign($registrationSign): self
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
        if (! $this->account) {
            throw new DomainException('Mandatory field: account');
        }
        if ($this->amount === null) {
            throw new DomainException('Mandatory field: amount');
        }
    }
}
