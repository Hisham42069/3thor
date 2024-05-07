<?php
session_start();
require 'contc.php'; // قم بتغيير 'contc.php' إلى اسم ملف اتصال قاعدة البيانات الخاص بك



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send'])) {
    // جلب بيانات المستخدم من الجلسة
    $userID = $_SESSION['UserID']; // تأكد من تغيير هذا إذا كان اسم المتغير المستخدم لديك مختلفًا

    // جلب بيانات المنتج من النموذج
    $productName = $_POST['productName']; // كانت Title في السابق
    $productDescription = $_POST['productDescription']; // كانت Description في السابق
    $productPrice = $_POST['productPrice']; // كانت Price في السابق
    $productLocation = $_POST['productLocation']; // كانت Category في السابق
    $productType = $_POST['productType']; // كانت Location في السابق
    
    // تحضير الاستعلام لإدراج المنتج في قاعدة البيانات
    $sql = "INSERT INTO product (Title, Description, Price, Location, Category, UserID) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$productName, $productDescription, $productPrice, $productLocation, $productType, $userID]);
    
    // جلب معرف المنتج الجديد الذي تم إضافته
    $productID = $conn->lastInsertId();

    // تحديد المسار الذي ستُرفع إليه الصور
    $targetDirectory = "uploads/";

    // التحقق من وجود المجلد "uploads"، وإن لم يكن موجودًا يتم إنشاؤه
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    // تحميل كل صورة من النموذج وحفظها في المجلد "uploads"
    foreach ($_FILES['productImages']['tmp_name'] as $key => $tmp_name) {
        $imageName = $_FILES['productImages']['name'][$key];
        $imageTmpName = $_FILES['productImages']['tmp_name'][$key];
        $imagePath = $targetDirectory . $imageName;

        // التحقق من نجاح عملية تحميل الصورة قبل إدراج اسم الصورة في قاعدة البيانات
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // تحضير الاستعلام لإدراج اسم الصورة ومعرف المنتج المرتبط في جدول الصور
            $sql = "INSERT INTO image (ImageDescription, ProductID) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$imageName, $productID]);
        } else {
            // في حالة فشل نقل الصورة
            echo "حدث خطأ أثناء تحميل الصورة.";
        }
    }

    // إعادة التوجيه بعد إضافة المنتج بنجاح
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();
}

// إضافة الاستعلام هنا لجلب المنتجات وترتيبها
$sql = "SELECT p.ProductID, p.UserID, p.Title, p.Description, p.Price, p.DatePosted, p.Location, p.Category, MIN(i.ImageDescription) as ImageDescription
        FROM product p
        LEFT JOIN image i ON p.ProductID = i.ProductID
        GROUP BY p.ProductID
        ORDER BY p.DatePosted DESC";

$stmtt = $conn->prepare($sql);
$stmtt->execute();
$products = $stmtt->fetchAll(PDO::FETCH_ASSOC);

?>



<?php
//session_start();
// التحقق من وجود متغير الجلسة الذي يحمل معرف المستخدم
//if (!isset($_SESSION['UserID'])) {
    // إذا لم يكن المستخدم قد سجل الدخول، قم بتوجيهه إلى صفحة تسجيل الدخول
//    echo"ablshhhhhhh";
//}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>عثور</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=https://fonts.googleapis.com/css?family=Inconsolata:400,500,600,700|Raleway:400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: MyPortfolio
  * Template URL: https://bootstrapmade.com/myportfolio-bootstrap-portfolio-website-template/
  * Updated: Mar 17 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
 <!-- Custom JavaScript -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

 <script>
        $(document).ready(function(){
            // عرض الـ alert بعد 3 ثواني
            $(".alert").fadeTo(2000, 500).slideUp(500, function(){
                $(".alert").slideUp(500);
            });
        });
    </script>
<script>
    // استخدم JavaScript للتمرير إلى الأعلى
    window.onload = function() {
        window.scrollTo(0, 0);
    }
</script>

<style>
        /* Add your CSS styles here */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .popup-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .product-image img {
    width: 200px; /* تحديد عرض ثابت للصور */
    height: 200px; /* تحديد ارتفاع ثابت للصور */
    object-fit: cover; /* جعل الصور تغطي العنصر بالكامل دون تشويهها */
    /* يمكنك إضافة أي خصائص إضافية تريدها هنا */
}


    </style>


</head>

<body>







  <!-- ======= اليرت تسجيل الدخول ======= -->
