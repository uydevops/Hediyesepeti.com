<!DOCTYPE html>
<html>
<head>
    <title>Kategori Listesi</title>
</head>
<body>

    <?php 
    include("vt.php");

    // Veritabanı bağlantısını global değişken olarak tanımlayalım
    global $baglan;

    // Kategori verilerini çekme fonksiyonu
    function getCategories($ust_kategori_id = 0) {
        global $baglan;
        $stmt = $baglan->prepare("SELECT * FROM kategoriler WHERE ust_kategori_id = ?");
        $stmt->bind_param("i", $ust_kategori_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows < 1) {
            return false;
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    // Kategori ağacını render eden fonksiyon
    function renderCategories($ust_kategori_id = 0) {
        $categories = getCategories($ust_kategori_id);

        if ($categories === false) {
            return;
        }

        echo '<ul>';
        foreach ($categories as $category) {
            echo '<li><a href="kategori.php?kid=' . htmlspecialchars($category["kategori_id"]) . '">' . htmlspecialchars($category["kategori_adi"]) . '</a>';
            renderCategories($category["kategori_id"]);
            echo '</li>';
        }
        echo '</ul>';
    }

    // Ana kategori ağacını render edelim
    renderCategories();
    ?>

</body>
</html>
