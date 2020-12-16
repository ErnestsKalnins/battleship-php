<?php

namespace Battleship;

class Position
{
    /**
     * @var string
     */
    private $column;
    private $row;
    private $isHit;

    /**
     * Position constructor.
     *
     * @param string $letter
     * @param string $number
     */
    public function __construct($letter, $number)
    {
        $this->column = Letter::validate(strtoupper($letter));
        $this->row = $number;
        $this->isHit = false;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getRow()
    {
        return $this->row;
    }

    public function __toString()
    {
        return sprintf("%s%s", $this->column, $this->row);
    }

    public function isHit()
    {
        return $this->isHit;
    }

    public function hit()
    {
        $this->isHit = true;
    }

    public function isAlignedTo($position): bool
    {
        return $this->row == $position->row ||
            $this->column == $position->column;
    }

    public function isAdjacentTo($position): bool
    {
        return abs($this->row - $position->row) + abs(ord($this->column) - ord($position->column)) == 1;
    }
}