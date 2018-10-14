<?php

namespace Railken\Tests;

use Illuminate\Support\Facades\DB;

class SimpleTest extends BaseTest
{
    public function getCacheVar()
    {
        $dir = __DIR__.'/var/cache';

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    public function getRandomString()
    {
        return str_random(80);
    }

    public function testSimple()
    {
        echo "\n\n---------------------------\n";
        $this->fsTest(1000);
        $this->qbTest(1000);
        $this->pdoTest(1000);
    }

    public function fsTest($n)
    {
        echo "\nTesting fs with {$n}...";
        $start = microtime(true);
        $dir = $this->getCacheVar();

        for ($i = 0; $i < $n; ++$i) {
            $filename = $dir.'/'.$i.'.txt';
            file_put_contents($filename, $this->getRandomString());
            unlink($filename);
        }

        echo microtime(true) - $start;
    }

    public function qbTest($n)
    {
        echo "\nTesting qb with {$n}...";

        $start = microtime(true);

        for ($i = 0; $i < $n; ++$i) {
            DB::table('foo')->insert(['name' => $i, 'body' => $this->getRandomString()]);
            DB::table('foo')->where(['name' => $i])->delete();
        }

        echo microtime(true) - $start;
    }

    public function pdoTest($n)
    {
        echo "\nTesting pdo with {$n}...";

        $con = new \PDO(env('DB_CONNECTION').':host='.env('DB_HOST').';port='.env('DB_PORT').';dbname='.env('DB_DATABASE').'', env('DB_USERNAME'), env('DB_PASSWORD'));

        $start = microtime(true);

        for ($i = 0; $i < $n; ++$i) {
            $stmt = $con->prepare('INSERT INTO foo (name, body) VALUES (?,?)');
            $stmt->execute([$i, $this->getRandomString()]);

            $stmt = $con->prepare('DELETE FROM foo WHERE name = ?');
            $stmt->execute([$i]);
        }

        echo microtime(true) - $start;
    }
}
