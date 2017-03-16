<?php

namespace MysteryCrates;

use pocketmine\scheduler\PluginTask;
use pocketmine\Player;
use pocketmine\block\Chest;

class Cooldown extends PluginTask {

    public function __construct(Main $plugin, Player $player) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function onRun($tick) {
        $this->plugin->setAllowed($this->player, true);
        $this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
    }
}
