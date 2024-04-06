<?php

namespace Khenford\marry;

use Khenford\marry\command\MarryCommand;
use Khenford\marry\database\SQLite;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\Server;

class Marry extends PluginBase{

    private static ?SQLite $data = null;
    private static ?Marry $instance = null;

    public function onEnable(): void{
        $this->saveDefaultConfig();
        self::$data = new SQLite($this, "marry");
        self::$instance = $this;
        $this->getServer()->getCommandMap()->register("", new MarryCommand("marry", "меню свадьбы"));
    }

    public static function getPrefix(): string{
        return self::getInstance()->getConfig()->get("prefix");
    }

    public static function getPrefixPlayer(Player $player){
       if(self::getDataBase()->isCouple($player->getPlayerInfo()->getUsername())){
           return self::getPrefix();
       }
       return null;
    }

    public static function getDataBase(): ?SQLite{
        return self::$data;
    }

    public static function getInstance(): ?Marry{
        return self::$instance;
    }
}