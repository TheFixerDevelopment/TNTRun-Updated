<?php

namespace TNTRun\commands\sub;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use TNTRun\commands\SubCmd;

class AddfloorSubCmd extends SubCmd{

    public function execute(CommandSender $sender, array $args) : bool{
        if(!($sender instanceof Player)){
            $sender->sendMessage($this->getMessage("error.in-game"));
            return true;
        }
        $this->getMain()->selection[strtolower($sender->getName())]["floors"][] = $sender->getFloorY();
        $sender->sendMessage($this->getMessage("commands.addfloor.created", ["POS" => $sender->getFloorY()]));
        return true;
    }

    public function getInfo(){
        return $this->getMessage("commands.addfloor.info");
    }
}
