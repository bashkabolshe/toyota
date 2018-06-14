var loader = '<div id="red_spinner"  style="text-align: center;"><img src="img/loader.gif" width="30" height="30"/></div>';
$(document).ready(function () {
    var feattext;
    var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
    $(".main").html(loader);
    $.getJSON("server/requests.php", {action: "getJSON"}, function (res) {
        if (res.result === "ok") {
            $(".main").html('<div class="alert alert-warning alert-dismissible fade show bdres" role="alert">\n\
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                                    <span aria-hidden="true">&times;</span></button>\n\
                                </div><div class="card">\n\
                                                    <div class="card-body">\n\
                                                        <h5 class="card-title">Параметры поиска</h5>\n\
                                                        Модель автомобиля:<select class="form-control cars"></select>\n\
                                                    <div class="grades_div"></div>\n\
                                                </div>\n\
                                                </div><br/><div class="card cardtohide"><div class="card-body results_div"></div></div><br/><div class="emaildiv"></div><div class="sendres"></div>');
            $(".bdres").append("В БД добавлено <b>" + res.rows + "</b> строк.");
            $(".cardtohide,.emaildiv").hide();
            $.getJSON("server/requests.php", {action: "getCars"}, function (res) {
                $(".cars").html('<option></option>' + res.result);
                $(".cars").bind("change", function () {//выбор машины
                    $(".cardtohide,.emaildiv").hide();
                    $(".results_div,.sendres").empty();
                    $(".grades_div").html('Комплектация: <select class="form-control grades"></select>');
                    $.getJSON("server/requests.php", {action: "getGrades", car_id: $(this).val()}, function (res) {
                        $(".grades").html('<option></option>' + res.result);
                        $(".grades").bind("change", function () {//выбор комплектации
                            $(".sendres").empty();
                            $(".cardtohide,.emaildiv").hide();
                            $(".results_div").html(loader);
                            if ($(this).val() === "") {
                                $(".cardtohide").show();
                                $(".results_div").html("Ничего не выбрано");
                            } else {
                                $.getJSON("server/requests.php", {action: "getGradesAll", grade_id: $(this).val()}, function (res) {
                                    $(".cardtohide,.emaildiv").show();
                                    $(".results_div").html(res.result);
                                    $(".features,.colorstablediv,.techspectablediv").hide();
                                    $(".features").css({"font-size": "10pt"});
                                    $("#togglefeat").click(function (e) {
                                        e.preventDefault();
                                        $(".features").toggle("slow", function () { });
                                    });
                                    $.getJSON("server/requests.php", {action: "getColors", grade_id: $(".grades").find(":selected").val()}, function (res) {
                                        $(".colorstablediv").html(res.result);
                                        $("#togglecolors").click(function (e) {
                                            e.preventDefault();
                                            $(".colorstablediv").toggle("slow", function () { });
                                        });
                                    });
                                    $.getJSON("server/requests.php", {action: "getTechspec", grade_id: $(".grades").find(":selected").val()}, function (res) {
                                        $(".techspectablediv").html(res.result);
                                        $("#toggletechspec").click(function (e) {
                                            e.preventDefault();
                                            $(".techspectablediv").toggle("slow", function () { });
                                        });
                                    });
                                });
                            }
                        });
                    });
                });
            });
            $(".emaildiv").html('Отправить содержимое на почту:<div class="input-group mb-3">\n\
                                <input type="email" class="form-control" id="emailadress" placeholder="E-mail" aria-label="E-mail">\n\
                                <div class="input-group-append">\n\
                                <button class="btn btn-outline-secondary" id="sendemail" type="button">Отправить</button></div></div>');

        }

        $("#sendemail").bind("click", function () {//кнопка отправить
            $(".sendres").html(loader);
            if (!email_regex.test($("#emailadress").val())) {
                $('#emailadress').tooltip({'trigger': 'manual', 'title': 'Проверьте адрес'}).tooltip("show");
            } else {
                $('#emailadress').tooltip("hide");
                console.log($(".cardtohide").html());
                $.post("server/sendemail.php", {action: "sendHtml", email: $('#emailadress').val(), msg: $(".cardtohide").html(), car_model: $(".cars").find(":selected").text(), grade: $(".grades").find(":selected").text()}, function (res) {
                    if (res.result == true) {
                        $(".sendres").html('Отправлено');
                    } else {
                        $(".sendres").html('Сбой отправки');
                    }

                }, 'json');
            }
        });
    });

});
