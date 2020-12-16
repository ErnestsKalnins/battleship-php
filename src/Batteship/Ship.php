<?php

namespace Battleship;

use InvalidArgumentException;

class Ship
{

    private $name;
    private $size;
    private $color;
    private $positions = array();

    public function __construct($name, $size, $color = null)
    {
        $this->name = $name;
        $this->size = $size;
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    public function addPosition($input)
    {
        $letter = substr($input, 0, 1);
        $number = substr($input, 1, 1);

        $inputPosition = new Position($letter, $number);

        if ($this->hasPosition($inputPosition)) {
            throw new InvalidArgumentException(sprintf("position %s already belongs to the ship", $inputPosition));
        }

        array_push($this->positions, $inputPosition);
    }

    public function &getPositions()
    {
        return $this->positions;
    }

    public function hasPosition($position) : bool
    {
        foreach ($this->positions as $shipPosition) {
            if ($position == $shipPosition) {
                return true;
            }
        }
        return false;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function shoot($shot)
    {
        foreach ($this->positions as $position) {
            if ($position == $shot) {
                $position->hit();
                return;
            }
        }
    }

    public function isSunk()
    {
        foreach ($this->positions as $position) {
            if (!$position->isHit()) {
                return false;
            }
        }
        return true;
    }
}