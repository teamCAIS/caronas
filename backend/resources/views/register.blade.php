<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form method='POST' action='../api/preCadastro' enctype="multipart/form-data">
			<input type="text" name="nome" placeholder="Nome">
			<input type="text" name="nascimento" placeholder="Nascimento">
			<input type="text" name="genero" placeholder="Genero">
			<input type="text" name="email" placeholder="Email">
			<input type="text" name="password" placeholder="Senha">
			<input type="file" name="documento">
			<button type="submit">Enviar</button>
		</form>
    </body>
</html>
