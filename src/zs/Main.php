<?php

namespace zs;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player as PMPlayer;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

    /** @var PurePerms */
    private $purePerms;

    public function onEnable(): void {
        
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // Initialize PurePerms
        $this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

        if ($this->purePerms === null) {
            $this->getLogger()->error("PurePerms not found. This plugin requires PurePerms.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $joinedPlayer = $event->getPlayer();
        $group = $this->getUserRank($joinedPlayer);

        $message = $this->getConfig()->get("join_popup_message");
        $message = str_replace("{player}", $joinedPlayer->getName(), $message);

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

        $message = $this->getConfig("leave_popup_message");
        $message = str_replace("{player}", $leftPlayer->getName(), $message);

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

    private function getUserRank(PMPlayer $player): ?string {
        if ($this->purePerms !== null) {
            $group = $this->purePerms->getUserDataMgr()->getGroup($player);

            if ($group !== null) {
                return $group->getName();
            }
        }

        return null;
    }
}
