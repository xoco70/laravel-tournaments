<?php

use Illuminate\Support\Facades\DB;

function setFKCheckOff()
{
    switch (DB::getDriverName()) {
        case 'mysql':
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            break;
        case 'sqlite':
            DB::statement('PRAGMA foreign_keys = OFF');
            break;
    }
}

function setFKCheckOn()
{
    switch (DB::getDriverName()) {
        case 'mysql':
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            break;
        case 'sqlite':
            DB::statement('PRAGMA foreign_keys = ON');
            break;
    }
}