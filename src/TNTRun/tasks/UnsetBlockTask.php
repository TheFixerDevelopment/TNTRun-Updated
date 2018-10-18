<?php

namespace TNTRun\tasks;

use pocketmine\block\Block;
use pocketmine\scheduler\Task;
use TNTRun\Main;

class UnsetBlockTask extends Task{

    private $tntRun;
    private $block;

    public function __construct(Main $tntRun, Block $block){

        $this->tntRun = $tntRun;
        $this->block = $block;
    }

    public function onRun(int $tick) : void{
        $this->block->getLevel()->setBlock($this->block, Block::get(Block::AIR));
    }

}