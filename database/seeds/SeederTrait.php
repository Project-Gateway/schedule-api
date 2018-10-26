<?php

namespace Database\seeds;

trait SeederTrait
{
    protected function seedData($table, $fields, $data, $key = ['id'], $updateSeq = true, $seqValue = 100, $seqName = null)
    {

        $insertData = array_map(function ($item) use ($fields) {
            return array_combine($fields, $item);
        }, $data);

        $keys = is_array($key) ? $key : [$key];

        foreach ($insertData as $row) {
            $condition = [];
            foreach ($keys as $key) {
                $condition[$key] = $row[$key];
            }
            if (app('db')->table($table)->where($condition)->count()) {
                app('db')->table($table)->where($condition)->update($row);
            } else {
                app('db')->table($table)->insert($row);
            }
        }

        if ($updateSeq) {
            if ($seqName === null) {
                $seqName = "{$table}_{$keys[0]}_seq";
            }

            app('db')->statement("ALTER SEQUENCE $seqName RESTART WITH $seqValue;");
        }
    }
}
