<?php

use Battleship\GameController;
use Battleship\Position;
use Battleship\Letter;
use Battleship\Color;

class App
{
    private static $myFleet = array();
    private static $enemyFleet = array();
    private static $console;
    private static $fieldMin = 5;
    private static $fieldMax = 26;
    private static $currentLines;
    private static $currentRows;
    private static $player1ShootsMatrix = [];
    private static $player2ShootsMatrix = []; // used for computer or second player

    static function run()
    {
        self::$console = new Console();
        self::$console->setForegroundColor(Color::MAGENTA);

        self::$console->println("                                     |__");
        self::$console->println("                                     |\\/");
        self::$console->println("                                     ---");
        self::$console->println("                                     / | [");
        self::$console->println("                              !      | |||");
        self::$console->println("                            _/|     _/|-++'");
        self::$console->println("                        +  +--|    |--|--|_ |-");
        self::$console->println("                     { /|__|  |/\\__|  |--- |||__/");
        self::$console->println("                    +---------------___[}-_===_.'____                 /\\");
        self::$console->println("                ____`-' ||___-{]_| _[}-  |     |_[___\\==--            \\/   _");
        self::$console->println(" __..._____--==/___]_|__|_____________________________[___\\==--____,------' .7");
        self::$console->println("|                        Welcome to Battleship                         BB-61/");
        self::$console->println(" \\_________________________________________________________________________|");
        self::$console->println();
        self::$console->resetForegroundColor();
        self::InitializeGame();
        self::StartGame();
    }

    public static function InitializeEnemyFleet()
    {
        self::$enemyFleet = GameController::initializeShips();

        array_push(self::$enemyFleet[0]->getPositions(), new Position('B', 4));
        array_push(self::$enemyFleet[0]->getPositions(), new Position('B', 5));
        array_push(self::$enemyFleet[0]->getPositions(), new Position('B', 6));
        array_push(self::$enemyFleet[0]->getPositions(), new Position('B', 7));
        array_push(self::$enemyFleet[0]->getPositions(), new Position('B', 8));

        array_push(self::$enemyFleet[1]->getPositions(), new Position('E', 6));
        array_push(self::$enemyFleet[1]->getPositions(), new Position('E', 7));
        array_push(self::$enemyFleet[1]->getPositions(), new Position('E', 8));
        array_push(self::$enemyFleet[1]->getPositions(), new Position('E', 9));

        array_push(self::$enemyFleet[2]->getPositions(), new Position('A', 3));
        array_push(self::$enemyFleet[2]->getPositions(), new Position('B', 3));
        array_push(self::$enemyFleet[2]->getPositions(), new Position('C', 3));

        array_push(self::$enemyFleet[3]->getPositions(), new Position('F', 8));
        array_push(self::$enemyFleet[3]->getPositions(), new Position('G', 8));
        array_push(self::$enemyFleet[3]->getPositions(), new Position('H', 8));

        array_push(self::$enemyFleet[4]->getPositions(), new Position('C', 5));
        array_push(self::$enemyFleet[4]->getPositions(), new Position('C', 6));
    }

    public static function getFieldSize() {
        return [self::$currentLines, self::$currentRows];
    }

    public static function getRandomPosition()
    {
        $letter = Letter::value(random_int(0, self::$currentRows - 1));
        $number = random_int(0, self::$currentLines - 1);

        return new Position($letter, $number);
    }


    /**
     * Initialize game field size.
     */
    public static function InitializeFieldSize() {
        while(true) {
            self::$console->println("Please enter the field rows (min: " . self::$fieldMin . ", max: " . self::$fieldMax . "):");
            $input = (int)readline("");
            if($input < self::$fieldMin|| $input > self::$fieldMax) {
                self::$console->setForegroundColor(Color::RED);
                self::$console->println("Wrong field rows");
                self::$console->resetForegroundColor();
            }
            else {
                self::$currentRows = $input;
                break;
            }
        }
        while(true) {
            self::$console->println("Please enter the field lines (min: " . self::$fieldMin . ", max: " . self::$fieldMax . "):");
            $input = (int)readline("");
            if($input < self::$fieldMin|| $input > self::$fieldMax) {
                self::$console->setForegroundColor(Color::RED);
                self::$console->println("Wrong field lines");
                self::$console->resetForegroundColor();
            }
            else {
                self::$currentLines = $input;
                break;
            }
        }
    }

