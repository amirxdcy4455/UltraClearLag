<?php

declare(strict_types=1);

namespace Amirxd\ClearLag\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Amirxd\ClearLag\Manage\ClearManager;

class ClearItemTask extends Task {

    private ClearManager $clearManager;
    private int $warningCounter = 0;
    
    public function __construct(ClearManager $clearManager) {
        $this->clearManager = $clearManager;
    }

    public function onRun(): void {
        $plugin = $this->clearManager->plugin;
        $warningTime = $plugin->getSettingManager()->getWarningTime() ?? 30;
        
        
        if ($this->warningCounter === $warningTime) {
            $this->broadcastWarning($warningTime);
        }
        
        
        if ($this->warningCounter >= $warningTime) {
            $count = $this->clearManager->clearAllItems();
            $this->broadcastCleared($count);
            $this->warningCounter = 0;
        } else {
            $this->warningCounter++;
        }
    }
    
    private function broadcastWarning(int $time): void {
        $message = "§c[§6ClearLag§c] §fClearing items in §e{$time}§f seconds!";
        Server::getInstance()->broadcastMessage($message);
    }
    
    private function broadcastCleared(int $count): void {
        $message = "§a[§6ClearLag§a] §fCleared §e{$count} §fitems!";
        Server::getInstance()->broadcastMessage($message);
    }
}