<?php
ob_start(); // Mulai output buffering
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include('../koneksi.php'); // Pastikan koneksi database sudah di-include

// Proses untuk menghapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM mading WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?status=deleted");
    exit();
}

// Cek apakah ada aksi logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

include("template/header.php");

// Ambil data dari database
$query = "SELECT m.*, k.nama as kategori_nama 
          FROM mading m 
          LEFT JOIN kategori k ON m.kategori_id = k.id";
$result = $koneksi->query($query);
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Artikel /</span> Daftar Artikel</h4>

    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo "<div class='alert alert-success'>Data berhasil ditambahkan</div>";
        } elseif ($_GET['status'] == 'deleted') {
            echo "<div class='alert alert-info'>Data berhasil dihapus</div>";
        } elseif ($_GET['status'] == 'published') {
            echo "<div class='alert alert-success'>Artikel berhasil dipublikasikan</div>";
        }
    }
    ?>

    <!-- Responsive Table -->
    <div class="card">
        <h5 class="card-header">Daftar Artikel</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>No</th>
                        <th>Judul</th>
                        <th>Gambar</th>
                        <th>Caption</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; // Inisialisasi variabel nomor
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo isset($row['judul']) ? htmlspecialchars($row['judul']) : 'Tidak ada judul'; ?></td>
                        <td>
                            <?php if(isset($row['gambar']) && !empty($row['gambar'])): ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $row['id']; ?>">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar']); ?>" alt="Gambar" style="width: 100px; height: auto;">
                                </a>
                                <!-- Modal -->
                                <div class="modal fade" id="imageModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="imageModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="imageModalLabel<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['judul']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar']); ?>" alt="Gambar" style="width: 100%; height: auto;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                Tidak ada gambar
                            <?php endif; ?>
                        </td>
                        <td><?php 
                            if (isset($row['caption'])) {
                                $caption = strip_tags($row['caption']);
                                echo (strlen($caption) > 50) ? substr($caption, 0, 50) . '...' : $caption;
                            } else {
                                echo 'Tidak ada caption';
                            }
                        ?></td>
                        <td><?php echo isset($row['status']) ? htmlspecialchars($row['status']) : 'Tidak ada status'; ?></td>
                        <td><?php echo isset($row['tanggal']) ? $row['tanggal'] : 'Tidak ada tanggal'; ?></td>
                        <td><?php echo isset($row['kategori_nama']) ? htmlspecialchars($row['kategori_nama']) : 'Tanpa Kategori'; ?></td>
                        <td>
                            <a href="edit_artikel.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="index.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='7'>Tidak ada data artikel.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Responsive Table -->
</div>
<!-- / Content -->

<?php
include("template/footer.php");
ob_end_flush(); // Akhiri dan kirim output buffer
?>