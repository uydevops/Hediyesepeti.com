<?php

session_start();
include("vt.php");

global $baglanti;

// Kullanıcı girişi işlemi
function userLogin($mail, $sifre, $beni_hatirla) {
    global $baglanti;

    $sifre = md5($sifre);
    $stmt = $baglanti->prepare("SELECT * FROM users WHERE mail = ? AND sifre = ?");
    $stmt->bind_param("ss", $mail, $sifre);
    $stmt->execute();
    $result = $stmt->get_result();
    $say = $result->num_rows;

    $stmt_admin = $baglanti->prepare("SELECT * FROM users WHERE mail = ? AND sifre = ? AND yetki = 1");
    $stmt_admin->bind_param("ss", $mail, $sifre);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin_say = $result_admin->num_rows;

    if ($admin_say > 0) {
        $_SESSION["admin"] = true;
        $_SESSION["admin_mail"] = $mail;
        header("location: admin.php");
        exit();
    } elseif ($say > 0) {
        $_SESSION["oturum"] = true;
        $_SESSION["mail"] = $mail;
        if ($beni_hatirla) {
            setcookie('email', $mail, time() + 60 * 60 * 24 * 7);
            setcookie('sifre', $_POST["giris_sifre"], time() + 60 * 60 * 24 * 7);
        } else {
            if (isset($_COOKIE["email"])) {
                setcookie("email", "", time() - 3600);
            }
            if (isset($_COOKIE["sifre"])) {
                setcookie("sifre", "", time() - 3600);
            }
        }
        header("location: index.php");
        exit();
    } else {
        echo "<script type='text/javascript'>alert('E-posta veya şifre yanlış!');</script>";
    }
}

// Kullanıcı kayıt işlemi
function userRegister($isim, $mail, $sifre) {
    global $baglanti;

    $sifre = md5($sifre);
    $kayit_tarih = date('Y-m-d');
    $stmt = $baglanti->prepare("SELECT * FROM users WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();
    $say = $result->num_rows;

    if ($say > 0) {
        echo "<script type='text/javascript'>alert('Bu maile ait kullanıcı var!');</script>";
        header("Refresh:1; url=" . $_SERVER["HTTP_REFERER"]);
        exit();
    } else {
        $stmt_insert = $baglanti->prepare("INSERT INTO users (ad, mail, sifre, tarih) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $isim, $mail, $sifre, $kayit_tarih);
        $stmt_insert->execute();
        $_SESSION["oturum"] = true;
        $_SESSION["mail"] = $mail;
        header("location: index.php");
        exit();
    }
}

// Çıkış işlemi
function userLogout($isAdmin) {
    global $baglanti;

    if ($isAdmin) {
        unset($_SESSION['admin']);
        header("location: index.php");
        exit();
    } else {
        if (isset($_COOKIE["urun"])) {
            foreach ($_COOKIE["urun"] as $urun => $value) {
                setcookie('urun[' . $urun . ']', '', time() - 86400);
                $stmt_stok = $baglanti->prepare("UPDATE urunler SET stok = stok + 1 WHERE urun_id = ?");
                $stmt_stok->bind_param("i", $urun);
                $stmt_stok->execute();
            }
        }
        unset($_SESSION['oturum']);
        header("location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

if (isset($_POST['giris_yap_buton'])) {
    userLogin($_POST["giris_mail"], $_POST["giris_sifre"], isset($_POST["beni_hatirla"]));
}

if (isset($_POST['uye_ol_buton'])) {
    userRegister($_POST["uye_isim"], $_POST["kayit_mail"], $_POST["kayit_sifre"]);
}

if (isset($_GET["do"])) {
    if ($_GET["do"] == "admin_cikis") {
        userLogout(true);
    } elseif ($_GET["do"] == "cikis") {
        userLogout(false);
    }
}
?>
