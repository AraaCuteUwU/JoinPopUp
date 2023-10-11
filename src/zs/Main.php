<?php

namespace zs;

use _64FF00\PureChat\PureChat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {

    /** @var ?PureChat */
    private ?PureChat $pureChat;

    public function onEnable(): void {
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // Initialize pureChat
        $this->pureChat = $this->getServer()->getPluginManager()->getPlugin("PureChat");

        if ($this->pureChat === null) {
            $this->getLogger()->error("PureChat not found. This plugin requires PureChat.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $joinedPlayer = $event->getPlayer();
        $group = $this->getUserRank($joinedPlayer);

        $message = "{rank} §r§eJoin Server!";
        // $message = str_replace("{player}", $joinedPlayer->getName(), $message);

        if ($group !== null) {
            $message = str_replace("{rank}", $group, $message);
        } else {
            $message = str_replace("{rank}", "Default", $message); // Replace with a default rank name if needed
        }

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->sendPopup($message);
        }

        $event->setJoinMessage(""); // Disable the default join message
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $leftPlayer = $event->getPlayer();
        $group = $this->getUserRank($leftPlayer);

        $message = "{rank} §r§cLeft Server!";
        // $message = str_replace("{player}", $leftPlayer->getName(), $message);

        if ($group !== null) {
            $message = str_replace("{rank}", $group, $message);
        } else {
            $message = str_replace("{rank}", "Default", $message); // Replace with a default rank name if needed
        }

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->sendPopup($message);
        }

        $event->setQuitMessage(""); // Disable the default quit message
    }

    private function getUserRank(Player $player): ?string {
        if ($this->pureChat !== null) {
            // thanks to fredy
            $nametag = $this->pureChat->getNametag($player);
    
            if ($nametag !== null) {
                return $nametag;
            }
        }
    
        return null;
    }
}
