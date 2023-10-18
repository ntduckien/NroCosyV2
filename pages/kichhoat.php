<?php
    require_once('../core/config.php'); 
    require_once('../core/head.php'); 
    $thongbao = null;
    session_start();
    if (!isset($_SESSION['logger']['username'])) {
        die("Bạn chưa đăng nhập.");
    }

    // Lấy username từ session
    $username = $_SESSION['logger']['username'];

    $sql_active = "SELECT active FROM account WHERE username = '$username'";
    $result = $config->query($sql_active);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $active = $row["active"];
    }

    $sql_thoivang = "SELECT thoi_vang FROM account WHERE username = '$username'";
    $result1 = $config->query($sql_thoivang);

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $thoi_vang = $row1["thoi_vang"];
    }
    if (isset($_POST['submit'])) {
        if ($active == 0) {
            if ($thoi_vang >= $gia_mtv) {
                $sql = "UPDATE account SET active = 1, thoi_vang = thoi_vang - $gia_mtv WHERE username = '$username'";
                $result = $config->query($sql);
                if ($result === TRUE) {
                    $thongbao = '<span style="color: green; font-size: 12px; font-weight: bold;">Bạn đã kích hoạt thành công!</span>';
                } else {
                    $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Xảy ra lỗi!</span>';
                }
            } else {
                $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Không đủ thỏi vàng, vui lòng nạp!</span>';
            }
        } else {
            $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Bạn đã kích hoạt thành viên rồi!</span>';
        }
    }
    
    
?> 
<main>
  <div style="background: #ffe8d1; border-radius: 7px; box-shadow: 0px 2px 5px black;" class="pb-1">
    <div class="text-center col-lg-5 col-md-10" style="margin: auto;">
      <small>Trang thái: 
        <?php 
            if ($active == 0) {
                echo '<b style="color: red">Chưa kích hoạt!</b>';
            } else {
                echo '<b style="color: green">Đã kích hoạt!</b>';
            }
        ?> 
      </small>
      <br> <?=$thongbao;?> <form method="POST" action="">
        <div class="text-center mt-1">
          <input class="btn btn-lg btn-dark btn-block" style="border-radius: 10px;width: 100%; height: 50px;" type="submit" name="submit" value="Bấm để kích hoạt thành viên" />
        </div>
      </form>
    </div>
  </div>
</main> <?php require_once('../core/end.php'); ?>