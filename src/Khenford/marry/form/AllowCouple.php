<?php

namespace Khenford\marry\form;

use com\formconstructor\form\SimpleForm;
use com\formconstructor\form\element\simple\Button;
use Khenford\marry\Marry;
use pocketmine\player\Player;

class AllowCouple{
    public function __construct(Player $player, Player $couple){
        $form = new SimpleForm("Брак");
        $form->addContent("Игрок ".$couple->getPlayerInfo()->getUsername()." хочет вам предложить вступить с ним в брак?");
        $form->addButton(new Button("Да", function () use ($player, $couple){
            Marry::getDataBase()->createCouple($player->getPlayerInfo()->getUsername(), $couple->getPlayerInfo()->getUsername(), date("d.m.y"));
            $messageTemplate = "Вы успешно вступили в брак с игроком %s";
            $playerUsername = $player->getPlayerInfo()->getUsername();
            $coupleUsername = $couple->getPlayerInfo()->getUsername();
            $playerMessage = sprintf($messageTemplate, $coupleUsername);
            $coupleMessage = sprintf($messageTemplate, $playerUsername);
            $player->sendMessage($playerMessage);
            $couple->sendMessage($coupleMessage);

        }));
        $form->addButton(new Button("Нет", function () use ($player){
            $player->sendMessage("Игрок отказался вступать с вами в брак!");
        }));
        $form->send($couple);
    }
}