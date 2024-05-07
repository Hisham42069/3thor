
<?php
session_start();
require 'contc.php'; // توصيل قاعدة البيانات

// التحقق من وجود معرف المستخدم في الجلسة
if(isset($_SESSION['UserID'])) {
    // استعلام لاستعادة معلومات الحساب
    $user_id = $_SESSION['UserID'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // التحقق من تطابق كلمة المرور
    $entered_password = $_POST['confirm_password'];
    $stored_password = $user['Password']; // كلمة المرور الموجودة في قاعدة البيانات
    if ($entered_password == $stored_password) {
        // تم تأكيد كلمة المرور

        // قم بتحديث البيانات في قاعدة البيانات
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $stmt_update = $conn->prepare("UPDATE users SET Email = ?, Phone = ? WHERE UserID = ?");
        $stmt_update->execute([$email, $phone, $user_id]);

        // عرض رسالة نجاح عملية التحديث مباشرة في الصفحة
        <script>
        if(isset($message)): 
          alert("<?php echo $message; ?>");
         endif; 
      </script>
              $message = "تم تحديث البيانات بنجاح!";

        // إعادة توجيه المستخدم إلى الصفحة نفسها بعد تحديث البيانات
        header("Location: about.php");
        exit(); // التأكد من أنه لا يتم تنفيذ أي كود آخر بعد عملية إعادة التوجيه
    } else {
        // عرض رسالة خطأ كلمة المرور مباشرة في الصفحة
        echo "<div class='alert alert-danger'>كلمة المرور غير صحيحة!</div>";
        header("Location: about.php");
    }
} 
?>