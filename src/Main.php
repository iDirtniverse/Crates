<?php

namespace MysteryCrates;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;

use pocketmine\item\Item;
use pocketmine\entity\Entity;
use pocketmine\block\Block;

use pocketmine\network\protocol\AddItemEntityPacket;
use pocketmine\network\protocol\BlockEventPacket;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\SetEntityMotionPacket;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;

use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\Particle;
use pocketmine\level\particle\MobSpawnParticle;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\sound\ExplodeSound;
use pocketmine\level\sound\GhastSound;
use pocketmine\level\Level;
use pocketmine\level\Position;

use pocketmine\tile\Tile;
use pocketmine\tile\Chest as ChestTile;

use pocketmine\block\Chest;
use pocketmine\block\EndPortalFrame;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\entity\Effect;
use pocketmine\entity\InstantEffect;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;

use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener{

  const NETWORK_ID = 66;
  public $disallow = [];
  public $cfg;
  public $eco;
  public function onEnable() {
    
    if(!is_dir($this->getDataFolder())) {
      mkdir($this->getDataFolder());
      }
      
    $this->cfg = new Config($this->getDataFolder() ."MysteryCrates.yml", Config::YAML, [
    #Menssagem Ao Abrir A Caixa
	"World" => "ABL",
    	"Msg" => "§l§aSouls Called!§r",
	"id" => 437
    ]);
    
    $this->eco = EconomyAPI::getInstance();
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    
    }
    
  public function spawnItem(Chest $chest, Item $item, Player $player){
    $item->setCount(1);
    $pk = new AddItemEntityPacket();
    $pk->eid = Entity::$entityCount++;
    $pk->item = $item;
    $pk->x = $chest->getX() + 0.5;
    $pk->y = $chest->getY() + 1.3;
    $pk->z = $chest->getZ() + 0.5;
    $pk->yaw = 0;
    $pk->pitch = 0;
    $pk->roll = 0;
    $player->dataPacket($pk);
    $this->getServer()->getScheduler()->scheduleDelayedTask(new DespawnItem($this, $player, $pk), 15 * 3);
  }

  public function onInteract(PlayerInteractEvent $event){
    $player = $event->getPlayer();
    $item = $player->getInventory()->getItemInHand();
    $level = $this->getServer()->getDefaultLevel();
    $block = $event->getBlock();
    $x = $block->getX();
    $y = $block->getY();
    $z = $block->getZ();
    $pos = new Vector3($x + .5, $y + .5, $z + .5);
    if($block->getSide(Vector3::SIDE_DOWN)->getId() == 120){
          if($item->getId() == "437, 0, 1"){
          $reward = rand(1,24);
          switch($reward){
          case 1:
           $text = "Enchanted Diamond Sword";
           $name = "Chest";
           $item2 = Item::get(276,0,1);
           $enchant = Enchantment::getEnchantment(9);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(5);
           $item2->addEnchantment($enchant);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;     
          case 2:
           $item2 = Item::get(278,0,1);
           $enchant = Enchantment::getEnchantment(15);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(3);
           $enchant2 = Enchantment::getEnchantment(17);
           $enchant2->setLevel(3);
           $item2->addEnchantment($enchant);
           $item2->addEnchantment($enchant2);
           $text = "Enchanted Diamond Pickaxe";
           $name = "Chest";
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 3:
           $item2 = Item::get(466,0,1);
           $text = "Enchanted Golden Apple";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 4:
           $item2 = Item::get(466,0,1);
           $text = "Enchanted Golden Apple 3x";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(466,0,2));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 5:
           $item2 = Item::get(466,0,1);
           $text = "Enchanted Golden Apple 5x";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(466,0,4));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 6:
           $item2 = Item::get(52,12,1);
           $text = "§e§lSpawner";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 7:
           $item2 = Item::get(52,32,1);
           $text = "§e§lSpawner";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 8:
           $item2 = Item::get(57,0,1);
           $text = "Diamond Block 7x";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(57,0,6));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 9:
           $item2 = Item::get(57,0,1);
           $text = "Diamond Block x3";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(57,0,3));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 10:
           $item2 = Item::get(41,0,1);
           $text = "Gold Block 7x";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(41,0,6));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 11:
           $text = "Enchanted Bow + Arrows";
           $name = "Chest";
           $item2 = Item::get(261,0,1);
           $enchant = Enchantment::getEnchantment(19);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(5);
           $item2->addEnchantment($enchant);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(262,0,16));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;     
          case 12:
           $text = "Enchanted Bow";
           $name = "Chest";
           $item2 = Item::get(261,0,1);
           $enchant = Enchantment::getEnchantment(19);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(3);
           $item2->addEnchantment($enchant);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;     
          case 13:
           $text = "Enchanted Bow";
           $name = "Chest";
           $item2 = Item::get(261,0,1);
           $enchant = Enchantment::getEnchantment(19);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(1);
           $item2->addEnchantment($enchant);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;     
          case 14:
           $text = "Enchanted Axe";
           $name = "Chest";
           $item2 = Item::get(279,0,1);
           $enchant = Enchantment::getEnchantment(9);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(5);
           $item2->addEnchantment($enchant);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;     
          case 15:
           $text = "Enchanted Diamond Chestplate";
           $name = "Chest";
           $item2 = Item::get(311,0,1);
           $enchant = Enchantment::getEnchantment(0);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $enchant->setLevel(5);
           $item2->addEnchantment($enchant);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;     
          case 16:
           $item2 = Item::get(311,0,1);
           $text = "Diamond Armour Set";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem(Item::get(310, 0, 1));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(312, 0, 1));
           $player->getInventory()->addItem(Item::get(313, 0, 1));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 17:
           $item2 = Item::get(312,0,1);
           $enchant = Enchantment::getEnchantment(0);
           $enchant->setLevel(3);
           $item2->addEnchantment($enchant);
           $text = "Enchanted Diamond Leggings";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 18:
           $item2 = Item::get(322,0,1);
           $text = "Golden Apple x5";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(322,0,4));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 19:
           $item2 = Item::get(46,0,1);
           $text = "TNT x32 + Flint and Steel";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(46,0,31));
           $player->getInventory()->addItem(Item::get(259,0,1));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 20:
           $item2 = Item::get(384,0,1);
           $text = "XP Bottle x16";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(384,0,15));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 21:
           $item2 = Item::get(3,0,1);
           $text = "Dirt x64";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(3,0,63));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 22:
           $item2 = Item::get(42,0,1);
           $text = "Iron Block x32";
           $name = "Chest";
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(42,0,31));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 23:
           $item2 = Item::get(41,0,1);
           $text = "Gold Block x16";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(41,0,15));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 24:
           $item2 = Item::get(264,0,1);
           $text = "Diamond x32";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $player->getInventory()->addItem($item2);
           $player->getInventory()->addItem(Item::get(264,0,31));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          case 24:
           $item2 = Item::get(264,0,1);
           $text = "$". $this->cfg->get("Money-case24") ."";
           $name = "Chest";
           $this->getServer()->getScheduler()->scheduleDelayedTask(new TempoTask($this, $player), 15 * 3);
           $this->spawnItem($block, $item2, $player);
           $this->spawnOpenChest($player, $block);
           $this->spawnNamedText($block, $player, $text);
           $this->spawnNamedChest($block, $player, $name);
           $this->setAllowed($player, false);
           $player->sendTip($this->cfg->get("Msg"));
           $this->eco->addMoney($player->getName(), $this->cfg->get("Money-case24"));
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CloseChest($this, $player, $block), 15 * 3);
           $this->getServer()->getScheduler()->scheduleDelayedTask(new CoolDown($this, $player), 15 * 3);
          break;
          }
   $b = Block::get(201);
   $item->setCount($item->getCount() - 1);
			$player->getInventory()->setItem($player->getInventory()->getHeldItemSlot(), $item);
          $level->addSound(new GhastSound($player));
          $level->addParticle(new DestroyBlockParticle($pos, $b));
	  $level->addParticle(new MobSpawnParticle($pos, $b));
	  $name = $player->getName();
	  $this->getServer()->broadcastMessage("Â§dSouls Well Â§8Â§l// Â§rÂ§e".$name."Â§7 has called out to the souls!");
        $event->setCancelled(true);
        	}
			elseif($item->getId() !== "437, 0, 1"){
			$player->sendMessage("Â§dSouls Well Â§8Â§l// Â§rÂ§ePlease gather your dragon breathe before calling out to souls!");
			for($i = 1; $i <= 1000; $i++) {
                    $player->knockBack($player, 0, $x, $z, 1);
                	}
		}
        }
    }

  public function spawnOpenChest(Player $player, Chest $chest){
    $pk = new BlockEventPacket();
    $pk->x = $chest->getX();
    $pk->y = $chest->getY();
    $pk->z = $chest->getZ();
    $pk->case1 = 1;
    $pk->case2 = 2;
    $player->dataPacket($pk);
  }

  public function spawnClosedChest(Player $player, Chest $chest){
    $pk = new BlockEventPacket();
    $pk->x = $chest->getX();
    $pk->y = $chest->getY();
    $pk->z = $chest->getZ();
    $pk->case1 = 1;
    $pk->case2 = 0;
    $player->dataPacket($pk);
  }

  public function spawnNamedText(Chest $chest, Player $player, String $text){
    $pk = new AddEntityPacket();
    $pk->eid = Entity::$entityCount++;
    $pk->type = self::NETWORK_ID;
    $pk->x = $chest->getX() + 0.5;
    $pk->y = $chest->getY();
    $pk->z = $chest->getZ() + 0.5;
    $pk->yaw = 0;
    $pk->pitch = 0;
    $pk->metadata = [
        2 => [4, "§7" . $text ],
        15 => [2, 0],
        ];
    $player->dataPacket($pk);
    $this->getServer()->getScheduler()->scheduleDelayedTask(new DespawnText($this, $player, $pk), 15 * 3);
  }

  public function spawnNamedChest(Chest $chest, Player $player, String $text){
  }

  public function setAllowed(Player $p, bool $b) : bool{
    if($b){
      $this->disallowed[$p->getName()] = $p->getName();
      return true;
    }

    unset($this->disallowed[$p->getName()]);
    return true;
  }

  public function isAllowed(Player $p) : bool{
    return isset($this->disallowed[$p->getName()]);
  }


  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    $this->setAllowed($player, true);
  }
}
