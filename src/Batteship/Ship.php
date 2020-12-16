<?php

namespace Battleship;

use InvalidArgumentException;

class Ship
{

    private $name;
    private $size;
    private $color;
    private $positions = [];

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

    public function addPosition($position)
    {
        if ($this->hasPosition($position)) {
            throw new InvalidArgumentException(sprintf("position %s already belongs to the ship", $position));
        }

        if (!$this->isPositionValid($position)) {
            throw new InvalidArgumentException(sprintf("position %s is not valid", $position));
        }

        array_push($this->positions, $position);
    }

    public function &getPositions()
    {
        return $this->positions;
    }

    public function hasPosition($position): bool
    {
        foreach ($this->positions as $shipPosition) {
            if ($position == $shipPosition) {
                return true;
            }
        }

        return false;
    }

    public function isPositionValid($position): bool
    {
        if (count($this->positions) == 0) {
            return true;
        }

        $isAdjacent = false;
        $isAligned = true;
        foreach ($this->positions as $shipPosition) {
            if (!$shipPosition->isAlignedTo($position)) {
                return false;
            }

            if ($shipPosition->isAdjacentTo($position)) {
                $isAdjacent = true;
            }
        }

        return $isAdjacent && $isAligned;
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

    public function printableStatus()
    {
        $start = $this->isSunk() ? "Sunk:" : "Alive:" . " [";
        $end = "]";
        $middle = "=";

        foreach ($this->getPositions() as $position) {
            $start .= $middle;
        }

        return $start .= $end;
    }
    public function formatPositions()
    {
        return implode($this->positions, " ");
    }
}
