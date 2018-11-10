<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="estilos.css">
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

	<form id="form" method='POST' action=''>
		{{ csrf_field() }}
		<label>
			Email
			<input id="email" name="email" type="email"> <br>
		</label>
		<label>
			Senha
			<input id="senha" name="password" type="password"> <br>
		</label>
		<button id="btn-login">Login</button>
		
	</form>
</body>
</html>