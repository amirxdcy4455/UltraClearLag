<?php

declare(strict_types=1);

namespace Amirxd\ClearLag\Manage;

use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\world\World;
use Amirxd\ClearLag\UltraClearLag;
use Amirxd\ClearLag\Tasks\ClearItemTask;

class ClearManager {

    private UltraClearLag $plugin;

    public function __construct(UltraClearLag $plugin) {
        $this->plugin = $plugin;
    }

    public function startClearTasks(): void {
        $interval = $this->plugin->getSettingManager()->getClearItemsTimer();
        $this->plugin->getScheduler()->scheduleRepeatingTask(
            new ClearItemTask($this), 
            $interval * 20
        );
        $this->plugin->getLogger()->info("§7Clear task started (§e{$interval}s§7 interval)");
    }
    
    
    public function clearAllItems(): int {
        $count = 0;
        
        foreach ($this->getAllWorlds() as $world) {
            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof ItemEntity) {
                    $entity->close();
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    
    public function clearAllEntities(): int {
        $count = 0;
        
        foreach ($this->getAllWorlds() as $world) {
            foreach ($world->getEntities() as $entity) {
                if ($this->shouldClear($entity)) {
                    if(!$this->isSafeWorld($entity->getWorld())){
                        $entity->close();
                        $count++;    
                    }
                }
            }
        }
        
        return $count;
    }
    
    private function isSafeWorld(World $world):bool{
        $worldList = $this->plugin->getSettingManager()->getExemptWorlds();
        foreach($worldList as $a){
            if($world->getFolderName() === $a)return true;
        }
        return false;
    }

    private function shouldClear(Entity $entity): bool {
        return $entity instanceof ItemEntity;
    }
    /** @return World[] */
    private function getAllWorlds(): array {
        return $this->plugin->getServer()->getWorldManager()->getWorlds();
    }
    
    
    public function getItemCount(): int {
        $count = 0;
        
        foreach ($this->getAllWorlds() as $world) {
            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof ItemEntity) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
}