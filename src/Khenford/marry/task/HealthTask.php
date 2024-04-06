<?php

namespace Khenford\marry\task;

use pocketmine\scheduler\Task;
use pocketmine\world\particle\HeartParticle;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class HealthTask extends Task {

    private static $activeTasks = [];
    private $player;
    private $couple;
    private $maxRuns;

    public function __construct(Player $player, Player $couple, int $maxRuns = 60) {
        $this->player = $player;
        $this->couple = $couple;
        $this->maxRuns = $maxRuns;
    }

    public function onRun(): void {
        $username = $this->player->getPlayerInfo()->getUsername();
        if (!isset(self::$activeTasks[$username])) {
            self::$activeTasks[$username] = $this->maxRuns;
        } else {
            self::$activeTasks[$username]--;
            if (self::$activeTasks[$username] <= 0) {
                $this->getHandler()->cancel();
                unset(self::$activeTasks[$username]);
                return;
            }
        }

        if(!$this->player->isOnline() || !$this->couple->isOnline()) {
            $this->getHandler()->cancel();
            unset(self::$activeTasks[$username]);
            return;
        }


        $world = $this->player->getWorld();
        $pos = $this->player->getPosition();

        $world1 = $this->couple->getWorld();
        $pos1 = $this->couple->getPosition();


        $x = $pos->x + mt_rand(-100, 100) / 100.0;
        $y = $pos->y + 2 + mt_rand(-50, 50) / 100.0;
        $z = $pos->z + mt_rand(-100, 100) / 100.0;

        $x1 = $pos1->x + mt_rand(-100, 100) / 100.0;
        $y1 = $pos1->y + 2 + mt_rand(-50, 50) / 100.0;
        $z1 = $pos1->z + mt_rand(-100, 100) / 100.0;

        $world1->addParticle(new Vector3($x1, $y1, $z1), new HeartParticle());
        $world->addParticle(new Vector3($x, $y, $z), new HeartParticle());
    }
}

