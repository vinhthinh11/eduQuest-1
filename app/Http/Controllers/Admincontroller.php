<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Admincontroller extends Controller
{
    public function getAdmin()
    {
        // $check = Auth::guard('admins')->check();
        // dd($check);
        if (session()->has('login') && session()->get('login') == true) {
            $admin = new admin();
            $getAllAdmin = $admin->getAdmin();

            return response()->json([
                'getAllAdmin' => $getAllAdmin,
            ]);
        } else {

        }
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
