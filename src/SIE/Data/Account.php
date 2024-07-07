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
use SIE\Exception\InvalidArgumentException;

/**
 * Transaction, see section 11#TRANS at page 33 in "SIE_filformat_ver_4B_ENGLISH.pdf"
 */
class Account
{
    /**
     * Account number
     * @var integer
     */
    protected $id;

    /**
     * Account name
     * @var string
     */
    protected $name;

    /**
     * Constructs an Account
     * @param $accountNumber
     * @throws InvalidArgumentException
     */
    public function __construct($accountNumber)
    {
        if ($accountNumber === null) {
            throw new InvalidArgumentException('AccountNumber cannot be null.');
        }
        $this->id = $accountNumber;
    }

    /**
     * Get account number
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get account name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set account name
     * @param string $name
     * @return Account
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format.
     * @throws DomainException
     */
    public function validate()
    {
        if (!$this->name) {
            throw new DomainException('AccountName must be set.');
        }
    }
}
