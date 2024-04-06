<?php

namespace Khenford\marry\form;

use com\formconstructor\form\element\simple\Button;
use com\formconstructor\form\SimpleForm;
use Khenford\marry\form\buttons\Home;
use Khenford\marry\Marry;
use Khenford\marry\form\buttons\CreateCouple;
use Khenford\marry\form\buttons\RemoveCouple;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Menu{
    public function __construct(Player $player){
        $form = new SimpleForm("Свадьба");
        if(Marry::getDataBase()->isCouple($player->getPlayerInfo()->getUsername())){
            $form->addContent("Поцелуев: ".Marry::getDataBase()->getKiss($player->getPlayerInfo()->getUsername())."\nДом: ".Marry::getDataBase()->getHome($player->getPlayerInfo()->getUsername())."\nДата брака: ".Marry::getDataBase()->getDataCouple($player->getPlayerInfo()->getUsername()));
        }
        $form->addButton(new Button("Создать брак", function () use ($player){
            if(Marry::getDataBase()->isCouple($player->getPlayerInfo()->getUsername())){
                $player->sendMessage("Вы состоите в браке с ".Marry::getDataBase()->getCouple($player->getPlayerInfo()->getUsername()));
                return;
            }
            new CreateCouple($player);
        }));
        if(Marry::getDataBase()->isCouple($player->getPlayerInfo()->getUsername())){
            $form->addButton(new Button("Развестись", function () use ($player){
                $player->sendMessage("Вы успешно развелись с ".Marry::getDataBase()->getCouple($player->getPlayerInfo()->getUsername()));
                Marry::getDataBase()->removeCouple($player->getPlayerInfo()->getUsername());
            }));
            if(Marry::getDataBase()->isHome($player->getPlayerInfo()->getUsername())){
                $form->addButton(new Button("Удалить точку дома", function () use ($player){
                    $player->sendMessage("Вы успешно удалили точку дома!");
                    Marry::getDataBase()->delHome($player->getPlayerInfo()->getUsername());
                }));
                $form->addButton(new Button("Телепортироваться к точке дома", function () use ($player){
                    $position = Marry::getDataBase()->getHomePosition($player->getPlayerInfo()->getUsername());
                    $player->sendMessage("Вы успешно телепортировались к точке дома!");
                    $player->teleport(new Vector3($position["x"], $position["y"], $position["z"]));
                }));
            }else{
                $form->addButton(new Button("Установить точку дома", function () use ($player){
                    new Home($player);
                }));
            }
        }
        $form->send($player);
    }
}