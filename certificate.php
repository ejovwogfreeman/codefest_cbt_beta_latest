<?php

include('./config/db.php'); // Include database connection

$certificate = null; // Initialize certificate variable

if (isset($_GET['id'])) {
  $certificate_id = $_GET['id'];

  $query = "SELECT * FROM certificates WHERE certificate_id = ?";
  $stmt = mysqli_prepare($conn, $query);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $certificate_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
      $certificate = $row; // Store result in associative array
    }

    mysqli_stmt_close($stmt);
  }
}

mysqli_close($conn);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $certificate['student_name'] ?> Certificate Of Completion</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=SUSE:wght@100..800&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Concert+One&family=Dancing+Script:wght@400..700&family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Great+Vibes&display=swap");

    * {
      margin: 0px;
      padding: 0px;
      box-sizing: border-box;
      font-family: "SUSE";
    }

    body {
      font-family: "Georgia", serif;
      text-align: center;
      padding-top: 65px;
      background: #f0f0f0;
    }

    .border {
      position: absolute;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
      z-index: 0;
    }

    .certificate {
      width: 1000px;
      min-width: 1000px;
      margin: auto;
      padding: 50px;
      background: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      position: relative;
      border-radius: 10px;
    }

    .certificate h1 {
      font-size: 35px;
      text-transform: uppercase;
      color: #b88a44;
      margin-top: -5px;
      margin-bottom: 5px;
    }

    .certificate h2 {
      font-size: 24px;
      color: #333;
    }

    .certificate .name {
      font-size: 35px;
      color: #3e5390;
      margin-top: 10px;
      margin-bottom: 10px;
      font-family: "Great Vibes", cursive;
      font-weight: 900;
      letter-spacing: 5px;
    }

    .certificate p {
      font-size: 18px;
      color: #555;
      margin-top: 10px;
    }

    .logo {
      margin-top: 60px;
      margin-bottom: 20px;
    }

    .signatures {
      display: flex;
      justify-content: space-between;
      margin-top: -10px;
      padding: 0 70px;
      align-items: center;
    }

    .signatures div {
      text-align: center;
      position: relative;
    }

    .signatures p {
      margin-bottom: 20px;
    }

    .signatures .ribbon {
      width: 100px;
      margin: 20px;
      margin-bottom: 55px;
    }

    .signatures div:first-child img {
      width: 100px;
      position: absolute;
      top: -50px;
      right: 50px;
    }

    .signatures div:last-child img {
      width: 100px;
      position: absolute;
      top: -30px;
      right: 45px;
    }

    .sign-line {
      width: 200px;
      border-top: 3px solid #3e5390;
      margin: 10px auto;
    }

    .date {
      font-style: italic;
      color: #777;
    }

    .contents {
      position: relative;
    }

    .print-btn {
      padding: 10px 20px;
      font-size: 16px;
      background: #3e5390;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
      position: fixed;
      display: flex;
      align-items: center;
      top: 10px;
      right: 10px;
    }

    @media screen and (max-width: 900px) {
      .certificate {
        width: 100%;
      }
    }

    @media print {
      body * {
        visibility: hidden;
      }

      .certificate .certificate * {
        visibility: visible;
      }

      .certificate {
        position: absolute;
        top: 0;
        left: 0;
      }

      .certificate {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>
  <div class="print-section">
    <button id="print-btn" class="print-btn">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="1.5em"
        height="1.5em"
        viewBox="0 0 24 24">
        <path fill="currentColor" d="M5 20h14v-2H5zM19 9h-4V3H9v6H5l7 7z" />
      </svg>
      <span>DOWNLOAD</span>
    </button>
  </div>
  <div class="certificate">
    <img src="images/cert-bg.png" alt="" class="border" />
    <div class="contents">
      <img src="images/codefest_trans.png" alt="Institution Logo" class="logo" />
      <h1>CERTIFICATE OF COMPLETION</h1>
      <h2><?php echo strtoupper($certificate['program_name']) ?></h2>
      <?php if ($certificate['program_level'] !== 'null'): ?>
        <h3>(<?php echo strtoupper($certificate['program_level']) ?>)</h3>
      <?php endif; ?>
      <p>This certificate is proudly awarded to</p>
      <h2 class="name"><?php echo $certificate['student_name'] ?></h2>
      <p>
        In fulfilment of the training hours, tests, lab projects and final<br />
        presentation to the body of academic excos for the course
      </p>
      <p class="date"><?php echo date('F Y', strtotime($certificate['created_at'])); ?></p>
      <div class="signatures">
        <div>
          <img src="images/presidentsign.png" alt="" />
          <div class="sign-line"></div>
          <p>President</p>
        </div>
        <img src="images/ribbon.png" alt="" class="ribbon" />
        <div>
          <img src="images/provostsign.png" alt="" />
          <div class="sign-line"></div>
          <p>Provost</p>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
  document.getElementById("print-btn").addEventListener("click", function() {
    const {
      jsPDF
    } = window.jspdf;

    // Target the certificate div
    const certificateDiv = document.querySelector(".certificate");

    html2canvas(certificateDiv, {
      scale: 2
    }).then((canvas) => {
      const imgData = canvas.toDataURL("image/png");
      const pdf = new jsPDF("l", "mm", [297, 210]); // A4 Landscape

      pdf.addImage(
        imgData,
        "PNG",
        0,
        0,
        297, // Width in mm
        210 // Height in mm
      );
      pdf.save("<?php echo $certificate['student_name'] ?> Certificate Of Completion.pdf");
    });
  });
</script>