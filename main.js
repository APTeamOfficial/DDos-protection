grecaptcha.ready(function () {
    updatestatus("verifying ...");
    grecaptcha.execute($("#site-key").val(), {action: 'submit'}).then(function (token) {
        updatestatus("validating recaptcha");
        var postdata = JSON.stringify({"token": token});
        var url = $("#validation-url").val();
        var req = new XMLHttpRequest();
        req.onerror = function () {
            updatestatus("failed, retrying", "red");
            setTimeout(function () {
                window.location = window.location;
            }, 2000);
        };
        req.ontimeout = function () {
            updatestatus("failed, retrying", "red");
            setTimeout(function () {
                window.location = window.location;
            }, 2000);
        };
        req.onload = function () {
            if (req.readyState === 4) {
                var res = req.responseText;
                if (res.includes("status")) {
                    res = JSON.parse(res);
                    if (res["status"]) {
                        updatestatus("success, redirecting to target url", "green");
                        var max = 3;
                        $("#num-counter").text(max + "s");
                        $("#final-text").css({display: "unset"});
                        setInterval(function () {
                            if (max === 0) {
                                window.location = $("#callback-url").val();
                            } else {
                                max -= 1;
                                $("#num-counter").text(max + "s");
                            }
                        }, 1000);
                    } else {
                        updatestatus("failed, retrying", "red");
                        setTimeout(function () {
                            window.location = window.location;
                        }, 2000);
                    }
                } else {
                    updatestatus("failed, retrying", "red");
                    setTimeout(function () {
                        window.location = window.location;
                    }, 2000);
                }
            }
        };
        req.open("POST", url, true);
        req.setRequestHeader("Content-Type", "application/json");
        req.send(postdata);
    });

    function updatestatus(text, color) {
        $("#status").text(text).css({color: color});
    }

    function success_redir(current, callback) {
        if (current === 0) {
            window.location = callback;
        } else {
            $("#num-counter").text(current + "s");
        }
    }
});