    public static function InitializeMyFleet()
    {
        self::$myFleet = GameController::initializeShips();
        self::$console->println("Please position your fleet (Game board has size from A to " . Letter::$letters[self::$currentLines - 1] . " and 1 to " . self::$currentRows . ") :");

        foreach (self::$myFleet as $ship) {

            self::$console->println();
            printf("Please enter the positions for the %s (size: %s)", $ship->getName(), $ship->getSize());

            for ($i = 1; $i <= $ship->getSize(); $i++) {
                printf("\nEnter position %s of %s (i.e A3):", $i, $ship->getSize());
                while (true) {
                    $input = readline("");
                    try {
                        $ship->addPosition($input);
                        break;
                    } catch (InvalidArgumentException $ex) {
                        printf($ex->getMessage());
                        self::$console->println();
                    }
                }
            }
        }
    }

    public static function beep()
    {
        echo "\007";
    }

    public static function clearScreen() {
        print("\033[2J\033[;H");
    }

    public static function InitializeGame()
    {
        self::InitializeFieldSize();
        self::InitializeMyFleet();
        self::InitializeEnemyFleet();
    }

    public static function StartGame()
    {
        self::$console->println("\033[2J\033[;H");
        self::$console->println("                  __");
        self::$console->println("                 /  \\");
        self::$console->println("           .-.  |    |");
        self::$console->println("   *    _.-'  \\  \\__/");
        self::$console->println("    \\.-'       \\");
        self::$console->println("   /          _/");
        self::$console->println("  |      _  /\" \"");
        self::$console->println("  |     /_\'");
        self::$console->println("   \\    \\_/");
        self::$console->println("    \" \"\" \"\" \"\" \"");

        while (true) {
            self::$console->println("");
            self::$console->println("Player, it's your turn");
            self::$console->println("Enter coordinates for your shot :");
            $position = readline("");

            try {
                self::clearScreen();
                $parsedPosition = self::parsePosition($position);

                $isHit = GameController::checkIsHit(self::$enemyFleet, $parsedPosition);
                if ($isHit) {
                    self::beep();
                    self::$console->println("                \\         .  ./");
                    self::$console->println("              \\      .:\" \";'.:..\" \"   /");
                    self::$console->println("                  (M^^.^~~:.'\" \").");
                    self::$console->println("            -   (/  .    . . \\ \\)  -");
                    self::$console->println("               ((| :. ~ ^  :. .|))");
                    self::$console->println("            -   (\\- |  \\ /  |  /)  -");
                    self::$console->println("                 -\\  \\     /  /-");
                    self::$console->println("                   \\  \\   /  /");
                }

                echo $isHit ? "Yeah ! Nice hit !" : "Miss";

                self::$player1ShootsMatrix[$parsedPosition->getColumn()][$parsedPosition->getRow()] = $isHit;
                self::drawMap(self::$player1ShootsMatrix, Color::CHARTREUSE);

                self::$console->println();


                $enemyFleetSunk = true;
                foreach (self::$enemyFleet as $ship)
                {
                    if (!$ship->isSunk()) {
                        $enemyFleetSunk = false;
                        break;
                    }
                }

                if ($enemyFleetSunk)
                {
                    self::$console->println("\nYou are the winner!");
                    self::getHappyEnding();
                    exit();
                }

                $position = self::getRandomPosition();
                $isHit = GameController::checkIsHit(self::$myFleet, $position);

                self::$player2ShootsMatrix[$position->getColumn()][$position->getRow()] = $isHit;
                self::drawMap(self::$player2ShootsMatrix, Color::YELLOW);
                self::$console->println();
                printf("Computer shoot in %s%s and %s", $position->getColumn(), $position->getRow(), $isHit ? "hit your ship !\n" : "miss");
                if ($isHit) {
                    self::beep();

                    self::$console->println("                \\         .  ./");
                    self::$console->println("              \\      .:\" \";'.:..\" \"   /");
                    self::$console->println("                  (M^^.^~~:.'\" \").");
                    self::$console->println("            -   (/  .    . . \\ \\)  -");
                    self::$console->println("               ((| :. ~ ^  :. .|))");
                    self::$console->println("            -   (\\- |  \\ /  |  /)  -");
                    self::$console->println("                 -\\  \\     /  /-");
                    self::$console->println("                   \\  \\   /  /");

                    $myFleetSunk = true;
                    foreach (self::$myFleet as $ship)
                    {
                        if (!$ship->isSunk()) {
                            $myFleetSunk = false;
                            break;
                        }
                    }

                    if ($myFleetSunk)
                    {
                        self::$console->println("\nYou lost!");
                        self::getSadEnding();
                        exit();
                    }
                }
            }
            catch(Exception $exception) {
                self::$console->setForegroundColor(Color::RED);
                self::$console->println($exception->getMessage());
                self::$console->resetForegroundColor();
            }
        }
    }

