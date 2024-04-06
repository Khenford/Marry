<?php

namespace Khenford\marry\database;

use Khenford\marry\Marry;

class SQLite{

    private static \SQLite3 $database;

    public function __construct(Marry $plugin, string $name){
        if(!is_dir($plugin->getDataFolder())){
            @mkdir($plugin->getDataFolder());
        }

        self::$database = new \SQLite3($plugin->getDataFolder().$name);
        $this->initDataBase();
    }

    public function initDataBase(): void{
        self::getSQLite()->exec(query: "CREATE TABLE IF NOT EXISTS `users` (`id` INTEGER PRIMARY KEY, `username` TEXT, `couple` TEXT, `data` TEXT, `kiss` INTEGER, `home` TEXT, `position` TEXT)");
    }

    public function isCouple(string $username): bool{
        $query = self::getSQLite()->prepare("SELECT * FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        if($result->fetchArray(SQLITE3_ASSOC)){
            return true;
        }
        return false;
    }

    public function createCouple(string $username, string $couple, string $data): void{
        $query = self::getSQLite()->prepare("INSERT INTO `users` (`username`, `couple`, `data`, `kiss`, `home`, `position`) VALUES (:username, :couple, :data, :kiss, :home, :position);");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $query->bindValue(":couple", $couple, SQLITE3_TEXT);
        $query->bindValue(":data", $data, SQLITE3_TEXT);
        $query->bindValue(":kiss", 0, SQLITE3_INTEGER);
        $query->bindValue(":home", "null", SQLITE3_TEXT);
        $query->bindValue(":position", "null", SQLITE3_TEXT);
        $query->execute();
        \GlobalLogger::get()->info("New couple: ".$username." & ".$couple);
    }

    public function addKiss(string $username, int $count): void{
        $query = self::getSQLite()->prepare("SELECT `kiss` FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $kiss = $row["kiss"] + $count;
        }
        $update = self::getSQLite()->prepare("UPDATE `users` SET `kiss`=:kiss WHERE `username`=:username OR `couple`=:username");
        $update->bindValue(":username", $username, SQLITE3_TEXT);
        $update->bindValue(":kiss", $kiss, SQLITE3_TEXT);
        $update->execute();
    }

    public function setHome(string $username, string $name, float $x, float $y, float $z): void{
        $position = json_encode(["x" => $x, "y" => $y, "z" => $z]);
        $update = self::getSQLite()->prepare("UPDATE `users` SET `position`=:position,`home`=:home WHERE `username`=:username OR `couple`=:username");
        $update->bindValue(":username", $username, SQLITE3_TEXT);
        $update->bindValue(":position", $position, SQLITE3_TEXT);
        $update->bindValue(":home", $name, SQLITE3_TEXT);
        $update->execute();
    }

    public function getKiss(string $username): int{
        $query = self::getSQLite()->prepare("SELECT `kiss` FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            return $row["kiss"];
        }
        return 'null';
    }

    public function getHome(string $username): string{
        $query = self::getSQLite()->prepare("SELECT `home` FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            return $row["home"];
        }
        return 'null';
    }

    public function getHomePosition(string $username): array{
        $query = self::getSQLite()->prepare("SELECT `position` FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            return json_decode($row["position"], true);
        }
        return [];
    }

    public function isHome(string $username): bool{
        $query = self::getSQLite()->prepare("SELECT `home` FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        if($row = $result->fetchArray(SQLITE3_ASSOC)){
            if($row["home"] !== 'null'){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    public function delHome(string $username): void{
        $update = self::getSQLite()->prepare("UPDATE `users` SET `position`=:position,`home`=:home WHERE `username`=:username OR `couple`=:username");
        $update->bindValue(":username", $username, SQLITE3_TEXT);
        $update->bindValue(":position", "null", SQLITE3_TEXT);
        $update->bindValue(":home", "null", SQLITE3_TEXT);
        $update->execute();
    }

    public function removeCouple(string $username): void{
        $query = self::getSQLite()->prepare("DELETE FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $query->execute();
    }

    public function getDataCouple(string $username): string{
        $query = self::getSQLite()->prepare("SELECT `data` FROM `users` WHERE `username`=:username OR `couple`=:username");
        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $query->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            return $row["data"];
        }
        return 'null';
    }

    public function getCouple(string $value): string {
        $query = self::getSQLite()->prepare("SELECT `username`, `couple` FROM `users` WHERE `username` = :value OR `couple` = :value");
        $query->bindValue(':value', $value, SQLITE3_TEXT);
        $result = $query->execute();
        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($row["username"] === $value) {
                return $row["couple"] ?? 'null';
            } elseif ($row["couple"] === $value) {
                return $row["username"] ?? 'null';
            }
        }
        return 'null';
    }

    private static function getSQLite(): \SQLite3{
        return self::$database;
    }
}