<?php
            if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
              echo '
              <div class="container">
                      <div class="row justify-content-center mt-5">
                          <div class="col-md-6">
                              <div class="alert alert-success" role="alert">
                                  تم إنشاء الحساب بنجاح!
                              </div>
                          </div>
                      </div>
                  </div>>';
            }
?>
  <!-- ======= Navbar ======= -->
  <div class="collapse navbar-collapse custom-navmenu" id="main-navbar">
    <div class="container py-2 py-md-5">
      <div class="row align-items-start">
        <div class="col-md-2">
          <ul class="custom-menu">
            <li class="active"><a href="index.html">Home</a></li>
            <li><a href="about.html">About Me</a></li>
            <li><a href="services.html">Services</a></li>
            <li><a href="works.html">Works</a></li>
            <li><a href="contact.html">Contact</a></li>
          </ul>
        </div>
        <div class="col-md-6 d-none d-md-block  mr-auto">
          <div class=" d-flex">
            
            <div>
            </div>
          </div>
        </div>
        <div class="col-md-4 d-none d-md-block">
          <h3>Hire Me</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut officiisexplicabo inventore. <br> <a href="#">myemail@gmail.com</a></p>
        </div>
      </div>

    </div>
  </div>

  <nav class="navbar navbar-light custom-navbar">
    <div class="container">
      <a class="navbar-brand" href="#">عثور</a>
      <a href="#" class="burger" data-bs-toggle="collapse" data-bs-target="#main-navbar">
        <span></span>
      </a>
    </div>
  </nav>

  <main id="main">

    <!-- ======= Works Section ======= -->
    <section class="section site-portfolio">
      <div class="container">
        <div class="row mb-5 align-items-center">
          <div class="col-md-12 col-lg-6 mb-4 mb-lg-0" data-aos="fade-up">

          
          
      <div class="row justify-content-start"> 
        <div class="row justify-content-between">
          <div class="col-4">


          <!--     -->



          <a href="#" onclick="openPopup()" class="readmore">لنشر</a>
         
<div id="productPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>تعبئة بيانات المنتج</h2>
        <form  method="post" enctype="multipart/form-data">
            <label for="productName">اسم المنتج:</label>
            <input type="text" id="productName" name="productName" required><br><br>

            <label for="productDescription">وصف المنتج:</label>
            <textarea id="productDescription" name="productDescription" required></textarea><br><br>

            <label for="productPrice">سعر المنتج:</label>
            <input type="text" id="productPrice" name="productPrice" required><br><br>

            <label for="productType">نوع المنتج:</label>
            <select id="productType" name="productType" required>
                <option value="">اختر نوع المنتج</option>
                <option value="إلكترونيات">إلكترونيات</option>
                <option value="ملابس">ملابس</option>
                <option value="أثاث">أثاث</option>
                <!-- Add more options as needed -->
            </select><br><br>

            <label for="productLocation">موقع المنتج:</label>
            <input type="text" id="productLocation" name="productLocation" required><br><br>

            <label for="productImages">صور المنتج:</label> 
           <input type="file" id="productImages" name="productImages[]" multiple required><br><br>
          
    
            <input type="submit" name="send" value="نشر">
        </form>
    </div>
</div>


<script>
    // JavaScript function to open the popup
    function openPopup() {
        document.getElementById("productPopup").style.display = "block";
    }

    // JavaScript function to close the popup
    function closePopup() {
        document.getElementById("productPopup").style.display = "none";
    }
</script>





          <!--     -->

      </div>
      



      <div class="col-4">
    <?php
   // session_start(); // بدء الجلسة

    function printWelcomeMessage() {
        if (isset($_SESSION['user'])) {
            $username = $_SESSION['user'];
            echo "<p> $username</p>";
            echo '<a href="about.php" class="readmore mb-4">عرض معلومات الحساب</a>';
            echo '<a href="end_session.php" class="readmore border-danger bg-danger-subtle border-2 mb-4">تسجيل خروج</a>';
        } else {
            echo '<a href="login1.php" class="readmore mb-4">سجل دخول</a>';
        }
    }

    printWelcomeMessage();
    ?>
