<?php

function minute($time)

{

    $onceBol = explode(" ", $time);

    $sds = explode(":", $onceBol[1]);

    return $sds[1];

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

function forgotusername()

{

    /*
    $recaptcha = \StaticDatabase\StaticDatabase::settings('recaptcha');

    if ($recaptcha == 1){

        $secret = \StaticDatabase\StaticDatabase::settings('secretkey');

        $ip = $_SERVER['REMOTE_ADDR'];

        $captcha = $_POST['g-recaptcha-response'];

        $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");

        $captchaRes = json_decode($rsp, true);

        date_default_timezone_set('Europe/Istanbul');

        if (!$captchaRes['success']) {

            Session::set('cError', 'recaptcha');

            URI::redirect('recuperare/account');

            exit;

        }

    }

    */
    $filter = new Filter();
    $hash = new Hash();
    global $account;
    global $lng;

    $email = $filter->mailFilter($_POST['email']);

    if($email == ''){

        $result['result'] = false;
        $result['message'] =$lng[76] ;

    }elseif ($email == false){

        $result['result'] = false;
        $result['message'] =$lng[76] ;

    }else{

        $controlsor = $account->prepare('SELECT login,email,t_date, id FROM account WHERE email=:email');
        $controlsor->execute(['email'=>$email]);
        $control = $controlsor->fetch(PDO::FETCH_ASSOC);

        if (($controlsor->rowCount()) <= 0){

            $result['result'] = false;
            $result['message'] =$lng[76] ;

        }else{



            $tDate = $control['t_date'];

            $now = date('Y-m-d H:i:s', strtotime('-10 minutes') );

            $residual = minute($tDate);

            if ($now <= $tDate){

                session_set('residual', $residual);

                $result['result'] = false;
                $now = date('i');
                $residual = session_get('residual');
                $kalan = $now - $residual;
                $kalans = 11 - $kalan;
                $result['message'] = "10 Dakika arayla mail gönderimi yapabilirsiniz.Lütfen bekleyiniz. Kalan süre : $kalans dakika";

            }else{

                $arg = generateRandomString(8);

                $token = $hash->create('md5',$arg.$email,settings('tokenkey'));

                $link = site_url('recuperare-account-response?str='.$arg.'&token='.$token);

                $text = "Sayın <b>Kullanıcı</b>, sistemimizden hesap adı isteminde bulundunuz. <br> Aşağıdaki linki tıkladığınızda Hesap Adınızı Görüntüleyeceksiniz .<br> Lütfen adımları takip ediniz. <br><b>NOT : Lütfen browser'ınızda sayfamız giriş yapılı şekilde bu linke tıklayınız. Ve aynı browser'dan aşağıdaki linke gidiniz.</b><br><br><br><a href='$link'><button style='background-color: #1AB2E8; width: 350px; padding: 0; height: 50px; border-radius: 7px;border: none;'><b>Hesap Adınızı Öğrenmek  için buraya tıklayınız. </b></button></a><br><br>Url olarak : $link<br><br><br> <b>".settings('settings_gamename')." Yönetimi</b><br><br><br>";

                sendmail(settings('settings_gamename')." Hesap Adınız",$email,$text);

                $date = date('Y-m-d H:i:s');

               $sorgu =  $account->prepare('UPDATE account SET 
                t_status=:t_status,
                t_key=:t_key,
                t_token=:t_token,
                t_date=:t_date,
                t_type=:t_type WHERE  id=' . $control['id']);
               $sorgu->execute(['t_status'=>'1',
                   't_key'=>$arg,
                   't_token'=>$token,
                   't_date'=>$date,
                   't_type'=>'16']);


                $result['result']= True;
                $result['message']= $lng[77];

            }

        }

    }
    return $result;

}

require view('recuperare-account');