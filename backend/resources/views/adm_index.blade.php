<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Página Inicial Administrador</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
		<style>
			h1,table{
				position:relative;
			}
			.tabela{
				border-collapse:collapse;
				border-spacing:0;
				width:100%;
				display:table
				}
			.tabela{
				border:1px solid #ccc
				}
			.tabela-header{
				color:#fff!important;
				background-color:#f44336!important
			}
		</style>
    </head>
    <body>
		<button onclick="window.location='{{ route("admin.logout")}}'">Logout</button>
	   <h1>Pré-cadastros</h1>
       <table class='tabela'>
			<thead>
				<tr class='tabela-header'>
				   <td> Nome </td> 
				   <td> Nascimento </td>
				   <td> Email </td>
				   <td> Genero </td>
				   <td> Documento </td>
				   <td> Status </td>
				   <td> Validar </td>
				</tr>
			</thead>
		   @foreach($precadastros as $value)
			<tr>
			   <td> {{$value->nome}} </td> 
			   <td> {{$value->nascimento}} </td>
			   <td> {{$value->email}} </td>
			   @if($value->genero == 0)
			   <td> Masculino </td>
			   @else
					@if($value->genero == 1)
						<td>Feminino</td>
					@else
						<td>Prefere não identificar
					@endif
				@endif
			   <td> <a href="../{{$value->url_documento}}">Link</a> </td>
			    @if($value->status == 0)
			   <td> Não validado </td>
			   @else
					@if($value->status == 1)
						<td>Validado</td>
					@else
						<td>Bloqueado
					@endif
				@endif
			   @if($value->status == 0)
			   <td> <button type='button' onclick="window.location='{{ route("admin.validarUsuario", $value->id)}}'">Aceitar</button> <button type='button' onclick="window.location='{{ route("admin.recusarUsuario", $value->id)}}'">Recusar</button> </td>
			   @endif
			</tr>
			<br>
		  @endforeach
		</tbody>
    </body>
</html>
