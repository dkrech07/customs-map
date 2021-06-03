<?php
// Соединение с базой MySQL
$connection = new mysqli("localhost", "root", "root", "customs_map");
// Выбираем кодировку UTF-8
$connection->set_charset("utf8");

/**
 * Выполняет подключение и запрос к базе данных
 * В случае ошибки при подключении к БД, возвращает сообщение об ошибке
 * @param  object $con Ресурс соединения
 * @param  string $sql SQL-запрос к базе данных
 * @param  string $type Варианты массива, полученного при обращении к базе данных
 * @return array
 */
function select_query($con, $sql, $type = 'all')
{
    mysqli_set_charset($con, "utf8");
    $result = mysqli_query($con, $sql) or trigger_error("Ошибка в запросе к базе данных: " . mysqli_error($con), E_USER_ERROR);

    if ($type === 'assoc') {
        return mysqli_fetch_assoc($result);
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$customs_list = select_query($connection, "SELECT ADRTAM FROM excel2mysql");

$customs_addresses = [];
foreach ($customs_list as $custom) {
    $customs_addresses[] = $custom['ADRTAM'];
}

//header('Content-Type: application/json');
json_encode($customs_addresses);
