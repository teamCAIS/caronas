<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="estilos.css">
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

	<form id="form">

		<label>
			Email
			<input id="email" name="email" type="email"> <br>
		</label>
		<label>
			Senha
			<input id="senha" name="senha" type="password"> <br>
		</label>
		<button id="btn-login">Login</button>
		
	</form>
	
	<script>
		
		var url = '..'
	
		document.querySelector('#form').addEventListener('submit', (event) => {
			event.preventDefault();
			
			payload = {
				email: String(document.querySelector('#email').value),
				password: String(document.querySelector('#senha').value)
			}
			fetch('', {
				method: 'post',
				body: JSON.stringify(payload),
				headers: {
					"Content-Type": "application/json"
				  }
			})
			.then(response => response.json())
			.then(result =>{
				fetch('../public/api/historicoPassageiro', {
					method: 'get',
					headers: {
						"Content-Type": "application/json",
						"Authorization": "bearer "+result['data']['token']
					  }
				})
				.then(response => response.json())
				.then(result => {
					console.log(result);
				});
			});
		})
		
	</script>

</body>
</html>