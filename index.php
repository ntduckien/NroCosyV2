<?php 
  require_once('core/config.php'); 
  require_once('core/head.php'); 
?>
        <div class="p-1 mt-1 ibox-content" style="border-radius: 7px; box-shadow: 0px 0px 5px black;">
          <br>
          <main>
            <h1 class="h3 mb-3 font-weight-normal">
              <b>
                <u>
                  <center>Bảng Xếp Hạng Top Sức Mạnh</center>
                </u>
              </b>
            </h1>
            <div class="table-responsive">
              <div style="line-height: 15px;font-size: 12px;padding-right: 5px;margin-bottom: 8px;padding-top: 2px;" class="text-center">
                <span class="text-black" style="vertical-align: middle;">Thoát nick để xem BXH của bản thân!</span>
              </div>
              <table class="table table-hover table-nowrap">
                <tbody style="border-color: black;">
                  <tr>
                    <th scope="col">Top</th>
                    <th scope="col">Nhân vật</th>
                    <th scope="col">Sức Mạnh</th>
                  </tr>
                  <?php
                    // Truy vấn dữ liệu từ cột data_point
                    $sql = "SELECT id, name, SUBSTRING_INDEX(SUBSTRING_INDEX(data_point, ',', 2), ',', -1) AS second_value
                    FROM player
                    ORDER BY second_value + 0 DESC
                    LIMIT 5";


                    $result = $config->query($sql);

                    if ($result->num_rows > 0) {
                      $data = array();
                      while ($row = $result->fetch_assoc()) {
                          $data[] = $row;
                      }

                      usort($data, function ($a, $b) {
                          return $b['second_value'] - $a['second_value'];
                      });
                      foreach ($data as $index => $row) {
                       echo '<tr>
                              <td><b>#'.($index + 1) .'</b></td>
                              <td>'.$row['name'].'</td>
                              <td>'.number_format($row['second_value']).' Sức Mạnh</td>
                            </tr>';
                      }
                    } else {
                      echo ' <tr>
                              <td colspan="3" align="center"><span style="font-size:100%;"><< Bảng Xếp Hạng Trống >></span></td>
                            </tr>';
                    }
                  ?> 
                </tbody>
              </table>
            </div>
            <h1 class="h3 mb-3 font-weight-normal">
              <b>
                <u>
                  <center>Bảng Xếp Hạng Top Nhiệm Vụ</center>
                </u>
              </b>
            </h1>
            <div class="table-responsive">
              <div style="line-height: 15px;font-size: 12px;padding-right: 5px;margin-bottom: 8px;padding-top: 2px;" class="text-center">
                <span class="text-black" style="vertical-align: middle;">Thoát nick để xem BXH của bản thân!</span>
              </div>
              <table class="table table-hover table-nowrap">
                <tbody style="border-color: black;">
                  <tr>
                    <th scope="col">Top</th>
                    <th scope="col">Nhân vật</th>
                    <th scope="col">Nhiệm Vụ Thứ</th>
                  </tr>
                  <?php
                  $stt = 1;
                  $data = mysqli_query($config, "SELECT name, JSON_EXTRACT(data_task, '$[0]') AS second_value
                                                  FROM player
                                                  ORDER BY CAST(JSON_EXTRACT(data_task, '$[0]') AS UNSIGNED) DESC
                                                  LIMIT 5;");

                  // Sử dụng biến cờ để theo dõi có dữ liệu hay không
                  $hasData = false;

                  while ($row = mysqli_fetch_array($data)) {
                      $hasData = true;
                      echo '<tr>
                            <td><b>#'.$stt.'</b></td>
                            <td>'.$row['name'].'</td>
                            <td>'.number_format($row['second_value']).' Nhiệm Vụ</td>
                          </tr>';
                      $stt++;
                  }

                  // Kiểm tra biến cờ và hiển thị thông báo "Lịch Sử Trống" nếu không có dữ liệu
                  if (!$hasData) {
                      echo ' <tr>
                                <td colspan="3" align="center"><span style="font-size:100%;"><< Bảng Xếp Hạng Trống >></span></td>
                              </tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <h1 class="h3 mb-3 font-weight-normal">
              <b>
                  <center>Bảng Xếp Hạng Đệ Tử</center>
              </b>
            </h1>
            <div class="table-responsive">
              <div style="line-height: 15px;font-size: 12px;padding-right: 5px;margin-bottom: 8px;padding-top: 2px;" class="text-center">
                <span class="text-black" style="vertical-align: middle;">Cập nhật 5 phút 1 lần</span>
              </div>
              <table class="table table-hover table-nowrap">
                <tbody style="border-color: black;">
                  <tr>
                    <th scope="col">Top</th>
                    <th scope="col">Nhân vật</th>
                    <th scope="col">Sức Mạnh</th>
                  </tr>
                  <?php
                   $stt = 1;
                   $data = mysqli_query($config, "SELECT name, SUBSTRING_INDEX(SUBSTRING_INDEX(pet, ',', 8), ',', -1) AS second_value
                                                   FROM player
                                                   ORDER BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(pet, ',', 8), ',', -1) AS UNSIGNED) DESC
                                                   LIMIT 5;");

                   // Sử dụng biến cờ để theo dõi có dữ liệu hay không
                   $hasData = false;

                   while ($row = mysqli_fetch_array($data)) {
                       $hasData = true;
                       echo '<tr>
                             <td><b>#'.$stt.'</b></td>
                             <td>'.$row['name'].'</td>
                             <td>'.number_format($row['second_value']).' Sức Mạnh</td>
                           </tr>';
                       $stt++;
                   }

                   // Kiểm tra biến cờ và hiển thị thông báo "Lịch Sử Trống" nếu không có dữ liệu
                   if (!$hasData) {
                       echo ' <tr>
                                 <td colspan="3" align="center"><span style="font-size:100%;"><< Bảng Xếp Hạng Trống >></span></td>
                               </tr>';
                   }
                   ?>
                    </tbody>
              </table>
            </div>
          </main>
        </div>
      </main>
      <div class="modal right fade" id="Noti_Home" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-mdb-backdrop="static" data-mdb-keyboard="true">
    <div class="modal-dialog modal-side modal-bottom-right ">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2c2c2c; color: #FFF; text-align: center;">
                <img src="../hoangvietdung_public/images/logo/logo.png" style="display: block; margin-left: auto; margin-right: auto; max-width: 150px;">
            </div>
            <div class="modal-body">
            <center><p style="padding: 10px">
                    <b style="color:red"><u>Thông Báo</u></b><br>
                    Tham gia <?=$server_name;?> trên các nền tảng mạng xã hội nhé.!<br><br>
                    <a href="https://zalo.me/g/bvhtme411" class="btn btn-download" style="border-radius: 10px; color: #FFFFFF;" target="_blank"><b>Box Zalo</b></a>
                </p>
          </center>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#Noti_Home').modal('show');
    })
</script>
<?php require_once('core/end.php'); ?>