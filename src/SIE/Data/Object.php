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

use SIE\Exception\InvalidArgumentException;

/**
 * Represents an "value" in a Dimension: An Object.
 */
class Object
{
    /**
     * Dimension id
     * @var Dimension
     */
    protected $dimension;

    /**
     * Object identifier
     * @var string
     */
    protected $id;

    /**
     * Object name
     * @var string
     */
    protected $name;


    /**
     * Constructor
     * @param string $id
     * @throws InvalidArgumentException
     */
    public function __construct($id)
    {
        if (!$id) throw new InvalidArgumentException('Mandatory parameter: id');
        $this->id = $id;
    }

    /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Dimension
     * @return Dimension
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * Set dimension
     * @param Dimension $dimension
     * @return Object
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     * @param string $name
     * @return Object
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