    public static function parsePosition($input)
    {
        $letter = strtoupper(substr($input, 0, 1));
        $number = (int)filter_var($input, FILTER_SANITIZE_NUMBER_INT);

        if(!is_numeric($number)) {
            throw new Exception("Not a number: $number");
        }

        if($number < 1 || $number > self::$currentLines) {
            throw new Exception("Out of a game field. Your number: $number, maximum number: " . self::$currentLines);
        }

        if(!in_array($letter, Letter::$letters)) {
            throw new Exception("Letter not exist: $letter");
        }

        if(array_search($letter, Letter::$letters) >= self::$currentRows ) {
            throw new Exception("Out of a game field. Your letter: $letter, maximum letter: " . Letter::$letters[self::$currentRows-1]);
        }
        return new Position($letter, $number);
    }

    /**
     * @param $matrix
     */
    public static function drawMap($matrix, $color)
    {
        self::$console->setForegroundColor($color);
        self::$console->println();
        self::$console->println(($color === Color::CHARTREUSE ? "Your" : "Enemy") . " shoots");

        printf("  ");
        for ($y = 1; $y <= self::$currentLines; $y++) {
            printf($y . ($y >= 10 ? "" : " "));
        }
        self::$console->println();
        for ($x = 1; $x <= self::$currentRows; $x++) {
            $letter = Letter::$letters[$x - 1];
            printf($letter . " ");
            for ($y = 1; $y <= self::$currentLines; $y++) {
                if (isset($matrix[$letter][$y])) {
                    if ($matrix[$letter][$y]) {
                        printf("☑ ");
                    } else {
                        printf("☒ ");
                    }
                } else {
                    printf("☐ ");
                }
            }
            self::$console->println();
        }
        self::$console->resetForegroundColor();
    }

    public static function getHappyEnding()
    {
        self::$console->println(' __      __         __                 _____       .__  .__   ');
        self::$console->println('/  \    /  \_____ _/  |_  ____________/ ____\____  |  | |  |  ');
        self::$console->println('\   \/\/   /\__  \\   __\/ __ \_  __ \   __\\__  \ |  | |  |  ');
        self::$console->println(' \        /  / __ \|  | \  ___/|  | \/|  |   / __ \|  |_|  |__');
        self::$console->println('  \__/\  /  (____  /__|  \___  >__|   |__|  (____  /____/____/');
        self::$console->println('       \/        \/          \/                  \/           ');
        self::$console->println('');
        self::$console->println('');
        self::$console->println('                  _.._');
        self::$console->println('   _________....-~    ~-.______');
        self::$console->println('~~~                            ~~~~-----...___________..--------');
        self::$console->println('                                           |   |     |');
        self::$console->println('                                           | |   |  ||');
        self::$console->println('                                           |  |  |   |');
        self::$console->println("                                           |'. .' .`.|");
        self::$console->println("___________________________________________|0oOO0oO0o|____________");
        self::$console->println(" -          -         -       -      -    / '  '. ` ` \    -    -");
        self::$console->println("      --                  --       --   /    '  . `   ` \    --");
        self::$console->println("---            ---          ---       /  '                \ ---");
        self::$console->println("     ----               ----        /       ' ' .    ` `    \  ----");
        self::$console->println("-----         -----         ----- /   '   '        `      `   \/");
        self::$console->println("     .-~~-.          ------     /          '    . `     `    `  \/");
        self::$console->println("    (_^..^_)-------           /  '    '      '      `");
        self::$console->println("      ||||          --------/     '     '   '");
    }

