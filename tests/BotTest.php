<?php

namespace TestTicTacToe;

use Exception;
use PHPUnit\Framework\TestCase;
use TicTacToe\GameEndedException;

class BotTest extends TestCase
{
    public function testCheckGameState() {
        $scenarios = [
            [
                'boardState' => [
                    ['X','X','X'],
                    ['X','O','O'],
                    ['O','O','']
                ],
                'expectedException' => true,
                'expectedMessage' => 'Game won by X on first_row',
            ],
            [
                'boardState' => [
                    ['X','O','X'],
                    ['X','O','O'],
                    ['O','O','']
                ],
                'expectedException' => true,
                'expectedMessage' => 'Game won by O on second_col',
            ],
            [
                'boardState' => [
                    ['O','X','X'],
                    ['X','O','O'],
                    ['O','O','']
                ],
                'expectedException' => false,
            ]
        ];
        foreach ($scenarios as $k => $scenario) {
            try {
                $bot = new BotForTests();
                $bot->checkGameState($scenario['boardState']);
                $this->assertFalse($scenario['expectedException']);
            } catch (GameEndedException $e) {
                $this->assertTrue($scenario['expectedException'], $e->getMessage());
                $this->assertEquals($scenario['expectedMessage'], $e->getMessage());
            } catch (\Throwable $t) {
                $this->fail('Unexpected type of exception. Got an '.$t->toString());
            }
        }
    }

    public function testParseBoardState() {
        $scenarios = [
            [
                'boardState' => [
                    ['X','X','X'],
                    ['X','O','O'],
                    ['O','O','']
                ],
                'expectedResult' => ['X' => ['00', '01', '02', '10'], 'O' => ['11', '12', '20', '21'], '' => ['22']],
            ],
            [
                'boardState' => [
                    ['','',''],
                    ['','',''],
                    ['','','']
                ],
                'expectedResult' => ['X' => [], 'O' => [], '' => ['00','01','02','10','11','12','20','21','22']],
            ]
        ];
        foreach ($scenarios as $scenario) {
            $bot = new BotForTests();
            $bot->parseBoardState($scenario['boardState']);
            $this->assertEquals($scenario['expectedResult'], $bot->getBoardStateMapped());
        }
    }

    public function testValidateBoardState() {
        $scenarios = [
            [
                'boardState' => [],
                'expectedResult' => 'Invalid board state. Should be an array with 3 items',
            ],
            [
                'boardState' => [1,2,3],
                'expectedResult' => 'Invalid board state. Row #0 should contain one array',
            ],
            [
                'boardState' => [['', ''],2,3],
                'expectedResult' => 'Invalid board state. Row #0 of array needs to have 3 values',
            ],
            [
                'boardState' => [['','','A'],['','',''],['','','']],
                'expectedResult' => 'Invalid board state. Invalid value for Row #0 Column #2',
            ]
        ];
        foreach ($scenarios as $scenario) {
            $bot = new BotForTests();
            try {
                $bot->validateBoardState($scenario['boardState']);
            } catch (Exception $e) {
                $this->assertEquals($scenario['expectedResult'], $e->getMessage());
            }
        }
    }

    public function testComputerWillWin()
    {
        $scenarios = [
            [
                'boardState' => [
                    ['X','X',''],
                    ['X','','O'],
                    ['O','O','']
                ],
                'playerUnit' => 'O',
                'expectedResult' => 'Game won by O on third_row',
            ],
            [
                'boardState' => [
                    ['X','X',''],
                    ['X','','O'],
                    ['O','O','']
                ],
                'playerUnit' => 'X',
                'expectedResult' => 'Game won by X on first_row',
            ],
            [
                'boardState' => [
                    ['X','O',''],
                    ['X','X','O'],
                    ['O','O','']
                ],
                'playerUnit' => 'X',
                'expectedResult' => 'Game won by X on diagonal_topleft',
            ],
        ];
        foreach ($scenarios as $scenario) {
            try {
                $bot = new BotForTests();
                $bot->calculateNextMove($scenario['boardState'], $scenario['playerUnit']);
                $this->fail('Computer should have win');
            } catch (GameEndedException $e) {
                $this->assertEquals($scenario['expectedResult'], $e->getMessage());
            }
        }
    }

    public function testComputerWontLetPlayerWin()
    {
        $scenarios = [
            [
                'boardState' => [
                    ['X','X',''],
                    ['X','','O'],
                    ['O','','']
                ],
                'playerUnit' => 'O',
                'expectedResult' => [2, 0, 'O'],
            ],
            [
                'boardState' => [
                    ['X','',''],
                    ['X','X','O'],
                    ['O','','']
                ],
                'playerUnit' => 'O',
                'expectedResult' => [2, 2, 'O'],
            ],
            [
                'boardState' => [
                    ['','','X'],
                    ['O','','O'],
                    ['','X','']
                ],
                'playerUnit' => 'X',
                'expectedResult' => [1, 1, 'X'],
            ],
        ];
        foreach ($scenarios as $scenario) {
            $bot = new BotForTests();
            $ret = $bot->calculateNextMove($scenario['boardState'], $scenario['playerUnit']);
            $this->assertEquals($scenario['expectedResult'], $ret);
        }
    }

    public function testSetMovesWeight()
    {
        $scenarios = [
            [
                'boardState' => [
                    ['X','X',''],
                    ['X','','O'],
                    ['O','','']
                ],
                'playerUnit' => 'O',
                'expectedResult' => [
                    [
                        'move' => '02',
                        'weight' => 100,
                    ]
                ],
            ],
            [
                'boardState' => [
                    ['X','X',''],
                    ['X','','O'],
                    ['O','','']
                ],
                'playerUnit' => 'X',
                'expectedResult' => [
                    [
                        'move' => '02',
                        'weight' => 100,
                    ]
                ],
            ],
            [
                'boardState' => [
                    ['X','',''],
                    ['X','','O'],
                    ['O','','']
                ],
                'playerUnit' => 'X',
                'expectedResult' => [
                    [
                        'move' => '01',
                        'weight' => 2,
                    ],
                    [
                        'move' => '02',
                        'weight' => 1,
                    ],
                    [
                        'move' => '11',
                        'weight' => 2,
                    ],
                    [
                        'move' => '21',
                        'weight' => 1,
                    ],
                    [
                        'move' => '22',
                        'weight' => 1,
                    ]
                ],
            ],
        ];
        foreach ($scenarios as $scenario) {
            $bot = new BotForTests();
            $bot->parseBoardState($scenario['boardState']);
            $possible_moves = $bot->getPossibleMovesList();
            $ret = $bot->setMovesWeight($possible_moves, $scenario['playerUnit']);
            $this->assertEquals($scenario['expectedResult'], $ret);
        }
    }

    public function testGameEnded()
    {
        $scenarios = [
            [
                'boardState' => [
                    ['X','X','O'],
                    ['X','O','O'],
                    ['O','X','X']
                ],
            ],
        ];
        foreach ($scenarios as $scenario) {
            try {
                $bot = new BotForTests();
                $bot->calculateNextMove($scenario['boardState'], 'X');
            } catch (Exception $e) {
                $this->assertEquals('No more moves', $e->getMessage());
            }
        }
    }
}

