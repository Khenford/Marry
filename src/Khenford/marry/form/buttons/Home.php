<?php

namespace Khenford\marry\form\buttons;

use com\formconstructor\form\CustomForm;
use com\formconstructor\form\element\custom\Input;
use com\formconstructor\form\response\CustomFormResponse;
use Khenford\marry\Marry;
use pocketmine\player\Player;

class Home{
    public function __construct(Player $player){
        $form = new CustomForm("Установка точки дома");
        $form->addElement("home", (new Input("Название дома"))->setPlaceholder("Text..."));

        $form->setHandler(function (Player $player, CustomFormResponse $response){
            $home = $response->getInput("home")->getValue();
            $position = $player->getPosition();
            Marry::getDataBase()->setHome($player->getPlayerInfo()->getUsername(), $home, $position->getFloorX(), $position->getFloorY(), $position->getFloorZ());
            $player->sendMessage("Точка дома успешно установлена! Для телепортации пропишите /marry home");
        });
        $form->send($player);
    }
}