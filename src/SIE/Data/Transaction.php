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
     * @var Account
     */
    protected $account;

    /**
     * Array with objects, uses the dimension as keys, and the value is set to Object.
     * @var Object[]
     */
    protected $objects;

    /**
     * Amount of transaction
     * @var float
     */
    protected $amount;

    /**
     * Date of transaction (optional)
     * @var string
     */
    protected $date;

    /**
     * Text of transaction (optional)
     * @var string
     */
    protected $text;

    /**
     * Quantity of transaction (optional)
     * @var string
     */
    protected $quantity;

    /**
     * Sign (optional)
     * @var string
     */
    protected $registrationSign;

    /**
     * Construct a Transaction
     */
    public function __construct()
    {
        $this->objects = [];
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
     * Set account
     * @param Account $account
     * @return Transaction
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Get all objects for this transaction as an array with pairs for dimension, object
     * @return array
     */
    public function getObjectsAsArrayPairs()
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
     * @param int $dimension Dimension to search
     * @return Object|null
     */
    public function getObject($dimension)
    {
        // search by dimension
        if (isset($this->objects[$dimension])) {
            return $this->objects[$dimension];
        }
        // not found
        return null;
    }

    /**
     * Get all objects for this transaction
     * @return Object[]
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Add object to the transaction
     * @param \SIE\Data\Object $object
     * @return Transaction
     * @throws DomainException
     */
    public function addObject(Object $object)
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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     * @param float $amount
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get date
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     * @param string $date
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get text
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     * @param string $text
     * @return Transaction
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get quantity
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     * @param string $quantity
     * @return Transaction
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get registration sign
     * @return string
     */
    public function getRegistrationSign()
    {
        return $this->registrationSign;
    }

    /**
     * Set registration sign
     * @param string $registrationSign
     * @return Transaction
     */
    public function setRegistrationSign($registrationSign)
    {
        $this->registrationSign = $registrationSign;
        return $this;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format.
     * @throws DomainException
     */
    public function validate()
    {
        if (!$this->account) {
            throw new DomainException('Mandatory field: account');
        }
        if ($this->amount === null) {
            throw new DomainException('Mandatory field: amount');
        }
    }
}
