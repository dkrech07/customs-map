<html>
    <head>
        <link rel="stylesheet" href="libs/dropzone.css">
        <link rel="stylesheet" href="libs/dropzone.css">
        <script src="libs/dropzone.js"></script>

        <script src="https://api-maps.yandex.ru/2.1/?apikey=4f3c09de-626b-498a-bc29-cff656b39532&lang=ru_RU" type="text/javascript"></script>
    </head>
    <body>
        <main>

        <div id="map" style="width: 800px; height: 400px"></div>

        <form method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
            <input type="file" name="uploadfile">
            <input type="submit" value="Загрузить файл">
        </form>
        </main>
        <script type="text/javascript">
            // Функция ymaps.ready() будет вызвана, когда
            // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
            ymaps.ready(init);
            function init(){
                // Создание карты.
                var myMap = new ymaps.Map("map", {
                    // Координаты центра карты.
                    // Порядок по умолчанию: «широта, долгота».
                    // Чтобы не определять координаты центра карты вручную,
                    // воспользуйтесь инструментом Определение координат.
                    center: [55.76, 37.64],
                    // Уровень масштабирования. Допустимые значения:
                    // от 0 (весь мир) до 19.
                    zoom: 7
                });

                // Создаем геообъект с типом геометрии "Точка".
        myGeoObject = new ymaps.GeoObject({
            // Описание геометрии.
            geometry: {
                type: "Point",
                coordinates: [55.8, 37.8]
            },
            // Свойства.
            properties: {
                // Контент метки.
                iconContent: 'Я тащусь',
                hintContent: 'Ну давай уже тащи'
            }
        }, {
            // Опции.
            // Иконка метки будет растягиваться под размер ее содержимого.
            preset: 'islands#blackStretchyIcon',
            // Метку можно перемещать.
            draggable: true
        }),
        myPieChart = new ymaps.Placemark([
            55.847, 37.6
        ], {
            // Данные для построения диаграммы.
            data: [
                {weight: 8, color: '#0E4779'},
                {weight: 6, color: '#1E98FF'},
                {weight: 4, color: '#82CDFF'}
            ],
            iconCaption: "Диаграмма"
        }, {
            // Зададим произвольный макет метки.
            iconLayout: 'default#pieChart',
            // Радиус диаграммы в пикселях.
            iconPieChartRadius: 30,
            // Радиус центральной части макета.
            iconPieChartCoreRadius: 10,
            // Стиль заливки центральной части.
            iconPieChartCoreFillStyle: '#ffffff',
            // Cтиль линий-разделителей секторов и внешней обводки диаграммы.
            iconPieChartStrokeStyle: '#ffffff',
            // Ширина линий-разделителей секторов и внешней обводки диаграммы.
            iconPieChartStrokeWidth: 3,
            // Максимальная ширина подписи метки.
            iconPieChartCaptionMaxWidth: 200
        });

    myMap.geoObjects
        .add(new ymaps.Placemark([55.833436, 37.715175], {
            balloonContent: '<strong>серобуромалиновый</strong> цвет'
        }, {
            preset: 'islands#dotIcon',
            iconColor: '#735184'
        }))
            }
        </script>
    </body>
</html>

<?php
$file_name = $_FILES['uploadfile']['name'];
$file_path = 'uploads/';
$file_url = 'uploads/' . $file_name;

move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file_path . $file_name);

// Подключаем библиотеку
require_once "PHPExcel.php";

// Функция преобразования листа Excel в таблицу MySQL, с учетом объединенных строк и столбцов.
// Значения берутся уже вычисленными. Параметры:
//     $worksheet - лист Excel
//     $connection - соединение с MySQL (mysqli)
//     $table_name - имя таблицы MySQL
//     $columns_name_line - строка с именами столбцов таблицы MySQL (0 - имена типа column + n)
function excel2mysql($worksheet, $connection, $table_name, $columns_name_line = 0)
{
    // Проверяем соединение с MySQL
    if (!$connection->connect_error) {
        // Строка для названий столбцов таблицы MySQL
        $columns_str = "";
        // Количество столбцов на листе Excel
        $columns_count = PHPExcel_Cell::columnIndexFromString($worksheet->getHighestColumn());

        // Перебираем столбцы листа Excel и генерируем строку с именами через запятую
        for ($column = 0; $column < $columns_count; $column++) {
            $columns_str .= ($columns_name_line == 0 ? "column" . $column : $worksheet->getCellByColumnAndRow($column, $columns_name_line)->getCalculatedValue()) . ",";
        }

        // Обрезаем строку, убирая запятую в конце
        $columns_str = substr($columns_str, 0, -1);

        // Удаляем таблицу MySQL, если она существовала
        if ($connection->query("DROP TABLE IF EXISTS " . $table_name)) {
            // Создаем таблицу MySQL
            if ($connection->query("CREATE TABLE " . $table_name . " (" . str_replace(",", " TEXT NOT NULL,", $columns_str) . " TEXT NOT NULL)")) {
                // Количество строк на листе Excel
                $rows_count = $worksheet->getHighestRow();

                // Перебираем строки листа Excel
                for ($row = $columns_name_line + 1; $row <= $rows_count; $row++) {
                    // Строка со значениями всех столбцов в строке листа Excel
                    $value_str = "";

                    // Перебираем столбцы листа Excel
                    for ($column = 0; $column < $columns_count; $column++) {
                        // Строка со значением объединенных ячеек листа Excel
                        $merged_value = "";
                        // Ячейка листа Excel
                        $cell = $worksheet->getCellByColumnAndRow($column, $row);

                        // Перебираем массив объединенных ячеек листа Excel
                        foreach ($worksheet->getMergeCells() as $mergedCells) {
                            // Если текущая ячейка - объединенная,
                            if ($cell->isInRange($mergedCells)) {
                                // то вычисляем значение первой объединенной ячейки, и используем её в качестве значения
                                // текущей ячейки
                                $merged_value = $worksheet->getCell(explode(":", $mergedCells)[0])->getCalculatedValue();
                                break;
                            }
                        }

                        // Проверяем, что ячейка не объединенная: если нет, то берем ее значение, иначе значение первой
                        // объединенной ячейки
                        $value_str .= "'" . (strlen($merged_value) == 0 ? $cell->getCalculatedValue() : $merged_value) . "',";
                    }

                    // Обрезаем строку, убирая запятую в конце
                    $value_str = substr($value_str, 0, -1);

                    // Добавляем строку в таблицу MySQL
                    $connection->query("INSERT INTO " . $table_name . " (" . $columns_str . ") VALUES (" . $value_str . ")");
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }

    return true;
}

// Соединение с базой MySQL
$connection = new mysqli("localhost", "root", "root", "customs_map");
// Выбираем кодировку UTF-8
$connection->set_charset("utf8");

// Загружаем файл Excel
$PHPExcel_file = PHPExcel_IOFactory::load($file_url);

// Преобразуем первый лист Excel в таблицу MySQL
$PHPExcel_file->setActiveSheetIndex(0);
echo excel2mysql($PHPExcel_file->getActiveSheet(), $connection, "excel2mysql0", 1) ? "OK\n" : "FAIL\n";

// Перебираем все листы Excel и преобразуем в таблицу MySQL
foreach ($PHPExcel_file->getWorksheetIterator() as $index => $worksheet) {
    echo excel2mysql($worksheet, $connection, "excel2mysql" . ($index != 0 ? $index : ""), 1) ? "OK\n" : "FAIL\n";
}