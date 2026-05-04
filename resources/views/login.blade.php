<!DOCTYPE html>
<html>
<head>
    <title>Login Cashier</title>
</head>
<body>

    <h1>Login ERP POS</h1>

    <form method="POST" action="/login">
        @csrf

        <label>Email</label><br>
        <input type="email" name="email"><br><br>

        <label>Password</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Login</button>
    </form>

</body>
</html>