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
        
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // Initialize PurePerms
        $this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

        if ($this->purePerms === null) {
            $this->getLogger()->error("PurePerms not found. This plugin requires PurePerms.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public function onDisable():void {
        $this->saveResource("config.yml");
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $joinedPlayer = $event->getPlayer();
        $group = $this->getUserRank($joinedPlayer);
    
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            if ($group !== null) {
                $message = $this->config->get("join_popup_message", "{rank} {player} Join Server!");
                $message = str_replace("{player}", $joinedPlayer->getName(), $message);
                $message = str_replace("{rank}", $group, $message);
                $player->sendPopup($message);
            }
        }
    
        $event->setJoinMessage(""); // Disable the default join message
    }
    

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $leftPlayer = $event->getPlayer();
        $group = $this->getUserRank($leftPlayer);
    
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            if ($group !== null) {
                $message = $this->config->get("leave_popup_message", "{rank} {player} Left Server!");
                $message = str_replace("{player}", $leftPlayer->getName(), $message);
                $message = str_replace("{rank}", $group, $message);
                $player->sendPopup($message);
            }
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

