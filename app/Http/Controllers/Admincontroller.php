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
    // public function indexLogin()
    // {
    //     return view('loginTest');
    // }

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
        $result = [];

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->path();

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filePath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $count = 0;
            $errList = [];

            foreach ($sheetData as $key => $row) {
                if ($key < 4) {
                    continue;
                }

                if (empty($row['A'])) {
                    continue;
                }

                $name = $row['B'];
                $username = $row['C'];
                $email = $row['D'];
                $password = md5($row['E']);
                $birthday = $row['F'];
                // $gender = ($row['G'] == 'Nam') ? 2 : (($row['G'] == 'Nữ') ? 3 : 1);
                $admin = new Admin([
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'birthday' => $birthday,
                    'gender_id' => $gender,
                    'last_login' => now(),
                ]);

                // Lưu đối tượng Admin
                if ($admin->saveQuietly()) {
                    $count++;
                } else {
                    $errList[] = $row['A'];
                }
            }

            unlink($filePath);

            if (empty($errList)) {
                $result['status_value'] = "Thêm thành công " . $count . " tài khoản!";
                $result['status'] = 1;
            } else {
                $result['status_value'] = "Lỗi! Không thể thêm tài khoản có STT: " . implode(', ', $errList) . ', vui lòng xem lại.';
                $result['status'] = 0;
            }
        } else {
            $result['status_value'] = "Không tìm thấy tệp được tải lên!";
            $result['status'] = 0;
        }

        return response()->json($result);
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



    public function updateAdmin(Request $request)
    {
        $admin = Admin::where('id', $request->id)->first();

        $data = $admin->all();
        if (isset($admin)) {
            $request->update($data);
            return response()->json([
                'status'    => true,
                'message'   => 'Cập nhật khách hàng thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Cập nhật khách hàng không thành công!',
            ]);
        }
    }
}
