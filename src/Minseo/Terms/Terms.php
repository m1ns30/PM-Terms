<?php

namespace Minseo\Terms;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\form\Form;
use pocketmine\utils\TextFormat;

class Terms extends PluginBase implements Listener
{
    public static $agreeList = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());

        if (!isset(self::$agreeList[$name])) {
            $this->sendTermsForm($player);
        }
    }

    public function sendTermsForm(Player $player): void
    {
        $player->sendForm(new TermsForm());
    }
}

class TermsForm implements Form
{
    public function jsonSerialize()
    {
        return [
            "type" => "custom_form",
            "title" => TextFormat::BOLD . "Terms",
            "content" => [
                [
                    "type" => "label",
                    "text" => TextFormat::GRAY . "Welcome to the Server!"
                ],
                [
                    "type" => "toggle",
                    "text" => TextFormat::GRAY . "Are you part of the server community?"
                ],
                [
                    "type" => "toggle",
                    "text" => TextFormat::GRAY . "Are you familiar with the server rules?"
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if (is_null($data) || count($data) !== 3 || !$data[1] || !$data[2]) {
            $player->kick(TextFormat::RED . "You did not agree to the server terms.");
            return;
        }

        $name = strtolower($player->getName());
        Terms::$agreeList[$name] = true;
        $player->sendMessage(TextFormat::GREEN . "You have agreed to the terms of the server. Have a good time!");
    }
}
