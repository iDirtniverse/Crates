<?php

namespace MysteryCrates;

use pocketmine\scheduler\PluginTask;
use pocketmine\Player;
use pocketmine\block\Chest;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
class DespawnText extends PluginTask {

    public function __construct(Main $plugin, Player $player, AddEntityPacket $pk) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->player = $player;
        $this->pk = $pk;
    }

    public function onRun($tick) {
        $players = $this->plugin->getServer()->getOnlinePlayers();
        $pk = new RemoveEntityPacket();
        $pk->eid = $this->pk->eid;
        foreach ($players as $pl) {
        $pl->directDataPacket($pk);
        }
        $this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
    }
}
