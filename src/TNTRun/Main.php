<?php

namespace TNTRun;

use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use TNTRun\arena\Arena;
use TNTRun\commands\TNTRunCommand;
use TNTRun\manager\MessageManager;
use TNTRun\manager\MoneyManager;
use TNTRun\manager\SignHandler;
use TNTRun\stats\MySQLStatsProvider;
use TNTRun\stats\SQLiteStatsProvider;

class Main extends PluginBase{
    /** @var stats\StatsProvider */
    private $stats;
    /** @var Arena[] */
    public $arenas = [];
    public $selection = [];
    /** @var SignHandler */
    private $signHandler;
    /** @var PlayerData */
    private $playerData;
    /** @var MoneyManager */
    private $moneyManager;
    /** @var manager\MessageManager */
    private $messageManager;
    /** @var string */
    private $tag;
    /** @var TNTRunCommand */
    private $tntRunCommand;
    /** @var array  */
    public $colors = [
        "0" => TextFormat::BLACK,
        "1" => TextFormat::DARK_BLUE,
        "2" => TextFormat::DARK_GREEN,
        "3" => TextFormat::DARK_AQUA,
        "4" => TextFormat::DARK_RED,
        "5" => TextFormat::DARK_PURPLE,
        "6" => TextFormat::GOLD,
        "7" => TextFormat::GRAY,
        "8" => TextFormat::DARK_GRAY,
        "9" => TextFormat::BLUE,
        "a" => TextFormat::GREEN,
        "b" => TextFormat::AQUA,
        "c" => TextFormat::RED,
        "d" => TextFormat::LIGHT_PURPLE,
        "e" => TextFormat::YELLOW,
        "f" => TextFormat::WHITE
    ];

    public function onEnable() : void{
        if(!file_exists($this->getDataFolder()."/resources"))
            @mkdir($this->getDataFolder()."/resources", 0755, true);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->info(TextFormat::GREEN."TNTRun Enabled!");
        $this->saveDefaultConfig();
        $this->tntRunCommand = new TNTRunCommand($this);
        $this->getServer()->getCommandMap()->register("tntrun", $this->tntRunCommand);
        switch(strtolower($this->getConfig()->get("stats-provider"))){
            case "sqlite3":
                $this->stats = new SQLiteStatsProvider($this);
                break;
            case "mysql":
                $this->stats = new MySQLStatsProvider($this);
                break;
            default:
                $this->stats = new SQLiteStatsProvider($this);
                break;
        }
        $this->tag = trim($this->getConfig()->get("tag"));
        foreach($this->colors as $code => $c)
            $this->tag = str_replace($this->getConfig()->get("code").$code, $c, $this->tag);
        $this->tag .= " ";

        $this->signHandler = new SignHandler($this);
        $this->playerData = new PlayerData($this);
        $this->moneyManager = new MoneyManager($this);
        $this->messageManager = new MessageManager($this);
        $this->loadArenas();
    }

    public function getTag(){
        return $this->tag;
    }

    public function getMessageManager(){
        return $this->messageManager;
    }

    public function getMoneyManager(){
        return $this->moneyManager;
    }

    public function getPlayerData(){
        return $this->playerData;
    }

    public function getSign(){
        return $this->signHandler;
    }

    public function getStats(){
        return $this->stats;
    }

    public function onLoad() : void{
        $this->getLogger()->info(TextFormat::YELLOW."Loading TNTRun...");
    }

    public function onDisable() : void{
        $this->getLogger()->info(TextFormat::RED."TNTRun Disabled");
        $this->getConfig()->save();
        $this->saveArenas();
    }

    public function getCommands(){
        return $this->tntRunCommand;
    }

    public function getSubCommands(){
        return $this->getFile()."src/". __NAMESPACE__."/commands/sub/";
    }

    public function getLobby(){
        $level = $this->getServer()->getLevelByName($this->getConfig()->get("lobby"));
        return $level !== null ? $level->getSafeSpawn() : $this->getServer()->getDefaultLevel()->getSafeSpawn();
    }

    private function loadArenas(){
        if(file_exists($this->getDataFolder()."arenas.yml")){
            $arenas = yaml_parse_file($this->getDataFolder()."arenas.yml");
            foreach($arenas as $data){
                $this->arenas[strtolower($data["name"])] = new Arena($this, $data);
            }
        }
    }

    private function saveArenas(){
        $save = [];
        foreach($this->arenas as $arena){
            $str = $arena->getStructureManager();
            $spawn = $str->getSpawn();
            $save[] = ["name" => $arena->getName(),
                "pos1" => [
                    "x" => $str->getPos1()["x"],
                    "z" => $str->getPos1()["z"]
                ],
                "pos2" => [
                    "x" => $str->getPos2()["x"],
                    "z" => $str->getPos2()["z"]
                ],
                "floors" => $str->getFloors(),
                "levelName" => $str->getLevelName(),
                "spawn" => ["x" => $spawn->x, "y" => $spawn->y, "z" => $spawn->z]
            ];
        }
        yaml_emit_file($this->getDataFolder()."arenas.yml", $save);
    }

}
