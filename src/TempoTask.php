<?php

namespace MysteryCrates;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\block\Chest;

class TempoTask extends PluginTask {

    public $plugin;
    public $seconds;

      public function __construct(Main $plugin, Player $player) {
          parent::__construct($plugin);
          $this->plugin = $plugin;
          $this->seconds = 5;
      }

      public function getPlugin() {
          return $this->plugin;
      }

      public function onRun($tick) {
          //TASK
          $this->getPlugin()->getLogger()->info("Task " . $this->seconds . "!");
          //Check
          if($this->seconds === 5) {
              //Inicio
          $this->getPlugin()->getLogger()->info("Task " . $this->seconds . "!");
          }
              //FIM
          if($this->seconds === 0) {
              $this->getPlugin()->getLogger()->info("Task " . $this->seconds . "!");
              $this->getPlugin()->removeTask($this->getTaskId());
          }
          //Diminuir - Para O $this->seconds
          $this->seconds--;
      }
}
