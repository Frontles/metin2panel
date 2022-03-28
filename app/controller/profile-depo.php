<?php


function mail_onay()

{

    global $account;
    $aid = session_get('user_id');

    $sorgu = $account->prepare('SELECT mailaktive,status,availDt FROM account WHERE id=:id');
    $sorgu->execute([
        'id'=>$aid
    ]);

    $sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
    return $sonuc;
}

function generateRandomString($length = 10)

{

    $characters = '0123456789';

    $charactersLength = strlen($characters);

    $randomString = '';

    for ($i = 0; $i < $length; $i++) {

        $randomString .= $characters[rand(0, $charactersLength - 1)];

    }

    return $randomString;

}

function minute($time)

{

    $onceBol = explode(" ", $time);

    $sds = explode(":", $onceBol[1]);

    return $sds[1];

}
function change_depo()

{

    /*
    $recaptcha = \StaticDatabase\StaticDatabase::settings('recaptcha');

    if ($recaptcha == 1) {

        $secret = \StaticDatabase\StaticDatabase::settings('secretkey');

        $ip = $_SERVER['REMOTE_ADDR'];

        $captcha = $_POST['g-recaptcha-response'];

        $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");

        $captchaRes = json_decode($rsp, true);

        if (!$captchaRes['success']) {

            Session::set('cError', 'recaptcha');

            URI::redirect('profile/depo');

            exit;

        }

    }
*/
    date_default_timezone_set('Asia/Bahrain');

    $filter = new Filter();
    $functions = new Functions();
    $hash = new Hash();
    $password = $filter->passwordFilter($_POST['password']);

    global $account;
    global $lng;


    if ($password == false) {

        $result['result'] = false;
        $result['message'] =$lng[126];

    } else {

        $aid = session_get('user_id');

        $cLogin = session_get('user_name');

        $dbpasssor = $account->prepare("SELECT id,email,password,t_status,t_date FROM account WHERE id =:id ");
        $dbpasssor->execute(['id'=>$aid]);
        $dbpass= $dbpasssor->fetch(PDO::FETCH_ASSOC);


        $getPassword = password_verify($password,$dbpass['password']);



        if ($getPassword <= 0) {

            $result['result'] = false;
            $result['message'] =$lng[131] ;


        } else {

            $getinfo = $dbpass;


            $tDate = $getinfo['t_date'];

            $now = date('Y-m-d H:i:s', strtotime('-10 minutes'));

            $residual = minute($tDate);

            if ($now <= $tDate) {

                session_set('residual', $residual);

                $result['result'] = false;
                 $now = date('i');
                $residual = session_get('residual');
                $kalan = $now - $residual;
                $kalans = 11 - $kalan;
                $result['message'] = "10 Dakika arayla mail gönderimi yapabilirsiniz.Lütfen bekleyiniz. Kalan süre : $kalans dakika";

            } else {

                $getMail = $getinfo['email'];

                $arg = generateRandomString(6);

                $token = $hash->create('md5', $arg . $aid . $cLogin, settings('tokenkey'));

                $link = site_url('profile-depo-response?str=' . $arg . '&token=' . $token);

                $text = "Sayın <b>$cLogin</b>, sistemimizden depo şifresi isteminde bulundunuz. <br> Aşağıdaki linki tıkladığınızda yeni depo şifreniz belirlenecektir.<br> Lütfen adımları takip ediniz. <br><b>NOT : Lütfen browser'ınızda sayfamız giriş yapılı şekilde bu linke tıklayınız. Ve aynı browser'dan aşağıdaki linke gidiniz.</b><br><br><br><a href='$link'><button style='background-color: #1AB2E8; width: 350px; padding: 0; height: 50px; border-radius: 7px;border: none;'><b>Depo şifrenizi değiştirmek için buraya tıklayınız. </b></button></a><br><br>Url olarak : $link<br><br><br> <b>" . settings('settings_gamename') . " Yönetimi</b><br><br><br>";

                sendmail(settings('settings_gamename') . " Depo Şifresi Değişikliği", $getMail, $text);

                $date = date('Y-m-d H:i:s');

                $sorgu1 = $account->prepare('UPDATE account SET 
                    t_status=:t_status ,
                    t_key=:t_key,
                    t_token=:t_token,
                    t_date=:t_date,
                    t_type=:t_type
                    WHERE id='. $aid);
                $sorgu1->execute([
                    't_status' => '1',
                    't_key' => $arg,
                    't_token' => $token,
                    't_date' => $date,
                    't_type' => '1'
                ]);

                setplayerlog( '6', "depo şifresi değişikliği için $getMail adresine mail gönderildi");

                $result['result']= True;
                $result['message']= 'Depo Şifresi Değişim isteğiniz Epostanıza iletildi..';

            }

        }

    }

    return $result;
}


require view('profile-depo');