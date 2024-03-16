<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Admincontroller extends Controller
{
    public function getAdmin()
    {
        $admin = new admin();
        $getAllAdmin = $admin->getAdmin();

        return response()->json([
            'getAllAdmin' => $getAllAdmin,
        ]);
    }
    public function indexLogin()
    {
        return view('loginTest');
    }

    public function submitLogin(Request $request)
    {
        $result = [];

        // Kiểm tra xem người dùng đã gửi dữ liệu (username và password) hay chưa
        if ($request->has(['username', 'password'])) {
            $username = $request->input('username');
            $password = md5($request->input('password'));

            // Tìm kiếm người dùng trong cơ sở dữ liệu
            $user = Admin::where('username', $username)
                ->orWhere('email', $username)
                ->first();

            if ($user) {
                if ($password == $user->password) {
                    $result['status_value'] = "Đăng nhập thành công, chuẩn bị chuyển hướng...";
                    $result['status'] = 1;

                    // Đặt session thông qua middleware
                    auth('admins')->login($user);

                    // Hoặc sử dụng session flash để tạo thông báo chỉ một lần
                    session()->flash('permission', $user->permission);
                } else {
                    // Nếu mật khẩu không hợp lệ, trả về tin nhắn lỗi
                    $result['status_value'] = "Sai mật khẩu!";
                    $result['status'] = 0;
                }
            } else {
                $result['status_value'] = "Tài khoản hoặc email không tồn tại!";
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
        Auth::guard('admins')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
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
