subject_head.forEach(subject_head => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${admin.admin_id}</td>
                    <td>${admin.name}</td>
                    <td>${admin.username}</td>
                    <td>${admin.email}</td>
                    <td>${admin.birthday}</td>
                    <td>${admin.gender_id}</td>
                    <td>${admin.subject_id}</td>
                    <td>
                        <button onclick="deleteAdmin(${admin.admin_id})">Delete</button>
                        <button onclick="updateAdmin(${admin.admin_id})">Update</button>
                    </td>
                `;
                adminList.appendChild(row);
            });
        }

        // Hàm xóa admin
        function deleteTBM(adminId) {
            fetch('/truongbomon/delete-tbm', {
                    method: 'DELETE',
                    headers: {
                        '   Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                    fetchTBMList(); // Sau khi xóa, làm mới danh sách tbm
                    document.getElementById('message').textContent = data.message;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message').textContent = 'Error occurred while deleting TBM';
                });
        }


        // Hàm cập nhật tbm
        function updateTBM(tbmId) {
            // TODO: Thêm mã để cập nhật admin
        }
        document.getElementById('createTBMForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            fetch('/truongbomon/create-tbm', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('message').textContent = data.result.status_value;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>
