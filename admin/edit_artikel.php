<?php
ob_start(); // Mulai output buffering
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include('../koneksi.php');

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: index.php");
    exit();
}

// Ambil data artikel
$query = "SELECT * FROM mading WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$artikel = $result->fetch_assoc();

if (!$artikel) {
    header("Location: index.php");
    exit();
}

// Tambahkan query untuk mengambil kategori
$query_kategori = "SELECT * FROM kategori ORDER BY nama ASC";
$result_kategori = $koneksi->query($query_kategori);

// Proses untuk mengedit data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $caption = $_POST['caption'];
    $status = $_POST['status'];
    $kategori_id = $_POST['kategori_id'];
    
    // Proses kategori baru
    if ($kategori_id == 'new' && !empty($_POST['kategori_baru'])) {
        $kategori_baru = $_POST['kategori_baru'];
        $stmt = $koneksi->prepare("INSERT INTO kategori (nama) VALUES (?)");
        $stmt->bind_param("s", $kategori_baru);
        $stmt->execute();
        $kategori_id = $koneksi->insert_id;
    }

    // Update query untuk menyertakan kategori
    $query = "UPDATE mading SET judul = ?, caption = ?, status = ?, kategori_id = ? WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssis", $judul, $caption, $status, $kategori_id, $id);
    
    // Jika ada gambar baru diupload
    if ($_FILES['gambar']['size'] > 0) {
        $gambar = $_FILES['gambar']['tmp_name'];
        $gambar_blob = file_get_contents($gambar);
        $query = "UPDATE mading SET judul = ?, gambar = ?, caption = ?, status = ?, kategori_id = ? WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssi", $judul, $gambar_blob, $caption, $status, $kategori_id, $id);
    }
    
    if ($stmt->execute()) {
        header("Location: index.php?status=updated");
        exit();
    } else {
        $error = "Gagal mengupdate data: " . $stmt->error;
    }
}

include("template/header.php");
?>

<!-- Include Quill stylesheet -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Artikel /</span> Edit Artikel</h4>

    <?php
    if (isset($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
    ?>

    <!-- Form Edit -->
    <div class="card mb-4">
        <h5 class="card-header">Edit Artikel</h5>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data" id="articleForm">
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($artikel['judul']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>
                <div class="mb-3">
                    <label for="caption" class="form-label">Caption</label>
                    <div id="editor" style="height: 300px;"></div>
                    <input type="hidden" name="caption" id="captionInput">
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="published" <?php echo $artikel['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="draft" <?php echo $artikel['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($kategori = $result_kategori->fetch_assoc()): ?>
                            <option value="<?php echo $kategori['id']; ?>"><?php echo htmlspecialchars($kategori['nama']); ?></option>
                        <?php endwhile; ?>
                        <option value="new">+ Tambah Kategori Baru</option>
                    </select>
                </div>
                <div class="mb-3" id="kategoriBaruDiv" style="display:none;">
                    <label for="kategoriBaru" class="form-label">Nama Kategori Baru</label>
                    <input type="text" class="form-control" id="kategoriBaru" name="kategori_baru">
                </div>
                <button type="submit" class="btn btn-primary">Update Artikel</button>
            </form>
        </div>
    </div>
</div>
<!-- / Content -->

<!-- Include Quill library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['link', 'image'],
        ['clean']
      ]
    }
  });

  // Set existing content
  quill.root.innerHTML = <?php echo json_encode($artikel['caption']); ?>;

  document.getElementById('articleForm').onsubmit = function() {
    document.getElementById('captionInput').value = quill.root.innerHTML;
  };
</script>

<script>
document.getElementById('kategori').addEventListener('change', function() {
    const kategoriBaru = document.getElementById('kategoriBaruDiv');
    if (this.value === 'new') {
        kategoriBaru.style.display = 'block';
    } else {
        kategoriBaru.style.display = 'none';
    }
});
</script>

<?php
include("template/footer.php");
ob_end_flush(); // Akhiri dan kirim output buffer
?>
