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
        <form method='POST' action='../api/cadastroFinalUsuario' enctype="multipart/form-data">
			<input type="file" name="image">
			<input type="text" name="codigo_validacao" placeholder="Código de Validação">
			<input type="text" name="tipo" placeholder="Tipo">
			<br>
			<input type="text" name="modelo" placeholder="Modelo Carro">
			<input type="text" name="placa" placeholder="Placa Carro">
			<input type="text" name="corCarro" placeholder="Cor Carro">
			<button type="submit">Enviar</button>
		</form>
    </body>
</html>
