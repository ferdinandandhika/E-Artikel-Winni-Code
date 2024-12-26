<?php
ob_start(); // Mulai output buffering
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include('../koneksi.php');

// Proses untuk menghapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM mading WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: draft_artikel.php?status=deleted");
    exit();
}

// Tambahkan ini di bagian atas file, setelah include koneksi
if (isset($_GET['action']) && $_GET['action'] == 'publish' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "UPDATE mading SET status = 'published' WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: index.php?status=published");
        exit();
    } else {
        $error = "Gagal mempublikasikan artikel: " . $stmt->error;
    }
}

include("template/header.php");

// Ambil data draft dari database
$query = "SELECT * FROM mading WHERE status = 'draft' ORDER BY tanggal DESC";
$result = $koneksi->query($query);
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Artikel /</span> Daftar Draft Artikel</h4>

    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'deleted') {
            echo "<div class='alert alert-info'>Data berhasil dihapus</div>";
        }
    }
    ?>

    <!-- Responsive Table -->
    <div class="card">
        <h5 class="card-header">Daftar Draft Artikel</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>No</th>
                        <th>Judul</th>
                        <th>Gambar</th>
                        <th>Caption</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                        <td>
                            <?php if(!empty($row['gambar'])): ?>
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
                            $caption = strip_tags($row['caption']);
                            echo (strlen($caption) > 50) ? substr($caption, 0, 50) . '...' : $caption;
                        ?></td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td>
                            <a href="edit_artikel.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="draft_artikel.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus draft ini?')">Hapus</a>
                            <a href="draft_artikel.php?action=publish&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin mempublikasikan artikel ini?')">Publish</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='6'>Tidak ada draft artikel.</td></tr>";
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
ob_end_flush();
?>
