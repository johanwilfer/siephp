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
 * Verification, see section 11#VER at page 37 in "SIE_filformat_ver_4B_ENGLISH.pdf"
 */
class Verification
{
    /**
     * Verification no
     * @var string
     */
    protected $id;

    /**
     * Verification date
     * @var string
     */
    protected $date;

    /**
     * Verification text (optional)
     * @var string
     */
    protected $text;

    /**
     * Registration date (optional)
     * @var string
     */
    protected $registrationDate;

    /**
     * Sign can be the name, signature or user id of the person or process that generated the
     * transaction item or last edited the transaction item. Signature can be omitted.
     * @var string
     */
    protected $registrationSign;

    /**
     * Transactions for this Verification
     * @var Transaction[]
     */
    protected $transactions = [];

    /**
     * Construct a Verification
     * @param $verificationId
     * @throws InvalidArgumentException
     */
    public function __construct($verificationId)
    {
        if ($verificationId === null) {
            throw new InvalidArgumentException('VerificationNumber cannot be null.');
        }
        $this->id = $verificationId;
    }

    /**
     * Get verification Id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Date
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     * @param string $date
     * @return Verification
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
     * @return Verification
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get registration date
     * @return string
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set registration date
     * @param string $registrationDate
     * @return Verification
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;
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
     * Set registartion sign
     * @param string $registrationSign
     * @return Verification
     */
    public function setRegistrationSign($registrationSign)
    {
        $this->registrationSign = $registrationSign;
        return $this;
    }

    /**
     * Add a transaction
     * @param Transaction $transaction
     * @return Verification
     */
    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;
        return $this;
    }

    /**
     * Get all transactions
     * @return Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Validate the data, valid data should be exportable to SIE-format.
     *
     * @throws DomainException
     */
    public function validate()
    {
        if (!$this->date) {
            throw new DomainException('Mandatory field date');
        }
        if (count($this->transactions) === 0) {
            throw new DomainException('No transactions for verification id "' . $this->id . '".');
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
        //FIXME The round() is due to precision loss in float operation. Maybe use Money\Money here instead
        if (round($sum, 2) != 0) {
            throw new DomainException('The verification id "' . $this->id . '" have a non-zero sum: ' . $sum);
        }
    }
}