    public static function getSadEnding()
    {
        self::$console->println('▓██   ██▓ ▒█████   █    ██     ██▓     ▒█████    ██████ ▄▄▄█████▓');
        self::$console->println(' ▒██  ██▒▒██▒  ██▒ ██  ▓██▒   ▓██▒    ▒██▒  ██▒▒██    ▒ ▓  ██▒ ▓▒');
        self::$console->println('  ▒██ ██░▒██░  ██▒▓██  ▒██░   ▒██░    ▒██░  ██▒░ ▓██▄   ▒ ▓██░ ▒░');
        self::$console->println('  ░ ▐██▓░▒██   ██░▓▓█  ░██░   ▒██░    ▒██   ██░  ▒   ██▒░ ▓██▓ ░ ');
        self::$console->println('  ░ ██▒▓░░ ████▓▒░▒▒█████▓    ░██████▒░ ████▓▒░▒██████▒▒  ▒██▒ ░ ');
        self::$console->println('   ██▒▒▒ ░ ▒░▒░▒░ ░▒▓▒ ▒ ▒    ░ ▒░▓  ░░ ▒░▒░▒░ ▒ ▒▓▒ ▒ ░  ▒ ░░   ');
        self::$console->println(' ▓██ ░▒░   ░ ▒ ▒░ ░░▒░ ░ ░    ░ ░ ▒  ░  ░ ▒ ▒░ ░ ░▒  ░ ░    ░    ');
        self::$console->println(' ▒ ▒ ░░  ░ ░ ░ ▒   ░░░ ░ ░      ░ ░   ░ ░ ░ ▒  ░  ░  ░    ░      ');
        self::$console->println(' ░ ░         ░ ░     ░            ░  ░    ░ ░        ░           ');
        self::$console->println('');
        self::$console->println('');
        self::$console->println('       ________________                              _______________ ');
        self::$console->println('      /                \                            / /           \ \ ');
        self::$console->println('     / /          \ \   \                          |    -    -       \\');
        self::$console->println('     |                  |                          | /        -   \  |');
        self::$console->println('    /                  /                           \                 \\');
        self::$console->println('   |      ___\ \| | / /                             \____________  \  \\');
        self::$console->println('   |      /           |                             |            \    | ');
        self::$console->println('   |      |     __    |                             |             \   \ ');
        self::$console->println('  /       |       \   |                             |              \  | ');
        self::$console->println('  |       |        \  |                             | ====          | |');
        self::$console->println('  |       |       __  |                             | (o-)      _   | | ');
        self::$console->println('  |      __\     (_o) |                             /            \  | |');
        self::$console->println('  |     |             |     Heh Heh Heh            /            ) ) | |');
        self::$console->println('   \    ||             \      /      Huh Huh Huh  /             ) / | | ');
        self::$console->println('    |   |__             \    /                \  |___            - |  | ');
        self::$console->println("    |   |           (*___\  /                  \    *'             |  | ");
        self::$console->println("    |   |       _     |    /                    \  |____           |  |");
        self::$console->println("    |   |    //_______|                             ####\          |  |");
        self::$console->println("    |  /       |_|_|_|___/\                        ------          |_/  ");
        self::$console->println("     \|       \ -         |                        |                | ");
        self::$console->println("      |       _----_______/                        \_____           | ");
        self::$console->println("      |      /                                          \           |");
        self::$console->println("      |_____/                                            \__________|");
    }
}
