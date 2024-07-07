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
    public const CONST_DEFAULT_SERIES = 'A';

    /**
     * Number series designation
     *
     * @var string
     */
    protected $id;

    /**
     * #VER - these are the numbered verifications that will be included in ascending order when calling getVerifications()
     *
     * @var Verification[]
     */
    protected $verifications;

    /**
     * #VER - these don't have a verification number and will be included after the ones in ascending order when calling getVerifications()
     *
     * @var Verification[]
     */
    protected $verificationsPreProcessingSystem;

    /**
     * Construct a VerificationSeries
     *
     * @param string $id
     */
    public function __construct($id = self::CONST_DEFAULT_SERIES)
    {
        $this->verifications = [];
        $this->verificationsPreProcessingSystem = [];
        $this->id = $id;
    }

    /**
     * Get series identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * add verification
     *
     * @return VerificationSeries
     *
     * @throws DomainException
     */
    public function addVerification(Verification $verification)
    {
        $id = $verification->getId();
        // does the verification already exist?
        if (isset($this->verifications[$id])) {
            throw new DomainException('The verification id "' . $id . '" in the series "' . $this->id . '" does already exist.');
        }

        $this->verifications[$id] = $verification;

        return $this;
    }

    /**
     * add verification from pre-processing system - this uses less strict validation than addVerification
     *
     * The SIE standard states:
     * "Series and/or verno can be submitted empty where the file format is used to input
     *  transactions (using files of type 4I) from a pre-processing system to a financial
     *  reporting program. The series or verification number is in this case set by the
     *  financial reporting program."
     *
     * @return VerificationSeries
     *
     * @throws DomainException
     */
    public function addVerificationPreProcessingSystem(Verification $verification)
    {
        $this->verificationsPreProcessingSystem[] = $verification;

        return $this;
    }

    /**
     * Get all verifications
     *
     * @return Verification[]
     */
    public function getVerifications()
    {
        // sort numbered verifications by id
        ksort($this->verifications);
        // array_merge will overwrite duplicate string keys with the value from the last array
        // but we don't add string keys to $this->verificationsPreProcessingSystem
        $verifications = array_merge($this->verifications, $this->verificationsPreProcessingSystem);

        return $verifications;
    }

    /**
     * Get verification - will only find numbered verifications
     *
     * @param string $id Search for verification number
     *
     * @return Verification|null
     */
    public function getVerification($id)
    {
        // not found
        return $this->verifications[$id] ?? null;
    }

    /**
     * Validate verifications in this series
     *
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
