<?php
include('koneksi.php');
include('template/header.php');

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: index.php");
    exit();
}

$query = "SELECT * FROM mading WHERE id = ? AND status = 'published'";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$artikel = $result->fetch_assoc();

if (!$artikel) {
    header("Location: index.php");
    exit();
}
?>

<div class="site-cover site-cover-sm same-height overlay single-page" 
     style="background-image: url('data:image/jpeg;base64,<?php echo base64_encode($artikel['gambar']); ?>');"
     onclick="openImageModal()">
  <div class="container">
    <div class="row same-height justify-content-center">
      <div class="col-md-6">
        <div class="post-entry text-center">
          <h1 class="mb-4"><?php echo htmlspecialchars($artikel['judul']); ?></h1>
          <div class="post-meta align-items-center text-center">
            <span class="d-inline-block mt-1">Tanggal Publikasi</span>
            <span>&nbsp;-&nbsp; <?php echo date('d F Y', strtotime($artikel['tanggal'])); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="row blog-entries element-animate">
      <div class="col-md-12 col-lg-12 main-content">
        <div class="post-content-body">
          <div class="message-box">
            <?php echo $artikel['caption']; ?>
          </div>
        </div>
      </div>
      <!-- END main-content -->
    </div>
  </div>
</section>

<!-- Modal untuk menampilkan gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel"><?php echo htmlspecialchars($artikel['judul']); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img src="data:image/jpeg;base64,<?php echo base64_encode($artikel['gambar']); ?>" alt="<?php echo htmlspecialchars($artikel['judul']); ?>" style="width: 100%; height: auto;">
      </div>
    </div>
  </div>
</div>

<style>
  .site-cover {
    cursor: pointer;
  }
  .message-box {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #000000;
    font-family: inherit; /* Menggunakan font default */
  }
  .message-box p {
    margin-bottom: 10px;
    color: #000000;
    font-family: inherit; /* Menggunakan font default */
  }
</style>

<script>
function openImageModal() {
  var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
  myModal.show();
}
</script>

<?php
include('template/footer.php');
?>