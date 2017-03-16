<?php

namespace MysteryCrates;

use pocketmine\scheduler\PluginTask;
use pocketmine\Player;
use pocketmine\block\Chest;
use pocketmine\network\protocol\BlockEventPacket;

class CloseChest extends PluginTask {

    public function __construct(Main $plugin, Player $player, Chest $chest) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->player = $player;
        $this->chest = $chest;
    }

    public function onRun($tick) {
        $pk = new BlockEventPacket();
        $pk->x = $this->chest->getX();
        $pk->y = $this->chest->getY();
        $pk->z = $this->chest->getZ();
        $pk->case1 = 1;
        $pk->case2 = 0;
        $this->player->dataPacket($pk);
        $this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
    }
}
