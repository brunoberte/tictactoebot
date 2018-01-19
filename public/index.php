<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TicTacToe</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
    <script src="assets/js/app.js"></script>
</head>
<body>
<div class="container">
    <h1 class="text-center">Let' play TicTacToe</h1>
    <div class="game-container">
        <div class="card">
            <div class="card-body">
                <div id="state1">
                    <p>Choose your team</p>
                    <div class="btn-group" id="character-choose" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-secondary">X</button>
                        <button type="button" class="btn btn-secondary">O</button>
                    </div>
                </div>
                <div id="state2" style="display: none;">
                    <form action="#" id="form-board">
                        <input type="hidden" id="playerUnit" name="playerUnit" value="" />
                        <input type="hidden" id="b00" name="boardState[0][0]" value="" />
                        <input type="hidden" id="b01" name="boardState[0][1]" value="" />
                        <input type="hidden" id="b02" name="boardState[0][2]" value="" />
                        <input type="hidden" id="b10" name="boardState[1][0]" value="" />
                        <input type="hidden" id="b11" name="boardState[1][1]" value="" />
                        <input type="hidden" id="b12" name="boardState[1][2]" value="" />
                        <input type="hidden" id="b20" name="boardState[2][0]" value="" />
                        <input type="hidden" id="b21" name="boardState[2][1]" value="" />
                        <input type="hidden" id="b22" name="boardState[2][2]" value="" />
                    </form>
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <span class="player-1 nav-link active" href="#">Player: <span></span></span>
                        </li>
                        <li class="nav-item">
                            <span class="player-2 nav-link disabled" href="#">Computer: <span></span></span>
                        </li>
                    </ul>
                    <br>
                    <div class="board">
                        <p class="text-center">
                            <button class="btn btn00" data-position="00"></button>
                            <button class="btn btn01" data-position="01"></button>
                            <button class="btn btn02" data-position="02"></button>
                        </p>
                        <p class="text-center">
                            <button class="btn btn10" data-position="10"></button>
                            <button class="btn btn11" data-position="11"></button>
                            <button class="btn btn12" data-position="12"></button>
                        </p>
                        <p class="text-center">
                            <button class="btn btn20" data-position="20"></button>
                            <button class="btn btn21" data-position="21"></button>
                            <button class="btn btn22" data-position="22"></button>
                        </p>
                    </div>

                    <small>*Blue: active player</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
