
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
</head>
<body class="antialiased">
    <form id="add_via_file" enctype="multipart/form-data" action="{{ route('check_add_tbm_via_file') }}" method="POST">
        @csrf
        <div class="file-field input-field col s6">
            <div class="btn input-field">
                <span>File</span>
                <input type="file" name="file" id="file" required>
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text">
            </div>
            <br>
            <div class="input-field" style="padding-left: 0">
                <button class="btn" type="submit" name="submit">Thêm</button>
            </div>
        </div>
    </form>
</body>
</html>
