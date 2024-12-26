<?php
include('koneksi.php');
include('template/header.php');

// Inisialisasi variabel pencarian dan pengurutan
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Query untuk mengambil semua kategori
$query_kategori = "SELECT k.*, COUNT(m.id) as jumlah_artikel 
                  FROM kategori k 
                  LEFT JOIN mading m ON k.id = m.kategori_id AND m.status = 'published'
                  GROUP BY k.id 
                  ORDER BY k.nama ASC";
$result_kategori = $koneksi->query($query_kategori);
?>

<style>
    .post-entry-alt {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .post-entry-alt:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .post-entry-alt .img-link {
        display: block;
        overflow: hidden;
    }
    .post-entry-alt .img-link img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .post-entry-alt:hover .img-link img {
        transform: scale(1.05);
    }
    .post-entry-alt .excerpt {
        padding: 15px;
        font-family: inherit; 
    }
    .post-entry-alt h2 {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    .post-entry-alt .post-meta {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 10px;
    }
    .post-entry-alt p {
        font-size: 0.95rem;
        color: #333;
        font-family: inherit; 
    }
    .read-more {
        display: inline-block;
        margin-top: 10px;
        color: #284454;
        text-decoration: none;
        font-weight: bold;
    }
    .read-more:hover {
        text-decoration: underline;
    }
    .search-form {
        margin-bottom: 20px;
    }
    .sort-buttons {
        margin-bottom: 20px;
    }
    .sort-buttons .btn {
        margin-right: 10px;
    }
    
    /* Style untuk search bar */
    .search-form .form-control {
        border: 2px solid #284454;
        border-radius: 5px 0 0 5px;
        box-shadow: 0 0 5px rgba(40, 68, 84, 0.3);
        color: #333;
        height: 46px;
    }
    
    .search-form .btn {
        border: 2px solid #284454;
        border-left: none;
        border-radius: 0 5px 5px 0;
        background-color: #284454;
        color: #fff;
        box-shadow: 0 0 5px rgba(40, 68, 84, 0.3);
        height: 46px;
        padding: 0 20px;
        font-size: 16px;
    }
    
    .search-form .btn:hover {
        background-color: #1c2f3a;
    }
    
    .search-form .input-group {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    ::placeholder {
        color: #666;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Style untuk kategori filter */
    .kategori-filter {
        overflow-x: auto;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .kategori-filter::-webkit-scrollbar {
        height: 5px;
    }

    .kategori-filter::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }

    .kategori-filter::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    .kategori-filter .btn {
        white-space: nowrap;
        border-radius: 20px;
        padding: 8px 15px;
        transition: all 0.3s ease;
    }

    .kategori-filter .btn:hover {
        transform: translateY(-2px);
    }

    .kategori-filter .badge {
        font-size: 0.8em;
        padding: 4px 8px;
        border-radius: 10px;
    }

    /* Style untuk placeholder search bar */
    .search-form .form-control::placeholder {
        color: #284454; /* Warna biru tua */
        opacity: 0.7; /* Tingkat transparansi */
    }
    
    /* Untuk browser Firefox */
    .search-form .form-control::-moz-placeholder {
        color: #284454;
        opacity: 0.7;
    }
    
    /* Untuk browser Edge */
    .search-form .form-control:-ms-input-placeholder {
        color: #284454;
        opacity: 0.7;
    }
    
    /* Untuk browser Internet Explorer */
    .search-form .form-control::-ms-input-placeholder {
        color: #284454;
        opacity: 0.7;
    }
</style>

<?php
// Modifikasi query utama untuk artikel
$query = "SELECT m.*, k.nama as kategori_nama 
          FROM mading m 
          LEFT JOIN kategori k ON m.kategori_id = k.id 
          WHERE m.status = 'published'";

// Tambahkan filter kategori jika ada
if (!empty($kategori_filter)) {
    $query .= " AND m.kategori_id = " . (int)$kategori_filter;
}

// Tambahkan pencarian jika ada
if (!empty($search)) {
    $query .= " AND (m.judul LIKE '%$search%' OR m.caption LIKE '%$search%')";
}

// Tambahkan pengurutan
$query .= " ORDER BY m.tanggal DESC LIMIT 6";

$result = $koneksi->query($query);
?>

<section class="section">
	<div class="container">

		<div class="row mb-4">
			<div class="col-sm-6">
				<h2 class="posts-entry-title">Artikel Winni Code</h2>
			</div>
		</div>

        <div class="row">
            <div class="col-12">
                <form class="search-form" action="" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Carilah artikel di sini..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="kategori-filter d-flex gap-2">
                    <a href="index.php" class="btn <?php echo empty($kategori_filter) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Semua Artikel
                    </a>
                    <?php while ($kategori = $result_kategori->fetch_assoc()): ?>
                        <a href="?kategori=<?php echo $kategori['id']; ?>" 
                           class="btn <?php echo ($kategori_filter == $kategori['id']) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <?php echo htmlspecialchars($kategori['nama']); ?> 
                            <span class="badge bg-secondary ms-1"><?php echo $kategori['jumlah_artikel']; ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="sort-buttons">
                    <a href="?sort=judul&order=<?php echo ($sort == 'judul' && $order == 'ASC') ? 'DESC' : 'ASC'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?><?php echo !empty($kategori_filter) ? '&kategori='.$kategori_filter : ''; ?>" class="btn btn-outline-primary">
                        Urutkan berdasarkan Judul
                        <?php if($sort == 'judul'): ?>
                            <i class="fas fa-sort-<?php echo $order == 'ASC' ? 'up' : 'down'; ?>"></i>
                        <?php endif; ?>
                    </a>
                    <a href="?sort=tanggal&order=<?php echo ($sort == 'tanggal' && $order == 'DESC') ? 'ASC' : 'DESC'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?><?php echo !empty($kategori_filter) ? '&kategori='.$kategori_filter : ''; ?>" class="btn btn-outline-primary">
                        Urutkan berdasarkan Tanggal
                        <?php if($sort == 'tanggal'): ?>
                            <i class="fas fa-sort-<?php echo $order == 'ASC' ? 'up' : 'down'; ?>"></i>
                        <?php endif; ?>
                    </a>
                    <a href="?sort=kategori&order=<?php echo ($sort == 'kategori' && $order == 'ASC') ? 'DESC' : 'ASC'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?><?php echo !empty($kategori_filter) ? '&kategori='.$kategori_filter : ''; ?>" class="btn btn-outline-primary">
                        Urutkan berdasarkan Kategori
                        <?php if($sort == 'kategori'): ?>
                            <i class="fas fa-sort-<?php echo $order == 'ASC' ? 'up' : 'down'; ?>"></i>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>

		<div class="row">
			<?php 
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()): 
			?>
			<div class="col-lg-4 mb-4">
				<div class="post-entry-alt">
					<a href="detail.php?id=<?php echo $row['id']; ?>" class="img-link">
						<img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>" class="img-fluid">
					</a>
					<div class="excerpt">
                        <h2><a href="detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['judul']); ?></a></h2>
                        <div class="post-meta align-items-center text-left clearfix">
                            <span class="d-inline-block mt-1">Kategori:</span>
                            <span class="badge bg-primary"><?php echo isset($row['kategori_nama']) ? htmlspecialchars($row['kategori_nama']) : 'Tanpa Kategori'; ?></span>
                            <span>&nbsp;-&nbsp; <?php echo date('d F Y', strtotime($row['tanggal'])); ?></span>
                        </div>
                        <div><?php echo substr(strip_tags($row['caption']), 0, 45) . '...'; ?></div>
                        <p><a href="detail.php?id=<?php echo $row['id']; ?>" class="read-more">Baca Selengkapnya</a></p>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            } else {
                echo "<div class='col-12'><p>Tidak ada artikel yang tersedia saat ini.</p></div>";
            }
            ?>
        </div>
    </div>
</section>

<?php
include('template/footer.php');
?>