<?php
{
    require_once "config.php";
    require_once "Front.php";
    $ip = $_SERVER["REMOTE_ADDR"];
    function verify_rec($ip, $rec)
    {
        $curl = curl_init();
        curl_setopt_array($curl,
            [
                CURLOPT_URL => "https://www.google.com/recaptcha/api/siteverify",
                CURLOPT_POST => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => "secret=" . SECRET_KEY . "&response=$rec&remoteip=$ip"
            ]);
        $source = curl_exec($curl);
        if (curl_error($curl)) {
            return false;
        } else {
            if (strpos($source, "success") !== false) {
                $source = json_decode($source, true);
                if ($source["success"]) {
                    $res = create_hash($ip, $rec);
                    if ($res !== false) {
                        return $res;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    function create_hash($ip, $rec)
    {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if ($connection->connect_errno) {
            $connection->close();
            return false;
        } else {
            $time = strtotime(date("Y/m/d H:i:s")) + EXPIRE_TIME;
            $hash = md5($ip . "|" . $rec . "|" . $time);
            $connection->query(<<<body
INSERT INTO `tokens_pool`(`token`, `ip`, `time`) VALUES ("$hash","$ip","$time")
body
            );
            $connection->close();
            return $hash;
        }
    }

    function check_cookie($ip, $cookie)
    {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if ($connection->connect_errno) {
            $connection->close();
            return false;
        } else {
            $query = $connection->query(<<<body
SELECT * FROM `tokens_pool` WHERE `ip`="$ip" AND `token`="$cookie"
body
            );
            if ($query->num_rows > 0) {
                $res = $query->fetch_assoc();
                $check = strtotime(date("Y/m/d H:i:s"));
                if (intval($res["time"]) >= $check) {
                    $connection->close();
                    return true;
                } else {
                    $res = $connection->query(<<<body
DELETE FROM `tokens_pool` WHERE `ip`="$ip" AND `token`="$cookie"
body
                    );
                    return false;
                }
            } else {
                $connection->close();
                return false;
            }
        }
    }

    function get_callback_url()
    {
        $callback_url = "";
        if (isset($_GET["ref"])) {
            $url = parse_url($_GET["ref"], PHP_URL_HOST);
            if ($url == $_SERVER["HTTP_HOST"]) {
                $callback_url = $_GET["ref"];
            }
        } else {
            $callback_url = "https://" . $_SERVER["HTTP_HOST"];
        }
        return $callback_url;
    }

    function validate_ref($custom_callback = "")
    {
        global $ip;
        $callback_url = get_callback_url();
        if (strlen($custom_callback) > 0) {
            $callback_url = $custom_callback;
        }
        $front = new Front();
        $front->callback_url = $callback_url;
        $front->ip = $ip;
        $front->sitekey = SITE_KEY;
        $front->host = $_SERVER["HTTP_HOST"];
        $front->validation_url = SCRIPT_PATH . "?verify=true";
        $source = $front->generate();
        if (isset($_COOKIE["adptoken"])) {
            $cookie = $_COOKIE["adptoken"];
            if (check_cookie($ip, $cookie)) {
                header("Location: $callback_url");
            } else {
                setcookie("adptoken", "", time() - 3600);
                echo $source;
            }
        } else {
            echo $source;
        }
    }

    function validate_check()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        if (isset($_COOKIE["adptoken"])) {
            $cookie = $_COOKIE["adptoken"];
            if (check_cookie($ip, $cookie)) {
                return true;
            } else {
                setcookie("adptoken", "", time() - 3600);
                return false;
            }
        } else {
            return false;
        }
    }

    if (isset($_GET["verify"])) {
        $data = file_get_contents("php://input");
        if (strpos($data, "token") !== false) {
            $data = json_decode($data, true);
            if (isset($data["token"])) {
                $token_res = verify_rec($ip, $data["token"]);
                if ($token_res != false) {
                    setcookie("adptoken", $token_res, time() + EXPIRE_TIME);
                    $array = [
                        "status" => true,
                    ];
                    die(json_encode($array));
                } else {
                    $array = [
                        "status" => false,
                    ];
                    die(json_encode($array));
                }
            } else {
                $array = [
                    "status" => false,
                ];
                die(json_encode($array));
            }
        } else {
            $array = [
                "status" => false,
            ];
            die(json_encode($array));
        }
    }else if(isset($_GET["ref"])){
        validate_ref();
    }
}
?>