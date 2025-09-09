<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashbord - SAS SMK Taruna Bakti Kertosono</title>
</head>
<body>
    <h1>ini dashboard</h1>
    <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-danger">Logout</button>
</form>

<script>
    // Cegah kembali ke halaman sebelumnya (misalnya login)
    history.pushState(null, document.title, location.href);
    window.addEventListener('popstate', function () {
        history.pushState(null, document.title, location.href);x
    });
</script>


</body>
</html>
