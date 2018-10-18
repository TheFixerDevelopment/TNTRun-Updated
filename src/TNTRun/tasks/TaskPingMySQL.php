<?php

namespace TNTRun\tasks;

use TNTRun\Main;
use pocketmine\scheduler\Task;

class TaskPingMySQL extends Task{
    /** @var Main */
    private $tntRun;
        
    public function __construct(Main $tntRun){
	
        $this->tntRun = $tntRun;
    }
        
    public function onRun(int $tick) : void{
        $this->tntRun->getStats()->ping();
    }
}