<?php
    require_once('../core/config.php'); 
    require_once('../core/head.php'); 
    $thongbao = null;
    session_start();
    if (!isset($_SESSION['logger']['username'])) {
        die("Bạn chưa đăng nhập.");
    }
        $username = $_SESSION['logger']['username'];
        $sql = "SELECT id FROM account WHERE username = '$username'";
        $result = $config->query($sql);
        if ($result->num_rows > 0) {
          $row_hvd = $result->fetch_assoc();
          $user_id = $row_hvd["id"];
        }
        if (isset($_POST['submit'])) {
        if (empty($_POST['telco']) || empty($_POST['amount']) || empty($_POST['serial']) || empty($_POST['code']) || empty($_POST['captcha'])) 
        {
            $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Bạn cần nhập đầy đủ thông tin hoặc xác minh captcha!</span>';
        } else {
            // Lấy giá trị Captcha từ người dùng
            $userCaptcha = $_POST['captcha'];

            // Lấy giá trị Captcha lưu trong session
            $captchaAnswer = $_SESSION['captcha'];

            // Kiểm tra xem Captcha có đúng không
            if ($userCaptcha != $captchaAnswer) {
                $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">Captcha không đúng. Vui lòng thử lại.</span>';
            } else {
                $partner_id = $partner_id_config; // TẠO Ở DOITHE1S
                $partner_key = $partner_key_config;  // TẠO Ở DOITHE1S
                $dataPost = array();
                $dataPost['request_id'] = rand(100000000, 999999999); //Mã đơn hàng của bạn
                $dataPost['code'] = $_POST['code'];
                $dataPost['partner_id'] = $partner_id;
                $dataPost['serial'] = $_POST['serial'];
                $dataPost['telco'] = $_POST['telco'];
                $dataPost['amount'] = $_POST['amount'];
                $dataPost['command'] = 'charging';  // NẠP THẺ
                $dataPost['sign'] = md5($partner_key.$_POST['code'].$_POST['serial']); //mã hóa chữ ký :md5(partner_key + code + serial)
                $data = http_build_query($dataPost);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://doithe1s.vn/chargingws/v2');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $obj = json_decode($result);
                if ($obj->status == 99) {
                    //Gửi thẻ thành công, đợi duyệt.
                    $thongbao = '<span style="color: orange; font-size: 12px; font-weight: bold;">'.$obj->message.'</span>';
                    // Thẻ đúng, chèn dữ liệu vào bảng "napthe"
                    $user_nap = $user_id;
                    $telco = $_POST['telco'];
                    $serial = $_POST['serial'];
                    $code = $_POST['code'];
                    $amount = $_POST['amount'];

                    // Chuẩn bị và thực thi câu truy vấn SQL để chèn dữ liệu vào bảng
                    $insert_query = "INSERT INTO napthe (user_nap, telco, serial, code, amount, status) 
                                     VALUES ('$user_nap', '$telco', '$serial', '$code', $amount, 99)";
                    if (mysqli_query($config, $insert_query)) {
                        // Chèn thành công
                        // Bạn có thể hiển thị thông báo thành công nếu cần thiết
                    } else {
                        // Chèn thất bại
                        // Bạn có thể hiển thị thông báo lỗi nếu cần thiết
                    }
                } elseif ($obj->status == 1) {
                    //Thẻ đúng
                    $thongbao = '<span style="color: green; font-size: 12px; font-weight: bold;">'.$obj->message.'</span>';
                } elseif ($obj->status == 2) {
                    //Thẻ đúng nhưng sai mệnh giá
                    $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">'.$obj->message.'</span>';
                } elseif ($obj->status == 3) {
                    //Thẻ lỗi
                    $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">'.$obj->message.'</span>';
                } elseif ($obj->status == 4) {
                    //Bảo trì
                    $thongbao = '<span style="color: red; font-size: 12px; font-weight: bold;">'.$obj->message.'</span>';
                } else {
                    //Lỗi khác
                    $thongbao = '<span style="color: orange; font-size: 12px; font-weight: bold;">'.$obj->message.'</span>';
                }
            }
        }
    }   
