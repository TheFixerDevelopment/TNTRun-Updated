<?php

namespace TNTRun\commands\sub;

use pocketmine\command\CommandSender;
use TNTRun\commands\SubCmd;
use pocketmine\Player;

class Pos2SubCmd extends SubCmd{

    public function execute(CommandSender $sender, array $args) : bool{
        if(!($sender instanceof Player)){
            $sender->sendMessage($this->getMessage("error.in-game"));
            return true;
        }
        $this->getMain()->selection[strtolower($sender->getName())]["pos2"] = ["x" => $sender->getFloorX(), "z" =>  $sender->getFloorZ(), "level" => $sender->getLevel()->getName()];
        $sender->sendMessage($this->getMessage("commands.pos2.set", ["POS" => "x=".$sender->getFloorX().", z=".$sender->getFloorZ().", level=".$sender->getLevel()->getName()]));
        return true;
    }

    public function getInfo(){
        return $this->getMessage("commands.pos2.info");
    }
}
