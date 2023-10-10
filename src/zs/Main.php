<?php

namespace zs;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player as PMPlayer;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    /** @var PurePerms */
    private $purePerms;
    private $config;

    public function onEnable(): void {
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();

        // Initialize PurePerms
        $this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

        if ($this->purePerms === null) {
            $this->getLogger()->error("PurePerms not found. This plugin requires PurePerms.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $group = $this->getUserRank($player);
        $message = $this->config->get("join_popup_message");
        foreach ($this->getServer()->getOnlinePlayers() as $player) {

        if ($group !== null) {
            // $message = $this->config->get("join_popup_message", "{rank} {player} Join Server!");
            $message = str_replace("{player}", $player->getName(), $message);
            $message = str_replace("{rank}", $group, $message);
            $player->sendPopup($message);
        }
    }

        $event->setJoinMessage(""); // Disable the default join message
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $group = $this->getUserRank($player);
        $message = $this->config->get("leave_popup_message");
        foreach ($this->getServer()->getOnlinePlayers() as $player) {

        if ($group !== null) {
            // $message = $this->config->get("leave_popup_message", "{rank} {player} Left Server!");
            $message = str_replace("{player}", $player->getName(), $message);
            $message = str_replace("{rank}", $group, $message);
            $player->sendPopup($message);
        }
    }
        $event->setQuitMessage("");// Disable the default leave message
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

