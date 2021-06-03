<?php
require_once('functions.php');
require_once('customs.php');
?>
<html>

<head>
    <meta charset="utf-8">
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
    <script>
        ymaps.ready(init);

        function init() {
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

            var addresses = <?php print_r($customs_addresses); ?>;

            addresses.foreach((item) => {
                ymaps.geoQuery(ymaps.geocode(item)).addToMap(myMap);
            });




            // var myGeocoder = ymaps.geocode("Екатеринбург");
            // console.log(myGeocoder);
            // myGeocoder.then(
            //     function(res) {
            //         // Выведем в консоль данные, полученные в результате геокодирования объекта.
            //         console.log('Все данные геообъекта: ', res.geoObjects.get(0).properties.getAll());
            //     },
            //     function(err) {
            //         // Обработка ошибки.
            //     }
            // );
        }
    </script>
</body>

</html>