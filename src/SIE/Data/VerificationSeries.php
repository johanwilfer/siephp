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
final class VerificationSeries
{
    /**
     * See page 37 in "SIE_filformat_ver_4B_ENGLISH.pdf": #VER
     */
    public const CONST_DEFAULT_SERIES = 'A';

    /**
     * Number series designation
     */
    private readonly string $id;

    /**
     * #VER - these are the numbered verifications that will be included in ascending order when calling getVerifications()
     *
     * @var Verification[]
     */
    private array $verifications = [];

    /**
     * #VER - these don't have a verification number and will be included after the ones in ascending order when calling getVerifications()
     *
     * @var Verification[]
     */
    private array $verificationsPreProcessingSystem = [];

    /**
     * Construct a VerificationSeries
     */
    public function __construct(string $id = self::CONST_DEFAULT_SERIES)
    {
        $this->id = $id;
    }

    /**
     * Get series identifier
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @throws DomainException
     */
    public function addVerification(Verification $verification): self
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
     */
    public function addVerificationPreProcessingSystem(Verification $verification): self
    {
        $this->verificationsPreProcessingSystem[] = $verification;

        return $this;
    }

    /**
     * Get all verifications
     *
     * @return Verification[]
     */
    public function getVerifications(): array
    {
        // sort numbered verifications by id
        ksort($this->verifications);

        // array_merge will overwrite duplicate string keys with the value from the last array
        // but we don't add string keys to $this->verificationsPreProcessingSystem
        return array_merge($this->verifications, $this->verificationsPreProcessingSystem);
    }

    /**
     * Get verification - will only find numbered verifications
     */
    public function getVerification(string $id): ?Verification
    {
        return $this->verifications[$id] ?? null;
    }

    /**
     * Validate verifications in this series
     *
     * @throws DomainException
     */
    public function validate(): void
    {
        // validate verifications
        foreach ($this->verifications as $verification) {
            $verification->validate();
        }
    }
}
