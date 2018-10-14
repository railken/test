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

    public function testSimpleContent()
    {
        $dir = $this->getCacheVar();

        $start = microtime(true);

        $n = 1000;

        echo "\n";
        echo "----------------------\n";
        echo "Testing with (write and remove): $n...\n";
        echo "----------------------\n";


        for ($i = 0; $i < $n; ++$i) {
            $filename = $dir.'/'.$i.'.txt';
            file_put_contents($filename, $this->getRandomString());
            unlink($filename);
        }

        $files = (microtime(true) - $start);
        $start = microtime(true);

        for ($i = 0; $i < $n; ++$i) {
            DB::table('foo')->insert(['name' => $i, 'body' => $this->getRandomString()]);
            DB::table('foo')->where(['name' => $i])->delete();
        }

        $db = (microtime(true) - $start);

        echo 'Files (file_put_contents + unlink): '.$files;
        echo "\n";
        echo 'Mysql (insert + remove): '.$db;
    }
}
