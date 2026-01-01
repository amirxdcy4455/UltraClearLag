<?php

declare(strict_types=1);

namespace Amirxd\ClearLag\Manage;

use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\world\World;
use Amirxd\ClearLag\UltraClearLag;
use Amirxd\ClearLag\Tasks\ClearItemTask;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\Throwable;

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
    
    public function DoClear(): array {
        $itemsCleared = $this->clearAllItems();
        $entitiesCleared = $this->clearAllEntities();
        
        return [
            "items" => $itemsCleared,
            "entities" => $entitiesCleared,
            "total" => $itemsCleared + $entitiesCleared
        ];
    }
    
    public function clearAllItems(): int {
        $count = 0;
        
        foreach ($this->getAllWorlds() as $world) {
            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof ItemEntity) {
                    if($this->isSafeWorld($entity->getWorld()))continue;
                    $entity->close();
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    
    public function clearAllEntities(): int {
        $count = 0;
        $clearableTypes = $this->plugin->getSettingManager()->getClearableEntities();
        
        foreach ($this->getAllWorlds() as $world) {
            if ($this->isSafeWorld($world)) continue;
            
            foreach ($world->getEntities() as $entity) {
                if ($this->shouldClearEntity($entity, $clearableTypes)) {
                    $entity->close();
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    private function shouldClearEntity(Entity $entity, array $clearableTypes): bool {
        foreach ($clearableTypes as $type) {
            switch ($type) {
                case "item":
                    if ($entity instanceof ItemEntity) return true;
                    break;
                case "arrow":
                    if ($entity instanceof Arrow) return true;
                    break;
                case "snowball":
                case "egg":
                case "ender_pearl":
                    if ($entity instanceof Throwable) return true;
                    break;
            }
        }
        return false;
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