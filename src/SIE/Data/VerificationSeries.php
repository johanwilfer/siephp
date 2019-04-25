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
 * Represents series with verifications
 */
class VerificationSeries
{
    /**
     * See page 37 in "SIE_filformat_ver_4B_ENGLISH.pdf": #VER
     */
    const CONST_DEFAULT_SERIES = 'A';

    /**
     * Number series designation
     * @var string
     */
    protected $id;

    /**
     * #VER
     * @var Verification[]
     */
    protected $verifications;


    /**
     * Construct a VerificationSeries
     * @param string $id
     */
    public function __construct($id = self::CONST_DEFAULT_SERIES)
    {
        $this->verifications = [];
        $this->id = $id;
    }

    /**
     * Get series identifier
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * add verification
     * @param Verification $verification
     * @return VerificationSeries
     * @throws DomainException
     */
    public function addVerification(Verification $verification)
    {
        $id = $verification->getId();

        if ($id) {
            // does the verification already exist?
            if (isset($this->verifications[$id])) {
                throw new DomainException('The verification id "' . $id . '" in the series "' . $this->id . '" does already exist.');
            }

            $this->verifications[$id] = $verification;
        } else {
            $this->verifications[] = $verification;
        }

        return $this;
    }

    /**
     * Get all verifications
     * @return Verification[]
     */
    public function getVerifications()
    {
        // return array sorted by verification id
        ksort($this->verifications);
        return $this->verifications;
    }

    /**
     * Get verification
     * @param string $id Search for verification number
     * @return Verification|null
     */
    public function getVerification($id)
    {
        // search by id
        if (isset($this->verifications[$id])) {
            return $this->verifications[$id];
        }
        // not found
        return null;
    }


    /**
     * Validate verifications in this series
     * @throws DomainException
     */
    public function validate()
    {
        // validate verifications
        foreach ($this->verifications as $verification) {
            $verification->validate();
        }
    }
}