?>
<main>
      
      <div class="p-1 mt-1 ibox-content" style="border-radius: 7px; box-shadow: 0px 0px 5px black;">
                <div class="card">
                  <div class="card-header">
                    <b>Nạp tiền (Thẻ Cào)</b>
                    <br>
                    <b class="badge" style="background-color: rgb(243, 146, 101);">Tỉ giá quy đổi: 10.000đ = 10.000đ</b>
                    <a href="/atm_bank"><b class="badge" style="background-color: rgb(101, 160, 243);">Nạp Qua Ngân Hàng (<b>AUTO</b>)</b></a>
                  </div>
                  <div class="card-body">
                    <form style="margin: auto;"
                      method="post" action="/napthe">
                      <?=$thongbao;?>
                      <div class="form-group">
                        <label for="pwd">
                          <b>Loại thẻ:</b>
                        </label>
                        <select class="form-control mt-1" name="telco" style="border-radius: 7px; box-shadow: red 0px 0px 5px;">
                          <option value="">Chọn loại thẻ</option>
                          <option value="VIETTEL">Viettel</option>
                          <option value="VINAPHONE">Vinaphone</option>
                          <option value="MOBIFONE">Mobifone</option>
                        </select>
                      </div>
                      <div class="form-group mt-2">
                        <label>
                          <b>Mã thẻ:</b>
                        </label>
                        <input class="form-control mt-1" type="number" name="code" placeholder="Mã thẻ" style="border-radius: 7px; box-shadow: red 0px 0px 5px;">
                      </div>
                      <div class="form-group mt-2">
                        <label>
                          <b>Seri thẻ:</b>
                        </label>
                        <input class="form-control mt-1" type="number" name="serial" placeholder="Seri thẻ" style="border-radius: 7px; box-shadow: red 0px 0px 5px;">
                      </div>
                      <div class="form-group mt-2">
                        <label>
                          <b>Mệnh giá thẻ:</b>
                        </label>
                        <select class="form-control mt-1" name="amount" style="border-radius: 7px; box-shadow: red 0px 0px 5px;">
                          <option value="">Chọn mệnh giá thẻ</option>
                          <option value="10000">10,000 VNĐ</option>
                          <option value="20000">20,000 VNĐ</option>
                          <option value="30000">30,000 VNĐ</option>
                          <option value="50000">50,000 VNĐ</option>
                          <option value="100000">100,000 VNĐ</option>
                          <option value="200000">200,000 VNĐ</option>
                          <option value="300000">300,000 VNĐ</option>
                          <option value="500000">500,000 VNĐ</option>
                          <option value="1000000">1,000,000 VNĐ</option>
                        </select>
                      </div>
                      <div class="row mt-2">
                        <label>
                          <b>Xác minh:</b>
                        </label>
                      <div class="col-6">
                        <input type="text" class="form-control mt-1" name="captcha" placeholder="Nhập captcha" style="border-radius: 7px; box-shadow: red 0px 0px 5px;">
                      </div>
                      <div class="col-6 mt-2">
                        <div class="style_captchaContainer__LdFYB">
                          <!-- Hiển thị hình ảnh captcha -->
                          <img src="../core/captcha.php" alt="Captcha" class="captcha-image">
                        </div>
                      </div>
                    </div>
                      <div class="form-group mt-2">
                        <button name="submit" type="submit" class="btn btn-action text-white" style="border-radius: 7px;">Gửi thẻ</button>
                      </div>
                    </form>
                  </div>
                </div>
            <hr>
            <div class="table-responsive">
              <div style="line-height: 15px;font-size: 12px;padding-right: 5px;margin-bottom: 8px;padding-top: 2px;" class="text-center">
                <span class="text-black" style="vertical-align: middle;">Hãy <a href="/pages/napthe.php"><b><u>Loading</u></b></a> lại website để cập nhật!</span>
              </div>
              <table class="table table-hover table-nowrap">
                <tbody style="border-color: black;">
                  <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Nhà Mạng</th>
                    <th scope="col">Seri</th>
                    <th scope="col">Mã</th>
                    <th scope="col">Mệnh Giá</th>
                    <th scope="col">Trạng Thái</th>
                  </tr>
                   <?php
                      // Lấy user_id từ session
                      $username = $_SESSION['logger']['username'];
                      $sql = "SELECT id FROM account WHERE username = '$username'";
                      $result = $config->query($sql);
                      if ($result->num_rows > 0) {
                          $row_hvd = $result->fetch_assoc();
                          $user_id = $row_hvd["id"];
                      }
                      $result_account = mysqli_query($config, $user_id);
                      // Trang hiện tại (mặc định là 1 nếu không được chỉ định)
                      $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                      // Số lượng bản ghi hiển thị trên mỗi trang
                      $recordsPerPage = 5;

                      // Tính toán vị trí bắt đầu của bản ghi trong truy vấn SQL dựa trên trang hiện tại
                      $startFrom = ($page - 1) * $recordsPerPage;

                      // Truy vấn dữ liệu từ bảng "napthe" của người dùng đã đăng nhập với giới hạn số lượng bản ghi và vị trí bắt đầu
                      $query_nap = "SELECT * FROM napthe WHERE user_nap = $user_id LIMIT $startFrom, $recordsPerPage";
                      $result = mysqli_query($config, $query_nap);
                      // Sử dụng biến cờ để theo dõi có dữ liệu hay không
                      $hasData = false;
                      // Hiển thị dữ liệu lên bảng
                      while ($row = mysqli_fetch_assoc($result)) {
                      $hasData = true;
                    ?>
                    <tr>
                              <td><b>#<?=$row['id'];?></b></td>
                              <td><b><?=$row['telco'];?><b></td>
                              <td><?=$row['serial'];?></td>
                          <td><?=$row['code'];?></td>
                          <td><?=number_format($row['amount']);?> VNĐ</td>
                              <td><b>
                              <?php
                              if ($row["status"] == 99) {
                                  echo '<font color="orange">Thẻ Chờ</font>';
                              }
                            if ($row["status"] == 1) {
                                  echo '<font color="green">Thẻ Đúng</font>';
                              } 
                            if ($row["status"] == 3) {
                                  echo '<font color="red">Thẻ Sai</font>';
                              } 
                            if ($row["status"] == 2) {
                                  echo '<font color="red">Thẻ Sai Mệnh Giá</font>';
                              }
                            ?>
                              <b></td>
                   </tr> 
                    <?php } 
                                
          // Kiểm tra biến cờ và hiển thị thông báo "Lịch Sử Trống" nếu không có dữ liệu
                    if (!$hasData) {
                       echo ' <tr>
                                 <td colspan="6" align="center"><span style="font-size:100%;"><< Lịch Sử Nạp Trống >></span></td>
                               </tr>';
                   }
                   ?>
                </tbody>
              </table>
           <?php
          // ...
          // Tính tổng số trang dựa trên số lượng bản ghi và số lượng bản ghi hiển thị trên mỗi trang
          $totalPages = ceil(mysqli_num_rows(mysqli_query($config, "SELECT * FROM napthe WHERE user_nap = $user_id")) / $recordsPerPage);
          ?>

          <!-- Thêm vào sau phần hiển thị lịch sử nạp thẻ -->
          <div class="pagination">
              <?php
              // Hiển thị nút Previous (trang trước)
              if ($page > 1) {
                  echo '<a href="napthe.php?page=' . ($page - 1) . '"><< Trước</a>';
              }

              // Hiển thị các nút trang
              for ($i = 1; $i <= $totalPages; $i++) {
                  echo '<a href="napthe.php?page=' . $i . '">' . $i . '</a>';
              }

              // Hiển thị nút Next (trang kế tiếp)
              if ($page < $totalPages) {
                  echo '<a href="napthe.php?page=' . ($page + 1) . '">Sau >></a>';
              }
              ?>
          </div>
          <style>
              .pagination {
                  display: flex;
                  justify-content: center;
              }

              .pagination a {
                  color: black;
                  padding: 8px 16px;
                  text-decoration: none;
                  border: 1px solid #ddd;
                  margin: 0 4px;
              }

              .pagination a.active {
                  background-color: #4CAF50;
                  color: white;
              }

              .pagination a:hover:not(.active) {
                  background-color: #ddd;
              }
          </style>
            </div>
            </div>
    <div>
    </div>
</main>
<?php require_once('../core/end.php'); ?>