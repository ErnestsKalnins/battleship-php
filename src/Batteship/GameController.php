<?php

namespace Battleship;

use Console;
use InvalidArgumentException;

class GameController
{
    public static function checkIsHit(array $fleet, $shot)
    {
        $console = new Console();

        if ($fleet == null) {
            throw new InvalidArgumentException("ships is null");
        }

        if ($shot == null) {
            throw new InvalidArgumentException("shot is null");
        }

        foreach ($fleet as $ship) {
            foreach ($ship->getPositions() as $position) {
                if ($position == $shot) {
                    if ($position->isHit()) {
                        $console->println("This field has already been hit. Choose another one.");
                        return false;
                    }

                    $ship->shoot($shot);
                    return true;
                }
            }
        }

        return false;
    }

    public static function initializeShips()
    {
        return Array(
            new Ship("Aircraft Carrier", 5, Color::CADET_BLUE),
            new Ship("Battleship", 4, Color::RED),
            new Ship("Submarine", 3, Color::CHARTREUSE),
            new Ship("Destroyer", 3, Color::YELLOW),
            new Ship("Patrol Boat", 2, Color::ORANGE));
    }

    public static function isShipValid($ship)
    {
        return count($ship->getPositions()) == $ship->getSize();
    }

    public static function getRandomPosition()
    {
        $rows = 8;
        $lines = 8;

        $letter = Letter::value(random_int(0, $lines - 1));
        $number = random_int(0, $rows - 1);

        return new Position($letter, $number);
    }
}