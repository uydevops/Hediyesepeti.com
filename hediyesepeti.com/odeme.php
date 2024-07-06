<?php
ob_start();
session_start();
include("sepet_actions.php");
include("vt.php");

if (!isset($_COOKIE["urun"])) {
    header('location:index.php');
    exit();
}

function getCities($baglanti) {
    $stmt = $baglanti->prepare("SELECT * FROM iller");
    $stmt->execute();
    return $stmt->get_result();
}

function displayOrderForm($baglanti) {
    $cities = getCities($baglanti);
    ?>
    <div class="teslimat_bilgileri">
        <div class="teslimat_baslik"><b>Teslimat Adresi (Alıcı)</b></div>
        <form action="" method="post">
            <input type="text" name="alici_ad_soyad" placeholder="Alıcının Adı Soyadı" class="form_elamanlari" required>
            <input type="tel" name="alici_telefon" placeholder="Telefon" class="form_elamanlari" required>
            <select class="form_elamanlari" style="width: 100%;" name="il" required>
                <option>İl seçiniz</option>
                <?php while ($iller = $cities->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($iller["il"]); ?>"><?php echo htmlspecialchars($iller["il"]); ?></option>
                <?php } ?>
            </select>
            <textarea placeholder="Teslimat adresiniz" name="teslimat_adresi" class="form_elamanlari" style="width: 100%; height:70px;" required></textarea>
            <div class="clear"></div>
            <div class="teslimat_baslik"><b>Sipariş Notunuz</b></div>
            <textarea placeholder="Varsa sipariş hakkında yazmak istedikleriniz..." name="siparis_notu" class="form_elamanlari" style="width: 100%; height:70px;"></textarea>
            <input type="checkbox" name="satin_alma_sozlesmesi" required>Sözleşmeyi okudum onaylıyorum.
            <input type="submit" name="siparisi_tamamla" value="Siparişi Tamamla">
        </form>
        <div class="clear"></div>
    </div>
    <?php
}

function displayLoginForm() {
    ?>
    <div class="teslimat_bilgileri">
        <div class="teslimat_baslik"><b>Üye Girişi</b></div>
        <form action="" method="post">
            <input type="email" name="odeme_e_posta" placeholder="E-mail Adresini Giriniz" class="form_elamanlari" required>
            <input type="password" name="odeme_sifre" placeholder="Şifrenizi Giriniz" class="form_elamanlari" required>
            <input type="submit" name="giris_yap" value="Giriş Yap">
        </form>
        <div class="clear"></div>
    </div>
    <?php
}

function displayRegisterForm() {
    ?>
    <div class="teslimat_bilgileri">
        <div class="teslimat_baslik"><b>Üyelik Oluştur - [</b> <a href="?satin_al&uyeyim">zaten üyeyim</a><b>]</b></div>
        <form action="" method="post">
            <input type="text" name="ad_soyad" placeholder="Ad Soyad" class="form_elamanlari" required>
            <input type="tel" name="telefon" placeholder="Telefon" class="form_elamanlari" required>
            <input type="email" name="e_posta" placeholder="E-mail Adresini Giriniz" class="form_elamanlari" required>
            <input type="password" name="sifre" placeholder="Şifrenizi Giriniz" class="form_elamanlari" required>
            <input type="checkbox" name="uye_sozlesmesi" required>Üyelik sözleşmesini okudum onaylıyorum.
            <input type="submit" name="uye_ol" value="Üye Ol">
        </form>
        <div class="clear"></div>
    </div>
    <?php
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepet</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/theme.css">
</head>
<body>
    <div class="sepet_header">
        <div class="sepet_header_ic">
            <a href="index.php"><div class="sepet_logo"></div></a>
        </div>
    </div>
    <div class="sepet_content">
        <?php 
        if (isset($_GET['satin_al'])) {
            if (isset($_SESSION['oturum'])) {
                displayOrderForm($baglanti);
            } else {
                if (isset($_GET["uyeyim"])) {
                    displayLoginForm();
                } else {
                    displayRegisterForm();
                    displayOrderForm($baglanti);
                }
            }
        }
        ?>
    </div>
    <div class="sepet_footer">
        <div class="copyright">
            <span>Copyright© <a href="#">hediyesepeti.com</a></span>
        </div>
    </div>
</body>
</html>
