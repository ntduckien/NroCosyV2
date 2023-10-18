<?php 
    require_once('../core/config.php'); 
    require_once('../core/head.php'); 
    $thongbao = null;
    session_start();
    if (isset($_SESSION['logger']['username'])) {
        echo '<script>window.location.href = "/";</script>';
        exit();
    }
    if (isset($_POST['submit']) && $_POST['username'] != '' && $_POST['password'] != '') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $userCaptcha = $_POST['captcha']; // Lấy câu trả lời captcha nhập từ người dùng

        // Lấy câu trả lời captcha lưu trong session
        $captchaAnswer = $_SESSION['captcha'];

        // Kiểm tra xem câu trả lời captcha có đúng không
        if ($userCaptcha != $captchaAnswer) {
            $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Captcha không đúng. Vui lòng thử lại.</span>';
        } else {
            $sql = "SELECT * FROM account WHERE username = '$username' AND password = '$password'";
            $account = mysqli_query($config, $sql);
            if (mysqli_num_rows($account) > 0) {
                $_SESSION['logger']['username'] = $username;
                $_SESSION['logger']['password'] = $password;
                echo '<script>window.location.href = "/";</script>';
                $thongbao = '<span style="color: green; font-size: 12px; font-weight: bold;">Đăng nhập thành công!</span>';
            } else {
                $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Sai tài khoản và mật khẩu!</span>';
            }
        }
    }
?>
<main>
<div style="background: #ffe8d1; border-radius: 7px; box-shadow: 0px 2px 5px black;" class="pb-1">
                <form class="text-center col-lg-5 col-md-10" style="margin: auto;"
                      method="post" action="">
                    <h1 class="h3 mb-3 font-weight-normal">Đăng Nhập Tài Khoản</h1>
                    <?=$thongbao;?>
                    <input style="height: 50px; border-radius: 15px; font-weight: bold;" name="username"
                           type="text" class="form-control mt-1" placeholder="Tên tài khoản" autofocus="">
                    <span style="color: red; font-size: 12px; font-weight: bold;">
                                            </span>
                    <input style="height: 50px; border-radius: 15px; font-weight: bold;" name="password"
                           type="password" class="form-control mt-1" placeholder="Mật khẩu">
                    <span style="color: red; font-size: 12px; font-weight: bold;">
                                            </span>
                   
                    <span style="color: red; font-size: 12px; font-weight: bold;">
                                            </span>
                    <div class="row mt-2">
                      <div class="col-6">
                        <input type="text" class="form-control mt-1" name="captcha" placeholder="Nhập captcha" style="height: 50px; border-radius: 15px; font-weight: bold;">
                      </div>
                      <div class="col-6 mt-2">
                        <div class="style_captchaContainer__LdFYB">
                          <!-- Hiển thị hình ảnh captcha -->
                          <img src="../core/captcha.php" alt="Captcha" class="captcha-image">
                        </div>
                      </div>
                    </div>
                    <div class="text-center mt-1">
					                        <button class="btn btn-lg btn-dark btn-block" style="border-radius: 10px;width: 100%; height: 50px;"
                                type="submit" name="submit">Đăng nhập</button>
                    </div>
                </form>

</div>
</main>
<?php require_once('../core/end.php'); ?>