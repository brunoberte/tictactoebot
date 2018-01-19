<?php

include '../vendor/autoload.php';

try {
    $bot = new TicTacToe\Bot();
    $ret = $bot->makeMove($_POST['boardState'], $_POST['playerUnit']);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['move' => $ret]);
} catch (\TicTacToe\GameEndedException $e) {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => $e->getMessage()]);
} catch (\TicTacToe\NoMoreMovesException $e) {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => $e->getMessage()]);
} catch (Exception $e ) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode($e->getMessage());
}


