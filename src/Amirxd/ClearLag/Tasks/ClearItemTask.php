<?php

declare(strict_types=1);

namespace Amirxd\ClearLag\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Amirxd\ClearLag\Manage\ClearManager;
use Amirxd\ClearLag\UltraClearLag;

class ClearItemTask extends Task {

    private ClearManager $clearManager;
    private int $secondsLeft;

    public function __construct(ClearManager $clearManager) {
        $this->clearManager = $clearManager;
        $this->secondsLeft = UltraClearLag::getInstance()->getSettingManager()->getClearItemsTimer() ?? 300;
    }

    public function onRun(): void {
        $plugin = UltraClearLag::getInstance();
        $warningTime = $plugin->getSettingManager()->getWarningTime() ?? 30;
        $clearInterval = $plugin->getSettingManager()->getClearItemsTimer() ?? 300;
        
        
        if ($this->secondsLeft === $warningTime) {
            $this->broadcastWarning($warningTime);
        }
        
        
        if ($this->secondsLeft <= 0) {
            $result = $this->clearManager->DoClear();
            $this->broadcastCleared($result['items'], $result['entities']);
            $this->secondsLeft = $clearInterval;
        }
        
        $this->secondsLeft--;
    }
    
    private function broadcastWarning(int $time): void {
        $message = "§c[§6ClearLag§c] §fClearing items in §e{$time}§f seconds!";
        Server::getInstance()->broadcastMessage($message);
    }
    
    private function broadcastCleared(int $items, int $entities): void {
        $total = $items + $entities;
        $message = "§a[§6ClearLag§a] §fCleared §e{$items} §fitems and §e{$entities} §fentities (§e{$total} §ftotal)!";
        Server::getInstance()->broadcastMessage($message);
    }
}