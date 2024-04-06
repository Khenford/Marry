<?php

namespace Khenford\marry\command;

use Khenford\marry\form\Menu;
use Khenford\marry\task\HealthTask;
use Khenford\marry\Marry;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MarryCommand extends Command{

    public function __construct(string $name, Translatable|string $description = ""){
        parent::__construct($name, $description);
        $this->setPermission("marry.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($sender instanceof Player){
            if(isset($args[0])){
                switch ($args[0]){
                    case "kiss":
                        if (!Marry::getDataBase()->isCouple($sender->getPlayerInfo()->getUsername())) {
                            $sender->sendMessage("Вы не состоите в браке!");
                            return true;
                        }
                        if(Marry::getDataBase()->isCouple($sender->getPlayerInfo()->getUsername())){
                            $couple = Marry::getInstance()->getServer()->getPlayerExact(Marry::getDataBase()->getCouple($sender->getPlayerInfo()->getUsername()));
                            if($couple->isOnline()){
                                if($sender->getPosition()->distance($couple->getPosition()->asVector3()) <= 2){
                                    Marry::getDataBase()->addKiss($sender->getPlayerInfo()->getUsername(), 1);
                                    Marry::getInstance()->getScheduler()->scheduleRepeatingTask(new HealthTask($sender, $couple, 40), 12);
                                    $couple->sendMessage("Вы поцеловались с игроком: ".$sender->getPlayerInfo()->getUsername());
                                    $sender->sendMessage("Вы поцеловались с игроком: ".$couple->getPlayerInfo()->getUsername());
                                }else{
                                    $sender->sendMessage("Игрок не онлайн!");
                                }
                            }
                        }
                        break;
                    case "home":
                        if (!Marry::getDataBase()->isCouple($sender->getPlayerInfo()->getUsername())){
                            $sender->sendMessage("Вы не состоите в браке!");
                            return true;
                        } elseif(!Marry::getDataBase()->isHome($sender->getPlayerInfo()->getUsername())){
                            $sender->sendMessage("У вас еще не установлена точка дома!");
                            return true;
                        }
                        $position = Marry::getDataBase()->getHomePosition($sender->getPlayerInfo()->getUsername());
                        $sender->sendMessage("Вы успешно телепортировались к точке дома!");
                        $sender->teleport(new Vector3($position["x"], $position["y"], $position["z"]));
                        break;
                }
            }else{
                new Menu($sender);
            }

        }
        return true;
    }
}