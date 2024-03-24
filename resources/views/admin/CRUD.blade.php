<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
</head>

<body>
    <!-- Chuyển token CSRF vào JavaScript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <h2>Create Admin</h2>
    <form id="createAdminForm">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name"><br>

        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username"><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br>

        <label for="birthday">Birthday:</label><br>
        <input type="date" id="birthday" name="birthday"><br>

        <label for="gender">Gender:</label><br>
        <select id="gender" name="gender">
            <option value="1">Male</option>
            <option value="2">Female</option>
            <option value="3">Other</option>
        </select><br><br>

        <button type="submit">Create Admin</button>
    </form>
    <div id="message"></div>


    <h2>Admin List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Birthday</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="adminList">
            <!-- Dữ liệu admin sẽ được thêm vào đây -->
        </tbody>
    </table>

    <div id="message"></div>
    <div id="updateAdminForm">
        <!-- Form cập nhật admin sẽ được thêm vào đây -->
    </div>

    <script>
        // Lấy token CSRF từ thẻ meta
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Lấy danh sách admin khi trang được tải
        document.addEventListener("DOMContentLoaded", function() {
            fetchAdminList();
        });

        // Hàm gửi yêu cầu lấy danh sách admin
        function fetchAdminList() {
            fetch('/admin/get')
                .then(response => response.json())
                .then(data => {
                    displayAdminList(data.getAllAdmin);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Hàm hiển thị danh sách admin
        function displayAdminList(admins) {
            const adminList = document.getElementById('adminList');
            adminList.innerHTML = ''; // Xóa nội dung cũ

            admins.forEach(admin => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${admin.admin_id}</td>
            <td>${admin.name}</td>
            <td>${admin.username}</td>
            <td>${admin.email}</td>
            <td>${admin.birthday}</td>
            <td>${admin.gender_id}</td>
            <td>
                <button onclick="deleteAdmin(${admin.admin_id})">Delete</button>
                <button onclick="updateAdmin(${admin.admin_id})">Update</button>
            </td>
        `;
                adminList.appendChild(row);
            });
        }

        // Hàm xóa admin
        function deleteAdmin(adminId) {
            fetch('/admin/delete-admin', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        admin_id: adminId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    fetchAdminList(); // Sau khi xóa, làm mới danh sách admin
                    document.getElementById('message').textContent = data.message;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message').textContent = 'Error occurred while deleting admin';
                });
        }

        // Hàm cập nhật admin
        function updateAdmin(adminId) {
            fetch(`/admin/get-admin/${adminId}`)
                .then(response => response.json())
                .then(admin => {
                    // Hiển thị form cập nhật với dữ liệu của admin
                    const updateAdminForm = document.getElementById('updateAdminForm');
                    if (updateAdminForm) {
                        updateAdminForm.innerHTML = `
                    <h2>Update Admin</h2>
                    <form id="updateForm" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="admin_id" value="${admin.admin_id}">
                        <label for="name">Name:</label><br>
                        <input type="text" id="updateName" name="name" value="${admin.name}"><br>
                        <label for="username">Username:</label><br>
                        <input type="text" id="updateUsername" name="username" value="${admin.username}"><br>
                        <label for="password">Password:</label><br>
                        <input type="text" id="updatePassword" name="password" value="${admin.password}"><br>
                        <label for="email">Email:</label><br>
                        <input type="text" id="updateEmail" name="email"value="${admin.email}"><br>
                        <label for="birthday">Birthday:</label><br>
                        <input type="date" id="updateBirthday" name="birthday" value="${admin.birthday}"><br>
                        <label for="gender">Gender:</label><br>
                        <input type="text" id="updateGender" name="gender" value="${admin.gender_id}"><br>
                        <label for="avatar">Avatar:</label><br>
                        <input type="file" id="updateAvatar" name="avatar"><br><br>

                        <button type="submit">Update Admin</button>
                    </form>
                    <div id="updateMessage"></div>
                `;
                        // Submit form cập nhật
                        document.getElementById('updateForm').addEventListener('submit', function(event) {
                            event.preventDefault();
                            var formData = new FormData(this);

                            fetch(`/admin/update-admin/${admin.admin_id}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: formData,
                                })
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('updateMessage').textContent = data.message;
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        });
                    } else {
                        console.error('Element with id "updateAdminForm" does not exist');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>

</html>
