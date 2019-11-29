<?php

/**
 *
 * @param type $sql
 * @return type
 */
function unprepared($sql)
{
    return Illuminate\Support\Facades\DB::unprepared($sql);
}

/**
 *
 * @param type $name
 * @return \Illuminate\Database\Connection
 */
function db($name = 'mysql')
{
    return Illuminate\Support\Facades\DB::connection($name);
}

/**
 *
 * @return \Illuminate\Support\Facades\Schema
 */
function schema($name = 'mysql')
{
    return \Illuminate\Support\Facades\Schema::connection($name);
}

function begin()
{
    return Illuminate\Support\Facades\DB::beginTransaction();
}

function rollback()
{
    return Illuminate\Support\Facades\DB::rollBack();
}

function commit()
{
    return Illuminate\Support\Facades\DB::commit();
}

/**
 *
 * @param string $table
 * @param string $as
 * @return type
 */
function table($table, $as = null)
{
    return Illuminate\Support\Facades\DB::table($table, $as);
}

function table_seed($table, $values = [])
{
    begin();
    foreach ($values as $value) {
        table($table)->insert($value);
    }
    commit();
}

function table_create($table, $columns = [])
{
    $db = schema();
    $info = collect($db->getColumnListing($table))->map(function ($v) use ($db, $table) {
        $type = $db->getColumnType($table, $v);
        $type = ($type === 'blob') ? 'binary' : $type;
        return [
            $v => $type
        ];
    })->filter(function ($v, $k) {
        return !in_array(key($v), ['id', 'created_at', 'updated_at']);
    })->collapse();
    if ($db->hasTable($table)) {
        $db->table($table, function (Illuminate\Database\Schema\Blueprint $table) use ($columns, $info) {
            if ($info->count() < 1) {
                $table->increments('id');
            }
            foreach ($columns as $key => $value) {
                if (count(explode('.', $value)) > 1) {
                    $relation = explode('.', $value);
                    if ($info->keys()->contains($key)) {

                        $table->dropForeign($table->getTable() . '_' . $key . '_foreign');
                        $table->foreign($key)->references($relation[1])->on($relation[0]);
                    } else {
                        $table->integer($relation[0])->unsigned();
                        $table->foreign($relation[0])->references($relation[1])->on($relation[0]);
                    }
                } else {
                    if ($info->keys()->contains($key)) {
                        $table->{$value}($key)->change();
                    } else {
                        $table->{$value}($key)->nullable();
                    }
                }
            }
            if ($info->count() < 1) {
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent();
            }
        });
    } else {
        $db->create($table, function (Illuminate\Database\Schema\Blueprint $table) use ($columns, $info) {
            if ($info->count() < 1) {
                $table->increments('id');
            }
            foreach ($columns as $key => $value) {
                if (count(explode('.', $value)) > 1) {
                    $relation = explode('.', $value);
                    if ($info->keys()->contains($key)) {
                        $table->dropForeign($table . '_' . $relation[0] . '_foreign');
                        $table->foreign($key)->references($relation[1])->on($relation[0]);
                    } else {
                        $table->integer($key)->unsigned();
                        $table->foreign($key)->references($relation[1])->on($relation[0]);
                    }
                } else {
                    if ($info->keys()->contains($key)) {
                        $table->{$value}($key)->change();
                    } else {
                        $table->{$value}($key)->nullable();
                    }
                }
            }
            if ($info->count() < 1) {
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent();
            }
        });
    }
}

function raw($value)
{
    return Illuminate\Support\Facades\DB::raw($value);
}
