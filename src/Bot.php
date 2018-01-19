<?php

namespace TicTacToe;

class Bot implements MoveInterface
{
    private $win_positions = [
        'first_row' => ['00', '01', '02'],
        'second_row' => ['10', '11', '12'],
        'third_row' => ['20', '21', '22'],
        'first_col' => ['00', '10', '20'],
        'second_col' => ['01', '11', '21'],
        'third_col' => ['02', '12', '22'],
        'diagonal_topleft' => ['00', '11', '22'],
        'diagonal_bottomleft' => ['02', '11', '20'],
    ];
    protected $board_state_mapped = false;
    protected $playerUnits = ['X', 'O'];

    /**
     * Makes a move using the $boardState
     * $boardState contains 2 dimensional array of the game
     * field
     * X represents one team, O - the other team, empty
     * string means field is
     * not yet taken.
     * example
     * [['X', 'O', '']
     * ['X', 'O', 'O']
     * ['', '', '']]
     * Returns an array, containing x and y coordinates for
     * next move, and th
     * e unit that now occupies it.
     * Example: [2, 0, 'O'] - upper right corner - O player
     *
     * @param array  $boardState Current board state
     * @param string $playerUnit Player unit representation
     *
     * @return array
     * @throws \Exception
     */
    public function makeMove(
        $boardState,
        $playerUnit = 'X'
    ) {
        $this->validateBoardState($boardState);
        $this->parseBoardState($boardState);
        $this->checkGameState($boardState);

        return $this->calculateNextMove($boardState, $playerUnit);
    }

    /**
     * Check if game is won or lost
     * @param array $boardState
     * @throws GameEndedException
     */
    protected function checkGameState(array $boardState)
    {
        if (!$this->board_state_mapped) {
            $this->parseBoardState($boardState);
        }
        foreach ($this->win_positions as $position_key => $item) {
            foreach ($this->playerUnits as $playerUnit) {
                $win = true;
                foreach($item as $x) {
                    if (!in_array($x, $this->board_state_mapped[$playerUnit])) {
                        $win = false;
                    }
                }
                if ($win) {
                    throw new GameEndedException('Game won by ' . $playerUnit . ' on ' . $position_key);
                }
            }
        }
    }

    /**
     * @param array $boardState
     */
    protected function parseBoardState(array $boardState)
    {
        $this->board_state_mapped = ['X' => [], 'O' => [], '' => []];
        foreach ($boardState as $row => $values) {
            foreach ($values as $column => $playerUnit) {
                $this->board_state_mapped[$playerUnit][] = $row . $column;
            }
        }
    }

    /**
     * @param array $boardState
     * @return bool
     * @throws \Exception
     */
    protected function validateBoardState(array $boardState)
    {
        if (count($boardState) != 3) {
            throw new \Exception('Invalid board state. Should be an array with 3 items');
        }
        foreach ($boardState as $row => $values) {
            if (!is_array($values)) {
                throw new \Exception("Invalid board state. Row #{$row} should contain one array");
            }
            if (count($values) != 3) {
                throw new \Exception("Invalid board state. Row #{$row} of array needs to have 3 values");
            }
            foreach ($values as $column => $value) {
                if ($value != '' && preg_match('/[XO]/', $value) == 0) {
                    throw new \Exception("Invalid board state. Invalid value for Row #{$row} Column #{$column}");
                }
            }
        }

        return true;
    }

    /**
     * @param $boardState
     * @param $playerUnit
     * @return array
     * @throws GameEndedException
     * @throws NoMoreMovesException
     */
    protected function calculateNextMove($boardState, $playerUnit)
    {
        if (!$this->board_state_mapped) {
            $this->parseBoardState($boardState);
        }

        $possible_moves = $this->getPossibleMovesList();
        if (empty($possible_moves)) {
            throw new NoMoreMovesException('No more moves');
        }

        $possible_moves = $this->setMovesWeight($possible_moves, $playerUnit);
        $possible_moves = $this->sortMoves($possible_moves);

        $selected_move = array_shift($possible_moves);

        $column = (int)$selected_move['move'][1];
        $row = (int)$selected_move['move'][0];
        $boardState[$row][$column] = $playerUnit;
        $this->board_state_mapped[$playerUnit][] = $selected_move['move'];
        $this->checkGameState($boardState);

        return [$column, $row, $playerUnit];
    }

    /**
     * @return array
     */
    protected function getPossibleMovesList()
    {
        return array_map(function($v) {
            return [
                'move' => $v,
                'weight' => 0,
            ];
        }, $this->board_state_mapped['']);
    }

    /**
     * @param array $possible_moves
     * @param string $playerUnit
     * @return array
     */
    protected function setMovesWeight(array $possible_moves, string $playerUnit)
    {
        //check for possible wins
        foreach ($possible_moves as $item) {
            if ($this->checkWin($item['move'], $playerUnit)) {
                return [
                    [
                        'move' => $item['move'],
                        'weight' => 100
                    ]
                ];
            }
        }

        //check if is possible to avoid to lose
        foreach ($possible_moves as $item) {
            if ($this->checkWontLose($item['move'], $playerUnit)) {
                return [
                    [
                        'move' => $item['move'],
                        'weight' => 100
                    ]
                ];
            }
        }

        // set weight for possible moves
        $opponentUnit = $playerUnit == 'X' ? 'O' : 'X';
        foreach ($possible_moves as $k => $v) {
            foreach ($this->win_positions as $position_key => $item) {
                // if there is any position occupied by the opponent, discard this position
                foreach($item as $x) {
                    if (in_array($x, $this->board_state_mapped[$opponentUnit])) {
                        continue 2;
                    }
                }

                if (in_array($v['move'], $item)) {
                    $possible_moves[$k]['weight']++;
                }
            }
        }

        return $possible_moves;
    }

    /**
     * @param array $possible_moves
     * @return array
     */
    private function sortMoves(array $possible_moves)
    {
        uasort($possible_moves, function ($a, $b) {
            if ($a['weight'] == $b['weight']) return 0;
            return ($a['weight'] < $b['weight']) ? 1 : -1;
        });

        return $possible_moves;
    }

    /**
     * @param $move
     * @param $playerUnit
     * @return bool
     */
    private function checkWin($move, $playerUnit)
    {
        $player_moves = array_merge($this->board_state_mapped[$playerUnit], [$move]);
        sort($player_moves);
        foreach ($this->win_positions as $position_key => $item) {
            $win = true;
            foreach($item as $x) {
                if (!in_array($x, $player_moves)) {
                    $win = false;
                }
            }
            if ($win) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $move
     * @param $playerUnit
     * @return bool
     */
    private function checkWontLose($move, $playerUnit)
    {
        $opponentUnit = $playerUnit == 'X' ? 'O' : 'X';
        return $this->checkWin($move, $opponentUnit);
    }
}
