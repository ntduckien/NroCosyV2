<?php
require_once('../core/config.php'); 
// mặc định khi cấu hình api post ở doithe1s.vn callback gọi về post json

 $txtBody = file_get_contents('php://input');
// $jsonBody = json_decode($txtBody,true); // chuyển chuỗi JSON thành một mảng

// Ở ĐÂY MÌNH CHUYỂN CHUỖI THÀNH 1 ĐỐI TƯỢNG NHÉ 
 $jsonBody = json_decode($txtBody); 

    if (isset($jsonBody->callback_sign)) 
    {   
        /// status = 1 ==> thẻ đúng
        /// status = 2 ==> thẻ sai mệnh giá
        /// status = 3 ==> thẻ lỗi
        /// status = 99 ==> thẻ chờ xử lý

        //// Kết quả trả về sẽ có các trường như sau:
        $partner_key = $partner_key_config;// key của quý khách tại doithe1s
        
        //ĐỐI CHỮ KÝ, CŨNG CÓ THỂ BỎ QUA ĐỐI CHIẾU NẾU CẢM THẤY KHÔNG CẦN THIẾT 
        $callback_sign = md5($partner_key . $jsonBody->code . $jsonBody->serial);
        if ($jsonBody->callback_sign == $callback_sign) 
        {

            $getdata['status'] = $jsonBody->status; // Trạng thái thẻ
            $getdata['message'] = $jsonBody->message; // thông báo kèm theo thẻ
            $getdata['request_id'] = $jsonBody->request_id;   /// Mã giao dịch của bạn
            $getdata['trans_id'] = $jsonBody->trans_id;   /// Mã giao dịch của doithe1s.vn
            $getdata['declared_value'] = $jsonBody->declared_value;  /// Mệnh giá mà bạn khai báo 
            $getdata['value'] = $jsonBody->value;  /// Mệnh giá thực tế của thẻ
            $getdata['amount'] = $jsonBody->amount;   /// Số tiền bạn nhận về (VND)
            $getdata['code'] = $jsonBody->code;   /// Mã nạp
            $getdata['serial'] = $jsonBody->serial;  /// Serial thẻ
            $getdata['telco'] = $jsonBody->telco;   /// Nhà mạng
            print_r($getdata);
        }
        
        // Kiểm tra nếu đối chứng chữ ký hợp lệ
        $callback_sign = md5($partner_key . $jsonBody->code . $jsonBody->serial);
        if ($jsonBody->callback_sign == $callback_sign) {

            // Lấy cột "user_nap" từ bảng "napthe" dựa vào thông tin "code" và "serial" từ callback data
            $code = $jsonBody->code;
            $serial = $jsonBody->serial;
            $get_user_nap_sql = "SELECT user_nap FROM napthe WHERE code = '$code' AND serial = '$serial'";
            $result = $config->query($get_user_nap_sql);

            if ($result->num_rows > 0) {
                // Lấy dữ liệu từ kết quả truy vấn
                $row = $result->fetch_assoc();
                $user_nap = $row['user_nap'];

                // Tiến hành câu truy vấn UPDATE trong bảng "napthe" để cập nhật trạng thái (status)
                $update_status_sql = "UPDATE napthe SET status = $jsonBody->status WHERE code = '$code' AND serial = '$serial'";
                if ($config->query($update_status_sql) === TRUE) {
                    echo "Cập nhật trạng thái (status) trong bảng napthe thành công!";
                } else {
                    echo "Lỗi khi cập nhật trạng thái (status) trong bảng napthe: " . $config->error;
                }

                // Kiểm tra và cập nhật cột "coin" trong bảng "account" nếu $jsonBody->status = 1
                if ($jsonBody->status == 1) {
                    $declared_value = $jsonBody->declared_value;
					$declared_value_integer = (int) $declared_value;
                    // Update cột "coin" trong bảng "account" dựa vào thông tin "user_nap"
                    $account_update_sql = "UPDATE account SET coin = coin + $declared_value_integer WHERE id = '$user_nap'";

                    if ($config->query($account_update_sql) === TRUE) {
                        echo "Cập nhật cột coin thành công trong bảng account!";
                    } else {
                        echo "Lỗi khi cập nhật cột coin trong bảng account: " . $config->error;
                    }
                  
                  	// Update cột "vnd" trong bảng "account" dựa vào thông tin "user_nap"
                    $account_update_sql_vnd = "UPDATE account SET vnd = vnd + $declared_value_integer WHERE id = '$user_nap'";

                    if ($config->query($account_update_sql_vnd) === TRUE) {
                        echo "Cập nhật cột vnd thành công trong bảng account!";
                    } else {
                        echo "Lỗi khi cập nhật cột vnd trong bảng account: " . $config->error;
                    }
                }
            } else {
                // Không tìm thấy thông tin người dùng trong bảng "napthe"
                echo "Không tìm thấy thông tin người dùng trong bảng napthe!";
            }
        }
    }




?>