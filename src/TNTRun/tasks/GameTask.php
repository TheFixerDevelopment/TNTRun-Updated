<?php

namespace TNTRun\tasks;

use pocketmine\scheduler\Task;
use TNTRun\arena\Arena;
use TNTRun\Main;

class GameTask extends Task{

    public function __construct(Main $tntRun, Arena $arena){

        $this->tntRun = $tntRun;
        $this->arena = $arena;
    }

    public function onRun(int $tick) : void{
        $this->arena->getGameHandler()->runGame();
    }

}