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

		var baseUrl = '../public/api';

		async function getUserInfo(token) {
		    const response = await fetch(baseUrl+'/indexUsuario', {
		        method: 'get',
		        headers: {
		            "Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão'
		    
		    const data = await response.json();
		    return data;
		}

		async function getCorridaAtual(token) {
		    const response = await fetch(baseUrl+'/corridaAtualMotorista', {
		        method: 'get',
		        headers: {
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão';

		    const data = await response.json();
		    return data;
		}

		async function post(token, payload, rota) {
		    const response = await fetch(baseUrl+rota, {
		        method: 'post',
		        body: JSON.stringify(payload),
		        headers: {
		            "Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    });

		    if(!response.ok)
		        return 'Falha na conexão';

		    const result = await response.json();

		    if(result.status == "error")
		        return result.message;
		    
		    if(result.status == "success")
		        return result.status;
		}

		async function criarCorrida(token, payload) {
		    const result = await post(token, payload, '/criarCorridaMotorista');
		    return result;
		}
	
		document.querySelector('#form').addEventListener('submit', async (event) => {
			event.preventDefault();
			
			payload = {
				email: String(document.querySelector('#email').value),
				password: String(document.querySelector('#senha').value)
			}

			let response = await fetch('', {
				method: 'post',
				body: JSON.stringify(payload),
				headers: {
					"Content-Type": "application/json"
				}
			})

			let result = await response.json();
			let token = result.data.token;
			console.log(token);

			let infos = {
				filtroGenero: 3,
				filtroSaida: '',
				filtroHora: ''
			}

			let infos2 = {saida: 'CA', pontoEncontro: 'Escada', horario: '20:30:00', vagas: '4'}

			let infos3 ={id_corrida: 14}

			let infos4 = {
				id_denunciado: [1],
				comentario: 'Relato x',
				tipo: 'Outro'
			}

			let infos5 = {
				tipo: 1,
			    codigo_validacao: '77BA1547',
			    url_foto: '',
			}
			let infos6 = {
				tipo:2
			}
			let infos7 = {
				image: null,
				nome: "Samuel",
				genero:0,
				email:"samuel@email.com",
				password:"123456",
				modeloCarro: 'Palio',
				placaCarro: 'XXX-1234',
				corCarro: 'Preto'
			}
			//const dados = await cadastroFinal(token, infos5);
			//const dados = await sairCorrida(token);
			//const dados = await denuncia(token, infos4);
			//const dados = await concluirCorrida(token);
			//const dados = await entraCorrida(token, infos3);
			//const dados = await mostraFeed(token, infos);
			//const dados = await criarCorrida(token, infos2);
			//const dados = await getCorridaAtual(token);
			const dados = await getUserInfo(token);
			//const dados = await excluirCorrida(token);
			//const dados = await mudarTipo(token,infos6);
			//const dados = await inserirInfos(token,infos7);
			//const dados = await editarInfos(token,infos7);
			console.log(dados);

			
		});
		async function editarInfos(token, payload) {
			const response = await fetch(baseUrl+'/editarUsuario', {
		        method: 'post',
		        body: JSON.stringify(payload),
		        headers: {
		        	"Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão';

		    const data = await response.json();
		    return data;
		}
		async function inserirInfos(token, payload) {
			const response = await fetch(baseUrl+'/inserirInfosMotorista', {
		        method: 'post',
		        body: JSON.stringify(payload),
		        headers: {
		        	"Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão';

		    const data = await response.json();
		    return data;
		}
		async function mudarTipo(token, payload) {
			const response = await fetch(baseUrl+'/mudarTipoPerfilUsuario', {
		        method: 'post',
		        body: JSON.stringify(payload),
		        headers: {
		        	"Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão';

		    const data = await response.json();
		    return data;
		}
		async function mostraFeed(token, payload) {
			const response = await fetch(baseUrl+'/feedPassageiro', {
		        method: 'post',
		        body: JSON.stringify(payload),
		        headers: {
		        	"Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão';

		    const data = await response.json();
		    return data;
		}

		async function postBuscaUsuario(token, payload) {
			const response = await fetch(baseUrl+'/buscarNomeUsuario', {
		        method: 'post',
		        body: JSON.stringify(payload),
		        headers: {
		        	"Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão';

		    const data = await response.json();
		    return data;
		}

		async function entraCorrida(token, payload) {
		    const result = await post(token, payload, '/entrarCorridaPassageiro');
		    return result;
		}

		async function concluirCorrida(token) {
		    const response = await fetch(baseUrl+'/concluirCorridaMotorista', {
		        method: 'get',
		        headers: {
		            "Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão'
		    
		    const data = await response.json();
		    return data;
		}
		async function excluirCorrida(token) {
		    const response = await fetch(baseUrl+'/cancelarCorridaMotorista', {
		        method: 'get',
		        headers: {
		            "Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão'
		    
		    const data = await response.json();
		    return data;
		}

		async function denuncia(token, payload) {
		    const result = await post(token, payload, '/denunciarUsuario');
		    return result;
		}

		async function sairCorrida(token) {
		    const response = await fetch(baseUrl+'/sairCorridaPassageiro', {
		        method: 'get',
		        headers: {
		            "Content-Type": "application/json",
		            "Authorization": "bearer "+token
		        }
		    })

		    if(!response.ok)
		        return 'Falha na conexão'
		    
		    const data = await response.json();
		    return data;
		}

		async function cadastroFinal(token, payload) {
		    const result = await post(token, payload, '/cadastroFinalUsuario');
		    return result;
		}
		
	</script>

</body>
</html>