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

/**
 * Represents a dimension, and holds objects
 */
final class Dimension
{
    //FIXME Add support for custom dimensions (#DIM), id 20+ This means generating #DIM-fields for the SIE-export.

    /**
     * See 8.17 in "SIE_filformat_ver_4B_ENGLISH.pdf" "Reserved dimension numbers"
     *   1     = Cost centre / result unit
     *   2     = Cost bearer (is to be sub-dimension of 1)
     *   3-5   = Reserved for future expansion of the standard
     *   6     = Project
     *   7     = Employee
     *   8     = Customer
     *   9     = Supplier
     *   10    = Invoice
     *   11-19 = Reserved for future expansion of the standard
     *   20-   = Custom dimension
     */
    public const DIMENSION_COST_CENTRE = 1;

    public const DIMENSION_COST_BEARER = 2;

    public const DIMENSION_PROJECT = 6;

    public const DIMENSION_EMPLOYEE = 7;

    public const DIMENSION_CUSTOMER = 8;

    public const DIMENSION_SUPPLIER = 9;

    public const DIMENSION_INVOICE = 10;

    /**
     * Dimension identifier
     */
    private int $id;

    /**
     * #OBJEKT
     *
     * @var DimensionObject[]
     */
    private array $objects = [];

    /**
     * Create dimension
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Return id of the dimension
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Add object
     */
    public function addObject(DimensionObject $object): self
    {
        $object->setDimension($this);
        $this->objects[] = $object;

        return $this;
    }

    /**
     * Get Object with id
     */
    public function getObject(string $id): ?DimensionObject
    {
        // search for id
        foreach ($this->objects as $object) {
            if ($object->getId() === $id) {
                return $object;
            }
        }

        return null;
    }

    /**
     * Get objects for this dimension
     *
     * @return DimensionObject[]
     */
    public function getObjects(): array
    {
        ksort($this->objects);
        return $this->objects;
    }
}
