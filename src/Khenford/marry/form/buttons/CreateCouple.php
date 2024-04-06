<?php

namespace Khenford\marry\form\buttons;

use com\formconstructor\form\CustomForm;
use com\formconstructor\form\element\custom\Input;
use com\formconstructor\form\response\CustomFormResponse;
use Khenford\marry\form\AllowCouple;
use Khenford\marry\Marry;
use pocketmine\player\Player;

class CreateCouple{
    public function __construct(Player $player){
        $form = new CustomForm("Создание пары");
        $form->addElement("create", (new Input("Укажите ник игрока"))
                ->setPlaceholder("Text..."));

        $form->setHandler(function (Player $player, CustomFormResponse $response) {
            $input = $response->getInput("create")->getValue();
            $couple = Marry::getInstance()->getServer()->getPlayerExact($input);
            if($couple !== null){
                if($couple->isOnline()) {
                    if($input == $player->getPlayerInfo()->getUsername()){
                        $player->sendMessage("Ты не можешь этого сделать!");
                        return;
                    }
                    new AllowCouple($player, $couple);
                }else{
                    $player->sendMessage("Игрок не онлайн");
                }
            }else{
                $player->sendMessage("Данного игрока не существует на сервере");
            }
        });
        $form->send($player);

    }
}