</div>

    </div>
  </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="بحث" aria-label="Recipient's username" aria-describedby="basic-addon2">
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button">Button</button>
            </div>
          </div>

           
          </div>
          <div class="col-md-12 col-lg-6 text-start text-lg-end" data-aos="fade-up" data-aos-delay="100">
          <div class="filters">
    <a href="index1.php" class="<?= !isset($_GET['category']) || $_GET['category'] == '' ? 'active' : '' ?>">الكل</a>
    <a href="index1.php?category=إلكترونيات" class="<?= isset($_GET['category']) && $_GET['category'] == 'إلكترونيات' ? 'active' : '' ?>">إلكترونيات</a>
    <a href="index1.php?category=ملابس" class="<?= isset($_GET['category']) && $_GET['category'] == 'ملابس' ? 'active' : '' ?>">ملابس</a>
    <a href="index1.php?category=أثاث" class="<?= isset($_GET['category']) && $_GET['category'] == 'أثاث' ? 'active' : '' ?>">أثاث</a>
</div>
          </div>
        </div>

        
  
        <?php
// استعلام لجلب بيانات المنتجات مع الصور المرتبطة بها
$sql = "SELECT p.ProductID, p.UserID, p.Title, p.Description, p.Price, p.DatePosted, p.Location, p.Category, GROUP_CONCAT(i.ImageDescription) AS Images
        FROM product p
        LEFT JOIN image i ON p.ProductID = i.ProductID";

// إذا تم تحديد تصنيف، قم بإضافة شرط WHERE لتحديد الفئة
if(isset($_GET['category']) && $_GET['category'] != '') {
    $category = $_GET['category'];
    $sql .= " WHERE p.Category = :category";
}

$sql .= " GROUP BY p.ProductID
        ORDER BY p.DatePosted DESC"; // ترتيب النتائج حسب تاريخ النشر بتنازلي

// تنفيذ الاستعلام
$stmtt = $conn->prepare($sql);

// إذا تم تحديد تصنيف، قم بربط قيمة المتغير "category" مع البارامتر المستخدم في الاستعلام
if(isset($_GET['category']) && $_GET['category'] != '') {
    $stmtt->bindParam(':category', $category);
}

$stmtt->execute();

// جلب البيانات كمصفوفة
$products = $stmtt->fetchAll(PDO::FETCH_ASSOC);

// عرض البيانات
foreach ($products as $product) {
  // حساب الوقت المنقضي منذ العرض بالثواني
  $displayTime = strtotime($product['DatePosted']);
  $currentTime = time(); // وقت الآن بالثواني
  $timeDiff = $currentTime - $displayTime;

  // حساب الزمن المناسب بالدقائق أو الساعات أو الأيام
  if ($timeDiff < 60) {
      $timeAgo = "الآن";
  } elseif ($timeDiff < 3600) {
      $timeAgo = "قبل " . floor($timeDiff / 60) . " دقيقة";
  } elseif ($timeDiff < 86400) {
      $timeAgo = "قبل " . floor($timeDiff / 3600) . " ساعة";
  } else {
      $timeAgo = "قبل " . floor($timeDiff / 86400) . " يوم";
  }

  // تقسيم الصور إلى مصفوفة
  $images = explode(",", $product['Images']);

  echo "
  <div id='portfolio-grid' class='row no-gutter' data-aos='fade-up' data-aos-delay='200'>
    <div class='item web col-sm-6 col-md-4 col-lg-4 mb-4'>
    <a href='work-single.php?id=" . $product['ProductID'] . "' class='item-wrap fancybox'>
        <div class='work-info'>
        <h3>" . $user['UserName'] . "</h3>
          <span>" . $product['Category'] . "</span>
        </div>";

        // عرض الصورة الأولى فقط إذا كانت هناك أكثر من صورة
        if (count($images) > 1) {
          echo "<div class='product-image'>
                  <img class='img-fluid' src='uploads/" . $images[0] . "'  alt='Product Image'>
                </div>";
        } else {
          echo "<div class='product-image'>
                  <img class='img-fluid' src='uploads/" . $images[0] . "'  alt='Product Image'>
                </div>";
        }

      echo "</a>
      <div class='p-1 text-white bg-dark-subtle container text-center'>
        <div class='row justify-content-around'>
          <div class='col-4'>
            " . $timeAgo . " <!-- عرض الوقت المنقضي بصيغة مختصرة -->
          </div>
          <div class='col-4'>
             " . $product['Title'] . "     
          </div>            
        </div>
        <div class='row justify-content-around'>
          <div class='col-4'>
            " . $product['Location'] . "
          </div>
          <div class='col-4'>
            " . $product['Price'] . "
          </div> 
        </div>         
      </div>
    </div>
  ";
}



