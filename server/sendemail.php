<?php

//

header('Content-Type: application/json; charset=utf-8');
include 'connect_db.php';
$input = filter_input_array(INPUT_POST);
$str = "";
$action = $input['action'];
$email = $input['email'];
$msg = $input['msg'];
$car_model = $input['car_model'];
$grade = $input['grade'];

$html = '<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <title>Модели и комплектации</title>
    </head>
    <body>
        <div class="container main" style="margin-top:10px;">
        <div class="card"><div class="card-body"><h5 class="card-title">Было выбрано</h5>
                                                        Модель автомобиля: <b>' . $car_model . '</b><br/>
                                                        Комплектация: <b>' . $grade . '</b></div></div>
                                                   
                      <div class="card">                          
            ' . $msg . '
                </div>    


        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script> 
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
        <script>
                $(document).ready(function () {
                    $("#togglefeat").click(function (e) {
                        e.preventDefault();
                        $(".features").toggle("slow", function () { });
                    });
                    $("#togglecolors").click(function (e) {
                        e.preventDefault();
                        $(".colorstablediv").toggle("slow", function () { });
                    });
                    $("#toggletechspec").click(function (e) {
                        e.preventDefault();
                        $(".techspectablediv").toggle("slow", function () { });
                    });

                });
            </script>
    </body>
</html>';

switch ($action) {
    case 'sendHtml':
        $filename = "configaration_" . $car_model . "_" . $grade . ".html";
        $fp = fopen($filename, "w");
        fwrite($fp, $html);
        fclose($fp);

        include "libmail.php"; // вставляем файл с классом
        $m = new Mail("UTF-8"); // начинаем 
        $m->log_on(true);
        $m->From("info@bashkabolshe.ru"); // от кого отправляется почта 
        $m->To($email); // кому адресованно
        $m->Subject("Конфигурация с сайта");
        $m->Body("Во вложении");
        $m->Attach($filename, "", "text/html", "attachment");
        $m->smtp_on("ssl://smtp.yandex.ru", "info@bashkabolshe.ru", "QWE123ASD", 465, 10); // если указана эта команда, отправка пойдет через SMTP 
        $m->Send();    // а теперь пошла отправка

        $status = $m->status_mail['status'];
        if ($status == "true") {
            unlink($filename);
        }
        $answer = array("answer" => 'ok', 'result' => $status);
        echo json_encode($answer);
        break;
    default : echo "Нет команды";
}


    