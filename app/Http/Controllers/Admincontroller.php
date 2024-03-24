<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Admincontroller extends Controller
{
    public function getAdmin()
    {
        // $check = Auth::guard('admins')->check();
        // dd($check);
        // if (session()->has('login') && session()->get('login') == true) {
        $admin = new admin();
        $getAllAdmin = $admin->getAdmin();

        return response()->json([
            'getAllAdmin' => $getAllAdmin,
        ]);
        // } else {
        // }
    }
    public function indexLogin()
    {
        return view('loginTest');
    }

    public function submitLogin(Request $request)
    {
        $result = [];

        if ($request->has('username') && $request->has('password')) {
            $username = $request->input('username');
            $password = md5($request->input('password'));

            $user = Admin::where('username', $username)
                ->orWhere('email', $username)
                ->first();

            if (!empty($user)) {
                // Kiểm tra mật khẩu
                if ($password == $user->password) {
                    // Đăng nhập thành công
                    // Auth::guard('admins')->login($user);

                    // Lưu thông tin vào session
                    session()->put('login', true);
                    session()->put('permission', 'admin');

                    $result['status_value'] = "Đăng nhập thành công, chuẩn bị chuyển hướng...";
                    $result['status'] = 1;
                } else {
                    $result['status_value'] = "Sai tài khoản hoặc mật khẩu!";
                    $result['status'] = 0;
                }
            } else {
                $result['status_value'] = "Sai tài khoản hoặc mật khẩu!";
                $result['status'] = 0;
            }
        } else {
            $result['status_value'] = "Nhập tài khoản và mật khẩu!";
            $result['status'] = 0;
        }

        return response()->json([
            'result' => $result,
        ]);
    }



    public function logout(Request $request)
    {
        session()->forget('login');
        session()->forget('permission');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }


    public function check_add_admin_via_file(Request $request)
    {
        // Khởi tạo mảng kết quả
        $result = [];

        // Kiểm tra xem request có chứa file không
        if ($request->hasFile('file')) {
            // Lấy đường dẫn tạm thời của tệp
            $filePath = $request->file('file')->path();

            // Tạo đối tượng đọc file excel
            $reader = IOFactory::createReader('Xlsx');
            // Load file excel
            $spreadsheet = $reader->load($filePath);
            // Lấy dữ liệu từ sheet hiện tại và chuyển thành mảng
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Khởi tạo biến đếm và danh sách lỗi
            $count = 0;
            $errList = [];

            // Duyệt qua từng dòng trong dữ liệu của sheet
            foreach ($sheetData as $key => $row) {
                // Bỏ qua 3 dòng đầu tiên
                if ($key < 4) {
                    continue;
                }

                // Bỏ qua nếu cột A (STT) trống
                if (empty($row['A'])) {
                    continue;
                }

                // Lấy thông tin từ các cột
                $name = $row['B'];
                $username = $row['C'];
                $email = $row['D'];
                $password = md5($row['E']); // Mã hóa mật khẩu
                $birthday = $row['F'];
                $gender = ($row['G'] == 'Nam') ? 2 : (($row['G'] == 'Nữ') ? 3 : 1); // Xác định giới tính

                // Tạo đối tượng Admin mới
                $admin = new Admin([
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'birthday' => $birthday,
                    'gender_id' => $gender,
                    'last_login' => now(),
                ]);

                // Lưu đối tượng Admin vào database
                if ($admin->saveQuietly()) {
                    $count++;
                } else {
                    // Nếu không thành công, thêm STT vào danh sách lỗi
                    $errList[] = $row['A'];
                }
            }

            // Xóa tệp tạm sau khi xử lý xong
            unlink($filePath);

            // Kiểm tra xem có lỗi không
            if (empty($errList)) {
                // Nếu không có lỗi, trả về kết quả thành công
                $result['status_value'] = "Thêm thành công " . $count . " tài khoản!";
                $result['status'] = 1;
            } else {
                // Nếu có lỗi, trả về thông báo lỗi và danh sách STT lỗi
                $result['status_value'] = "Lỗi! Không thể thêm tài khoản có STT: " . implode(', ', $errList) . ', vui lòng xem lại.';
                $result['status'] = 0;
            }
        } else {
            // Nếu không có file được tải lên, trả về thông báo lỗi
            $result['status_value'] = "Không tìm thấy tệp được tải lên!";
            $result['status'] = 0;
        }

        // Trả về kết quả dưới dạng JSON
        return response()->json($result);
        // Cách viết ngắn gọn:
        // return response()->json([
        //     'result' => $result,
        // ]);
    }

    public function indexAdmin()
    {
        return view('admin.CRUD');
    }
    public function createAdmin(Request $request)
    {
        $result = [];

        $name = $request->input('name');
        $username = $request->input('username');
        $password = md5($request->input('password'));
        $email = $request->input('email');
        $birthday = $request->input('birthday');
        $gender = $request->input('gender');

        $admin = new Admin([
            'name' => $name,
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'birthday' => $birthday,
            'gender_id' => $gender,
            'last_login' => now(),

        ]);

        // Lưu admin mới vào cơ sở dữ liệu
        if ($admin->save()) {
            $result = $admin->toArray();
            $result['status_value'] = "Thêm thành công!";
            $result['status'] = 1;
        } else {
            $result['status_value'] = "Lỗi! Tài khoản đã tồn tại!";
            $result['status'] = 0;
        }

        // return response()->json($result);
        return response()->json([
            'result' => $result,
        ]);
    }

    public function deleteAdmin(Request $request)
    {
        $admin = Admin::find($request->admin_id);
        // dd($admin);
        if ($admin) {
            $admin->delete();
            return response()->json([
                'status'    => true,
                'message'   => 'Xoá admin thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Không tìm thấy admin!',
            ], 404);
        }
    }

    public function getAdminId($id)
    {
        $admin = Admin::where('admin_id', $id)->first();
        // dd($admin);
        if (!$admin) {
            return response()->json([
                'status'    => false,
                'message'   => 'Không tìm thấy admin!',
            ], 404);
        }

        return response()->json([
            'status'    => true,
            'admin'     => $admin,
        ]);
    }


    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::find($id);
        dd($admin);
        if (!$admin) {
            return response()->json([
                'status'    => false,
                'message'   => 'Không tìm thấy admin để cập nhật!',
            ], 404);
        }

        // Kiểm tra nếu có password trong request và mã hóa nó bằng MD5
        if ($request->has('password')) {
            $request->merge(['password' => md5($request->password)]);
        }

        // Kiểm tra xem request có chứa file avatar không
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            // Kiểm tra loại file (phải là PNG hoặc JPG)
            if (!in_array($avatar->getClientOriginalExtension(), ['png', 'jpg'])) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Chỉ chấp nhận file PNG hoặc JPG!',
                ]);
            }

            // Lưu ảnh vào thư mục public/images/avatars (hoặc thư mục bạn muốn)
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $avatarName);

            // Cập nhật tên ảnh vào cơ sở dữ liệu
            $admin->avatar = $avatarName;
        }

        // Cập nhật thông tin admin từ dữ liệu trong request
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->password = $request->password; // Đảm bảo rằng bạn đã mã hóa mật khẩu trước khi lưu
        $admin->email = $request->email;
        $admin->birthday = $request->birthday;
        $admin->gender_id = $request->gender_id;

        // Lưu thay đổi
        $admin->save();

        return response()->json([
            'status'    => true,
            'message'   => 'Cập nhật admin thành công!',
        ]);
    }
}