?>

   
   
    
    


       

              <!-- التاريخ و السعر نهايته-->
          </div>
        </div>
      </div>
    </section><!-- End  Works Section -->

    <!-- ======= Clients Section ======= -->
    <section class="section">
      <div class="container">
        <div class="row justify-content-center text-center mb-4">
          <div class="col-5">
            <h3 class="h3 heading">My Clients</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-4 col-sm-4 col-md-2">
            <a href="#" class="client-logo"><img src="assets/img/logo-adobe.png" alt="Image" class="img-fluid"></a>
          </div>
          <div class="col-4 col-sm-4 col-md-2">
            <a href="#" class="client-logo"><img src="assets/img/logo-uber.png" alt="Image" class="img-fluid"></a>
          </div>
          <div class="col-4 col-sm-4 col-md-2">
            <a href="#" class="client-logo"><img src="assets/img/logo-apple.png" alt="Image" class="img-fluid"></a>
          </div>
          <div class="col-4 col-sm-4 col-md-2">
            <a href="#" class="client-logo"><img src="assets/img/logo-netflix.png" alt="Image" class="img-fluid"></a>
          </div>
          <div class="col-4 col-sm-4 col-md-2">
            <a href="#" class="client-logo"><img src="assets/img/logo-nike.png" alt="Image" class="img-fluid"></a>
          </div>
          <div class="col-4 col-sm-4 col-md-2">
            <a href="#" class="client-logo"><img src="assets/img/logo-google.png" alt="Image" class="img-fluid"></a>
          </div>

        </div>
      </div>
    </section><!-- End Clients Section -->

    <!-- ======= Services Section ======= -->
    <section class="section services">
      <div class="container">
        <div class="row justify-content-center text-center mb-4">
          <div class="col-5">
            <h3 class="h3 heading">My Services</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
          </div>
        </div>
        <div class="row">

          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <i class="bi bi-card-checklist"></i>
            <h4 class="h4 mb-2">Web Design</h4>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
            <ul class="list-unstyled list-line">
              <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
              <li>Non pariatur nisi</li>
              <li>Magnam soluta quod</li>
              <li>Lorem ipsum dolor</li>
              <li>Cumque quae aliquam</li>
            </ul>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <i class="bi bi-binoculars"></i>
            <h4 class="h4 mb-2">Mobile Applications</h4>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
            <ul class="list-unstyled list-line">
              <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
              <li>Non pariatur nisi</li>
              <li>Magnam soluta quod</li>
              <li>Lorem ipsum dolor</li>
              <li>Cumque quae aliquam</li>
            </ul>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <i class="bi bi-brightness-high"></i>
            <h4 class="h4 mb-2">Graphic Design</h4>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
            <ul class="list-unstyled list-line">
              <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
              <li>Non pariatur nisi</li>
              <li>Magnam soluta quod</li>
              <li>Lorem ipsum dolor</li>
              <li>Cumque quae aliquam</li>
            </ul>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <i class="bi bi-calendar4-week"></i>
            <h4 class="h4 mb-2">SEO</h4>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
            <ul class="list-unstyled list-line">
              <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
              <li>Non pariatur nisi</li>
              <li>Magnam soluta quod</li>
              <li>Lorem ipsum dolor</li>
              <li>Cumque quae aliquam</li>
            </ul>
          </div>
        </div>
      </div>
    </section><!-- End Services Section -->

    <!-- ======= Testimonials Section ======= -->
    <section class="section pt-0">
      <div class="container">

        <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial">
                  <img src="assets/img/person_1.jpg" alt="Image" class="img-fluid">
                  <blockquote>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut officiis
                      explicabo inventore.</p>
                  </blockquote>
                  <p>&mdash; Jean Hicks</p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial">
                  <img src="assets/img/person_2.jpg" alt="Image" class="img-fluid">
                  <blockquote>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut officiis
                      explicabo inventore.</p>
                  </blockquote>
                  <p>&mdash; Chris Stanworth</p>
                </div>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>
    </section><!-- End Testimonials Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer class="footer" role="contentinfo">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <p class="mb-1">&copy; Copyright MyPortfolio. All Rights Reserved</p>
          <div class="credits">
            <!--
            All the links in the footer should remain intact.
            You can delete the links only if you purchased the pro version.
            Licensing information: https://bootstrapmade.com/license/
            Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=MyPortfolio
          -->
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
          </div>
        </div>
        <div class="col-sm-6 social text-md-end">
          <a href="#"><span class="bi bi-twitter"></span></a>
          <a href="#"><span class="bi bi-facebook"></span></a>
          <a href="#"><span class="bi bi-instagram"></span></a>
          <a href="#"><span class="bi bi-linkedin"></span></a>
        </div>
      </div>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>