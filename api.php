<?php

error_reporting(0);

if(!isset($_GET['do']) || empty(trim($_GET['do']))) {
    die('{}');
}

function dd($data)
{
    print '<pre>';
    print_r($data);
    die;
}

$db = new PDO('mysql:host=127.0.0.1;dbname=litebans;charset=utf8', 'root', '');

$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$perPage = 15;

switch (trim($_GET['do']))
{
    case 'bans':

        switch ($_GET['type'])
        {
            case 'mutes': $table = 'litebans_mutes'; break;
            case 'kicks': $table = 'litebans_kicks'; break;
            case 'warns': $table = 'litebans_warnings'; break;

            default: $table = 'litebans_bans';
        }

        $page = (int)($_GET['page'] ?? 1);
        if($page < 1) {
            $page = 1;
        }

        $name = trim($_GET['name'] ?? '');

        if(strlen($name) < 3) {
            $name = '';
        }

        if(!empty($name)) {
            $query = $db->prepare('SELECT count(*) as c FROM ' . $table . ' b INNER JOIN litebans_history h ON h.uuid = b.uuid WHERE h.name LIKE :name');
            $query->bindValue(':name', "$name%");
        } else {
            $query = $db->query('SELECT count(*) as c FROM ' . $table . ' b INNER JOIN litebans_history h ON h.uuid = b.uuid');
        }

        $query->execute();

        $count = (int) $query->fetch()['c'];

        $countPages = ceil($count / $perPage);

        if($countPages == 0) {
            $page = 1;
        } elseif($page > $countPages) {
            $page = $countPages;
        }

        $offset = ($page - 1) * $perPage;

        if(!empty($name)) {
            $query = $db->prepare('
            SELECT b.id, b.uuid, b.reason, b.banned_by_name, b.time, b.until, b.active, h.name
             FROM ' . $table . ' b
              INNER JOIN litebans_history h ON h.uuid = b.uuid
               WHERE h.name LIKE :name 
                ORDER BY b.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset);
            $query->bindValue(':name', "$name%");
        } else {
            $query = $db->prepare('
            SELECT b.id, b.uuid, b.reason, b.banned_by_name, b.time, b.until, b.active, h.name
             FROM ' . $table . ' b
              INNER JOIN litebans_history h ON h.uuid = b.uuid
               ORDER BY b.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset);
        }

        $query->execute();

        $result = [
            'page' => $page,
            'perPage' => $perPage,
            'countPages' => $countPages,
            'rows' => $query->fetchAll(),
        ];

        die(json_encode($result));

    default: die('{}');
}