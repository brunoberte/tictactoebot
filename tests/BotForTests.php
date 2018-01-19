<?php

namespace TestTicTacToe;

use TicTacToe\Bot;

class BotForTests extends Bot {

    public function getMapPlayer() {
        return $this->map_player;
    }

    public function parseBoardState(array $boardState)
    {
        parent::parseBoardState($boardState);
    }

    public function calculateNextMove($boardState, $playerUnit)
    {
        return parent::calculateNextMove($boardState, $playerUnit);
    }

    public function checkGameState(array $boardState)
    {
        parent::checkGameState($boardState);
    }

    public function validateBoardState(array $boardState)
    {
        return parent::validateBoardState($boardState);
    }

    public function getPossibleMovesList()
    {
        return parent::getPossibleMovesList();
    }

    public function setMovesWeight(array $possible_moves, string $playerUnit)
    {
        return parent::setMovesWeight($possible_moves, $playerUnit);
    }
}

